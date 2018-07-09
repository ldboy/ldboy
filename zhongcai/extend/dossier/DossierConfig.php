<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-02
 * Time: 14:42
 */
namespace dossier\dossierconfig;
class DossierConfig
{
    public static function getDossierType($id=0)
    {
        $types = array(
            1=>'损害赔偿纠纷',
            2=>'权属纠纷',
            3=>'侵权纠纷',
        );

        if($id){
            return ($types[$id] ? $types[$id] : $types );
        }else{
            return $types;
        }
    }

    public static function getMsg($type,$name)
    {
        return array(
            'type'=>$type,
            'typeName'=>$name
        );
    }

    public static function getDossierStatus($id=0)
    {
        $status = array(
            1=>'刚建立',
            5=>'已受理',
            6=>'已驳回',
            10=>'已完成',
            0=>'已取消',
        );
        if($id){
            return ($status[$id] ? $status[$id] : $status );
        }else{
            return $status;
        }
    }
}