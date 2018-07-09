<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/6
 * Time: 下午4:31
 */

namespace wslibs\wszc\publicnumber;
use EasyWeChat\Message\News;
use wslibs\wszc\notice\Notice;
use wslibs\wszc\publicnumber\kfmanager\Kfinfo;

class ReplyMsg
{

    public static function xml_parser($str){
        $xml_parser = xml_parser_create();
        if(!xml_parse($xml_parser,$str,true)){
            xml_parser_free($xml_parser);
            return false;
        }else {
            return (json_decode(json_encode(simplexml_load_string($str)),true));
        }
    }

    public static function replyMsg($data)
    {
        if(self::xml_parser($data)){
            $data = Config::xmlToArray($data);
        }

        switch($data['MsgType']){
            case BaseVar::MSGTYPE_IMAGE:
                $msg = self::replyImg();
                break;
            case BaseVar::MSGTYPE_TEXT:
                $msg = self::replyText($data);
                break;
            case BaseVar::MSGTYPE_EVENT:
                $msg = self::replyEvent($data);
                break;
            default:
                $msg = self::returnSuccess();
                break;
        }

        return $msg;
    }
    //事件推送
    public static function replyEvent($data)
    {
        if($data['EventKey']==BaseVar::BIND_USER){
            (new NewsModel(0))->zhongCaiBindTongZhi($data['FromUserName'],PublicNumber::getCode());
            return self::returnSuccess();
        }

        if($data['Event']==BaseVar::SUBSCRIBE){
            return self::replyText($data);
        }elseif($data['Event']==BaseVar::EVENT_LOCATION){
            PublicNumber::layupOtherInfo($data);
            return self::returnSuccess();
        }elseif($data['Event']==BaseVar::annual_renew){
            Notice::sendSms('15201634344','智慧仲裁服务号快到期了,哎呀,时间真快这都一年了,快点去充值吧');
            return  self::returnSuccess();
        }elseif($data['Event']==BaseVar::EVENT_TEMPLATESENDJOBFINISH){

            $data['Status'] == BaseVar::EVENT_TEMPLATESENDJOBFINISH_SUCCESS ?

                (new NewsModel(0))->updateRecord($data['MsgID'],BaseVar::FA_SONG_SUCCESS,$data['CreateTime'],$data)

                    : ( $data['Status'] == BaseVar::EVENT_TEMPLATESENDJOBFINISH_BLOCK ?

                        (new NewsModel(0))->updateRecord($data['MsgID'],BaseVar::FA_SONG_BLOCK,$data['CreateTime'],$data) :

                            (new NewsModel(0))->updateRecord($data['MsgID'],BaseVar::FA_SONG_FAILED,$data['CreateTime'],$data)

            );
        }

        return  self::returnSuccess();
    }

    public static function returnSuccess()
    {
        return  'success';
    }

    //回复图文消息
    private function transmitNews($object, $newsArray)
    {
        return self::returnSuccess();
    }

    //回复图片信息
    public static function replyImg()
    {
        return self::returnSuccess();
    }

    //回复文本信息
    public static function replyText($data)
    {
        list($is_can,$is_zhiding) = Kfinfo::isCheckKf(Kfinfo::getKfOnlineList());
        $info = [];
        $info['ToUserName'] = $data['FromUserName'];
        $info['FromUserName'] = BaseVar::YUANSHI_ZHANGHAO;
        $info['CreateTime'] = time();
        if($is_can){
            $info['MsgType'] = BaseVar::MSGTYPE_transfer_customer_service;
            if($is_zhiding){
                $info['TransInfo']['KfAccount']=BaseVar::$kf_gsj;
            }
        }else{
            $info['MsgType'] = BaseVar::MSGTYPE_TEXT;
        }

        $info['Content'] = self::common_text($data);

        return $info;
    }

    public static function common_text($data)
    {

        if($data['Event']==BaseVar::SUBSCRIBE){

            return self::getContentByGuanZhu();

        }elseif($data['Content']){

            return self::textMsg($data['Content']);
        }

        return self::getContentByElse();
    }

    public static function textMsg($content)
    {

        switch($content){
            case '仲裁':
                $content = self::getContentByzhongcai();
                break;
            case '帅哥':
                $content = self::getContentByshuaige();
                break;
            case '美女':
                $content = self::getContentByshuaige();
                break;
            case '石家庄仲裁委员会':
                $content = self::getContentSJZZCW();
                break;
            default:
                $content = self::getContentByElse();
                break;
        }

        return $content;
    }

    public static function getContentSJZZCW()
    {
        return "如有相关仲裁疑问，请咨询0311-86687359。\n这里的小哥哥小姐姐很优秀的哦!\n也会很耐心的解答你的疑惑哦!";
    }

    public static function getContentByshuaige()
    {
        return '哎呀,人家都不好意思了';
    }
    public static function getContentByElse()
    {
        return "智慧仲裁，解决民事争议的方式之一。智慧仲裁，数据统计。\n发起仲裁请登录网站 \nhttp://zc.wszx.cc";
    }

    public static function getContentByGuanZhu()
    {
        return "欢迎关注智慧仲裁服务号 \n\n智慧仲裁，解决民事争议的方式之一。智慧仲裁，数据统计。\n\n发起仲裁请登录网站 \nhttp://zc.wszx.cc";
    }

    public static function getContentByzhongcai()
    {
        return "智慧仲裁，解决民事争议的方式之一。智慧仲裁，数据统计。\n发起仲裁请登录网站 \nhttp://zc.wszx.cc";
    }
}