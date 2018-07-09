<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/23
 * Time: 上午8:53
 */

namespace wslibs\wszc\publicnumber\kfmanager;

use think\Db;
use wslibs\wszc\publicnumber\BaseVar;
use wslibs\wszc\publicnumber\Config;
use wslibs\wszc\publicnumber\PublicNumber;

class Kfinfo
{

    public static $_errcode = 0;


    //获取客服基本信息
    public static function getKfList()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/customservice/getkflist?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        $arr = \GuzzleHttp\json_decode($res,true);

        if($arr['errcode']){
            self::$_errcode = -1;
            return false;
        }

        return $arr;
    }

    public static function getKfOnlineList()
    {
        $link = 'https://api.weixin.qq.com/cgi-bin/customservice/getonlinekflist?access_token='.Config::getDbAccessToken();

        $res = file_get_contents($link);

        $arr = \GuzzleHttp\json_decode($res,true);

        if($arr['errcode']){
            self::$_errcode = -1;
            return false;
        }

        return $arr;
    }

    public static function isCheckKf($list)
    {
        $is_can = false;
        $is_zhiding = false;
        if(!$list['kf_online_list']){
            return [$is_can,$is_zhiding];
        }

        foreach($list['kf_online_list'] as $key=>$value){
            if($value['status']==1){
                $is_can = true;
            }

            if($value['status']==1 && $value['kf_account']==BaseVar::$kf_zhz && $value['accepted_case']<=BaseVar::$kf_zhz_accepted_case){
                $is_zhiding = true;
                break;
            }
        }
        return [$is_can,$is_zhiding];
    }

    public static function isAppoint($openid_from)
    {
        if(!$openid_from)  return false;

        $idcards = PublicNumber::checkOpenidExits($openid_from);

        if(!$idcards) return false;

        $roles = Db::name("dossier_roles")->where("idid",$idcards['id'])->select();
    }
}