<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/7
 * Time: 上午10:31
 */

namespace wslibs\wszc\publicnumber;

use wslibs\wszc\publicnumber\wxencode\WXBizMsgCrypt;

class MsgCrypt
{


    public function encryptMsg($replyMsg, $timeStamp, $nonce, &$encryptMsg)
    {

      return (new WXBizMsgCrypt())->encryptMsg($replyMsg,$timeStamp,$nonce,$encryptMsg);
    }

    public function decryptMsg($msgSignature, $timestamp = null, $nonce, $postData, &$msg)
    {
        dump($msgSignature);
        dump($timestamp);
        dump($nonce);
        dump($postData);
        return (new WXBizMsgCrypt())->decryptMsg($msgSignature,$timestamp,$nonce,$postData,$msg);

    }


    public function generate($encrypt, $signature, $timestamp, $nonce)
    {
        $format = "<xml>
                    <Encrypt><![CDATA[%s]]></Encrypt>
                    <MsgSignature><![CDATA[%s]]></MsgSignature>
                    <TimeStamp>%s</TimeStamp>
                    <Nonce><![CDATA[%s]]></Nonce>
                    </xml>";
        return sprintf($format, $encrypt, $signature, $timestamp, $nonce);
    }


    public function extract($xmltext)
    {
        try {
            $xml = new \DOMDocument();
            $xml->loadXML($xmltext);
            $array_e = $xml->getElementsByTagName('Encrypt');
            $array_a = $xml->getElementsByTagName('ToUserName');
            $encrypt = $array_e->item(0)->nodeValue;
            $tousername = $array_a->item(0)->nodeValue;
            return array(0, $encrypt, $tousername);
        } catch (\Exception $e) {
            //print $e . "\n";
            return array(BaseVar::$ParseXmlError, null, null);
        }
    }
}