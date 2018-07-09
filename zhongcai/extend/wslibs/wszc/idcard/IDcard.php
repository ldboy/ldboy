<?php


namespace wslibs\wszc\idcard;
use think\Db;

class IDcard
{

    public static function getIdId($idcard, $name = null, $info = false)
    {
        if (!$idcard) return 0;
        $has = \think\Db::name("idcards")->where("id_card", $idcard)->find();
        if ($has) {
            return $info ? $has : $has['id'];
        } else if ($name) {
            $idid = \think\Db::name("idcards")->insertGetId(array("id_card" => $idcard, "real_name" => $name));
            $has =  array("id_card" => $idcard, "real_name" => $name, "id" => $idid);
            return $info ? $has : $has['id'];
        }
        return 0;
    }

    public static function getName($ids)
    {
        $isinit = false;
        if (!is_array($ids))
        {
            $ids = explode(",",$ids);
            $isinit = true;
        }
        $list = Db::name("idcards")->whereIn("id",$ids)->selectOfIndex("id");
        if ($isinit &&  (count($ids)==1 ) )
        {
            return $list[$ids[0]];
        }
        return $list;

    }
}