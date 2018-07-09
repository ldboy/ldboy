<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8
 * Time: 19:48
 */

namespace wslibs\wszc;


use think\Db;

class DossierLog {

    const LOG_TYPE_CREATE = 1;// 创建
    const LOG_TYPE_COMPLETE= 2;// 完善卷宗资料
    const LOG_TYPE_ACCEPT = 3;// 受理
    const LOG_TYPE_DEFENCE = 4;// 答辩
    const LOG_TYPE_QUESTION = 5;// 质疑
    const LOG_TYPE_SET_UP_COURT = 6;// 组庭
    const LOG_TYPE_ADJUDICATION = 7;// 裁决
    const LOG_TYPE_GIVE_UP = 8;// 放弃
    const LOG_TYPE_REFUSE = 9;// 拒绝
    const LOG_TYPE_PILU = 10;// 披露
    const LOG_TYPE_SHENQINGHUIBI = 11;// 申请回避
    const LOG_TYPE_ACCEPT_PILU = 12;// 同意披露
    const LOG_TYPE_REFUSE_PILU = 13;// 拒绝披露
    const LOG_TYPE_ACCEPT_HUIBI = 14;// 同意申请回避
    const LOG_TYPE_REFUSE_HUIBI = 15;// 拒绝申请回避
    const LOG_TYPE_SEND_ACCEPT_FILE = 16;// 发送受理文件

    private static function getLogTypeDescribe($logType){
        $describe = [
            self::LOG_TYPE_CREATE=>'创建了此业务',
            self::LOG_TYPE_ACCEPT=>'受理了此业务',
            self::LOG_TYPE_DEFENCE=>'对此业务进行了答辩',
            self::LOG_TYPE_QUESTION=>'对业务证据提出质疑',
            self::LOG_TYPE_SET_UP_COURT=>'组建了仲裁庭',
            self::LOG_TYPE_ADJUDICATION=>'作出裁决',
            self::LOG_TYPE_GIVE_UP=>'放弃了此业务',
            self::LOG_TYPE_REFUSE=>'拒绝了此业务',
            self::LOG_TYPE_COMPLETE=>'完善了业务资料',
        ];
        $remark = '修改了此业务';
        if(isset($describe[$logType])){
            $remark = $describe[$logType];
        }
        return $remark;
    }
    public static function addLog($dossier_id,$uid,$name,$type,$extid=0)
    {
        $data = [
            'uid'=>$uid,
            'dossier_id'=>$dossier_id,
            'type'=>$type,
            'name'=>$name,
            'addtime'=>time(),
            'remark'=>self::getLogTypeDescribe($type),
            'ext_id'=>$extid,
        ];
        return Db::name('dossier_log')->insertGetId($data);
    }
    public static function getLogs($dossier_id){
        $list = Db::name('dossier_log')
            ->where('dossier_id',$dossier_id)
            ->select();
        foreach($list as $k=>$v){
            $v['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
            $list[$k]=$v;
        }
        return $list;
    }
    public static function getLogNum($dossier_id){
        $count =  (int)Db::name('dossier_log')
            ->where('dossier_id',$dossier_id)
            ->count();
        if(!$count){
            $count = '';
        }
        return $count;
    }

}