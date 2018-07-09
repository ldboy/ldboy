<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 10:26
 */
class SocketSend{

    public static function html2picture($weburl,$imgurl,$id,&$errmsg){

        if(strpos($weburl,"?")>0)
        {
            $weburl = $weburl."&img=1";
        }else{
            $weburl = $weburl."?img=1";
        }
        $out = "200\t\t$weburl\t\t$imgurl\t\t$id\r\n";
        return self::doSend($out,$errmsg);
    }

    public static function getCreditReport($idcard,$name){
        $fp = fsockopen("42.159.121.218", 6547, $errno, $errstr, 30);

        $out = "401\t\t" . $idcard . "\t\t" . $name . "\r\n";
        fwrite($fp, $out);
        $out = "";
        while($tmp = fgets($fp,128 ))
        {
            $out .= $tmp;
        }
        fclose($fp);
        return $out;
    }

    // 处理不需要返回信息的发送
    public static function doSend($sendMsg,&$errorMsg){
        $fp = fsockopen("124.239.196.194", 6547, $errno, $errstr, 30);//42.159.121.218
//        $fp = fsockopen("42.159.121.218", 6547, $errno, $errstr, 30);//42.159.121.218
        if (!$fp) {
            $errorMsg = $errstr;
            return false;
        } else {
            $result = fwrite($fp, $sendMsg);
            fclose($fp);
            if($result===false){
                $errorMsg='处理失败';
                return false;
            }else{
                return true;
            }
        }
    }

    public static function sendAndGet($msg,&$errorMsg)
    {
        $fp = fsockopen("42.159.121.218", 6547, $errno, $errstr, 30);
        if (!$fp) {
            $errorMsg = $errstr;
            return false;
        } else {
            // $out = $out = "402\t\t" . $filename. "\r\n";
            fwrite($fp, $msg);
            $out = "";
            while ($tmp = fgets($fp, 128)) {
                $out .= $tmp;
            }
            fclose($fp);
            return $out;
        }
    }

    public static function sendAndGetNfcs($msg,&$errorMsg)
    {
        $fp = fsockopen("124.239.196.194", 6547, $errno, $errstr, 30);


        //$fp = fsockopen("42.159.121.218", 6547, $errno, $errstr, 30);
        if (!$fp) {
            $errorMsg = $errstr;
            return false;
        } else {
            // $out = $out = "402\t\t" . $filename. "\r\n";
            fwrite($fp, $msg);
            $out = "";
            while ($tmp = fgets($fp, 128)) {
                $out .= $tmp;
            }
            fclose($fp);
            return $out;
        }
    }


    public static function nfcsUploadFile($o_filepath,$d_filename,$emsg)
    {
        //echo urlencode(file_get_contents($o_filepath));
        return self::sendAndGetNfcs("404\t\t" . $d_filename."\t\t". urlencode(file_get_contents($o_filepath))."\r\n",$emsg);
    }

    public static function nfcsUpload($name,$path)
    {
        return self::sendAndGetNfcs("402\t\t" . $name."\t\t".$path. "\r\n",$errmsg);
    }
    public static function nfcsUploadQuery($filename)
    {
        return self::sendAndGetNfcs("403\t\t" . $filename. "\r\n",$errmsg);
    }

}