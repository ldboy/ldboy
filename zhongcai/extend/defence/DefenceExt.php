<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-05
 * Time: 9:06
 */
namespace defence;
use think\Db;
use wslibs\wszc\Dossier;

class DefenceExt
{
    const DA_BIAN = 1;
    const ZHI_ZHENG = 2;
    const HUI_BI = 3;
    const PI_LOU = 4;
    const SHOU_LI = 5;
    public static $type = [1=>"答辩",2=>"质证",3=>"回避",4=>"披露",5=>"受理"];

    public static function getType($type)
    {
        return self::$type[$type];
    }

    public static function addDefence($dossier_id,$type,$operator,$doc_type,$doc_name,$word_url){
        if(!$dossier_id || !$type || !$operator){
            return false;
        }

        $data = [
            "dossier_id" => $dossier_id,
            "type" => $type,
            "status" => 0,
            "addtime" => time(),
            "operator" => $operator,
            "doc_type" => $doc_type,
            "value" => "",
            "doc_name" => $doc_name,
            "word_url" => $word_url
        ];

        return $re = Db::name("dossier_operation")->insertGetId($data);
    }

    public static function getDefence($dossier_id,$doc_type){
        return Db::name("dossier_operation")->where("dossier_id = '$dossier_id' and doc_type = '$doc_type'")->find();
    }
    public static function delDefence($dossier_id,$doc_type){
        return Db::name("dossier_operation")->where("dossier_id = '$dossier_id' and doc_type = '$doc_type'")->delete();
    }
    public static function changeDocStatus($doc_type,$dossier_id,$status){
        return Db::name("dossier_operation")->where("doc_type = '$doc_type' and dossier_id = '$dossier_id'")->update(array('status'=>$status));
    }

    public static function getValue($dossier_id,$doc_type){
        $value = self::getDefence($dossier_id,$doc_type)['value'];

        $data = unserialize($value);

        return $data;
    }

    public static function submitTable($dossier_id,$doc_type,$valueArr){
        if(!$dossier_id ||!$doc_type ||!$valueArr){
            return false;
        }

        self::changeDocStatus($doc_type,$dossier_id,1);
        Dossier::addLog($dossier_id,session("zc_admin_uid"),"name",2,"修改了文档");

        $data = [
            "value" => serialize($valueArr)
        ];

        return Db::name("dossier_operation")->where("dossier_id = '$dossier_id' and doc_type = '$doc_type'")->update($data);
    }
}