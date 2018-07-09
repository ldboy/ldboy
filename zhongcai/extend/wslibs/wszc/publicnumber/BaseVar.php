<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/6
 * Time: 下午4:38
 */

namespace wslibs\wszc\publicnumber;


class BaseVar
{



    const TOKEN = 'zhihuizhongcai';
    const YUANSHI_ZHANGHAO = 'gh_055eb98ef774';

    const BIND_USER = 'bind_user';    //绑定
    const SUBSCRIBE = 'subscribe';    //关注
    const annual_renew = 'annual_renew';    //年审通知开发者

    const MSGTYPE_TEXT = 'text';
    const MSGTYPE_transfer_customer_service = 'transfer_customer_service';  //转发至客服
    const MSGTYPE_EVENT = 'event';
    const MSGTYPE_IMAGE = 'image';
    const MSGTYPE_VOICE = 'voice';
    const MSGTYPE_VIDEO = 'video';
    const MSGTYPE_LINK = 'link';
    const MSGTYPE_SHORT_VIDEO = 'shortvideo';
    const MSGTYPE_LOCATION = 'location';


    const EVENT_LOCATION = 'LOCATION';
    const EVENT_CLICK = 'CLICK';
    const EVENT_VIEW = 'VIEW';
    const EVENT_TEMPLATESENDJOBFINISH = 'TEMPLATESENDJOBFINISH';
    const EVENT_TEMPLATESENDJOBFINISH_SUCCESS = 'success';
    const EVENT_TEMPLATESENDJOBFINISH_BLOCK = 'failed:user block';
    const EVENT_TEMPLATESENDJOBFINISH_FAILED = 'failed: system failed';

    const WEIYI_CODE = '7758';//此处若变,菜单需重新生成



    public static $TEMPLATE_ID_ZHONGCAI_TONGZHI = '1dG6_wimEl6E3EIXvpjJnIztvhWrM12-HKQslFCI97o';
    public static $TEMPLATE_ID_ZHONGCAI_ZUTING_TONGZHI = 'lYz-VDdddibgBXo4t0dUiui5lIU6EicYzVde0zJWuI0';
    public static $TEMPLATE_ID_ZHONGCAI_BINDUSER_TONGZHI = 'z83AmVciiDP5TO_YjPWVCWvsZ_xwl4zWhtoz7Y0lAQw';
    public static $TEMPLATE_ID_TASK_FAIL_TONGZHI = 'l3fsOvrlqQn_AhkUJTTPR37DysGe9vPExWBzcLtoWd8';
    public static $TEMPLATE_ID_ZHONGCAI_CAIJUE_TONGZHI = 'U3kzf-vqenhq29T10LOFDO49uqStrJCA2PsyJxps9OI';
    public static $TEMPLATE_ID_ZHONGCAI_DAIBAN_TONGZHI = 'KJpSmt2RrVBfdX9iJVZ_bs4Gwbjfq9BOyak_aAuek5E';
    public static $TEMPLATE_ID_ZHONGCAI_CAIJUE_RENLING_TONGZHI = 'HLbA4vlAUrKrltU4dTdHsGP1c4y3gmIYEf5mAngB4P4';





    public static $pubUrl = 'api.weixin.qq.com';
    public static $AppID = 'wx8519590753d41731';
    public static $AppSecret = 'df5ad09e65eeb640a65589b36c628983';
    public static $EncodingAESKey = 'PJtWa14lL6AUexe8SMeyACpCiTbt9UCsJSKqLuBUMwi';


    public static $info_url = 'http://zcw.wszx.cc/admin/Wxcallback/info';
    public static $callback_url = 'http://zcw.wszx.cc/admin/Wxcallback/index';
    public static $my_url = 'http://zcw.wszx.cc/admin/wechat/publicnumber/usercenter';

    public static $layupType = [1=>self::EVENT_LOCATION,2=>self::EVENT_CLICK];


    public static $kf_zhz = 'kf2001@zhzhongcai';  //郑洪志微信客服号
    public static $kf_zhz_accepted_case = 9;  //郑洪志微信客服号最大接入消息人数  若修改  需登录https://mpkf.weixin.qq.com/cgi-bin/kfindex
    public static $kf_lifan = 'kf2002@zhzhongcai';  //李帆
    public static $kf_lifan_accepted_case = 1;
    public static $kf_gsj = 'kf2003@zhzhongcai';  //郭淑娇
    public static $kf_gsj_accepted_case = 9;



    const FA_SONG_FAIL = 0;
    const GANG_FA_SONG = 1;
    const FA_SONG_SUCCESS = 2;
    const FA_SONG_FAILED = 3;
    const FA_SONG_BLOCK = 4;

    public static $MSG_TYPE_ZHONGCAI = 1;
    public static $MSG_TYPE_ZHONGCAI_ZUTING = 2;
    public static $MSG_TYPE_ZHONGCAI_DAIBAN = 3;
    public static $MSG_TYPE_ZHONGCAI_CAIJUE = 4;
    public static $MSG_TYPE_TASK_FAIL = 5;




    public static $OK = 0;
    public static $ValidateSignatureError = -40001;
    public static $ParseXmlError = -40002;
    public static $ComputeSignatureError = -40003;
    public static $IllegalAesKey = -40004;
    public static $ValidateAppidError = -40005;
    public static $EncryptAESError = -40006;
    public static $DecryptAESError = -40007;
    public static $IllegalBuffer = -40008;
    public static $EncodeBase64Error = -40009;
    public static $DecodeBase64Error = -40010;
    public static $GenReturnXmlError = -40011;
}