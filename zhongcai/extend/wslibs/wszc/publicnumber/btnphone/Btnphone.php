<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/28
 * Time: 上午10:10
 */
namespace wslibs\wszc\publicnumber\btnphone;
use wslibs\wszc\btn\Btn;

class Btnphone
{
    public static function getBtnByPhone($did,$idid)
    {
        $btnList = Btn::getBtnHtml($did, $idid,true);

        $btnStr = [];

        if($btnList){
            foreach($btnList as $key=>$value){

                list($btnStr[$key]['url'],$btnStr[$key]['data_tip']) = self::getLinkByKey($key,$value,$did,$idid);

                $btnStr[$key]['name'] = $value['name'];

            }

        }
        return $btnStr;
    }

    private static function getLinkByKey($key,$value,$did,$idid)
    {
        $data_tip = $value['attr']['data-tip'] ? $value['attr']['data-tip'] :'确定要继续操作吗';

        switch($key){
            case 9:
                $link = url('wechat.myinfo/zdzcy',['did'=>$did,'idid'=>$idid]);
                break;
            case 15:
                $link = '';
                $data_tip = '温馨提示:'.$value['name'].'请到PC端登录操作,手机只能查看哦';
                break;
            case 20:
                $link = '';
                $data_tip = '温馨提示:'.$value['name'].'请到PC端登录操作,手机暂不支持';
                break;
            case 22:
                $link = url('wechat.myinfo/dabian',['did'=>$did,'idid'=>$idid ]);
                break;
            case 23:
                $link = url('wechat.myinfo/zhizheng',['did'=>$did,'idid'=>$idid ]);
                break;
            case 24:
                $link = url('wechat.myinfo/zjlist',['did'=>$did,'idid'=>$idid ]);
                break;
            case 25:
                $link = url('wechat.myinfo/huibismpl',['did'=>$did,'idid'=>$idid ]);
                break;
            case 29:
                $link = url('dossier.lian/spshouli',['id'=>$did,'is_phone'=>1 ]);
                break;
            case 33:
                $link = url('wechat.myinfo/guanxiaquan',['did'=>$did,'idid'=>$idid ]);
                break;
            case 36:
                $link = url('wechat.myinfo/otherlist',['did'=>$did,'idid'=>$idid  ]);
                break;
            case 102:
                $link = '';
                $data_tip = $value['name'].',文件正在执行,请勿重复操作';
                break;
            default :
                $link = url($value['url'], ['id'=>$did,'is_phone'=>1]);//21,26,2,4,101,

        }

        return [$link,$data_tip];
    }


}