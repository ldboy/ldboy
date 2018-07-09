<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2016/12/17
 * Time: 上午9:40
 */
namespace wslibs\wszc\mobilesend;


class MobileSend
{
    public static function send($phone, $businessId)
    {

        $SmsCenterSdk = new SmsCenterSdk();
        $res = $SmsCenterSdk->sendCode($phone,$businessId);
        if(!$res){
            return false;
        }else{
            return json_decode($res,true);
        }
    }

    public static function check($phone,$code,$businessId){
        $SmsCenterSdk = new SmsCenterSdk();
        $res = $SmsCenterSdk->check($phone,$code,$businessId);
        if(!$res){
            return false;
        }else{
            return json_decode($res,true);
        }


    }

    public static function sendText($phone,$businessId,$content){
        $SmsCenterSdk = new SmsCenterSdk();
        $res = $SmsCenterSdk->sendText($phone,$businessId,$content);
        if(!$res){
            return false;
        }else{
            return json_decode($res,true);
        }
    }

}