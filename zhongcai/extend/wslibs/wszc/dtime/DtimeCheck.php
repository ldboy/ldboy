<?php
namespace wslibs\wszc\dtime;
use think\Db;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/11
 * Time: 下午3:36
 */
class DtimeCheck
{
    public static function defense()
    {
        return Db::name("dossier")->alias("d")->join("dossier_time t", "t.id=d.id", "left")->where("d.sub_status",30)->where("t.time30","<",time()-\wslibs\wszc\Constant::Time_dabian)->update(array("d.sub_status"=>31,"t.time31"=>time()));
    }
}