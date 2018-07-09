<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-08
 * Time: 19:21
 */

namespace wslibs\wszc\defence;
use think\Db;

class DefenceExpand
{

    private static $status = [0 => "未签字" , 1 => "已签字"];

    /**
     * @param $dossier_id
     * @param $uid
     * @param $valueArr
     * @return bool|int|string
     */
    public static function addDefence($dossier_id,$uid,$valueArr){
        if(!$dossier_id || !$uid || !$valueArr)return false;

        $data = [
            'dossier_id' => $dossier_id,
            'idid' => $uid,
            'value' => json_encode($valueArr),
            'addtime' => time(),
            'is_sign' => 0
        ];

        return Db::name("dossier_defence")->insertGetId($data);
    }


    /**
     * @param $valueArr
     * @param $uid
     * @param $dossier_id
     * @return bool|int|string
     */
    public static function editValue($valueArr,$uid,$dossier_id){
        if(!$dossier_id || !$uid || !$valueArr)return false;

        $data = [
            'value' => json_decode($valueArr,true)
        ];

        return Db::name("dossier_defence")->where("dossier_id = '$dossier_id' and idid = '$uid'")->update($data);
    }


    /**
     * @param $status
     * @param $id
     * @return bool|int|string
     */
    public static function changeStatus($status,$id){
        if(!$status || !$id)return false;
        return Db::name("dossier_defence")->where("id = '$id'")->update(['status'=>$status]);
    }


    /**
     * @param $dossier_id
     * @param string $uid
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getRecordList($dossier_id,$uid = ''){
        $map['idid'] = $uid;
        $map['dossier_id'] = $dossier_id;

        if(!$uid){
            unset($map['idid']);
        }

        return Db::name("dossier_defence")->where($map)->select();
    }


    /**
     * @param string $status
     * @return array|mixed
     */
    public static function getDefenceStatus($status = ''){
        if(!$status) return self::$status;

        return self::$status[$status];
    }


    /**
     * @param $d_id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function getDefenceList($d_id){
        $list = Db::name("dossier_defence")
            ->alias("dd")
            ->join("zc_idcards id","dd.idid = id.id")
            ->field("dd.*,id.real_name")
            ->where("dd.dossier_id = '$d_id'")
            ->select();

        foreach ($list as $k => $v){
            $list[$k]['addtime'] = date("Y-m-d H:i:d",$v['addtime']);
        }

        return $list;
    }

    /**
     * @param $defence_id
     * @return array|false|\PDOStatement|string|\think\Model
     */
    public static function getDefenceFind($defence_id){
        $find = Db::name("dossier_defence")
            ->alias("dd")
            ->join("zc_idcards id","dd.idid = id.id")
            ->field("dd.*,id.real_name")
            ->where("dd.id = '$defence_id'")
            ->find();
        
        $find['addtime'] = date("Y-m-d H:i:s",$find['addtime']);

        return $find;
    }


    /**
     * @param $d_id
     * @param bool $manage
     * @return bool|int|mixed|string
     */
    public static function getDefenceNum($d_id){
        if(!$d_id) return false;


        $Num = Db::name("dossier")->field("defence_num,defence_num_dcl")->where("id = '$d_id'")->find();

        if($Num['defence_num'] == 0 || !$Num['defence_num']){
            $Num['defence_num'] = "无";
        }else{
            $Num['defence_num'] =  $Num['defence_num']."位";
        }

        if($Num['defence_num_dcl'] == 0){
            $Num['defence_num_dcl'] = "无";
        }else{
            $Num['defence_num_dcl'] = $Num['defence_num_dcl']."无";
        }


        return $Num;
    }
}