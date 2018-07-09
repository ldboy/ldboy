<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/10
 * Time: 下午10:16
 */
namespace wslibs\wszc\notice;

use think\Db;

use wslibs\wszc\mobilesend\MobileSend;

class Notice
{
    const ON = true;
    const SMS_WAY = 53;
    private static $common_content = '你有一笔仲裁业务需要处理，请到http://zc.wszx.cc登录处理';

    public static function sendToZhongCaiWei($dossierId){
        // zc_jigou_admin role = 2 是管理员 也就是 仲裁委主任
        $phone = Db::name('dossier')
            ->alias('d')
            ->join('jigou_admin ja','d.zc_jg_id=ja.th_id')
            ->where('d.id',$dossierId)
            ->where('ja.role',2)
            ->value('ja.phone');
        if(!$phone){
            return false;
        }
        return self::sendSms($phone);
    }


    //发短信
    /**
     * @param $phone
     * @param $content
     * @return bool
     */
    public static function sendSms($phone,$content='',$zhz_tip='')
    {
        if(!self::ON){
            return false;
        }
        if(!$phone){
            $phone = '15201634344';
            $content = '错误提醒,手机号为空了'.$zhz_tip;
        }
        if(!$content){
            $content = self::$common_content;
        }
        $res = MobileSend::sendText($phone,self::SMS_WAY,$content);
        if($res['code']!=1){
            return false;
        }
        return true;
    }
}