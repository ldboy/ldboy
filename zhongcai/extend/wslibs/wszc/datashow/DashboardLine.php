<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/16
 * Time: 下午3:21
 */

namespace wslibs\wszc\datashow;
ini_set('date.timezone','Asia/Shanghai');

use fast\Date;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\LoginUser;

class DashboardLine
{
    private $map = [];

    private $role = 0;

    public function __construct()
    {
        $this->role = LoginUser::getRole();
    }

    public function getSevenCount()
    {
        list($begin,$end) = $this->getSevenTime();
        $list = 0;

        if($this->role == Constant::Admin_Role_zhongcaiwei){
            $this->addWhere('d.addtime',['between',["$begin","$end"]]);
            $map['d.status'] = ['egt',2];
            $list = $this->getBoLangTuData();
        }

        if(!$list)
            return [0,0];
        else
            return $this->array_column($list);
    }


    private function getBoLangTuData()
    {
        $jg_id = Db::name('jigou_admin')->where("idid",LoginUser::getIdid())->find();
        if($jg_id){
            $this->addWhere('d.zc_jg_id',$jg_id['th_id']);
        }
        Db::query("SET time_zone = '+8:00'");
        $count = Db::name('dossier')->alias('d')->join('dossier_time t','d.id=t.id','left')->where($this->map)->field("FROM_UNIXTIME(d.addtime, '%Y-%m-%d') as add_time,if(t.time20>0,COUNT(*),0) AS count_sq, if(t.time30 >0,count(*),0) as count_sl")->group("add_time")->select();

        return $count;
    }


    private function array_column($list)
    {
        return [array_column($list,'count_sq','add_time'),array_column($list,'count_sl','add_time')];
    }

    public function getSevenTime()
    {
        return [Date::unixtime('day', -6), time()];
    }

    public function addWhere($key,$value)
    {
        if($key)
            $this->map[$key] = $value;
        else
            unset($this->map[$key]);

        return $this;
    }
}