<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/26
 * Time: 下午6:16
 */

namespace wslibs\wszc\datashow;


use think\Db;

class PersonInfo
{
    public static function getPersonList()
    {

        $person = Db::name('idcards')->alias('id')->join('dossier_users du','id.id=du.idid','left')->field('count(distinct(dossier_id)) as num,idid,id.*')->group('du.idid')->select();//->group('du.idid')

        return $person;

    }
}