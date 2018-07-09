<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/10
 * Time: 下午7:40
 */
namespace  wslibs\wszc\userlogin;
use think\Db;

class UserLogin
{
    const INVALID_TIME = 86400;     //多长时间过期

    public static function getInfoByCode($code)
    {
        $time = time();
        $login_code =  Db::name('login_code')->where("code='$code' and invalid_time >=$time")->find();

        if(!$login_code){
            return false;
        }

        $user_info = Db::name('dossier_users')->where('idid',$login_code['idid'])->field('id_num,phone,name')->find();

        return [$login_code,$user_info];
    }
    public static function isInvalid($code)
    {
        $info = Db::name('login_code')->where("code",$code)->find();

        if($info['invalid']<time()) return false;

        return  true;
    }


    /**
     * @param $dossier_id
     * @param $url
     * @param $idid
     * @param $code
     * @return int|string
     */
    public static function addCode($dossier_id,$url,$idid,&$code)
    {
        $code = $data['code'] = self::getRandCode();
        $data['url'] = $url;
        $data['dossier_id'] = $dossier_id;
        $data['idid'] = $idid;
        $data['invalid_time'] = time()+self::INVALID_TIME;

        return Db::name('login_code')->insert($data);
    }

    /**
     * @param int $length
     * @return string
     */
    public static function getRandCode($length = 10){
        $code = '';

        $chars = "123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ";

        $char_len = strlen($chars);

        for($i=0;$i<$length;$i++){

            $loop = mt_rand(0, ($char_len-1));

            $code .= $chars[$loop];
        }

        return md5($code);
    }
}