<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/22
 * Time: 上午10:44
 */
namespace wslibs\wszc\publicnumber\mylist;

use dossier\DossierDoc;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\DInfoValue;
use wslibs\wszc\Dossier;

class Mydossier
{
    private static $role = [
        1 => '申请人',
        2 => '被申请人',
        3 => '申请人代理',
        4 => '被申请人代理',
        10 => '仲裁委',
        0 => '仲裁员'
    ];
    public static function getMyDossierCount($idid)
    {
        $count =  Db::name('dossier_roles')->where("idid",$idid)->where("status",1)->count();

        return $count ? $count : 0;
    }

    public static function getMyDossierList($idid)
    {
        $dossier_ids = array_column((array)Db::name('dossier_roles')->where("idid",$idid)->where("status",1)->select(),'dossier_id');

        $list = Db::name('dossier')->alias('d')->join('dossier_time dt','d.id=dt.id','left')->whereIn('d.id',$dossier_ids)->order('d.id desc')->field('dt.time30,d.*')->select();

        return self::dealList($list,$dossier_ids,$idid);
    }

    public static function dealList($list,$dossier_ids,$idid)
    {
        $dangshirens = Db::name("dossier_users")->whereIn("dossier_id", $dossier_ids)->group("dossier_id,role")->field("dossier_id,role,group_concat(name order by r_no asc) as title,count(*) as num")->select();
        $renyuan = array();

        foreach ($dangshirens as $dangshiren) {
            $renyuan[$dangshiren['dossier_id']][$dangshiren['role']] = $dangshiren;
        }

        $statusArr = [1=>'申请中',2=>'待受理',3=>'已受理',4=>'已完成',0=>'已取消'];
        return array_map(function($value) use($statusArr,$renyuan,$idid){

            $dangshiren = $renyuan[$value['id']];

            $value['apply_peo'] = $dangshiren[Constant::D_Role_ShenQingRen]['title'];
            $value['bei_apply_peo'] = $dangshiren[Constant::D_Role_Bei_ShenQingRen]['title'];

            $value['zno_str'] = $value['zno'] ? DossierDoc::getZcNoByNo($value['zno'],$value['time30']) : '待立案';
            $value['addtime'] = date('Y-m-d H:i:s',$value['addtime']);
            $value['time30'] = date('Y-m-d H:i:s',$value['time30']);
            $value['status'] = $statusArr[$value['status']];
            $value['status_string'] = Dossier::getStatus($value['status'],$value['sub_status']);
            $value['zcjigou'] = '石家庄仲裁委员会';
            $value['info_link'] = WEB_SITE_ROOT.'admin/wechat/Mylist/detail?did='.$value['id'].'&idid='.$idid;

            return $value;
        },$list);
    }


    public static function getDangShiRen($d_id)
    {
        $dangshiren = Dossier::getDangShiRen($d_id, Constant::getDangshirenRoles());
        foreach ($dangshiren as $k => $v){
            if($v['role'] == 3){
                unset($dangshiren[$k]);
            }
        }

        foreach ($dangshiren as $k => $v) {
            if($v['role']==2){
                if($v['r_no']){
                    $dangshiren[$k]['role'] = '第'.DInfoValue::num2Upper($v['r_no']).self::$role[$v['role']];
                }else{
                    $dangshiren[$k]['role'] = self::$role[$v['role']];
                }
            }else{
                $dangshiren[$k]['role'] = self::$role[$v['role']];
            }
        }

        return $dangshiren;
    }
}