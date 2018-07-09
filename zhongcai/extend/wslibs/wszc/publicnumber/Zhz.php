<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/6
 * Time: 下午5:10
 */

namespace wslibs\wszc\publicnumber;


class Zhz
{

    //获得模板ID      http请求方式: POST
    public static function get_template_id_short()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/template/api_add_template?access_token='.Config::getDbAccessToken();

        $template_id_short = 'TM00015';    //模板库中模板的编号，有“TM**”和“OPENTMTM**”等形式

        $res = Config::curl_post($link,$template_id_short);

        if(!$res) return Config::getErrorMsg(-1,'获得模板ID失败');

        $data = json_decode($res,true);

        if(json_last_error()==JSON_ERROR_NONE){

            if($data['errcode']!=0){
                return Config::getErrorMsg($data['errcode'],$data['errmsg']);
            }

            return Config::getErrorMsg(1,$data['template_id']);
        }

        return Config::getErrorMsg(-23,'模板ID解析失败');
    }


    //获取模板列表
    public static function getModelList()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        if(!$res) return Config::getErrorMsg(-1,'获取模板列表失败');

        $data = json_decode($res,true);

        if(json_last_error()==JSON_ERROR_NONE){

            if($data['errcode']!=0){
                return Config::getErrorMsg($data['errcode'],$data['errmsg']);
            }

            return Config::getErrorMsg(1,$data);
        }

        return Config::getErrorMsg(-24,'模板列表解析失败');


    }







    //获取自定义菜单配置接口
    public static function get_menu_list()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        $list = \GuzzleHttp\json_decode($res,true);

        return $list;
    }


    //获取用户列表
    public static function getUserList()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/user/get?access_token='.Config::getDbAccessToken().'&next_openid=';

        $res = file_get_contents($link);

        return \GuzzleHttp\json_decode($res,true);
    }

    //获取微信服务器IP地址
    public static function getWxIPList()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/getcallbackip?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        return \GuzzleHttp\json_decode($res,true);
    }
    //
    public static function kfcount()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        return \GuzzleHttp\json_decode($res,true);
    }

}