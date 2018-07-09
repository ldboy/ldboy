<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/19
 * Time: 11:05
 * 管辖权异议的处理
 */
namespace wslibs\wszc;
use think\Db;

class Gxq{
    const status_1 = 1;// 刚提交表单
    const status_2 = 2;// 提交至仲裁委
    const status_3 = 3;// 主办 有管辖权
    const status_4 = 4;// 主办 没有管辖权
    const status_5 = 5;// 最终结果  有管辖权
    const status_6 = 6;// 最终结果 没有管辖权
    const status_7 = 7;// 主办已选 有管辖权 但还未编辑决定书发送至主任
    const status_8 = 8;// 主办已选 没有管辖权 但还未编辑决定书发送至主任
    // 添加异议
    public static function add($did,$matter,$reason){
        if(!$did||!$matter||!$reason){
            return false;
        }

        $data = [
            'd_id'=>$did,
            'matter'=>$matter,
            'reason'=>$reason,
            'status'=>1,
            'addtime'=>time(),
            'idid'=>LoginUser::getRoleThIdId()?LoginUser::getRoleThIdId():LoginUser::getIdid()
        ];
        $has = self::hasYy($did);
        if($has){

            if($has['status']==4){
                Db::name('gxq_yy')->where('id',$has['id'])->update($data);
            }
            return $has['id'];
        }
        return Db::name('gxq_yy')->insertGetId($data);
    }
    // 是否有异议
    public static function hasYy($did,$status=[],$notSelf=false){

        $map = [];
        $map['d_id'] = $did;
        if(!$notSelf){
            $map['idid'] = LoginUser::getRoleThIdId()?LoginUser::getRoleThIdId():LoginUser::getIdid();
        }
        if($status){
            if(count($status)==1){
                $map['status'] = $status[0];
            }else{
                $map['status'] = ['in',$status];
            }
        }
        return   Db::name('gxq_yy')
            ->where($map)
            ->find();
    }
    // 未发表意见的异议
    public static function unOpinion($did){
        // 不是本人发表的异议  未发表意见的

        // 应该发表意见的
        $ying_yy = Db::name('gxq_yy')
            ->where('d_id',$did)
            ->where('status',3)
            ->where('idid','<>',LoginUser::getIdid())
            ->order('id asc')
            ->column('id');
        if(!$ying_yy){
            return null;
        }
        // 已经发表意见的
        $yi_yy = Db::name('gxq_yj')
            ->whereIn('gxq_id',$ying_yy)
            ->where('idid',LoginUser::getIdid())
            ->where('status','>',1)
            ->order('gxq_id asc')
            ->column('gxq_id');
        // 未发表意见的 取差集
        $unOpinion = array_diff($ying_yy,$yi_yy);
        if($unOpinion){
            return $unOpinion[0];
        }
            return null;
    }

    // d_id查询异议
    public static function getYjByDid($did){
        return self::hasYy($did);
    }

    // 是否有异议意见
    public static function hasYyYj($yyid){
        return Db::name('gxq_yj')
            ->where('gxq_id',$yyid)
            ->where('idid',LoginUser::getIdid())
            ->find();
    }

    public static function getYyById($id){
        $info = Db::name('gxq_yy')
            ->alias('yy')
            ->join('dossier_users du','yy.d_id=du.dossier_id and yy.idid=du.idid','left')
            ->field('yy.*,du.name,du.r_no,du.role')
            ->where("yy.id = $id")
            ->find();
        if(input('aa')==1){
            dump($info);
        }
        if(!$info){
            return false;
        }

            if(in_array($info['role'],[Constant::D_Role_ShenQingRen,Constant::D_Role_ShenQingRen_Dl,Constant::D_Role_ShenQingRen_FR,])){
                $info['role'] = '申请人';
            }else{
                $info['role'] = '被申请人';
                if($info['r_no']){
                    $info['role'] = '第'.DInfoValue::num2Upper($info['r_no']).$info['role'];
                }
            }
        $info['addtime'] = date('Y-m-d',$info['addtime']);
        return $info;
    }
    public static function getYjById($id){
        return Db::name('gxq_yj')->find($id);
    }
    // 异议提交至仲裁委
    public static function subToZcw($id){
        return self::changeYyStatus($id,2);
    }

    // 添加意见
    public static function addYj($yyid,$reason){
        if(!$yyid||!$reason){
            return false;
        }
        $hasYj = self::hasYyYj($yyid);
        if($hasYj){
            return $hasYj['id'];
        }
        $data=[
            'gxq_id'=>$yyid,
            'idid'=>LoginUser::getIdid(),
            'addtime'=>time(),
            'reason'=>$reason,
            'status'=>1
        ];
        return Db::name('gxq_yj')->insertGetId($data);
    }
    // 修改异议状态
    public static function changeYyStatus($id,$status){
        return self::changeStatus($id,$status,'gxq_yy');
    }

    // 修改意见状态
    public static function changeYjStatus($id,$status){
        return self::changeStatus($id,$status,'gxq_yj');
    }

    private static function changeStatus($id,$status,$table){
        $data = [
            'status'=>$status
        ];
        return Db::name($table)->where('id',$id)->update($data);
    }

    // 获取异议列表
    public static function getYyList($did){
        $list = Db::name('gxq_yy')
            ->alias('yy')
            ->join('dossier_users du','yy.d_id=du.dossier_id and yy.idid=du.idid','left')
            ->field('yy.*,du.name,du.r_no,du.role')
            ->where('yy.status','>=',2)
            ->where('yy.d_id',$did)
            ->select();
        $statusStr = [
            2=>'待处理',
            3=>'已转发'
        ];
        foreach($list as $k=>$v){
            if(in_array($v['role'],[Constant::D_Role_ShenQingRen,Constant::D_Role_ShenQingRen_Dl,Constant::D_Role_ShenQingRen_FR,])){
                $v['role'] = '申请人';
            }else{
                $v['role'] = '被申请人';
                if($v['r_no']){
                    $v['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$v['role'];
                }
            }
            $v['addtime'] = date('Y-m-d',$v['addtime']);
            $v['statusStr'] = $statusStr[$v['status']];
            $list[$k] = $v;
        }
        return $list;
    }
    // 获取意见列表
//    public static function getYjList($did,$yyid=0){
//        $map = [];
//        $map['status'] = ['gt',1];
//        if($yyid){
//            $map['gxq_id'] = $yyid;
//        }
//        $list = Db::name('gxq_yj')->find($yyid);
//
//        return $list;
//    }

}