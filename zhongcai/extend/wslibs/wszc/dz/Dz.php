<?php
namespace wslibs\wszc\dz;

use think\Db;
use wslibs\wszc\User;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/19
 * Time: ÉÏÎç11:35
 */
class Dz
{
    public static function getShenQingDzInfo($did, $idid, $name)
    {
        $find = Db::name("dz")->where("dossier_id", $did)->where("idid", $idid)->where("status", 0)->find();
        if ($find) return $find;
        $data = array("dossier_id" => $did, "idid" => $idid, "status" => 0, "addtime" => time(), "role" => User::getDroleInDossier($did, $idid), "sgid" => 0, "name" => $name, "zids" => "");

        return self::getDzInfo(Db::name("dz")->insertGetId($data));
    }

    public static function getDzInfo($id)
    {
        return Db::name("dz")->find($id);
    }

    public static function subtoZhongCaiWei($id, $sgid,$docids)
    {
        if (is_array($docids)) {
            $docids = implode(",", $docids);
        }
        Db::name("dz")->where("id", $id)->update(array("zids" => $docids, "status" => 2,"sgid"=>$sgid));
        return true;
    }

    public static function getZhengJuList($did)
    {
        return Db::name("dz")->where("dossier_id",$did)->where("status",">=",2)->order("id desc")->select();
    }

    public static function shouLiOrJuJue($id,$ok)
    {
       return  Db::name("dz")->where("id", $id)->update(array(  "status" => $ok?3:4));
    }
}