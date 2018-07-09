<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/31
 * Time: 上午11:25
 */
namespace wslibs\wszc\publicnumber;
use think\Db;

class PublicNumber
{

    public static function layupOtherInfo($arr)
    {
        switch($arr['Event']){
            case BaseVar::EVENT_LOCATION:
                $type = 1;
                break;
            case BaseVar::EVENT_CLICK:
                $type = 2;
                break;
            default:
                $type = 0;
                break;
        }
        $data['type'] = $type;
        $data['openid'] = $arr['FromUserName'];
        $data['content'] = serialize($arr);
        $data['add_time'] = time();

        return Db::name('access_other_info')->insert($data);
    }


    //获取openID的前提code
    public static function getCode()
    {
        $url = BaseVar::$callback_url;

        $link = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.BaseVar::$AppID.'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state='.rand(10000,99999).'#wechat_redirect';

        return $link;
    }

    //获取openID的前提code
    public static function getMyCode()
    {
        $url = BaseVar::$my_url;

        $link = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.BaseVar::$AppID.'&redirect_uri='.urlencode($url).'&response_type=code&scope=snsapi_userinfo&state='.BaseVar::WEIYI_CODE.'#wechat_redirect';

        return $link;
    }

    //获取用户info
    public static function getUserInfo($access_token,$openid)
    {

        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token.'&openid='.$openid.'&lang=zh_CN';

        $res = file_get_contents($url);

        $arr = \GuzzleHttp\json_decode($res,true);

        if($arr['errcode'])    return $arr;

        return $arr;
    }

    //绑定openID
    public static function bindOpenid($openid,$card)
    {
        $is_cunzai = Db::name('idcards')->where('id_card',$card)->find();

        if(!$is_cunzai) return -1;

        if($is_cunzai['openid']) return -2;

        Db::name('idcards')->where('id_card',$card)->update(['openid'=>$openid,'openid_time'=>time()]);

        Config::saveAccessTokenIdId($is_cunzai['id'],$openid);

        (new NewsModel(0))->zhongCaiBindTongZhi($openid,PublicNumber::getCode());

        return true;
    }


    //此处的access_token与config的不是一个概念,是openID的前提
    public static function get_access_token($code,$state)
    {
        $link = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.BaseVar::$AppID.'&secret='.BaseVar::$AppSecret.'&code='.$code.'&grant_type=authorization_code';

        $res = file_get_contents($link);

        $arr = \GuzzleHttp\json_decode($res,true);

        $arr['state'] = $state;

        return $arr;
    }


    //自定义创建菜单  //一级最多3个,名称不超过4个字  2级  最多5个  不超过7个字
    public static function createMenu()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.Config::getDbAccessToken();

        $res = Config::curl_post($link,self::getButton());

        $result = \GuzzleHttp\json_decode($res,true);

        return $result;
    }


    public static function checkOpenidExits($openid)
    {
        return  Db::name("idcards")->where("openid='$openid'")->find();
    }

    public static function addUserInfo($access_token,$openid,$idid)
    {
        if(!$openid) return false;
        $data = self::getAccessUserInfo($openid);
        if($data){
            return $data;
        }else{
            $arr = self::getUserInfo($access_token,$openid);

            if($arr['errcode']){
                return $arr;
            }
        }
        $data = [];
        $data['idid'] = $idid;
        $data['openid'] = $arr['openid'];
        $data['nickname'] = $arr['nickname'];
        $data['sex'] = $arr['sex'];
        $data['province'] = $arr['province'];
        $data['city'] = $arr['city'];
        $data['country'] = $arr['country'];
        $data['headimgurl'] = $arr['headimgurl'];
        $data['privilege'] = 1;
        $data['unionid'] = 1;
        $res = Db::name('access_user_info')->insert($data);
        if($res)
        return $data;
        return $res;
    }

    public static function getAccessUserInfo($openid)
    {
        return  Db::name('access_user_info')->where("openid",$openid)->find();
    }


    public static function getButton()
    {
        $list['button'][] = self::getButtonList('绑定账号','click',BaseVar::BIND_USER,'http://zcw.wszx.cc/admin/Wxcallback/getCode',[]);
        $list['button'][] = self::getButtonList('我的','view','',self::getMyCode(),[]);

        return json_encode($list,JSON_UNESCAPED_UNICODE);
    }

    public static function getButtonList($name,$type,$key,$url,$button=[])
    {
        return $arr = [
            'name'=>$name,
            'type'=>$type,
            'key'=>$key,
            'url'=>$url,
//            'sub_button'=>$button,
        ];
    }

    public static function getSubButtonList($type,$name,$key,$sub_button=[])
    {
        return [
            'type'=>$type,
            'name'=>$name,
            'key'=>$key,
            'sub_button'=>$sub_button,
        ];
    }

}