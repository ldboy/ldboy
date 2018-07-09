<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-02
 * Time: 14:32
 */
namespace dossier\filemanager;

use think\db;
class FileManager
{
    public static function getThirdClientInfo($id)
    {
        return Db::name("third_client")->where("id = '$id'")->find();
    }

    public static function getZhongCaiInfo($id)
    {
        return Db::name('zc_jg')->where("id = ".$id)->find();
    }
}