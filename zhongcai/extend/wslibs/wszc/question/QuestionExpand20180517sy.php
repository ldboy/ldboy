<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-08
 * Time: 20:27
 */

namespace wslibs\wszc\question;
use think\Db;
use wslibs\wszc\LoginUser;

class QuestionExpand
{
    const DEFULE_STR = "无异议";

    public static $type = [
        1 => "legal", //合法性
        2 => "relation", //关联性
        3 => "reality", //真实性
        4 => "other" //其他
    ];


    // 获取质证id
    /**
     * @param $dossierId //
     * @param $idid
     */
    // 每个人只能质证一次
    public static function getQuestionId($dossierId,$idid){
        // 找没有完成的 如果没有生成一条
        $question = Db::name('dossier_question')
            ->where('dossier_id',$dossierId)
            ->where('idid',$idid)
            ->find();
        if($question){
            if($question['status']==0){
                return $question['id'];
            }else{
                return 0;
            }
        }
        $data = [];
        $data['dossier_id'] = $dossierId;
        $data['addtime'] = time();
        $data['idid'] = $idid;
        // dossier_id  idid 获取role
        $data['role'] = LoginUser::getRole();
        return Db::name('dossier_question')->insertGetId($data);
    }
    // 完成
    public static function finishQuestion($dossierId,$idid){
       return Db::name('dossier_question')
            ->where('dossier_id',$dossierId)
            ->where('idid',$idid)
            ->where('status',0)
            ->update(['status'=>1,'finish_time'=>time()]);
    }
    // 发送文件完成
    public static function sendFileFinish($dossierId,$id){
        self::plusNum($dossierId);
         return Db::name('dossier_question')
//            ->where('dossier_id',$dossierId)
//            ->where('idid',$idid)
                 ->where('id',$id)
            ->where('status',1)
            ->update(['status'=>2,'sendfile_time'=>time()]);

    }
    // 添加数量
    private static function plusNum($dossierId){

        return Db::execute("update zc_dossier
 set question_num=question_num+1 , question_num_dcl=question_num_dcl+1 where id=$dossierId");

    }
    // 减少数量
    private static function minusNum($dossierId){
        return Db::name('dossier')->where('id',$dossierId)->setDec('question_num_dcl');
    }
    // 查询质证数量 和 未处理质证数量
    public static function getQuestionNum($dossierId){
        $find = Db::name('dossier')->where('id',$dossierId)->field('question_num,question_num_dcl')->find();
        $find['question_num'] = $find['question_num']>0?$find['question_num']:'无';
        $find['question_num_dcl'] = $find['question_num_dcl']>0?$find['question_num_dcl']:'';
        return $find;
    }

    /**
     * @param $dossier_id
     * @param $evidence_id
     * @param $idid
     * @param $title
     * @param string $legal
     * @param string $relation
     * @param string $reality
     * @param string $other
     * @return bool|int|string
     */
    public static function addItem($dossier_id,$evidence_id,$idid,$title,$legal = '',$relation = '',$reality = '',$other = ''){
        if(!$dossier_id || !$evidence_id || !$idid){
            return false;
        }

        $qid = self::getQuestionId($dossier_id,$idid);
        if(!$qid){
            // 限制不能重复提交
            return false;
        }
        $data = [
            'q_id' => $qid,
            'evidence_id' => $evidence_id,
            'title' => $title,
            'addtime' => time()
        ];
        $data['legal'] = (!$legal?self::DEFULE_STR:$legal);
        $data['relation'] = (!$relation?self::DEFULE_STR:$relation);
        $data['reality'] = (!$reality?self::DEFULE_STR:$reality);
        $data['other'] = (!$other?self::DEFULE_STR:$other);

        if(
        $info = Db::name('dossier_question_list')
            ->where('q_id',$qid)
            ->where('evidence_id',$evidence_id)
            ->find()
        ){
          return Db::name("dossier_question_list")->where('id',$info['id'])->update($data);
        }
        return Db::name("dossier_question_list")->insertGetId($data);
    }

    /**
     * @param $dossier_id
     * @param string $idid
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public static function getQuestionList($dossier_id,$idid = ''){
        if(!$dossier_id){
            return false;
        }

        $map = [
            'dossier_id' => $dossier_id,
            'idid' => $idid
        ];

        if(!$idid){
            unset($map['idid']);
        }

        $list = Db::name("dossier_question")
            ->alias('dq')
            ->join('idcards idc','dq.idid=idc.id')
            ->field('dq.*,idc.real_name')
            ->where($map)
            ->select();
        foreach($list as $k=>$v){
            $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            if($v['status']==1){
                $v['finish_time'] = date('Y-m-d H:i:s',$v['finish_time']);
                $v['status'] = '已完成';
            }else{
                $v['status'] = '未完成';
            }
            $v['role'] = $v['role']==1?'申请人质证':'被申请人质证';
            $list[$k] = $v;
        }
        return $list;
    }


    public static function getOne($qid){
        return Db::name('dossier_question')->where('id',$qid)->find();
    }


    public static function getMy($dossier_id){
        return Db::name('dossier_question')
            ->where("dossier_id",$dossier_id)
            ->where('idid',LoginUser::getIdid())
            ->find();
    }

    public static function getMyDateil($dossier_id,$evidence_id){
        if(!$dossier_id || !$evidence_id) return false;
        $qid = self::getMy($dossier_id)['id'];
        return Db::name("dossier_question_list")->where("q_id",$qid)->where("evidence_id",$evidence_id)->find();
    }

    public static function getItemList($qid){
        $list = Db::name('dossier_question')
            ->alias('dq')
            ->join('dossier_question_list dql','dq.id = dql.q_id')
            ->join('idcards idc','dq.idid = idc.id')
            ->field('idc.real_name,dq.addtime as dq_addtime,dq.finish_time as dq_finish_time,dq.status,dql.*')
            ->where('dq.id',$qid)
//            ->where('dq.status',1)
            ->select();
        return $list;
    }
}