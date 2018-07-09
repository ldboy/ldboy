<?php
namespace wslibs\wszc;

use app\admin\controller\Login;
use think\Db;
use think\db\connector\Mysql;
use think\Session;
use wslibs\wszc\idcard\IDcard;

class User
{

    private static $userRoleInDossier = [];

    public static function getLoginUid()
    {

        return LoginUser::getIdid();

    }


    public static function getUserNameByIdids($idids)
    {

        $isa = false;
        if (!is_array($idids)) {
            $idids = explode(",", $idids);
        } else {
            $isa = true;
        }
        $idids = array_unique($idids);
        if ($idids)
            $list = Db::name('idcards')->whereIn("id", $idids)->column("id,real_name");
        else return array();
        if ((!$isa) && count($idids) === 1) {
            return $list[$idids[0]];
        } else  return $list;
    }

    public static function addUser($phone,$idid=0)
    {
        if ($userInfo = Db::name('user')->where('phone', $phone)->find()) {
            Db::name('user')->where('id',$userInfo['id'])->update(['idid'=>$idid]);
            $uid = $userInfo['id'];
        } else {
            $uid = Db::name('user')->insertGetId(['phone' => $phone,'idid'=>$idid]);
        }
        return $uid;
    }


    public static function addUserInfo($uid, $sex, $birthday, $address = '',$fax='',$email='')
    {

        $data = [
            'uid' => $uid,
            'sex' => $sex,
            'birthday' => $birthday,
            'address' => $address,
            'fax' => $fax,
            'email' => $email,
        ];

        if ($userInfo = Db::name('user_info')->where('uid', $uid)->find()) {
            $id = $userInfo['uid'];

            Db::name('user_info')->where('uid',$uid)->update($data);

        } else {

            $id = Db::name('user_info')->insert($data);
        }


        return $id;

    }

    public static function getSex($idcard)
    {

        return (substr($idcard, (strlen($idcard) == 15 ? 14 : 16), 1) % 2) == 0 ? '2' : '1';

    }

    public static function getBirthday($idcard)
    {
        return strlen($idcard) == 15 ? ('19' . substr($idcard, 6, 6)) : substr($idcard, 6, 8);
    }

    public static function addIdCard($id_card, $real_name)
    {
        if ($userInfo = Db::name('idcards')->where('id_card', $id_card)->find()) {
            $idid = $userInfo['id'];
        } else {
            $idid = Db::name('idcards')->insertGetId(['id_card' => $id_card, 'real_name' => $real_name]);
        }
        return $idid;
    }

    public static function getUserInfoByIdid($idid)
    {
        $info = Db::name('idcards')
            ->alias('idc')
            ->join('user u', 'idc.id = u.id', 'left')
            ->where('idc.id', $idid)
            ->find();
        return $info;
    }


    public static function getDroleInDossier($did,$uid=-1)
    {
        if (!$did) {
            return 0;


        }

        if ($uid == -1)
            $ididOrJgUserId = User::getLoginUid();
        else
            $ididOrJgUserId = $uid;
        return Db::name("dossier_roles")->where("dossier_id",$did)->where("idid",$ididOrJgUserId)->value("role");



    }

    // 获取当前登录用户在此业务中的角色
    public static function getRoleInDossier($dossier_id, $uid = -1)
    {
        // 1 申请人  2 被申请人  4 仲裁员   8 仲裁委主任 16 仲裁委秘书  32 总管理员 0 未知角色
        if (!$dossier_id) {
            return 0;


        }


        if ($uid == -1)
            $ididOrJgUserId = User::getLoginUid();
        else
            $ididOrJgUserId = $uid;


        if (!$ididOrJgUserId) {

            return 0;

        }

        if (LoginUser::isRole(Constant::Admin_Role_admin)) {
            $role = Constant::QX_ROLE_ADMIN;
            return $role;
        }

        $dossierInfo = Dossier::getSimpleDossier($dossier_id);

        if (!$dossierInfo) {
            return 0;
        }
        // 仲裁委
        if (LoginUser::isZhongCaiWeiZhuBan()) {
            if ($dossierInfo['zc_jg_id'] == LoginUser::getRoleThId()) {
                return Constant::QX_ROLE_ZHONGCAIWEI_MISHU;
            }
        }

        // 仲裁委
        if (LoginUser::isZhongCaiLiAanShenPi()) {
            if ($dossierInfo['zc_jg_id'] == LoginUser::getRoleThId()) {
                return Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN;
            }
        }


        if (LoginUser::isZhongCaiWeiCaiJueShenpi()) {
            if ($dossierInfo['zc_jg_id'] == LoginUser::getRoleThId()) {
                return Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE;
            }
        }
        // 银行
        if (LoginUser::isRole(Constant::Admin_Role_yinhang)) {
            if ($dossierInfo['zc_jg_id'] == LoginUser::getRoleThId()) {
                return Constant::QX_ROLE_SHENQINGREN;
            }
        }


        // 仲裁员
        if (LoginUser::isRole(Constant::Admin_Role_zhongcaiyuan)) {
            if (in_array(LoginUser::getRoleUid(), array_column(Dossier::getZhongcaiyuan($dossier_id), 'zcy_uid'))) {
                return Constant::QX_ROLE_ZHONGCAIYUAN;
            }
        }


        //  $role = self::getRoleInDossierPri($dossier_id, $ididOrJgUserId);

//
//        if ($role) {
//            return $role;
//        }

        // 先查是否是申请人 被申请人
        $role = 0;

        $dossierUserInfo = Db::name('dossier_users')
            ->where("dossier_id", $dossier_id)
            ->where('idid', $ididOrJgUserId)
            ->find();



        if ($dossierUserInfo) {
            if ($dossierUserInfo['role'] == 1) {
                $role = Constant::QX_ROLE_SHENQINGREN;
            } elseif ($dossierUserInfo['role'] == 2) {
                $role = Constant::QX_ROLE_BEISHENQINGREN;
            }
        }
        if ($role) {
            self::setRoleInDossier($dossier_id, $ididOrJgUserId, $role);
            return $role;
        }
        self::setRoleInDossier($dossier_id, $ididOrJgUserId, $role);
        return (int)$role;
    }

    private static function setRoleInDossier($dossier_id, $ididOrJgUserId, $role)
    {
        self::$userRoleInDossier[$dossier_id][$ididOrJgUserId] = $role;
    }

    private static function getRoleInDossierPri($dossier_id, $ididOrJgUserId)
    {
        if (isset(self::$userRoleInDossier[$dossier_id][$ididOrJgUserId])) {
            return self::$userRoleInDossier[$dossier_id][$ididOrJgUserId];
        }
        return 0;
    }
}