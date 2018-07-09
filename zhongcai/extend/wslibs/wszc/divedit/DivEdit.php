<?php
namespace wslibs\wszc\divedit;

use think\Db;
use wslibs\wszc\idcard\IDcard;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/26
 * Time: 下午4:11
 */
class DivEdit
{
    public static function add($doc_id, $idid, $name, $divtitle,$qrole, $div_id, $val0, $val1)
    {
        $data = array();
        $data['doc_id'] = $doc_id;
        $data['idid'] = $idid;
        $data['qrole'] = $qrole;
        $data['div_id'] = $div_id;
        $data['val0'] = $val0;
        $data['val1'] = $val1;
        $data['addtime'] = time();
        $data['name'] = $name;
        $data['title']=$divtitle;

        Db::name("dre")->where("doc_id",$doc_id)->where("div_id",$div_id)->update(['enable'=>0]);

        $out =  Db::name("dre")->insertGetId($data);
        return $out;
    }

    public static function getList($docid,$enable=null)
    {
        if ($enable===null)
        return  Db::name("dre")->where("doc_id",$docid)->order("id desc")->select();
        else
            return  Db::name("dre")->where("doc_id",$docid)->where("enable",1)->order("id desc")->select();
    }



}