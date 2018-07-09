<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/31
 * Time: 上午11:25
 */
namespace wslibs\wszc\publicnumber;
use think\Db;
use wslibs\wszc\LoginUser;


class Config
{

    public static $_errcode = 0;

    public static function get_access_token($is_add=false)
    {
        $res = file_get_contents(self::url());

        if(!$res) return false;

        $data = json_decode($res,true);

        if(json_last_error()==JSON_ERROR_NONE){

            if($res['errcode']!=0){
                self::$_errcode = -1;
                return false;
            }

            if($is_add){
                return $data;
            }
            return $data['access_token'];
        }
        self::$_errcode = -2;
        return false;
    }

    public static function addAccessToken($arr,$type=1)
    {

        $data['add_time'] = time();
        $data['expires_time'] = $data['add_time']+$arr['expires_in'];
        $data['access_token'] = $arr['access_token'];
        $data['idid'] = 0;
        $data['type'] = $type;
        if($type==2 ||$type==3){
            $data['openid'] = $arr['openid'];
            $data['state'] = $arr['state'];
        }

        return Db::name('access_token')->insert($data);
    }

    public static function saveAccessTokenIdId($idid,$openid)
    {

        $data['idid'] = $idid;

        return Db::name('access_token')->where("openid = '$openid'")->update($data);
    }

    private static function url()
    {
        return "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".BaseVar::$AppID."&secret=".BaseVar::$AppSecret;
    }

    public static function getDbAccessToken($is_gz_or_open=false,$code='',$state='')
    {
        $is_gz_or_open  ? $type = 2  : $type = 1;

        if($state==BaseVar::WEIYI_CODE){
            $type = 3;
            $AccessToken = '';
        }else{
            $AccessToken = self::getDbTokenInfo($type,$state);
        }


        if(!$AccessToken){

            if($is_gz_or_open){
                $arr = PublicNumber::get_access_token($code,$state);
                if($arr['errcode']){
                    return false;
                }
                self::addAccessToken($arr,$type) ;
            }else{
                self::addAccessToken(self::get_access_token(true),$type);
            }

            $AccessToken = self::getDbTokenInfo($type,$state);
        }

        return $AccessToken;
    }

    public static function getAccessTokenAndOpenid($code='',$state='')
    {
        $AccessToken = '';
        $openid = '';
        $type = 1;
        if($state==BaseVar::WEIYI_CODE){
            $type = 3;
        }

        if(!$AccessToken){
            $arr = PublicNumber::get_access_token($code,$state);
            
            if($arr['errcode']){
                return $arr;
            }
            self::addAccessToken($arr,$type);
            $AccessToken = $arr['access_token'];
            $openid = $arr['openid'];
        }
        return [$AccessToken,$openid];
    }



    public static function getOpenidByState($state,$type=2)
    {
        $time = time();
        return  Db::name('access_token')->where("expires_time>'$time' and type='$type' and state='$state'")->order('id desc')->value('openid');
    }

    public static function getDbTokenInfo($type,$state=1)
    {
        $time = time();
        if($type==2 || $type==3){
            $map = "expires_time>'$time' and type='$type'  and state='$state'";
        }else{
            $map = "expires_time>'$time' and type='$type'";
        }
        return  Db::name('access_token')->where($map)->order('id desc')->value('access_token');
    }

    public static function getErrorMsg($code,$msg)
    {
        return ['errcode'=>$code,'errmsg'=>$msg];
    }

    public static function curl_post($postUrl = '', $curlPost = '')
    {
        if (empty($postUrl) || empty($curlPost)) {
            self::$_errcode = -3;
            return false;
        }

        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,5);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        curl_setopt($ch,CURLOPT_URL,$postUrl);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);

        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$curlPost);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            curl_close($ch);
            return $error;
        }
    }
    //将XML转为array
    public static function xmlToArray($xml)
    {
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    //数组转XML
    public static function arrayToXml($arr)
    {
        $xml = "<xml>";
        foreach ($arr as $key=>$val)
        {
            if (is_numeric($val)){
                $xml.="<".$key.">".$val."</".$key.">";
            }else{
                $xml.="<".$key."><![CDATA[".$val."]]></".$key.">";
            }

        }
        $xml.="</xml>";
        return $xml;
    }
}