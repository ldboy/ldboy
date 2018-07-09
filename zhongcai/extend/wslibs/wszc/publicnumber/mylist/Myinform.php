<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/22
 * Time: 下午1:16
 */

namespace wslibs\wszc\publicnumber\mylist;


use think\Db;
use wslibs\wszc\mes\Inform;

class Myinform
{
    public static function getInformList($idid,$type=1)
    {
        $list = Db::name('inform')->alias('i')
            ->join('dossier d','i.ywid=d.id','left')
            ->join('idcards id','id.id=i.idid','left')
            ->join('dossier_time t','d.id=t.id','left')
            ->field('id.real_name,i.*,d.status as d_status,d.zno,d.addtime as d_addtime,d.title,t.time30')
            ->where("i.idid='$idid' and i.type='$type' and i.status=0")
            ->order('i.id desc')
            ->select();

        return (new Inform())->dealList($list);
    }

    public static function getMyInformCount($idid)
    {
        $count =  Db::name('inform')->where("idid",$idid)->where('type',1)->where("status",0)->count();

        return $count ? $count : 0;
    }
}