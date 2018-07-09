<?php
namespace wslibs\wszc\dtip;

use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;
use wslibs\wszc\Drole;
use wslibs\wszc\Dvalue;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\LoginUser;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/22
 * Time: 下午1:31
 */
class Dtip
{
    public static function getTip($did, $gid, $extid, $euid = array(),$is_phone=false)
    {
//        if (!is_array($did_or_info)) {
//            $info = Dossier::getSimpleDossier($did_or_info);
//        } else {
//            $info = $did_or_info;
//        }

        if (in_array($gid, array(1, 3, 5, 6, 7, 14,20, 25,29,30))) {
            $tip = self::getZhongCaiWei($did);
            if(LoginUser::isZhongCaiWeiZhuBan()){
                $tip = '';
            }
        } else if (in_array($gid, array(2))) {
            $tip = self::getShenqingren($did);
        } else if (in_array($gid, array(13))) {
            $tip = self::getBeiShenqingren($did);
        } else if (in_array($gid, array(18,23,26,22,19))) {
            $tip = self::getDangShiRen($did,$extid,$gid);
        } else if (in_array($gid, array(23,24,26))) {
            $tip = self::getZhongCaiYuan($did);
        } else if (in_array($gid, array(17))) {
            $tip = "立案审批主任";
        }elseif(in_array($gid,[31])){
            $tip = "以下材料发送至主任";
        } else{
            $tip = "";
        }


        if(LoginUser::isRole(Constant::Admin_Role_zhongcaiwei)){
            if(in_array($gid,array(4))){

                $tip = self::getZcyAndDsr($did);
            }

        }
        if ($tip) {

            if($is_phone){
                $tip = "以下资料将发送至: " . $tip;
            }else{
                $tip = "以下资料将发送至：" . $tip;
            }
        }
        return $tip;



    }

    public static function getZcyAndDsr($did)
    {
        return self::getZhongCaiYuan($did).",".self::getDangShiRen($did,0,0);
    }

    public static function getShenqingren($did)
    {
        return implode(",",array_column((Array)Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(1))->select(),"name"));

    }

    public static function getBeiShenqingren($did)
    {
        return implode(",",array_column((Array)Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(2, 4))->select(),"name"));

    }

    public static function getZhongCaiWei($did)
    {
        return "石家庄仲裁委员会";

    }

    public static function getZhongCaiYuan($did)
    {

        $info = Db::name("arbitrator")->where("dossier_id",$did)->where("status",1)->find();
        return $info['name'];
    }



    public static function getDangShiRen($did,$exid,$gid)
    {
        $idid = self::getIDidbyExid($exid,$gid);
        
        $name_s = Db::name("dossier_users")->where("dossier_id", $did)->whereIn('role',[1,2])->select();


        foreach($name_s as $key=>$value){

            if($value['type']==2){
                $com_name[$value['idid']] = $value['name'];
            }
            break;
        }

        $name = array_column($name_s,'name','idid');

        if($name[$idid]){
            $idid_name = $name[$idid];
            unset($name[$idid]);

            foreach($name as $key=>$value){
                if($com_name[$key]){
                    $name[$key] = $com_name[$key];
                }
            }

            return implode(",",array_values($name)).' (由'.$idid_name.'提供)';

        }
        return implode(",",array_values($name));
    }


    public static function getIDidbyExid($exid,$gid)
    {
        $idid = 0;

        if($exid){
            switch($gid){
                case 19:
                    $idid = Db::name('dossier_defence')->where("id",$exid)->value('idid');
                    break;
                case 18:
                    $idid = Db::name('dossier_question')->where("id",$exid)->value('idid');
                    break;
                case 26:
                    $idid = Db::name('dz')->where("id",$exid)->value('idid');
                    break;
                case 22 || 23:
                    $idid = Db::name('huibi')->where("id",$exid)->value('idid');
                    break;
                default:
                    $idid = 0;
            }
        }
        return $idid;
    }

    public static function getProposal($d_id, $gid, $exid){
        if(!$exid){
            return '';
        }
        $gidArr = [
          Constant::FILE_GROUP_tijiaozhengju_sqr,
          Constant::FILE_GROUP_tijiaozhengju_bsqr,
        ];
        if(!in_array($gid,$gidArr)){
            return '';
        }

        $proposal = Dvalue::getUniqueValueOfDossier($d_id,'zj_proposal_'.$exid);
        
        return $proposal;
    }
}