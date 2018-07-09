<?php
namespace wslibs\wszc\caijue;

use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;
use wslibs\wszc\Ds;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/19
 * Time: 下午4:38
 */
class Dcaijue
{

    public static function updateStatus($did, $tostatus, $data = array())
    {
        $data['c_status'] = $tostatus;
        Db::name("dossier_caijue")->where("id", $did)->update($data);

    }


    public static function autoSend($did)
    {


        $info = self::getCaiJueInfo($did);


        $role = User::getRoleInDossier($did, LoginUser::getIdid());

        if ($info['c_status'] == 1 && ($role != Constant::QX_ROLE_ZHONGCAIYUAN)) {
            return false;
        }
        if ($info['c_status'] == 2 && ($role != Constant::QX_ROLE_ZHONGCAIWEI_MISHU)) {
            return false;
        }
        if ($info['c_status'] == 3 && ($role != Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE)) {
            return false;
        }
        if ($info['c_status'] == 4 && ($role != Constant::QX_ROLE_ZHONGCAIWEI_MISHU)) {
            return false;
        }
        if ($info['c_status'] == 5 && ($role != Constant::QX_ROLE_ZHONGCAIYUAN)) {
            return false;
        }
        if ($info['c_status'] == 6 && ($role != Constant::QX_ROLE_ZHONGCAIWEI_MISHU)) {
            return false;
        }
        if ($info['c_status'] == 7) {
            return false;
        }
        if ($info['c_status'] == 1) {
            Ds::sendGroupFileToDocRole($did, Constant::FILE_GROUP_caijue, Constant::D_Role_ZhongCaiWei_JiGou);
           // Ds::sendGroupFileToUid($did, Constant::FILE_GROUP_caijue, LoginUser::getIdid());
        } else if ($info['c_status'] == 2) {
            Ds::sendGroupFileToDocRole($did, Constant::FILE_GROUP_caijue_fasongzhizhuren, Constant::D_Role_ZhongCaiWei_CaiJueShenPi);
           // Ds::sendGroupFileToUid($did, Constant::FILE_GROUP_caijue_fasongzhizhuren, LoginUser::getIdid());
        } else if ($info['c_status'] == 2) {
            //  Ds::sendGroupFileToDocRole($did, Constant::FILE_GROUP_caijue_fasongzhizhuren, Constant::D_Role_ZhongCaiWei_CaiJueShenPi);
            //Ds::sendGroupFileToUid($did, Constant::FILE_GROUP_caijue_fasongzhizhuren, LoginUser::getIdid());
        }
        Db::name("dossier_caijue")->where("id", $did)->setInc("c_status");
        Db::name("dossier_caijue")->where("id", $did)->update(['c_edit' => 0]);
        if ($info['c_status'] == 6) {
            Dossier::changeStatus($did, 4);
        }

        return true;
    }

    public static function update($did, $data = array())
    {
        if (isset($data['c_status'])) unset($data['c_status']);
        Db::name("dossier_caijue")->where("id", $did)->update($data);

    }
    public static function dahui($did )
    {

      return  Db::name("dossier_caijue")->where("id", $did)->update(['c_status'=>1,'c_edit'=>0]);

    }
    public static function updateNeirong($did, $data = array())
    {
        foreach ($data as $key => $value) {
            Dvalue::saveUniqueValueByDocMode($did, Constant::DOC_model_caijueshu, $key, $value);
        }

    }

    public static function getNeirong($did)
    {
        $docinfo = Ddocs::getOrInitFile($did, Constant::DOC_model_caijueshu);
        return Dvalue::getUniqueValueOfDoc($docinfo['id']);
    }

    public static function getLoginUserBtn($did)
    {
        $btn = array();
        $btn1 = array();
        $btn2 = array();
        $btn3 = array();
        $info = self::getCaiJueInfo($did);
        $info['status'] = $info['c_status'];
        $inrole = User::getDroleInDossier($did, LoginUser::getIdid());
        $c_edit = $info['c_edit'];

//         dump($inrole);
    //   dump($info);
       // var_dump(User::getRoleInDossier($did, LoginUser::getIdid()));

        if ($inrole) {
            // var_dump($inrole == Constant::D_Role_ZhongCaiYuan);
            if ($inrole == Constant::D_Role_ZhongCaiWei_GuanLiYuan) {
                if ($info['status'] == 2) {
                    $btn['title'] = "校对裁决书";

                    $btn1['title'] = "保存";
                    $btn2['title'] = "发送至主任审批";

                    $btn2['dialog'] = "1";
                    $btn2['ajax'] = "0";
                    $btn2['tip'] = "确定发送至主任审批吗？";

                    if ($c_edit)
                    {
                        $btn3['title'] = "发送至仲裁员";
                        $btn3['to_status'] = "1";
                    }

                } else if ($info['status'] == 4) {
                    $btn['title'] = "裁决书已审批";
                    $btn2['title'] = "发送至仲裁员签字";
                    $btn2['dialog'] = "0";
                    $btn2['ajax'] = "1";
                } else if ($info['status'] == 6) {
                    $btn['title'] = "印发裁决书";
                    $btn2['title'] = "主办去签署";
                    $btn2['tip'] = "确定去签署吗？";

                }
            } else if ($inrole == Constant::D_Role_ZhongCaiYuan) {

                if ($info['status'] == 1) {
                    $btn['title'] = "编写裁决书";

                    $btn1['title'] = "保存";
                    $btn2['title'] = "发送至仲裁委";
                    $btn2['dialog'] = "0";
                    $btn2['ajax'] = "1";
                } else if ($info['status'] == 5) {
                    $btn['title'] = "裁决书签字";
                    $btn2['title'] = "去签署";
                    $btn2['tip'] = "确定去签署吗？";
                }
            } else if ($inrole == Constant::D_Role_ZhongCaiWei_CaiJueShenPi ) {
                if ($info['status'] == 3) {


                    $btn['title'] = "校对裁决书";
                    $btn1['title'] = "修改";
                    $btn2['title'] = "审批";
                    $btn2['dialog'] = "1";
                    $btn2['ajax'] = "0";
                }

            }
        } else if (User::getRoleInDossier($did, LoginUser::getIdid()) == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE) {

            if ($info['status'] == 3) {

                $btn['title'] = "审批裁决书";
                $btn1['title'] = "修改";
                $btn2['title'] = "审批";

            }
        }



        return [$btn, $btn1, $btn2,$btn3];


    }

    public static function getCaiJueShu($did)
    {

        return Ddocs::getOrInitFile($did, Constant::DOC_model_caijueshu);


    }


    public static function canedit($status)
    {
        return $status <= 4;
    }

    public static function getCaiJueInfo($did)
    {
        $info = Db::name("dossier_caijue")->where("id", $did)->find();

        if (!$info) {
            Db::name("dossier_caijue")->insertGetId(array("id" => $did));
            return self::getCaiJueInfo($did);
        } else {
            return $info;
        }

    }

}