<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/8
 * Time: 下午2:29
 */

namespace userinfo;
use think\Db;

class DossierUser
{
    private static $_instance = null;

    private static $_dossier_id = 0;

    private function __construct($dossier)
    {
        if(!$dossier) return false;

        self::$_dossier_id = $dossier;

    }

    public static function getInstance($dossier)
    {

        if(self::$_instance==null){
            self::$_instance = new DossierUser($dossier);
        }

        return self::$_instance;
    }

    public function getUserInfo($role=1)
    {
        $map = [];
        $map['d.id'] = self::$_dossier_id;
        $map['du.role'] = $role;

        $info = Db::name('dossier')
            ->alias('d')
            ->join(' dossier_users du ','d.id=du.dossier_id')
            ->join('user u ','du.idid=u.idid')
            ->join('user_info ui ','u.id=ui.uid')
            ->where($map)
            ->select();

        $info = array_map(function($value){
            $value['birthday'] = date('Y年m月d日',$value['birthday']);
            if($value['sex']==1){
                $value['sex'] = '男';
            }elseif($value['sex']==2){
                $value['sex'] = '女';
            }else{
                $value['sex'] = '保密';
            }
            if($value['is_valid']==1){
                $value['sex'] = '有效';
            }else{
                $value['sex'] = '失效';
            }
            return  $value;
        },$info);
        return $info;
    }

}