<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 14:43
 */

namespace app\admin\controller\doccommon;

class DocCommon {
    private static  $type = [
        0=>'请选择',
        1=>'表单',
        2=>'操作',
        3=>'上传',
    ];
    private static  $subType = [
        1=>[1=>'text',2=>'radio',3=>'checkbox',4=>'select',5=>'textarea',6=>'datetime',7=>'date',8=>'number'],
        2=>[1=>'签字'],
        3=>'-'
    ];
    private static $subTypesArr = [
        0=>'没有可不选',
        1=>'text/签字',
        2=>'radio',
        3=>'checkbox',
        4=>'select',
        5=>'textarea',
        6=>'datetime',
        7=>'date',
        8=>'number',
    ];
    private static $evidence = [];


    public static function getDmaTypes(){
        return self::$type;
    }
    public static function getDmaSubTypes(){
        return self::$subTypesArr;
    }
  public static function dmaTypeName($typeInt){

      $name = '-';
      if(!$typeInt){
          return $name;
      }
     if(isset(self::$type[$typeInt])){
         $name = self::$type[$typeInt];
     }
      return $name;
  }
    public static function dmaSubTypeName($typeInt,$subTypeInt){
        $name = '-';
        if(!$typeInt||!$subTypeInt){
            return $name;
        }
        if(isset(self::$subType[$typeInt][$subTypeInt])){
            $name = self::$subType[$typeInt][$subTypeInt];
        }
        return $name;
    }
}