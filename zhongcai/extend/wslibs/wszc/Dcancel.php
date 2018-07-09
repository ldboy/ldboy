<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-31
 * Time: 15:48
 */

namespace wslibs\wszc;
use think\Db;

//撤回
class Dcancel
{
    const STATUS_GANGFAQI = 0;//刚发起
    const STATUS_BIAODANYITIJIAO = 1;//申请已提交表单
    const STATUS_WENJIANYITIJIAO = 2;//申请已提交文件
    const STATUS_ZHUBANCHULI = 3;//主办已处理
    const STATUS_ZHONGCAIYUANCHULI = 4;//仲裁员已处理
    const STATUS_ZHURENCHULI = 5;//主任已处理（已完成）
    const STATUS_JUJUECHEHUI = 6;//拒绝撤回

    const TYPE_WEISHOULI = 1; //卷宗未受理
    const TYPE_YISHOULI = 2; //卷宗已受理
    const TYPE_YIZUTING = 3; //卷宗已组庭

    public static function addCancel($d_id , $d_sub_status){
        if($has = Db::name("dossier_cancel")->where("dossier_id" , $d_id)->find()) return $has['id'];

        if($d_sub_status < 21){
            $type = self::TYPE_WEISHOULI;
        } else if ($d_sub_status > 21 && $d_sub_status < 32){
            $type = self::TYPE_YISHOULI;
        } else if ($d_sub_status > 32){
            $type = self::TYPE_YIZUTING;
        } else {
            $type = -1;
        }

        $data = ["dossier_id" => $d_id , "addtime" => time() , "status" => 0 , "type" => $type , "idid" => LoginUser::getIdid()];

        return Db::name("dossier_cancel")->insertGetId($data);
    }


    public static function changeStatus($d_id , $status){
        if($has = Db::name("dossier_cancel")->where("dossier_id" , $d_id)->where("status" , $status)->find()) return $has['id'];
        return Db::name("dossier_cancel")->where("dossier_id" , $d_id)->update(['status' => $status]);
    }

    public static function getgetCancelById($id){
        return Db::name("dossier_cancel")
            ->alias("dc")
            ->join("idcards id","dc.idid = id.id")
            ->field("id.real_name,dc.shixiang,dc.addtime,dc.id,dc.dossier_id")
            ->where("dc.id" , $id)
            ->find();
    }

    public static function getCancel($d_id , $list = false){
        if($list){
            $dclist = Db::name("dossier_cancel")
                ->alias("dc")
                ->join("idcards id","dc.idid = id.id")
                ->field("id.real_name,dc.shixiang,dc.addtime,dc.id")
                ->where("dc.dossier_id" , $d_id)
                ->select();

            foreach ($dclist as $k => $v){
                $dclist[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
            }

            return $dclist;
        }
        return Db::name("dossier_cancel")->where("dossier_id" , $d_id)->find();
    }

    public static function saveCancel($d_id , $data){
        return Db::name("dossier_cancel")->where("dossier_id",$d_id)->update($data);
    }


}