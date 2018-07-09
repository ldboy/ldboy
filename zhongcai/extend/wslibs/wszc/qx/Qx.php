<?php
namespace wslibs\wszc\qx;

use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\User;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/10
 * Time: 下午8:10
 */
class Qx
{


    private static function checkRole($rolearray)
    {

        $roles = array(Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_BEISHENQINGREN,
            Constant::QX_ROLE_BEISHENQINGREN,
            Constant::QX_ROLE_ZHONGCAIYUAN,
            Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN,
            Constant::QX_ROLE_ZHONGCAIWEI_MISHU,
            Constant::QX_ROLE_ADMIN);

        $rolearray = self::stringToArray($rolearray);
        $outrole = 0;
        foreach ($rolearray as $role) {
            if (!in_array($role, $roles)) {
                return false;

            }
            $outrole = $outrole | $role;
        }
        return $outrole;
    }

    public static function hasQxInDoc($uid, $docinfo)
    {

        if ($uid == $docinfo['uid']) {
            return true;
        }
        return $docinfo["qx"] && User::getRoleInDossier($docinfo['dossier_id']);
    }

    public static function addQxToDoc($role, $docids)
    {
        if ($role = self::checkRole($role)) {


            return Db::name("dr")->where("id", "in", self::stringToArray($docids))->exp("qx", " qx | $role")->update();
        }


        return false;

    }

    public static function addQxToDocFilesAndPdf($role, $docids)//添加文件查詢權限
    {
        if ($role = self::checkRole($role)) {

            return Db::name("drv")->whereIn("doc_id",self::stringToArray($docids))->whereIn("var_name",array(Constant::Dvalue_var_name_pdf,Constant::Dvalue_var_name_file))->exp("qx", " qx | $role")->update();
        }

        return false;
    }

    public static function addQxToDocValue($role, $vids)
    {
        if ($role = self::checkRole($role)) {
            return Db::name("drv")->where("id", "in", self::stringToArray($vids))->exp("qx", " qx | $role")->update();
        }

        return false;
    }
    public static function hasQxInDocValue($uid, $vinfo)
    {

        if ($uid == $vinfo['uid']) {
            return true;
        }
        return $vinfo["qx"] && User::getRoleInDossier($vinfo['dossier_id']);
    }
    public static function delQxToDoc($role, $docids)
    {
        if ($role = self::checkRole($role)) {
            return Db::name("dr")->where("id", "in", self::stringToArray($docids))->exp("qx", " qx ^$role")->update();
        }

        return false;

    }

    public static function delQxToDocValue($role, $vids)
    {
        if ($role = self::checkRole($role)) {
            return Db::name("drv")->where("id", "in", self::stringToArray($vids))->exp("qx", " qx ^$role")->update();
        }

        return false;
    }

    private static function stringToArray($ids)
    {

        return array_unique(is_array($ids) ? $ids : array_filter(explode(",", $ids)));

    }


}