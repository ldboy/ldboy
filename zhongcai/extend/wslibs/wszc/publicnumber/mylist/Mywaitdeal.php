<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/27
 * Time: 上午9:17
 */

namespace wslibs\wszc\publicnumber\mylist;


use app\admin\library\Auth;

use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\Dother;
use wslibs\wszc\User;


class Mywaitdeal
{
    public static function getMyWitDeal($idid)
    {
        return  Mydossier::getMyDossierList($idid);
    }

    public static function cdMyDossierInfoLogin($idid)
    {
        $role = self::isZhuRenOrZhuBan($idid);

        if(!$role) return false;

//        $role_role =$role==1 ?  Constant::D_Role_ZhongCaiWei_GuanLiYuan : Constant::D_Role_ZhongCaiWei_LiAnShenPi;

        if($zc_user = session('zcuser')){
            if($zc_user['login_idid']!=$idid || $zc_user['login_role']!=Constant::Admin_Role_zhongcaiwei ){
                if($role){

                    $role = Constant::Admin_Role_zhongcaiwei;

                    (new Auth())->loginByIdid($idid,$role);

                    return $role;
                }
                return false;
            }
            return $role;
        }

        $role = Constant::Admin_Role_zhongcaiwei;

        (new Auth())->loginByIdid($idid,$role);

        return $role;
    }

    public static function isZhuRenOrZhuBan($idid)
    {
        return  Db::name("jigou_admin")->where("idid",$idid)->value("role");
    }


    public static function isIdIdInDossier($idid,$did)
    {
        $result = Db::name('dossier_roles')->where('dossier_id',$did)->where('idid',$idid)->find();

        $dossier_info = Dossier::getSimpleDossier($did);

        if($dossier_info['sub_status']>=21) {
            if($result) return true;
            return false;
        }

        return true;
    }


    public static function getNav($did,$idid,$role_qx)
    {

        list($d_num,$d_num_total) = Mywaitdeal::getDbNum($did,$role_qx);

        list($q_num,$q_num_total) = Mywaitdeal::getZhiZNum($did,$role_qx);

        list($dz_num,$dz_num_total) = Mywaitdeal::getDzNum($did,$role_qx);

        list($h_num,$h_num_total) = Mywaitdeal::getHuiBiNum($did,$role_qx);

        list($yy_num,$yy_total) = Mywaitdeal::getGXQNum($did,$role_qx);

        $list = [];

        $list[] = self::getList('案件资料','','admin/wechat/casecailiao/index','',$did,$idid);
        $list[] = self::getList('当事人','总'.self::getDsrNum($did),'admin/wechat.myinfo/dangshiren','',$did,$idid);
        $list[] = self::getList('答辩','待'.$d_num.'总'.$d_num_total,'admin/wechat.myinfo/dabian','',$did,$idid);
        $list[] = self::getList('回避/声明/披露','待'.$h_num.'总'.$h_num_total,'admin/wechat.myinfo/huibismpl','',$did,$idid);
        $list[] = self::getList('质证','待'.$q_num.'总'.$q_num_total,'admin/wechat.myinfo/zhizheng','',$did,$idid);
        $list[] = self::getList('提交证据','待'.$dz_num.'总'.$dz_num_total,'admin/wechat.myinfo/zjlist','',$did,$idid);//http://zcw.wszx.cc/admin/wechat.myinfo/guanxiaquan?did=161
        $list[] = self::getList('管辖权异议','待'.$yy_num.'总'.$yy_total,'admin/wechat.myinfo/guanxiaquan','',$did,$idid);
        $list[] = self::getList('其他申请要求','总'.Dother::getOtherNum($did),'admin/wechat.myinfo/otherlist','',$did,$idid);
        $list[] = self::getList('日志',DossierLog::getLogNum($did),'admin/wechat.myinfo/loglist','',$did,$idid);

        return $list;
    }

    public static function getDsrNum($did)
    {
        return Db::name('dossier_users')->where("dossier_id",$did)->count();
    }

    public static function getDbNum($d_id,$zhubanOrzhuren=Constant::ZhongCaiWei_Role_ZhuBan)//1主办2主任
    {
        $zhubanOrzhuren==Constant::ZhongCaiWei_Role_ZhuBan ?

        $defence_count = Db::name('dossier_defence')
            ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=2,1,0)) as dcl ")
            ->where("dossier_id",$d_id)
            ->find() :
            $defence_count = Db::name('dossier_defence')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=1000,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find();

        $d_num = (int)$defence_count['dcl'];
        $d_num_total = (int)$defence_count['total'];

        return [$d_num,$d_num_total];

    }

    public static function getDzNum($d_id,$zhubanOrzhuren=1)
    {
        $zhubanOrzhuren==Constant::ZhongCaiWei_Role_ZhuBan ?

            $dz_count =  Db::name('dz')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=2,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find():
            $dz_count =  Db::name('dz')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=1000,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find();


        $dz_num = (int)$dz_count['dcl'];
        $dz_num_total = (int)$dz_count['total'];


        return [$dz_num,$dz_num_total];
    }

    public static function getZhiZNum($d_id,$zhubanOrzhuren=1)
    {
        $zhubanOrzhuren==Constant::ZhongCaiWei_Role_ZhuBan ?

            $question_count = Db::name('dossier_question')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=2,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find():
            $question_count = Db::name('dossier_question')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=1000,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find();
        $q_num = (int)$question_count['dcl'];
        $q_num_total = (int)$question_count['total'];


        return [$q_num,$q_num_total];
    }

    public static function getHuiBiNum($d_id,$zhubanOrzhuren=1)
    {
        $zhubanOrzhuren==Constant::ZhongCaiWei_Role_ZhuBan  ?
        $huibi_count = Db::name('huibi')
            ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=2,1,0)) as dcl ")
            ->where("dossier_id",$d_id)
            ->find():

            $huibi_count = Db::name('huibi')
                ->field(" sum(if(status>=2,1,0)) as total,sum(if(status=5 or status=6,1,0)) as dcl ")
                ->where("dossier_id",$d_id)
                ->find();

        $h_num = (int)$huibi_count['dcl'];
        $h_num_total = (int)$huibi_count['total'];

        return [$h_num,$h_num_total];
    }


    public static function getGXQNum($d_id,$zhubanOrzhuren=1)
    {
        $zhubanOrzhuren == Constant::ZhongCaiWei_Role_ZhuBan ?
        $yy_count = Db::name('gxq_yy')
            ->field("sum(if(status>=2,1,0)) as total,sum(if(status=2 or status=7 or status=8,1,0)) as dcl")
            ->where('d_id',$d_id)
            ->find():
            $yy_count = Db::name('gxq_yy')
                ->field("sum(if(status>=2,1,0)) as total,sum(if(status=3 or status=4,1,0)) as dcl")
                ->where('d_id',$d_id)
                ->find();

        $yy_num = (int)$yy_count['dcl'];
        $yy_total = (int)$yy_count['total'];
        return [$yy_num,$yy_total];
    }




    public static function getList($name,$num,$link,$tip,$did,$idid)
    {
        return array(
            'name'=>$name,
            'num'=>$num,
            'link'=>WEB_SITE_ROOT.$link."?did=".$did."&idid=".$idid,
            'tip'=>$tip,
        );
    }
}