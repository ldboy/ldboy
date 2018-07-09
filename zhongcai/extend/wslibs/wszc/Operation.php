<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/3
 * Time: 19:00
 */

namespace wslibs\wszc;


use think\Db;

class Operation {

    public static function addOneRecord($uid,$type){
       $data = [];
        $data['type'] = $type;
        $data['addtime'] = time();
        $data['status'] = 0;
        $data['operator'] = $uid;
        return Db::name('operation')->insertGetId($data);
    }

    public static function getRecordId($uid,$type){
        if(($has = Db::name('operation')
            ->where('operator',$uid)
            ->where('type',$type)
            ->where('status',0)
            ->find())
        ){
            Db::name('operation')->where('id',$has['id'])->update(['addtime'=>time()]);
            return $has['id'];
        }
        return self::addOneRecord($uid,$type);
    }


}