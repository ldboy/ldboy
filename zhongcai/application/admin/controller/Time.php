<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/11
 * Time: 上午9:30
 * /**
 * 答辩自动过期  http://zcw.wszx.cc/index.php/admin/time/defense
 */


namespace app\admin\controller;


use think\Db;
use wslibs\wszc\dtime\DtimeCheck;

class Time extends \app\common\controller\Backend
{
    public function defense()
    {

        echo DtimeCheck::defense();
        exit;
    }

    public function daoqi()
    {
        $d_id = $this->request->param("id/d");
        $is_phone = $this->request->param("is_phone/d");
        Db::name("dossier")->alias("d")->join("dossier_time t", "t.id=d.id", "left")->where("d.id",$d_id)->where("d.sub_status",30)->update(array("d.sub_status"=>31,"t.time31"=>time()));

        $is_phone ? $this->success("成功") :
        $this->success("成功", "", ['alert' => 1, 'wsreload' => 1]);
    }


}