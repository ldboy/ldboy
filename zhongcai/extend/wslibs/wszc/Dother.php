<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/23
 * Time: 11:19
 */
namespace wslibs\wszc;
use think\Db;
class Dother{
    public static function add($did,$type,$content=''){
        if(!$did){
            return false;
        }
        if($type==4&&!$content){
            return false;
        }
        $data = [
            'did'=>$did,
            'idid'=>LoginUser::getRoleThIdId()?LoginUser::getRoleThIdId():LoginUser::getIdid(),
            'addtime'=>time(),
            'type'=>$type,
            'status'=>1,
            'cont'=>$content,
        ];
        $has = self::hasOtherApplay($did);
        if($has){
            return $has['id'];
        }
        return Db::name('dossier_other')->insertGetId($data);
    }

    public static function hasOtherApplay($did,$status=[],$notSelf=false){
        $map = [];
        $map['did'] = $did;
        if(!$notSelf){
            $map['idid'] = LoginUser::getRoleThIdId()?LoginUser::getRoleThIdId():LoginUser::getIdid();
        }
        if($status){
            if(is_array($status)){
                if(count($status)>1){
                    $map['status'] = ['in',$status];
                }else{
                    $map['status'] = $status[0];
                }
            }else{
                $map['status'] = $status;
            }
        }
        $info = Db::name('dossier_other')
            ->where($map)
            ->find();
        return $info;
    }

    public static function changeStatus($id,$status){
        $data = [
            'status'=>$status
        ];
        return Db::name('dossier_other')->where('id',$id)->update($data);
    }

    public static function otherList($did){
        $list = Db::name('dossier_other')
            ->alias('do')
            ->join('dossier_users du','do.did=du.dossier_id and do.idid=du.idid','left')
            ->field('do.*,du.name,du.r_no,du.role')
            ->where('do.status','>=',1)
            ->where('do.did',$did)
            ->select();
        $typeStr=[
            1=>'申请调解',
            2=>'申请中止',
            3=>'申请鉴定',
            4=>'其他',
        ];
        foreach($list as $k=>$v){
            if($v['role']==1){
                $v['role'] = '申请人';
            }else{
                $v['role'] = '被申请人';
                if($v['r_no']){
                    $v['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$v['role'];
                }
            }
            $v['addtime'] = date('Y-m-d',$v['addtime']);
            $v['typeStr'] = $typeStr[$v['type']];
            $list[$k] = $v;
        }
        return $list;
    }

    public static function getOtherInfo($id){
        return Db::name('dossier_other')->where('id',$id)->find();
    }

    public static function getOtherNum($did){
        return Db::name('dossier_other')->where('did',$did)->count();
    }
}