<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/10/15
 * Time: 10:26
 */
namespace wslibs\socketsend;

class SocketSend
{

    public static function html2pictureBackUrl($weburl, $imgurl, $backurl = "", &$errmsg)
    {


        if (strpos($weburl, "?") > 0) {
            $weburl = $weburl . "&i_m_g=1";
        } else {
            $weburl = $weburl . "?i_m_g=1";
        }


        if (IS_LOC) {

            $imgurl = NAS_WSZX_DIR . $imgurl;

            $command = "/home/ddle/html2pdf/phantomjs-2.1.1-linux-x86_64/bin/phantomjs \"/home/ddle/html2pdf/phantomjs-2.1.1-linux-x86_64/bin/page.js\" " . '"' . $weburl . '"' . " \"$imgurl\"";


            exec($command, $retval, $status);

            file_get_contents($backurl);
            return true;
        } else {

            $out = "1982\t\t$weburl\t\t$imgurl\t\t0\t\t$backurl\r\n";

            return self::doSend($out, $errmsg);
        }
    }

    public static function html2picture($weburl, $imgurl, $id, &$errmsg)
    {


        if (IS_LOC) {
            $backurl = WEB_SITE_ROOT . "ywimgcallback/index/docid/" . $id;
        } else {
//            $backurl = WEB_SITE_ROOT . "Ywimgcallback-index.html?docid=$id&img=$imgurl";//_file_ok_
            $backurl = "ywimgcallback/index/docid/$id/img/$imgurl";//_file_ok_
        }
        return self::html2pictureBackUrl($weburl, $imgurl, $backurl, $errmsg);
    }

    public static function pdfMerger($weburl, $imgurl, $pdfs, $pdfmergename, $backurl, &$errmsg)
    {
        $out = "1982\t\t$weburl\t\t$imgurl\t\t0\t\t$backurl\t\t$pdfs\t\t$pdfmergename\r\n";
        //$out = "1982\t\t$weburl\t\t$imgurl\t\t$id\t\t$backurl\r\n";
        return self::doSend($out, $errmsg);
    }

    public static function getCreditReport($idcard, $name)
    {
        $fp = fsockopen("124.239.196.194", 6547, $errno, $errstr, 30);//219.232.253.39

        $out = "401\t\t" . $idcard . "\t\t" . $name . "\r\n";
        fwrite($fp, $out);
        $out = "";
        while ($tmp = fgets($fp, 128)) {
            $out .= $tmp;
        }
        fclose($fp);
        return $out;
    }

    // 处理不需要返回信息的发送
    private static function doSend($sendMsg, &$errorMsg)
    {
        usleep(200);

        $fp = fsockopen("124.239.196.194", 6547, $errno, $errstr, 30);//219.232.253.39

        if (!$fp) {

            $errorMsg = $errstr;
            return false;
        } else {
            $result = fwrite($fp, $sendMsg);
            fclose($fp);
            if ($result === false) {

                $errorMsg = '处理失败';
                return false;
            } else {

                return true;
            }
        }

    }

}


//SocketSend::html2picture("http://www.baidu.com","/home/zxsh/www/aaae.png",1,$msg);