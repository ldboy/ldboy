<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/11
 * Time: ����3:58
 */

namespace wslibs\wszc;


class LoginUser
{
    private static $_admin = -1;
    private static $_zcuser = -1;

    public static function isLogin()
    {
        return self::getAdminValue("id") && self::getZcuserValue("login_idid");

    }

    public static function getIdid()
    {

        $idid = request()->param("___idid__/d");
        if ($idid) return $idid;

        return (int)self::getZcuserValue("login_idid");

    }

    public static function getRole()
    {
        return self::getZcuserValue("login_role");

    }

    public static function getRoleUid()
    {
        return self::getZcuserValue("login_role_uid");

    }

    public static function getRoleThId()
    {
        return self::getZcuserValue("login_role_th_id");

    }

    public static function getRoleThIdId()
    {
        return self::getZcuserValue("login_role_th_idid");

    }

    public static function getRoleThName()
    {
        return self::getZcuserValue("login_role_th_name");

    }

    public static function getRoleRole()
    {
        return self::getZcuserValue("login_role_role");

    }

    public static function getRoleRoleName()
    {
        return self::getZcuserValue("login_role_role_name");

    }

    public static function isRole($role)
    {
        return self::getZcuserValue("login_role") == $role;
    }

    public static function isRoleRole($role)
    {
        return self::getZcuserValue("login_role_role") == $role;
    }

    public static function getUserName()
    {
        return self::getZcuserValue("login_name");
    }

    public static function isZhongCaiWei()
    {
        return self::isRole(Constant::Admin_Role_zhongcaiwei);
    }

    public static function isZhongCaiWeiZhuBan()
    {
        return self::isRole(Constant::Admin_Role_zhongcaiwei) && self::isRoleRole(Constant::ZhongCaiWei_Role_ZhuBan);
    }

    public static function isZhongCaiLiAanShenPi()
    {
        return self::isRole(Constant::Admin_Role_zhongcaiwei) && self::isRoleRole(Constant::ZhongCaiWei_Role_LianShenPi);
    }

    public static function isZhongCaiWeiCaiJueShenpi()
    {
        return self::isRole(Constant::Admin_Role_zhongcaiwei) && self::isRoleRole(Constant::ZhongCaiWei_Role_CaiJueShenPi);
    }

    public static function isZhongCaiYuan()
    {
        return self::isRole(Constant::Admin_Role_zhongcaiyuan);
    }

    public static function isShenQingFang()
    {
        return self::isRole(Constant::Admin_Role_yinhang);
    }

    public static function isBeiShenQingFang()
    {
        return self::isRole(Constant::Admin_Role_putongyonghu);
    }

    private static function getAdminValue($key)
    {
        if (self::$_admin == -1) {
            self::$_admin = session("admin");
        }
        return self::$_admin ? self::$_admin[$key] : null;
    }

    private static function getZcuserValue($key)
    {

        if (self::$_zcuser == -1) {
            self::$_zcuser = session("zcuser");
        }
        return self::$_zcuser ? self::$_zcuser[$key] : null;

    }


}