<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-12
 * Time: 14:01
 */

namespace wslibs\wszc;

use think\Db;
use wslibs\wszc\LoginUser;


class HuiBi
{

    const TYPE_PILU = 1; //披露
    const TYPE_HUIBI = 2; //回避

    public static function getHbRole($role)
    {
        $roleConfig = [
            1 => "申请人",
            2 => "被申请人"
        ];

        return $roleConfig[$role];
    }


    public static function addPilu($d_id, $value)
    {
        return self::addHuibi($d_id, $value, self::TYPE_PILU);
    }


    public static function addSqHuibi($d_id, $value)
    {
        return self::addHuibi($d_id, $value, self::TYPE_HUIBI);
    }

    public static function getPiluVal($d_id){
        return Db::name('huibi')
            ->where('dossier_id',$d_id)
            ->where('type',1)
            ->value('value');
    }


    public static function HuiBiList($d_id, $type = '',$matter_id=0)
    {
        if (!$d_id) return false;

        $map = [
            "hb.dossier_id" => $d_id,
        ];
        if($type){
            $map['hb.type'] = $type;
        }
        if($matter_id){
            $map = [];
            $map['hb.id'] = $matter_id;
        }

        $list = Db::name("huibi")
            ->alias("hb")
            ->join("idcards id", "hb.idid = id.id", "LEFT")
            ->where($map)
            ->field("hb.*,id.real_name,id.id as idid")
            ->where("status", ">=", 2)
            ->order('hb.type asc')
            ->select();
        $bsqr = Dossier::getDangShiRen($d_id,Constant::D_Role_Bei_ShenQingRen);
        $bsqr = array_column($bsqr,'r_no','idid');



        /*echo Db::name("huibi")->getLastSql();
        dump($list);*/


        foreach ($list as $k => $v) {
            $list[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
            if ($v['type'] == self::TYPE_PILU) {
                $list[$k]['typeStr'] = "披露";
                $list[$k]['gid'] = Constant::FILE_GROUP_pilu;

            }else if ($v['type'] == 3) {
                $list[$k]['typeStr'] = "声明";
                $list[$k]['gid'] = Constant::FILE_GROUP_shengming;
            } else {

                $list[$k]['typeStr'] = "<span class='label label-warning'>回避申请</span>";
                $list[$k]['gid'] = Constant::FILE_GROUP_huibi;
            }

            $role = Constant::getRoleName($v['role']);

            if($v['type']==2&&$v['role']==2){
                $role = '第'.DInfoValue::num2Upper($bsqr[$v['idid']]).$role;
            }
            $list[$k]['role'] = $role;

            $statusArr = [
                0=>'未处理',
                1=>'-',
                2=>'未处理',
                3=>'已受理',
                4=>'已拒绝',
                5=>'待主任审批',
            ];

            $list[$k]['statusStr'] = $statusArr[$v['status']];
//            if($v['type']==2){
//                $list[$k]['statusStr'] = '无需操作';
//            }else{
//
//            }

        }

        if (input("sy") == 66) {
            dump($list);
        }

        if($matter_id){
            return $list[0];
        }
        return $list;
    }


    public static function addHuibi($d_id, $value, $type)
    {
        if (!$d_id || !$value || !$type) return false;

        $idid = LoginUser::getIdid();

        if (!$has = Db::name("huibi")->where("dossier_id = '$d_id' and idid = '$idid' and type = '$type'")->find()) {
            $addDate = [
                "dossier_id" => $d_id,
                "idid" => $idid,
                "value" => $value,
                "addtime" => time(),
                "type" => $type,
                "role" => LoginUser::getRole()
            ];

            Dossier::changeStatus($d_id, Constant::DOSSIER_STATUS_PILUHUIBI);

            return Db::name("huibi")->insertGetId($addDate);

        } else {
            $editData = ["value" => $value, "addtime" => time()];
            return Db::name("huibi")->where("dossier_id = '$d_id' and idid = '$idid' and type = '$type'")->update($editData);
        }
    }


    public static function getOne($matter_id,$fromList=false)
    {
        if($fromList){
            return self::HuiBiList(1,0,$matter_id);
        }
        $info = Db::name('huibi')->where('id', $matter_id)->find();
        $info['addtime'] = date('Y-m-d H:i:s', $info['addtime']);
        $typeStr = [1 => '披露', 2 => '回避申请',3=>'声明'];
        $info['typeStr'] = $typeStr[$info['type']];
        $gidArr = [1 => Constant::FILE_GROUP_pilu, 2 => Constant::FILE_GROUP_huibi];
        $info['gid'] = $gidArr[$info['type']];
        $statusArr = [0 => '未处理', 1 => '已同意', 2 => '已拒绝'];
        $info['statusStr'] = $statusArr[$info['status']];
        return $info;
    }


    public static function update($id, $data)
    {
        return Db::name('huibi')->where('id', $id)->update($data);
    }

    private static $list = [];

    public static function addToList($did, $idid, $type, $role, $court_id)
    {
        self::$list[] = [
            'dossier_id' => $did,
            'idid' => $idid,
            'addtime' => time(),
            'value' => '',
            'type' => $type,
            'status' => 0,
            'role' => $role,
            'court_id' => $court_id
        ];
    }

    public static function submit()
    {
        return Db::name('huibi')->insertAll(self::$list);
    }

    // 批量生成 披露 回避 机会
    public static function createPiluHuiBi($did, $courtId)
    {
        // 找到仲裁员 生成披露
        $zcy = Drole::getUsersByRole($did, Constant::D_Role_ZhongCaiYuan);
        foreach ($zcy as $k => $v) {
            self::addToList($did, $v['idid'], self::TYPE_PILU, Constant::D_Role_ZhongCaiYuan, $courtId);
        }
        foreach ($zcy as $k => $v) {
            self::addToList($did, $v['idid'], 3, Constant::D_Role_ZhongCaiYuan, $courtId);
        }

        // 找到 申请人  生成回避
        $sqr = Drole::getUsersByRole($did, Constant::D_Role_ShenQingRen);
        foreach ($sqr as $k => $v) {
            self::addToList($did, $v['idid'], self::TYPE_HUIBI, Constant::D_Role_ShenQingRen, $courtId);
        }

        //找到 被申请人 生成回避
        $bsqr = Drole::getUsersByRole($did, Constant::D_Role_Bei_ShenQingRen);
        foreach ($bsqr as $k => $v) {
            self::addToList($did, $v['idid'], self::TYPE_HUIBI, Constant::D_Role_Bei_ShenQingRen, $courtId);
        }

        return self::submit();
    }

    public static function getCanHuibi($did, $idid)
    {
        if(!is_array($idid)){
            $idid = [$idid];
        }
        $find = Db::name("huibi")
            ->where("dossier_id = '$did'  and status < 2 and type <>3 and is_valid=1")
            ->whereIn('idid',$idid)
            ->find();
        return $find;
    }


    public static function IsPiLu($d_id , $idid){
        return Db::name("huibi")->where("dossier_id",$d_id)->whereIn("idid",$idid)->where("status",2)->find();
    }

    public static function getCanShenMing($did, $idid)
    {
        $find = Db::name("huibi")->where("dossier_id = '$did' and idid = " . $idid . " and status < 2 and type=3 and is_valid=1")->find();
        return $find;
    }


    public static function getHuiBiInfo($id)
    {
        return Db::name("huibi")->find($id);
    }

    public static function subtoZhongCaiWei($id, $sgid)
    {

        $info = self::getHuiBiInfo($id);
        $type = 0;
        if($info['type']==3){
            $type = 1;
        }elseif($info['type']==1){
            $type = 3;
        }
        if($type){
            Db::name("huibi")
                ->where("idid",$info['idid'])
                ->where('dossier_id',$info['dossier_id'])
                ->where('type',$type)
                ->update(['is_valid'=>0]);
        }

        return Db::name("huibi")->where("id", $id)->update(array("sgid" => $sgid, "status" => 2));
    }

    public static function subtoForm($id, $postData)
    {

        return Db::name("huibi")->where("id", $id)->update(array("value" => $postData['hb_value'], "status" => 1));
    }

    //受理or拒绝
    public static function shouLiOrJuJue($id, $shouli = 1,$doc_id=0)
    {
        $status = $shouli ? 3 : 0;
        $intShouLi = (int)$shouli;
        if($intShouLi>1){
            $status = $intShouLi;
        }
        if(!$status){
            Ddocs::reSign($doc_id);
        }
        return Db::name("huibi")->where("id", $id)->update(array("status" => $status));
    }

    public static function getHasSubmitByType($d_id,$type=[]){

        return Db::name('huibi')
            ->where('dossier_id',$d_id)
            ->where('status','>=',2)
            ->where('is_valid',1)
            ->whereIn('type',$type)
            ->find();
    }

}