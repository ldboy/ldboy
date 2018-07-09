<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/2/25
 * Time: 上午9:38
 */
namespace wslibs\wscontract;
use think\Db;
use wslibs\wscontract\contract\ContractCommon;
use wslibs\wscontract\contract\RepaymentContract;
use wslibs\wscontract\contract\WeiTuoShuContract;
use wslibs\wscontract\contract\GudongjueyiContract;
use wslibs\wscontract\signservice\SignServiceCommon;
use wslibs\wscontract\signservice\ZhongQianService;

class ContractTools {
    public static function getContractClass($name)
    {
        $class = "wslibs\wscontract\contract\\".$name."Contract";

        if (class_exists($class))
        {

            return new $class();
        }else{
            echo "$class not find";
            return false;
        }
    }
    public static function getContractClassByExistNo($c_no)
    {

        if($info = self::getInfo($c_no))
        {

            $class =  self::getContractClass($info['class_name']);
 
           return $class->setInfo($info);
        }else{
            return false;
        }
    }
    public static function getInfo($c_no)
    {

        return Db::name("ws_ht")->where("c_no",$c_no)->find();
    }


    public static function getServiceClass($qs_pingtai)
    {
        $config = self::getServiceConfig($qs_pingtai);
        $name = $config['class_name'];
 
        $class = "wslibs\wscontract\signservice\\".$name."Service";

        if (class_exists($class))
        {

            return new $class();
        }else{
            echo "$class not find";
            return false;
        }
    }
    public static function getServiceConfig($qs_pingtai)
    {
        $config = array();
        $config[2]['class_name'] = "ZhongQian";
        return $config[$qs_pingtai];
    }



    public static function getQianZiUcode($id, $is_compay = false, $id_code = "0")
    {
        return "zhzc_" . ($is_compay ? "com" . $id : $id) . "_" . $id_code;
    }

    public static function getUserIdCode($groupid, $companyid = 0)
    {
        return "group_" . $groupid . "_c" . $companyid;
    }

    public static function getCompanyIdCode($groupid)
    {
        return "cg_" . $groupid;
    }
}