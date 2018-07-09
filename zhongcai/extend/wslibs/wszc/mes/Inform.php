<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/11
 * Time: 上午10:25
 */

namespace wslibs\wszc\mes;


use dossier\DossierDoc;
use think\Db;
use wslibs\wszc\LoginUser;

class Inform
{
    public  $arr = [10=>'所有',0=>'未查看',1=>'已查看'];//,2=>'未完成',3=>'已完成'

    public  $where = [];

    public function __construct($type=1)
    {
        $this->where['i.idid'] = LoginUser::getIdid();
        $this->where['i.type'] = 1;
    }

    public  function getStatus()
    {
        return $this->arr;
    }

    public function addHere($status)
    {
        switch($status){

            case 0:
                $this->where['i.status'] = 0;
                break;
            case 1:
                $this->where['i.status'] = 1;
                break;
            case 2:
                $this->where['i.is_finish'] = 0;
                break;
            case 3:
                $this->where['i.is_finish'] = 1;
                break;
            default:
                unset ($this->where['i.status']);
                unset ($this->where['i.is_finish']);
                break;
        }

        return $this;
    }

    public  function addStatus($status)
    {
        $this->where['i.status'] = $status;
        return $this;
    }

    public function getCount()
    {
        return Db::name('inform')->alias('i')->where($this->where)->count();
    }

    public function getList($start,$limit)
    {
        if($_GET['zhz']==1){
            echo 'esds';
            exit;
        }
        $list = Db::name('inform')->alias('i')->join('dossier d','i.ywid=d.id','left')
            ->join('idcards id','id.id=i.idid','left')->join('dossier_time t','d.id=t.id','left')
            ->field('id.real_name,i.*,d.status as d_status,d.zno,d.addtime as d_addtime,d.title,t.time30')
            ->where($this->where)->order('i.id desc')
            ->limit($start,$limit)
            ->select();

        return $this->dealList($list);
    }

    public function dealList($list)
    {
        $status = [0=>'未查看',1=>'已查看'];
        $is_finish = [0=>'未完成',1=>'已完成'];
        $type = [1=>'操作日志',2=>'网站新闻'];

        $role = [11=>'当事人',12=>'仲裁员',10=>'主办',13=>'银行',1=>'admin'];
        return array_map(function($value) use($status,$is_finish,$type,$role){

            $value['zno_title'] = DossierDoc::getInfoTitle($value['zno'],$value['time30']);

            $value['zno'] = $value['zno'] ? DossierDoc::getZcNoByNo($value['zno'],$value['addtime']) : '---';

            if($_GET['zhz']==1){
                dump($value['cz_role']);
            }

            $value['cz_role'] = $role[$value['cz_role']];
            $value['addtime_'] = $value['addtime'];
            $value['addtime'] = date('Y-m-d H:i:s',$value['addtime_']);
            $value['addtime_kzt'] = date('Y年m月d日',$value['addtime_']);

            $value['status_'] = $status[$value['status']];
            $value['is_finish'] = $is_finish[$value['status']];
            $value['type_'] = $type[$value['type']];
            return $value;
        },$list);
    }

    public function getStatusCount()
    {
        $sub_status_arr = Db::name("inform")->alias('i')->where($this->where)->field("count(*) as num,sum(if(i.is_finish=0,'1',0)) as wei_is_finish,sum(if(i.is_finish=1,'1',0)) as yi_is_finish,sum(if(i.status=0,'1',0)) as wei_status,sum(if(i.status=1,'1',0)) as yi_status")->select();

        $sub_status = $sub_status_arr[0];
        $status_arr[0] =  $sub_status['wei_status']?$sub_status['wei_status']:0;
        $status_arr[1] = $sub_status['yi_status']?$sub_status['yi_status']:0;
        $status_arr[2] = $sub_status['wei_is_finish']?$sub_status['wei_is_finish']:0;
        $status_arr[3] = $sub_status['yi_is_finish']?$sub_status['yi_is_finish']:0;
        $status_arr[10] = $sub_status['num']?$sub_status['num']:0;

        return $status_arr;
    }


    public function DealStatus($typeList,$sub_key_num)
    {

        if(!$typeList){
            return $typeList;
        }
        $typeList_ = [];
        $colors = array("blue", "red", "yellow","green");

        foreach ($typeList as $key => $value) {

            $typeList_[$key] = array("key" => $key, "name" => $value, "num" => $sub_key_num[$key], "bage_color" => $colors[array_rand($colors)]);

        }

        return $typeList_;
    }
}