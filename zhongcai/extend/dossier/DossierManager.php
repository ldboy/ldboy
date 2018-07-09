<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-02
 * Time: 14:46
 */

namespace dossier;

use think\Db;
class DossierManager
{
    public static function addDossier($type,$zc_jg_id,$zc_jg_username,$third_jg_id)
    {
        $data = array();

        $data['type'] = $type;
        $data['zc_jg_id'] = $zc_jg_id;
        $data['zc_jg_username'] = $zc_jg_username;
        $data['third_jg_id'] = $third_jg_id;
        $data['status'] = 1;
        $data['is_pay'] = 0;
        $data['addtime'] = time();

        return Db::name('dossier')->insertGetId($data);

    }

    public static function getSimpleDossier($id)
    {
        return Db::name('dossier')->where("id = '$id' ")->find();
    }

    public static function saveTime($dossier_id,$stats)
    {

        if(!Db::name('dossier_times')->where("dossier_id = ".$dossier_id)->find()){
            Db::name('dossier_times')->insertGetId(['dossier_id'=>$dossier_id,'time1'=>time()]);
        }else{
            Db::name('dossier_times')->where("dossier_id = ".$dossier_id)->update(['status'.$stats=>time()]);
        }
    }


    public static function editDossierValue($dossier_id,$type,$imgs,$user_group,$doc_type)
    {
        $list = array();
        foreach($imgs as $value){
            $list[] =  array("img" => YwSign::getHttpPre() . $value, "src" => $value, "id" => md5($value));
        }

        $data = array();
        $data['dossier_id'] = $dossier_id;
        $data['type'] = $type;
        $data['user_group'] = $user_group;
        $data['doc_type'] = $doc_type;
        $data['val'] = serialize($list);

        return Db::name('dossier_document')->insertGetId($data);
    }


    public static function changeDossierStatus($dossier_id,$status)
    {

        if(Db::name('dossier')->where("id = $dossier_id and status = $status ")->find()){
            return true;
        }else
            return Db::name('dossier')->where("id = $dossier_id")->update(['status'=>$status]);
    }
}