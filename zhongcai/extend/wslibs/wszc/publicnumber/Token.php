<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/1
 * Time: 上午11:39
 */

namespace wslibs\wszc\publicnumber;
header('content-type:text');
class Token
{

    public function valid($signature,$timestamp,$nonce,$echoStr)

    {
        if($this->checkSignature($signature,$timestamp,$nonce)){
            return $echoStr;
        }
        return false;
    }


    private function checkSignature($signature,$timestamp,$nonce)

    {
        $token = BaseVar::TOKEN;

        $tmpArr = array($token, $timestamp, $nonce);

        sort($tmpArr,SORT_STRING);

        $tmpStr = implode( $tmpArr );

        $tmpStr = sha1( $tmpStr );

        if( $tmpStr == $signature ){

            return true;

        }else{

            return false;
        }

    }

    public function getSHA1($token, $timestamp, $nonce, $encrypt_msg)
    {

        try {
            $array = array($encrypt_msg, $token, $timestamp, $nonce);
            sort($array, SORT_STRING);
            $str = implode($array);
            return array(BaseVar::$OK, sha1($str));
        } catch (\Exception $e) {

            return array(BaseVar::$ComputeSignatureError, null);
        }
    }

}