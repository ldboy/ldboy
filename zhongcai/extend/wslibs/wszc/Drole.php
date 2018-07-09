<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/12
 * Time: ����11:21
 */

namespace wslibs\wszc;


use think\Db;

class Drole
{
    public static function addRole($did, $idid,$name, $role, $role_uid , $role_pid = 0)
    {
        $where = array("dossier_id" => $did, "idid" => $idid, "role" => $role);
        if (!$has = Db::name("dossier_roles")->where($where)->find()) {
            $data = $where;
            $data['addtime'] = time();
            $data['role_uid'] = $role_uid;
            $data['role_pid'] = $role_pid;
            $data['status'] = 1;
            $data['name'] = $name;
            return Db::name("dossier_roles")->insertGetId($data);

        } else {
            return Db::name("dossier_roles")->where("id = ".$has['id'])->update(array("status" => 1));
        }
    }

    public static function delRole($did, $idid, $role = 0)
    {
        $where = array("dossier_id" => $did, "idid" => $idid);
        if ($role) {
            $where['role'] = $role;
        }
        return Db::name("dossier_roles")->where($where)->update(array("status" => 0));

    }

    // 根据角色删除
    public static function delRoleByRole($did,$role){
        $where = [
            'dossier_id'=>$did,
            'role'=>$role,
        ];
        return Db::name('dossier_roles')->where($where)->update(['status'=>0]);
    }

    public static function getRoleByIdId($idid)
    {
        $where['idid'] = $idid;
        return Db::name("dossier_roles")->where($where)->find();
    }


    public static function getRoleByDid($did)
    {
        $where['dossier_id'] = $did;
        return Db::name("dossier_roles")->where($where)->find();
    }
    public static function addRoleFromLoginUser($did,$d_role){
        return self::addRole($did,LoginUser::getIdid(),LoginUser::getUserName(),$d_role,LoginUser::getRoleUid(),LoginUser::getRoleThId());
    }
    public static function getUsersByRole($did,$role){
        if(!is_array($role)){
            $role = [$role];
        }
        return Db::name('dossier_roles')
            ->where('dossier_id',$did)
            ->where('status',1)
            ->whereIn('role',$role)
            ->select();
    }

}