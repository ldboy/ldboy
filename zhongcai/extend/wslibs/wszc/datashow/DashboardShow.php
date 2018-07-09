<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/11
 * Time: 下午7:32
 */
namespace wslibs\wszc\datashow;

use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Dossier;
use wslibs\wszc\LoginUser;

class DashboardShow
{
    private $idid = 0;

    private $jg_id = 0;

    public function __construct()
    {
        $this->idid = LoginUser::getIdid();

        if(LoginUser::isZhongCaiWei() || LoginUser::isZhongCaiLiAanShenPi() || LoginUser::isZhongCaiWeiCaiJueShenpi()){
            $this->jg_id =  Db::name('jigou')->where('id',LoginUser::getRoleThId())->value('id');
        }else{
            return false;
        }

    }
    public static function getTemplateData()
    {
        $show = new DashboardShow();

        $out = ['all_element'=>[],'case_data'=>[],'list'=>[]];

        $all_case_list = $show->getAllCaseListByIdId($show->jg_id);

        $out['list']['all_list'] = $show->deal_data($all_case_list);

        list($list,$count) = $show->dealDataList($all_case_list);

        $out['list']['dsl_list'] = $show->deal_data($list[2]?$list[2]:[]);

        $out['list']['zcz_list'] = $show->deal_data($list[4]?$list[4]:[]);

        $out['all_element']['sl_num'] = $count[4]+$count[3];

        $out['all_element']['bank_num'] = $show->getBankNum($show->jg_id);

        $out['all_element']['person_num'] = $show->person($show->jg_id);

        $out['all_element']['zcy_num'] = $show->getZcy($show->jg_id);

        $out['case_data']['all_num'] = count($all_case_list);

        $out['case_data']['dsl_num'] = $count[2];

        $out['case_data']['db_num'] = $count[4];

        $out['case_data']['zcz_num'] = $count[3];


        return $out;
    }

    public function deal_data($list)
    {
        return array_slice($list,0,15,true);
    }

    public function person($jg_id)
    {
        $map = [];
        if($jg_id){
            $map['d.zc_jg_id'] = $jg_id;
        }

        $person = Db::name('dossier')->alias('d')->join('dossier_users du','d.id=du.dossier_id','left')->where($map)->field('count(distinct idid) as num')->select();//->group('du.idid')

        return $person[0]['num'] ? $person[0]['num'] : 0;
    }

    public function getZcy($jg_id)
    {
        $map = [];
        if($jg_id){
            $map['jz.jg_id'] = $jg_id;
        }
        $map['z.status'] = 1;

        return Db::name('jigou_zcy')->alias('jz')->join('zcy z','jz.zcy_id=z.id','LEFT')->where($map)->count(1);
    }

    public function getBankNum($zc_jg_id)
    {
        $map = [];
        if($zc_jg_id){
            $map['zc_jg_id'] = $zc_jg_id;
        }
        $map['status'] = 1;
        return Db::name('third_client')->where($map)->count(1);
    }

    public function array_group_by($list,$key)
    {
        $grouped = [];
        foreach ($list as $value) {
            $grouped[$value[$key]][] = $value;
        }

        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $parms = array_merge([$value], array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array('array_group_by', $parms);
            }
        }
        return $grouped;
    }

    public function dealDataList($list)
    {
        $out = $this->array_group_by($list,'status');
        $count[0] = count($out[0]);
        $count[1] = count($out[1]);
        $count[2] = count($out[2]);
        $count[3] = count($out[3]);
        $count[4] = count($out[4]);

        return [$out,$count];
    }


    public function getAllCaseListByIdId($jg_id)
    {
        $arr = Dossier::$arrStatus;

        return $all_case_list = array_map(function($value) use($arr){
            $value['addtime'] = date('Y年m月d日',$value['addtime']);
            $value['link'] = url('dossier.info/index',['id'=>$value['id']]);
            $value['detail'] = '详情';
            $value['zno_title'] = intval($value['zno']) ? date('y',$value['time22']?$value['time22']:$value['addtime']).'-'.intval($value['zno']) :  '待受理';

            $value['is_valid'] = $arr[$value['status']];
            return  $value;

        },self::getCaseForWhere($jg_id));
    }

    public function getCaseForWhere($jg_id= 0)
    {
        $map = [];
        if($jg_id){
            $map['d.zc_jg_id'] = $jg_id;
            $map['d.status'] = ['egt',2];
        }
        return Db::name('dossier')->alias('d')->join('dossier_time t','d.id=t.id','left')->field('d.*,t.time22')->where($map)->order('d.id desc')->limit(0,45)->select();
    }
}