<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/12
 * Time: 下午9:09
 */

namespace wslibs\wszc\datashow;


use think\Db;

class InfoData
{
    private $info = [];

    public function __construct()
    {
    }

    public function danger($role,$value)
    {
        if(!$value)  return 0;
        return $this->differenceDbName('dossier','id',$role,$value);
    }

    public function dabian($role,$value)
    {
        if(!$value)  return 0;
        return $this->differenceDbName('dossier_defence','dossier_id',$role,$value);
    }

    public function zhizheng($role,$value)
    {
        if(!$value)  return 0;

        return Db::name('dossier_question')->alias('dq')->join('dossier_question_list dql','dq.id=dql.q_id','left')->where("dq.dossier_id",$role['dossier_id'])->count(1);
    }

    public function zcy($role,$value)
    {
        if(!$value)  return 0;
        return $this->differenceDbName('arbitrator','dossier_id',$role,$value);
    }

    public function dialog($role,$value)
    {
        if(!$value)  return 0;

        return $this->differenceDbName('dossier_log','dossier_id',$role,$value);
    }

    public function zuting($role,$value)
    {
        if(!$value)  return 0;
        return 0;
        return Db::name('arbitrator')->where("dossier_id",$role['dossier_id'])->count(1);
    }

    public function operation($role,$value)
    {
        if(!$value)  return 0;

        return Db::name('dr')->where("dossier_id",$role['dossier_id'])->where("create_type",2)->count(1);
    }

    private function differenceDbName($db_name,$where,$role,$value)
    {
        if(!$value)  return 0;

        return Db::name($db_name)->where($where,$role['dossier_id'])->count(1);
    }
}