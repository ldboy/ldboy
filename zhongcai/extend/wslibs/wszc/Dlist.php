<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/12
 * Time: 上午8:30
 */

namespace wslibs\wszc;


use dossier\DossierDoc;
use think\Db;
use think\model\Collection;

class Dlist
{


    private $uid = 0;
    private $role = 0;

    private $start = 0;
    private $len = 30;
    private $where = [];

    public function __construct($idid = 0)
    {

        $this->uid = $idid;
        if ($this->uid) {
            $this->where["r.idid"] =   $this->uid;
        }
    }


    public function addWhere($key, $value)
    {
        $this->where["d.".$key] =   $value;
        return $this;
    }

    public function addWhereAll($key, $value)
    {
        $this->where[$key] =   $value;
        return $this;
    }

    public function setZhongCaiwei($id)
    {
        $this->where["d.zc_jg_id"] =   $id;
        return $this;
    }
    public function setYinhang($id)
    {
        $this->where["d.third_jg_id"] =   $id;
        return $this;
    }


    public function setStatus($status=-1)
    {
        if($status!=-1)
        $this->where["d.status"] =   $status;
        else
            unset( $this->where["d.status"]);
//        $this->where['d.status'] = ['egt',20];
        return $this;
    }


    public function daiCaiJueNum($role)
    {
        $this->addWhereElse($role);

        $list = Db::name("dossier_roles")->alias("r")
            ->join("dossier d", "r.dossier_id=d.id", "left")
            ->join('dossier_caijue dc','d.id=dc.id','left')
            ->where($this->where)
            ->where('r.status',1)
            ->group("d.id")
            ->count();

        return $list;
    }

    public function daiCaiJueList($role)
    {

        $this->addWhereElse($role);

        $list = Db::name("dossier_roles")->alias("r")
            ->join("dossier d", "r.dossier_id=d.id", "left")
            ->join("dossier_time t", "t.id=d.id",'left')
            ->join('dossier_users du','r.dossier_id=du.dossier_id','left')
            ->join('dossier_caijue dc','d.id=dc.id','left')
            ->where($this->where)
            ->where('r.status',1)
            ->group("d.id")
            ->field("d.*,t.*,du.phone,du.id_num,du.name")
            ->order("d.id desc")
            ->select();

        return $this->dealListData($list);
    }

    private function addWhereElse($role)
    {
        if($role==Constant::D_Role_ZhongCaiWei_GuanLiYuan){
            $this->where['dc.c_status'] = ['in',[2]];
        }elseif($role==Constant::D_Role_ZhongCaiWei_LiAnShenPi || $role==Constant::D_Role_ZhongCaiWei_CaiJueShenPi){
            $this->where['dc.c_status'] = 4;
        }elseif($role==Constant::Admin_Role_zhongcaiyuan){
            $this->where['dc.c_status'] = ['in',[1,3]];
        }else{
            $this->where['dc.c_status'] = ['in',[1,2,3,4,5]];
        }
    }

    public function getSubStatusCount($status = null)
    {
        unset($this->where['dc.c_status']);
        if($status){
            unset($this->where['d.sub_status']);
        }
        if($_GET['zhz']==111){
            dump($this->where);
        }
        $sub_status_arr = Db::name("dossier_roles")->alias("r")->join("dossier d", "r.dossier_id=d.id", "left")->where($this->where)->group("d.sub_status")->field("d.sub_status,count(distinct(d.id)) as num")->selectOfIndex("d.sub_status");
        return $sub_status_arr;
    }

    public function getList()
    {
        if($_GET['zhz']==11){
            dump($this->where);
        }
        unset($this->where['dc.c_status']);
        $list = Db::name("dossier_roles")->alias("r")
            ->join("dossier d", "r.dossier_id=d.id", "left")
            ->join("dossier_time t", "t.id=d.id",'left')
            ->join('dossier_users du','r.dossier_id=du.dossier_id')
            ->where($this->where)
            ->where('r.status',1)
            ->group("d.id")
            ->field("d.*,t.*,du.phone,du.id_num,du.name")
            ->order("d.id desc")
            ->select();


        $total = Db::name("dossier_roles")->alias("r")
            ->join("dossier d", "r.dossier_id=d.id", "left")
            ->join("dossier_time t", "t.id=d.id",'left')
            ->join('dossier_users du','r.dossier_id=du.dossier_id')
            ->where($this->where)->group("d.id")
            ->count();

        return array($total, $this->dealListData($list));
    }

    public function dealListData($list)
    {
        $dids = array_column($list, "id");


        $dangshirens = Db::name("dossier_users")->whereIn("dossier_id", $dids)->group("dossier_id,role")->field("dossier_id,role,group_concat(name) as title,count(*) as num")->select();
        $renyuan = array();

        foreach ($dangshirens as $dangshiren) {
            $renyuan[$dangshiren['dossier_id']][$dangshiren['role']] = $dangshiren;
        }

        return array_map(function ($value) use ($renyuan) {

            $dangshiren = $renyuan[$value['id']];

            $value['sq_time'] = $this->time($value['addtime']);
            $value['sl_time'] = $this->time($value['time30']);

            $value['sq_string'] = $dangshiren[Constant::D_Role_ShenQingRen]['title'] . ($dangshiren[Constant::D_Role_ShenQingRen]['num'] > 1 ?  (",共" . $dangshiren[Constant::D_Role_ShenQingRen]['num'] . "人" ): "");
            $value['bsq_string'] = $dangshiren[Constant::D_Role_Bei_ShenQingRen]['title'] . ($dangshiren[Constant::D_Role_Bei_ShenQingRen]['num'] > 1 ? (",共" . $dangshiren[Constant::D_Role_Bei_ShenQingRen]['num'] . "人") : "");
            $value['status_string'] = Dossier::getStatus($value['status']);

            if (! $value['status_string'])
            {
                $value['status_string']  = "-";
            }

            $value['sub_status_string'] = Dossier::getSubStatus($value['sub_status']);
   
            if (! $value['sub_status_string'])
            {
                $value['sub_status_string']  = "-";
            }


            $value['status_string_color'] ="bg-".Dossier::getStatusColor($value['status']);

            $value['zcw_name'] = '暂无';

            if (! $value['zcw_name']){
                $value['zcw_name']  = "-";
            }
            $value['zno'] = $value['zno'] ? DossierDoc::getZcNoByNo($value['zno'],$value['addtime']) : '---';
            return $value;
        }, $list);
    }

    private function time($time)
    {
        return $time ? date("Y-m-d H:i", $time) : "";
    }



    public function DealStatus($typeList,$sub_key_num,$role)
    {

        if(!$typeList){
            return $typeList;
        }

        $colors = array("blue", "red", "yellow","green");

        foreach ($typeList as $key => $value) {
            if($sub_key_num[$key]==0){
                $sub_key_num[$key] = '';
            }
            $typeList[$key] = array("key" => $key, "name" => $value, "num" => $sub_key_num[$key], "bage_color" => $colors[array_rand($colors)]);

        }

        return $typeList;
    }


    public function dealSubList($role,$sublist)
    {
        $role_status = Dossier::getStatusForRole($role);

        foreach($sublist as $key=>$value){
            if(!$role_status[$value['key']]){
                unset($sublist[$key]);
            }
        }
        return $sublist;
    }



}