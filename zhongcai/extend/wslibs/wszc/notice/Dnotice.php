<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/4
 * Time: ����11:35
 */

namespace wslibs\wszc\notice;


use dossier\DossierDoc;
use think\Db;
use think\Log;
use wslibs\wszc\Constant;
use wslibs\wszc\Dcancel;
use wslibs\wszc\dmail\Dmail;
use wslibs\wszc\Dossier;
use wslibs\wszc\dtip\Dtip;
use wslibs\wszc\HuiBi;
use wslibs\wszc\mes\Mes;
use wslibs\wszc\publicnumber\NewsModel;

class Dnotice
{

    const URL_dsr = 'http://zc.wszx.cc/zc_login-dsrindex.html';
    const URL_zcw = 'http://zc.wszx.cc/zc_login-index.html';
    public static function whenDmpFinish($did, $gid, $ext_id, $uid, $docs)
    {

//        file_put_contents('20180705.txt',[$did,$gid,$ext_id]);


        $dossier_info = Dossier::getSimpleDossier($did);
        $pre_fun = 'sendGid_';

        if($gid==Constant::FILE_GROUP_shenqing){//1

            $fun = $pre_fun.Constant::FILE_GROUP_shenqing;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif ($gid == Constant::FILE_GROUP_shouli){//2



        }elseif($gid == Constant::FILE_GROUP_dabian){//3

            $fun = $pre_fun.Constant::FILE_GROUP_dabian;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_zuting){//4

            $fun = $pre_fun.Constant::FILE_GROUP_zuting;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_shengming){//5

            $fun = $pre_fun.Constant::FILE_GROUP_shengming;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_huibi){//6

            $fun = $pre_fun.Constant::FILE_GROUP_huibi;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_zhizheng){//7

            $fun = $pre_fun.Constant::FILE_GROUP_zhizheng;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_wancheng){//10

            $fun = $pre_fun.Constant::FILE_GROUP_wancheng;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }else if ($gid == Constant::FILE_GROUP_bsqrshouli){//13

            $fun = $pre_fun.Constant::FILE_GROUP_bsqrshouli;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_pilu){//14

            $fun = $pre_fun.Constant::FILE_GROUP_pilu;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_zuting_again){//16

            $fun = $pre_fun.Constant::FILE_GROUP_zuting_again;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_lianshenpi){//17

            $fun = $pre_fun.Constant::FILE_GROUP_lianshenpi;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_zhizhengzhuanfa){//18



            $fun = $pre_fun.Constant::FILE_GROUP_zhizhengzhuanfa;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_dabian_zhuanfa){//19

            $fun = $pre_fun.Constant::FILE_GROUP_dabian_zhuanfa;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_zhidingzhongcaiyuan){//20

            $fun = $pre_fun.Constant::FILE_GROUP_zhidingzhongcaiyuan;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_pilu_zhuanfa){//22

            $fun = $pre_fun.Constant::FILE_GROUP_pilu_zhuanfa;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_shenming_zhuanfa){//23

            $fun = $pre_fun.Constant::FILE_GROUP_shenming_zhuanfa;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid==Constant::FILE_GROUP_tijiaozhengju_sqr){//25

            $fun = $pre_fun.Constant::FILE_GROUP_tijiaozhengju_sqr;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_zhengju_zhuanfa){//26

            $fun = $pre_fun.Constant::FILE_GROUP_zhengju_zhuanfa;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_caijue_fasongsuoyouren){//27

            $fun = $pre_fun.Constant::FILE_GROUP_caijue_fasongsuoyouren;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_huibi_huifu){//31

            $fun = $pre_fun.Constant::FILE_GROUP_huibi_huifu;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_chehuishenqing){//33

            $fun = $pre_fun.Constant::FILE_GROUP_chehuishenqing;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_chehuishenqing_zhubanzf){//34

            $fun = $pre_fun.Constant::FILE_GROUP_chehuishenqing_zhubanzf;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_chehuishenqing_zhuren_zf){//36

            $fun = $pre_fun.Constant::FILE_GROUP_chehuishenqing_zhuren_zf;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_chehuishenqing_zhuren_zf_zth){//41

            $fun = $pre_fun.Constant::FILE_GROUP_chehuishenqing_zhuren_zf_zth;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }elseif($gid == Constant::FILE_GROUP_tijiaozhengju_bsqr){//40

            $fun = $pre_fun.Constant::FILE_GROUP_tijiaozhengju_bsqr;
            self::$fun($dossier_info,$did,$gid,$ext_id);

        }

        return true;
    }


    public static function sendGid_44($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $ext_info = Db::name('gxq_yy')->where("id",$ext_id)->find();
        foreach($users as $key=>$value){
            if($value['idid']==$ext_info['idid']){
                unset($users[$key]);
            }
        }

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);


        $content0 = '案件[管辖权异议]提醒'; //msg
        $content1 = '案件[管辖权异议]提醒'; //微信
        $content2 = self::responseMsg($content1.','.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[管辖权异议]',self::URL_dsr);

        (new Dmail())->sendGuanXiaQuanAllPeoples($did,$gid,$ext_id);

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }

        return true;
    }
    public static function sendGid_41($dossier_info,$did,$gid,$ext_id)
    {
        return self::sendGid_36($dossier_info,$did,$gid,$ext_id);
    }


    public static function sendGid_40($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '被申请人[证据提交]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件证据提交申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[证据提交]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件证据提交申请'); //短信

        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');

        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();

        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);

        Notice::sendSms($phones['phone'],$sms_content);

        $info = Db::name('idcards')->where('id',$idid)->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);

        return true;
    }

//    public static function sendGid_39($dossier_info,$did,$gid,$ext_id)
//    {
//        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);
//
//        $dangshiren = array_column($users, 'name');
//
//        $apply_peo = $dangshiren[0];
//
//        $content1 = '[撤回申请仲裁员转发]提醒'; //微信
//        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件撤回申请仲裁员转发'; //微信事项描述
//
//        $sms_content = self::responseMsg('案件[撤回申请仲裁员转发]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件撤回申请仲裁员转发'); //短信
//
//        $info = Dcancel::getgetCancelById($ext_id);
//
//        if ($info['type'] == 3) {
//            $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
//            $idid_zhuren = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');
//
//            $phones_zhuabn =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();
//            $phones_zhuren =  Db::name('jigou_admin')->where("idid='$idid_zhuren'")->where('status',1)->where('role',2)->find();
//
//            $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
//            $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();
//
//
//            (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);
//            (new Mes())->msg_insert($did,$content1,$gid,$idid_zhuren,$ext_id);
//            (new Mes())->msg_insert($did,$content1,$gid,$zcy_info['idid'],$ext_id);
//
//            Notice::sendSms($phones_zhuabn['phone'],$sms_content);
//            Notice::sendSms($phones_zhuren['phone'],$sms_content);
//            Notice::sendSms($zcy_info['phone'],$sms_content);
//
//            $idid_s = [$idid,$idid_zhuren,$zcy_info['idid']];
//
//            $info = Db::name('idcards')->whereIn('id',$idid_s)->select();
//
//            foreach($info as $key=>$value){
//                (new NewsModel($did))->zhongCaiDaiBanTongZhi($value['id'],$value['openid'],'',$content1,$content2);
//            }
//
//        }
//
//        return true;
//    }

    public static function sendGid_36($dossier_info,$did,$gid,$ext_id)
    {
        $cancel = Db::name('dossier_cancel')->where('id',$ext_id)->where('status',5)->find();

        if(!$cancel){
            (new NewsModel(0))->TaskFailureReminding(2,'oMW-u0SiYggyLWs-Gs_F25kJ7nCk','主任转发撤回申请失败','撤回申请',$did.'-'.$gid.'-'.$ext_id,", $ext_id 的status不等于5",date('Y-m-d H:i:s',time()),'');
        }

        $users_apply = Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(1, 3))->select();

        $users = [];



        if($cancel['type']>=2){
            $users_beiapply = Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(2, 4))->select();

            $users = array_merge($users_beiapply,$users_apply);


            (new Dmail())->sendCheHuiApplyAllPeoples($did,$gid,$ext_id);

        }

        $dangshiren = array_column($users, 'name');

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);


        $content1 = '案件[撤回申请成功]通知'; //微信/msg
        $content2 = self::responseMsg($content1.','.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$dangshiren.'提交的案由为借款合同案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[撤回申请成功]',self::URL_dsr);

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }

        return true;
    }



//    public static function sendGid_34($dossier_info,$did,$gid,$ext_id)
//    {
//        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);
//
//        $dangshiren = array_column($users, 'name');
//
//        $apply_peo = $dangshiren[0];
//
//        $content1 = '[撤回申请主办转发]提醒'; //微信
//        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件撤回申请主办转发'; //微信事项描述
//
//        $sms_content = self::responseMsg('案件[撤回申请主办转发]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件撤回申请主办转发'); //短信
//
//        $info = Dcancel::getgetCancelById($ext_id);
//
//        if ($info['type'] == 2) {
//            $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
//            $idid_zhuren = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');
//
//            $phones_zhuabn =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();
//            $phones_zhuren =  Db::name('jigou_admin')->where("idid='$idid_zhuren'")->where('status',1)->where('role',2)->find();
//
//            $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
//            $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();
//
//
//            (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);
//            (new Mes())->msg_insert($did,$content1,$gid,$idid_zhuren,$ext_id);
//            (new Mes())->msg_insert($did,$content1,$gid,$zcy_info['idid'],$ext_id);
//
//            Notice::sendSms($phones_zhuabn['phone'],$sms_content);
//            Notice::sendSms($phones_zhuren['phone'],$sms_content);
//            Notice::sendSms($zcy_info['phone'],$sms_content);
//
//            $idid_s = [$idid,$idid_zhuren,$zcy_info['idid']];
//
//            $info = Db::name('idcards')->whereIn('id',$idid_s)->select();
//
//            foreach($info as $key=>$value){
//                (new NewsModel($did))->zhongCaiDaiBanTongZhi($value['id'],$value['openid'],'',$content1,$content2);
//            }
//
//
//        } elseif ($info['type'] == 3) {
//
//            $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
//            $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();
//            $zcy_phone = $zcy_info['phone'];
//
//            (new Mes())->msg_insert($did,$content1,$gid,$zcy_info['idid'],$ext_id);
//
//            Notice::sendSms($zcy_phone,self::responseMsg($content2.',请及时处理'));
//
//            $info = Db::name('idcards')->whereIn('id',$zcy_info['idid'])->find();
//
//            (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2."\n");
//        }
//        return true;
//    }
//


    public static function sendGid_33($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '案件[撤回申请]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件撤回申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[撤回申请]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件撤回申请'); //短信

        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');

        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();

        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);

        Notice::sendSms($phones,$sms_content);

        $info = Db::name('idcards')->where('id',$idid)->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);

        return true;
    }

    public static function sendGid_31($dossier_info,$did,$gid,$ext_id)
    {
        $info = HuiBi::getOne($ext_id);

        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $content1 = '对当事人[申请仲裁员回避]的回复提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件对当事人申请仲裁员回避的回复'; //微信事项描述

        $sms_content = self::responseMsg('案件对当事人[申请仲裁员回避]的回复提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件对当事人申请仲裁员回避的回复'); //短信

        if ($info['status'] == 5 || $info['status'] == 6) {
            $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
            $idid_zhuren = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');

            $phones_zhuabn =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();
            $phones_zhuren =  Db::name('jigou_admin')->where("idid='$idid_zhuren'")->where('status',1)->where('role',2)->find();


            (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);
            (new Mes())->msg_insert($did,$content1,$gid,$idid_zhuren,$ext_id);


            Notice::sendSms($phones_zhuabn['phone'],$sms_content);
            Notice::sendSms($phones_zhuren['phone'],$sms_content);


            $idid_s = [$idid,$idid_zhuren];

            $info = Db::name('idcards')->whereIn('id',$idid_s)->select();

            foreach($info as $key=>$value){
                (new NewsModel($did))->zhongCaiDaiBanTongZhi($value['id'],$value['openid'],'',$content1,$content2);
            }

        }

        if ($info['status'] == 3||$info['status']==4) {

            $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
            $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();

            $phones[100] = $zcy_info['phone'];
            $idids[100] = $zcy_info['idid'];

            foreach($idids as  $key=>$value){
                (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
            }

            foreach($phones as  $key=>$value){
                Notice::sendSms($value,$sms_content);
            }

            $info = Db::name('idcards')->whereIn('id',$idids)->select();

            foreach($info as $key=>$value){
                (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
            }
        }

        return true;
    }

    public static function sendGid_27($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $content1 = '案件[裁决书]通知'."\n";
        $content2 = '案件[裁决书]通知:'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[裁决书]';

        $zhuban_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
        $zhuren_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');

        $phone_zhuban =  Db::name('jigou_admin')->where("idid='$zhuban_idid'")->where('status',1)->where("role",1)->find();
        $phone_zhuren =  Db::name('jigou_admin')->where("idid='$zhuren_idid'")->where('status',1)->where("role",2)->find();

        $idids[10000] = $phone_zhuban['idid'];
        $phones[10000] = $phone_zhuban['phone'];
        $idids[1000] = $phone_zhuren['idid'];
        $phones[1000] = $phone_zhuren['phone'];

        $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
        $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();

        $idids[100] = $zcy_info['idid'];
        $phones[100] = $zcy_info['phone'];


        (new Dmail())->sendCaiJueAllPeoples($did,$gid,$ext_id);


        
        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            if($key>=100){
                Notice::sendSms($value,self::responseMsg($content2));
            }else{
                Notice::sendSms($value,self::responseMsg($content2,self::URL_dsr));
            }

        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            (new NewsModel($did))->zhongcaiCaiJueShuTongZhi($value['id'],$value['openid'],'');
        }

//        (new Dmail())->sendCaiJueAllPeoples($did,$gid,$ext_id);
        return true;
    }










    
    public static function sendGid_26($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $content0 = '您好,您有一份关于案件[证据待处理]提醒'; //微信
        $content1 = '案件[证据待处理]提醒'; //msg
        $content2 = self::responseMsg('案件[证据待处理]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[裁决书]');

        $paichu_idid = Db::name('dz')->where("id='$ext_id'")->value('idid');
        foreach($idids as $key=>$value){
            if($key==$paichu_idid){
                unset($idids[$key]);
            }
            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }
        (new Dmail())->sendZhengjuZhuanfa($did,$gid,$ext_id);
        foreach($phones as  $key=>$value){
            if($key==$paichu_idid){
                unset($phones[$key]);
            }
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($key==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content0);
        }


        return true;
    }


    public static function sendGid_25($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '申请人[证据提交]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件证据提交申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[证据提交]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件证据提交申请'); //短信

        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');

        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();

        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);

        Notice::sendSms($phones,$sms_content);

        $info = Db::name('idcards')->where('id',$idid)->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);

        return true;
    }

    public static function sendGid_24($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $content0 = '您好,您有一份新的案件[裁决书]提醒'; //微信
        $content1 = '案件[裁决书]提醒'; //msg
        $content2 = self::responseMsg('案件[裁决书]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[裁决书]');


        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content0);
        }
//        (new Dmail())->sendShouLiWenjianToBeiShenQingRen($did);
//        (new Dmail())->sendShouLiWenjianToShenQingRen($did);
        return true;
    }

    public static function sendGid_23($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $huibi = Db::name('huibi')->where("dossier_id",$did)->where("is_valid=1")->find();
        $paichu_idid = $huibi['idid'];

        $content0 = '仲裁员[声明]提醒'; //msg
        $content1 = '案件[声明]提醒'; //微信
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[声明]');


        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }
        (new Dmail())->sendShengmingAllPersons($did,$gid,$ext_id);

        return true;
    }


    public static function sendGid_22($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $huibi = Db::name('huibi')->where("id",$ext_id)->find();
        $paichu_idid = $huibi['idid'];

        $content0 = '仲裁员[披露]提醒'; //msg
        $content1 = '仲裁员[披露]提醒'; //微信
        $content2 = self::responseMsg($content1.','.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[披露]');








        

        (new Dmail())->sendShengmingAllPersons($did,$gid,$ext_id);


        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }

        return true;
    }


    public static function sendGid_20($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];
        $content1 = '案件[待声明/披露]提醒'; //微信:仲裁员
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[待声明/披露]';

        $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');

        $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();

        $zcy_phone = $zcy_info['phone'];

        (new Mes())->msg_insert($did,$content1,$gid,$zcy_info['idid'],$ext_id);

        Notice::sendSms($zcy_phone,self::responseMsg('案件[待声明/披露]提醒'.$content2.',请及时处理'),'did:'.$did.'gid:20');

        $info = Db::name('idcards')->whereIn('id',$zcy_info['idid'])->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2."\n");

        //仲裁员没有邮件
        return true;
    }



    public static function sendGid_18($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $question_info = Db::name('dossier_question')->where("id",$ext_id)->find();
        $paichu_idid = $question_info['idid'];

        $content0 = '案件[质证]提醒'; //msg
        $content1 = '您好,您有一份新的仲裁案件的[质证]提醒'; //微信
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[质证]');

        file_put_contents('qrtyuioppoiuytrewrytui.txt',[$did,$gid,$ext_id]);

        (new Dmail())->sendQuestionZhengJu($did,$gid,$ext_id);


        if($question_info['zids']){

            (new Dmail())->sendQuestionZhengJu1($did,$gid,$ext_id);
        }

        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }


        return true;
    }


    public static function sms_wx_info_mail($did,$gid,$ext_id,$idids,$phones,$content0,$content2,$content1,$paichu_idid)
    {
        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }
    }

    public static function sendGid_19($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $defence_info = Db::name('dossier_defence')->where("id",$ext_id)->find();
        $paichu_idid = $defence_info['idid'];

        $content0 = '您好,您有一份新的仲裁案件的[答辩]提醒'; //weixn
        $content1 = '案件[答辩]提醒'; //msg
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[答辩]');


        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        (new Dmail())->sendDaBianZhengJu($did,$gid,$ext_id);

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content0);
        }


        return true;
    }



    public static function sendGid_17($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

//        $content0 = '案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno'],$dossier_info['addtime']).'[已立案]提醒'; //msg
        $content1 = '案件[已立案]提醒'; //微信
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[已立案]');

        $zhuban_idid = Db::name('dossier_roles')->where("dossier_id='$did' and role=10")->value('idid');

        $zhuban_phone = Db::name('jigou_admin')->where("idid='$zhuban_idid'")->value('phone');
        $idids[100] = $zhuban_idid;
        $phones[100] = $zhuban_phone;

        foreach($idids as  $key=>$value){
            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();
        foreach($info as $key=>$value){
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }

        (new Dmail())->sendShouLiWenjianToBeiShenQingRen($did);
        (new Dmail())->sendShouLiWenjianToShenQingRen($did);


        return true;
    }

    public static function sendGid_16($dossier_info,$did,$gid,$ext_id)
    {
        return self::sendGid_4($dossier_info,$did,$gid,$ext_id,true);
    }


    public static function sendGid_14($dossier_info,$did,$gid,$ext_id)
    {
        $phones = [];

        $idids = [];

        $content1 = '仲裁员[披露]提醒'; //微信
        $sms_content = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'案由为借款合同、案号为'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件仲裁员[披露]');

        $zhuban_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
        $zhuren_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');

        $phone_zhuban =  Db::name('jigou_admin')->where("idid='$zhuban_idid'")->where('status',1)->where("role",1)->find();
        $phone_zhuren =  Db::name('jigou_admin')->where("idid='$zhuren_idid'")->where('status',1)->where("role",2)->find();

        $idids[10000] = $phone_zhuban['idid'];
        $phones[10000] = $phone_zhuban['phone'];
        $idids[1000] = $phone_zhuren['idid'];
        $phones[1000] = $phone_zhuren['phone'];

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){

            if($key>=100){
                Notice::sendSms($value,$sms_content);
            }else{
                Notice::sendSms($value,$sms_content,self::URL_dsr);
            }

        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();
        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1."\n");
        }

        return true;
    }


    public static function sendGid_13($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $content0 = '案件[已受理]提醒'; //msg
        $content1 = '您好,您有一份新的仲裁案件的[已受理]提醒'; //微信
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.',提交的案由为:借款合同, 案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno'] ).' 的仲裁案件[已受理]');

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }

        (new Dmail())->sendShouLiWenjianToBeiShenQingRen($did);
        (new Dmail())->sendShouLiWenjianToShenQingRen($did);
        return true;
    }

    public static function sendGid_10($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '案件[已完成]提醒'; //微信

        $sms_content = self::responseMsg('案件[已完成]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件已完成'); //短信

        $zhuban_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
        $zhuren_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');

        $phone_zhuban =  Db::name('jigou_admin')->where("idid='$zhuban_idid'")->where('status',1)->where("role",1)->find();
        $phone_zhuren =  Db::name('jigou_admin')->where("idid='$zhuren_idid'")->where('status',1)->where("role",2)->find();

        $idids[10000] = $phone_zhuban['idid'];
        $phones[10000] = $phone_zhuban['phone'];
        $idids[1000] = $phone_zhuren['idid'];
        $phones[1000] = $phone_zhuren['phone'];

        $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
        $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();

        $idids[100] = $zcy_info['idid'];
        $phones[100] = $zcy_info['phone'];

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){


//            Notice::sendSms($value,self::responseMsg($sms_content));
            if($key>=100){
                Notice::sendSms($value,self::responseMsg($sms_content));
            }else{
                Notice::sendSms($value,self::responseMsg($sms_content,self::URL_dsr));
            }




        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();


        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1."\n");
        }

        return true;

    }


    public static function sendGid_7($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '案件[质证]提醒'; //微信
//        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.',提交的案由为:借款合同 的案件质证申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[质证]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件质证申请'); //短信

        $zhuban_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
        $zhuren_idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=17")->value('idid');

        $phone_zhuban =  Db::name('jigou_admin')->where("idid='$zhuban_idid'")->where('status',1)->where("role",1)->find();
        $phone_zhuren =  Db::name('jigou_admin')->where("idid='$zhuren_idid'")->where('status',1)->where("role",2)->find();

        $idids[10000] = $phone_zhuban['idid'];
        $phones[10000] = $phone_zhuban['phone'];
        $idids[1000] = $phone_zhuren['idid'];
        $phones[1000] = $phone_zhuren['phone'];

//        $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
//        $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();
//
//        $idids[100] = $zcy_info['idid'];
//        $phones[100] = $zcy_info['phone'];

        foreach($idids as $key=>$value){

            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){

            if($key>=100){
                Notice::sendSms($value,self::responseMsg($sms_content));
            }else{
                Notice::sendSms($value,self::responseMsg($sms_content,self::URL_dsr));
            }


//            Notice::sendSms($value,$sms_content);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();


        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1."\n");
        }

        return true;
    }


    public static function sendGid_6($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '案件[回避]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件回避申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[回避]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件回避申请'); //短信

        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');

        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();

        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);

        Notice::sendSms($phones,$sms_content);

        $info = Db::name('idcards')->where('id',$idid)->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);

        return true;
    }

    public static function sendGid_5($dossier_info,$did,$gid,$ext_id)
    {
//        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);
//
//        $dangshiren = array_column($users, 'name');
//
//        $apply_peo = $dangshiren[0];
//
//        $content1 = '仲裁员[声明]提醒'; //微信
//        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的仲裁员[声明]'; //微信事项描述
//
//        $sms_content = self::responseMsg('仲裁员[声明]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的仲裁员[声明]'); //短信
//
//        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');
//
//        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();
//
//        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);
//
//        Notice::sendSms($phones,$sms_content);
//
//        $info = Db::name('idcards')->where('id',$idid)->find();
//
//        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);
//
//        return true;

        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $huibi = Db::name('huibi')->where("dossier_id",$did)->where("is_valid=1")->find();
        $paichu_idid = $huibi['idid'];

        $content0 = '仲裁员[声明]提醒'; //msg
        $content1 = '案件[声明]提醒'; //微信
        $content2 = self::responseMsg(date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).' 的仲裁案件[声明]');


        foreach($idids as $key=>$value){

            if($value==$paichu_idid){
                unset($idids[$key]);
            }

            (new Mes())->msg_insert($did,$content0,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$content2);
        }
        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            if($value['id']==$paichu_idid){
                unset($info[$key]);
            }
            (new NewsModel($did))->zhongCaiTongZhi($value['id'],$value['openid'],'',$content1);
        }
        (new Dmail())->sendShengmingAllPersons($did,$gid,$ext_id);

        return true;
    }


    //组庭
    public static function sendGid_4($dossier_info,$did,$gid,$ext_id,$is_chongxinzuting=false)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        $zcy_uid = Db::name('arbitrator')->where("dossier_id='$did'")->value('zcy_uid');
        $zcy_info = Db::name('zcy')->where("id='$zcy_uid'")->find();

        $phones[100] = $zcy_info['phone'];
        $idids[100] = $zcy_info['idid'];


        $content1 = '您好,您收到一份仲裁案件的[组庭]通知'; //微信
        $content_msg = '案件[组庭]通知'; //msg

        $sms_content = self::responseMsg('案件[组庭]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同案号为:'.DossierDoc::getZcNoByNo($dossier_info['zno']).'的案件组庭通知'); //短信


        if($is_chongxinzuting){
            $content1 = '您好,您收到一份仲裁案件的[重新组庭]通知'; //微信
            $content_msg = '案件[重新组庭]通知'; //msg
            $sms_content = self::responseMsg('案件[重新组庭]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人:'.$apply_peo.'提交的案由为借款合同 的案件重新组庭通知'); //短信
        }

        foreach($idids as  $key=>$value){

            (new Mes())->msg_insert($did,$content_msg,$gid,$value,$ext_id);
        }
     

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$sms_content);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){

            (new NewsModel($did))->zhongCaizutingTongZhi($value['id'],$value['openid'],'',$content1);
        }
        (new Dmail())->sendZuTingAllPersons($did,$gid,$ext_id,$is_chongxinzuting);
        return true;
    }

    public static function sendGid_3($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '案件[答辩]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的案件答辩申请'; //微信事项描述

        $sms_content = self::responseMsg('案件[答辩]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的案件答辩申请'); //短信

        $idid = Db::name("dossier_roles")->where("dossier_id='$did' and role=10")->value('idid');

        $phones =  Db::name('jigou_admin')->where("idid='$idid'")->where('status',1)->where('role',1)->find();

        (new Mes())->msg_insert($did,$content1,$gid,$idid,$ext_id);

        Notice::sendSms($phones,$sms_content);

        $info = Db::name('idcards')->where('id',$idid)->find();

        (new NewsModel($did))->zhongCaiDaiBanTongZhi($info['id'],$info['openid'],'',$content1,$content2);

        return true;
    }

    public static function sendGid_1($dossier_info,$did,$gid,$ext_id)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '新的案件[待受理]提醒'; //微信
        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的新仲裁申请'; //微信事项描述

        $sms_content = self::responseMsg('新的案件[待受理]提醒,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的新案件'); //短信

        $phones = array_column((array)Db::name('jigou_admin')->where("th_id={$dossier_info['zc_jg_id']}")->where('status',1)->where('role',1)->select(),'phone','idid');

        $idids = array_keys($phones);

        foreach($idids as  $key=>$value){
            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$sms_content);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
                (new NewsModel($did))->zhongCaiDaiBanTongZhi($value['id'],$value['openid'],'',$content1,$content2);
        }

        return true;
    }


    //主办操作时,主任的提醒  暂不知gid是哪个
    public static function sendZhuRenFromZhuBan($dossier_info,$did,$gid,$ext_id)
    {



        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);

        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $content1 = '新的案件[待立案]提醒'; //微信

        $content2 = date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同的新案件,主办已受理'; //微信事项描述

        $sms_content = self::responseMsg('新的案件[待受理]通知,'.date('Y年m月d日 H时i分',$dossier_info['addtime']).'申请人'.$apply_peo.'提交的案由为借款合同 的新案件主办已受理,请您及时处理'); //短信

        $phones = array_column((array)Db::name('jigou_admin')->where("th_id={$dossier_info['zc_jg_id']}")->where('status',1)->where('role',2)->select(),'phone','idid');

        $idids = array_keys($phones);

        foreach($idids as  $key=>$value){
            (new Mes())->msg_insert($did,$content1,$gid,$value,$ext_id);
        }

        foreach($phones as  $key=>$value){
            Notice::sendSms($value,$sms_content);
        }

        $info = Db::name('idcards')->whereIn('id',$idids)->select();

        foreach($info as $key=>$value){
            (new NewsModel($did))->zhongCaiDaiBanTongZhi($value['id'],$value['openid'],'',$content1,$content2);
        }

        return true;
    }

    public static function getDangShiRenNameRemoveDl($did)
    {
        $users = \wslibs\wszc\Dossier::getDangShiRen($did, 0);
        foreach($users as $key=>$value){
            if($value['role']==3 || $value['role']==4){
                unset($users[$key]);
            }
        }
        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        $phones = array_column($users,'phone','idid');

        $idids = array_keys($phones);

        return [$apply_peo,$phones,$idids];
    }

    public static function responseMsg($msg,$url = self::URL_zcw)
    {

        return '您好,'.$msg.',请到官网 '.$url.' 登录处理,或关注微信服务号:zhzhongcai';
    }
}