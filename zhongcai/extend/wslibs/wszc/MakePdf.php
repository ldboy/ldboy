<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: ÏÂÎç2:00
 */

namespace wslibs\wszc;


use think\Db;
use think\db\Query;
use wslibs\wscontract\ContractTools;
use wslibs\wscontract\WsContract;


class MakePdf
{
    public static function index($docid)
    {


        $_uid = User::getLoginUid();

        $query = new Query();
        $query->name('dr')
            ->alias('dr')
            ->join('zc_dossier_users u','dr.dossier_id = u.dossier_id')
            ->where('dr.id',$docid)
            ->where('u.idid',$_uid)
            ->field('dr.to_sign,dr.has_sign,dr.c_no,dr.c_class,u.*,dr.dossier_id');

        $list = Db::select($query);

        $com_id = 0;
        foreach ($list as $k=>$v){
            if($v['type']==2){
                $com_id = $v['id'];
            }
        }

        foreach ($list as $k=>$v){

            WsContract::createContractOnLoction($v['c_class'], $v['c_no']);
            $s_uid = ($v['type']-2==0 ? $v['id'] : $v['idid']);
            $s_id_code = ($v['type']-2==0 ? ContractTools::getCompanyIdCode(1) : ContractTools::getUserIdCode(1,$com_id) );

            //ContractTools::getQianZiUcode($s_uid,($v['type']-2==0?true:false),$s_id_code);Ç©×Ö
            WsContract::addSigner($v['c_no'], $s_uid,  ($v['type']-2==0?0:1),$s_id_code,$v['dossier_id']);

            WsContract::submitToService($v['c_no']);
        }


        return true;
    }


}