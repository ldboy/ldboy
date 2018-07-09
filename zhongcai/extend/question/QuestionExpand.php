<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-08
 * Time: 20:27
 */

namespace question;
use think\Db;

class QuestionExpand
{
    const DEFULE_STR = "无异议";

    public static $type = [
        1 => "legal", //合法性
        2 => "relation", //关联性
        3 => "reality", //真实性
        4 => "other" //其他
    ];


    /**
     * @param $dossier_id
     * @param $evidence_id
     * @param $uid
     * @param string $legal
     * @param string $relation
     * @param string $reality
     * @param string $other
     * @return bool|int|string
     */
    public static function addQuestion($dossier_id,$evidence_id,$uid,$legal = '',$relation = '',$reality = '',$other = ''){
        if(!$dossier_id || !$evidence_id || !$uid) return false;

        $data = [
            'dossier_id' => $dossier_id,
            'evidence_id' => $evidence_id,
            'uid' => $uid,
            'addtime' => time()
        ];

        !$legal ? $data['legal'] = self::DEFULE_STR : $data['legal'] = $legal;
        !$relation ? $data['relation'] = self::DEFULE_STR : $data['relation'] = $relation;
        !$reality ? $data['reality'] = self::DEFULE_STR : $data['reality'] = $reality;
        !$other ? $data['other'] = self::DEFULE_STR : $data['other'] = $other;

        return Db::name("dossier_question")->insertGetId($data);
    }

    /**
     * @param $dossier_id
     * @param string $uid
     * @return bool|false|\PDOStatement|string|\think\Collection
     */
    public static function getQuestion($dossier_id,$uid = ''){
        if(!$dossier_id) return false;

        $map = [
            'dossier_id' => $dossier_id,
            'uid' => $uid
        ];

        if(!$uid){
            unset($map['uid']);
        }

        return Db::name("dossier_question")->where($map)->select();

    }
}