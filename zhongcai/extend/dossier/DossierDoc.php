<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-04
 * Time: 15:59
 */
namespace dossier;

use think\Db;

class DossierDoc
{

    public static function create_zc_no($dossier_id=0,$is_str=false)
    {

        $zno = self::getMaxZcNo($dossier_id);

        if(!$zno['zno']) return false;

        if(!$dossier_id){
            $new_zno= str_pad(intval($zno['zno'])+1,5,'0',STR_PAD_LEFT);
            $time = time();
        }else{
            $new_zno = $zno['zno'];
            $time = $zno['addtime'];
        }

        if($is_str){
            $new_zno = self::getZcNoByNo($new_zno,$time);
        }
        return $new_zno;
    }

    public static function getMaxZcNo($dossier_id)
    {
        $map = [];
        $map['zno'] = array('neq','');
        if($dossier_id){
            $map['id'] = $dossier_id;
        }
        return Db::name('dossier')->where($map)->field('id,max(zno) as zno,addtime')->order('id desc')->find();
    }

    public static function getZcNoByNo($zc_no,$time='',$isJueDing=false)
    {
        $zc_noArr = explode('-',$zc_no);
        if(!$time){
            $dtime = Db::name('dossier')
                ->alias('d')
                ->join('dossier_time dt','d.id = dt.id')
                ->field('time30')
                ->where('d.zno',$zc_noArr[0])
                ->find();

            if($dtime['time30']){
                $time = $dtime['time30'];
            }
        }

        if($isJueDing){
            $msg = '石裁上决字['.date('Y',$time).']第'.$zc_no.'号';
        }else{
            $msg = '石裁上字['.date('Y',$time).']第'.$zc_no.'号';
        }
        return $msg;
    }

    public static function getInfoTitle($zc_no,$time30='')
    {

        return intval($zc_no) ? date('y',$time30?$time30:self::getTime22ByZno($zc_no)).'-'.intval($zc_no) :  '待受理';
    }

    public static function getTime22ByZno($zc_no)
    {
        $zc_noArr = explode('-',$zc_no);
        return Db::name('dossier')
            ->alias('d')
            ->join('dossier_time dt','d.id = dt.id')

            ->where('d.zno',$zc_noArr[0])
            ->value('dt.time30');
    }
}