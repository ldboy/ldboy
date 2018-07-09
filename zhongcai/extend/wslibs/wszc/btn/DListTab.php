<?php
namespace wslibs\wszc\btn;

use dossier\DossierDoc;
use think\Db;
use think\Validate;
use wslibs\wszc\Constant;

use wslibs\wszc\Dossier;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\LoginUser;


/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/21
 * Time: 下午12:38
 */
class DListTab
{


    private $tags = array(


        Constant::QX_ROLE_SHENQINGREN => array(

            ["name" => "所有", "tip_type" => "0", "count_all" => "1"],
            ["name" => "待申请", "tip_type" => "1", "where" => ["status" => 1], "count_status" => "1"],
            ["name" => "待立案", "tip_type" => "0", "where" => ["status" => 2], "count_status" => "2"],
            ["name" => "已立案", "tip_type" => "0", "where" => ["status" => 3], "count_status" => "3"],
            ["name" => "已结案", "tip_type" => "0", "where" => ["status" => 4], "count_status" => "4"],
            ["name" => "被拒绝", "tip_type" => "1", "where" => ["status" => 0,"sub_status"=>5], "count_status" => "0"],
            ["name" => "待处理","where"=>array("status"=>3),  "where_question" => ["status" => array("in", array(0, 1)), "idid" => "__YINHANG__"], "tip_type" => "1", "nodes" => [

                ["name" => "待质证", "tip_type" => "1","where"=>array("status"=>3), "where_question" => ["status" => array("in", array(0, 1)), "idid" => "__YINHANG__"]],
            ]],

        ),
        Constant::QX_ROLE_ZHONGCAIWEI_MISHU => array(

            ["name" => "所有", "tip_type" => "0", "count_all" => "1", "nodes" => [
                ["name" => "所有案件", "tip_type" => "0", "count_all" => "1"],
                ["name" => "待受理", "tip_type" => "1", "where" => ["sub_status" => 20], "count_sub_status" => "20"],
                ["name" => "立案审批中", "tip_type" => "0", "where" => ["sub_status" => 22], "count_sub_status" => "22"],
                ["name" => "答辩期", "tip_type" => "0", "where" => ["sub_status" => 30], "count_sub_status" => "30"],
                ["name" => "待组庭", "tip_type" => "0", "where" => ["sub_status" => 31], "count_sub_status" => "31"],
                ["name" => "已组庭", "tip_type" => "0", "where" => ["sub_status" => 31], "count_sub_status" => "31"],
                ["name" => "裁决书编写中", "tip_type" => "0", "where_caijue" => ["c_status" => 1]],
                ["name" => "裁决书待校验", "tip_type" => "1", "where_caijue" => ["c_status" => 2]],
                ["name" => "裁决书仲裁员签字中", "tip_type" => "0", "where_caijue" => ["c_status" => 3]],
                ["name" => "裁决书审批中", "tip_type" => "0", "where_caijue" => ["c_status" => 4]],
                ["name" => "已撤回", "tip_type" => "0", "where" => ["status" => 0]],
            ]],
            ["name" => "待受理", "tip_type" => "1", "where" => ["sub_status" => 20], "count_sub_status" => "20"],
            ["name" => "裁决书待校验", "tip_type" => "1", "where_caijue" => ["c_status" => 2]],
            ["name" => "其它待处理"  ,"where"=>array("status"=>['in',[2,3]]),"where_defence" => ["status" => 2], "where_chehui" => ["status" => 2], "where_question" => ["status" => 2], "where_dz" => ["status" => 2], "where_huibi" => ['status' => 2,"type" => array("in", [1, 2])], "tip_type" => "1", "nodes" => [




                ["name" => "质证待处理", "tip_type" => "1","where"=>array("status"=>3), "where_question" => ["status" => 2]],
                ["name" => "答辩待处理", "tip_type" => "1","where"=>array("status"=>3), "where_defence" => ["status" => 2]],
                ["name" => "回避申请待处理", "tip_type" => "1","where"=>array("status"=>3), "where_huibi" => ["status" => 2, "type" => 2]],
                ["name" => "撤回待处理", "tip_type" => "1","where"=>array("status"=>['in',[2,3]]), "where_chehui" => ["status" => ['in',[2,6]]]],
                ["name" => "披露待处理", "tip_type" => "1","where"=>array("status"=>3), "where_huibi" => ["status" => 2, "type" => 1]],
                ["name" => "证据提交待处理", "tip_type" => "1","where"=>array("status"=>3), "where_dz" => ["status" => 2]],
                ["name" => "管辖权异议处理", "tip_type" => "1","where"=>array("status"=>3), "where_gxq_yy" => ["status" =>['in',[2,7,8]]]],

            ]],


        ),
        Constant::QX_ROLE_BEISHENQINGREN => array(

            ["name" => "所有", "tip_type" => "0", "count_all" => "1"],

            ["name" => "待处理","where"=>array("status"=>3), "where_defence" => ["status" => array("in", array(0, 1)), "idid" => "__ME__"], "where_question" => ["status" => array("in", array(0, 1)), "idid" => "__ME__"], "tip_type" => "1", "nodes" => [

                ["name" => "待答辩", "tip_type" => "1"  ,"where"=>array("status"=>3) , "where_defence" => ["status" => array("in", array(0, 1)), "idid" => "__ME__"]],
                ["name" => "待质证", "tip_type" => "1","where"=>array("status"=>3), "where_question" => ["status" => array("in", array(0, 1)), "idid" => "__ME__"]],
            ]],


        ),




        
        Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN => array(

            ["name" => "所有", "tip_type" => "0", "count_all" => "1", "nodes" => [
                ["name" => "所有案件", "tip_type" => "0", "count_all" => "1"],

                ["name" => "待立案审批", "tip_type" => "1", "where" => ["sub_status" => 22], "count_sub_status" => "22"],
                ["name" => "答辩期", "tip_type" => "0", "where" => ["sub_status" => 30], "count_sub_status" => "30"],
                ["name" => "待指定仲裁员", "tip_type" => "1", "where" => ["sub_status" => 31], "count_sub_status" => "31"],
                ["name" => "已组庭", "tip_type" => "0", "where" => ["sub_status" => 31], "count_sub_status" => "31"],
                ["name" => "裁决书编写中", "tip_type" => "0", "where_caijue" => ["c_status" => 1]],
                ["name" => "裁决书待校验", "tip_type" => "0", "where_caijue" => ["c_status" => 2]],
                ["name" => "裁决书仲裁员签字中", "tip_type" => "0", "where_caijue" => ["c_status" => 3]],
                ["name" => "裁决书审批中", "tip_type" => "1", "where_caijue" => ["c_status" => 4]],
                ["name" => "已撤回", "tip_type" => "0", "where" => ["status" => 0]],
            ]],
            ["name" => "待立案审批", "tip_type" => "1", "where" => ["sub_status" => array("in",[22,23])], "count_sub_status" => "22,23"],
            ["name" => "待指定仲裁员", "tip_type" => "1", "where" => ["sub_status" => array("in",[31,35])], "count_sub_status" => "31,35"],
            ["name" => "待决定书审批", "tip_type" => "1", "where_chehui" => ["status" => 4], "where_huibi" => ["status" => ['in',[5,6,10,11]], "type" => 2]],
            ["name" => "待裁决书审批", "tip_type" => "1", "where_caijue" => ["c_status" => 4]],

        )
    ,
        Constant::QX_ROLE_ZHONGCAIYUAN => array(

            ["name" => "所有", "tip_type" => "0", "count_all" => "1"],
            ["name" => "待申明/披露", "tip_type" => "1", "where_huibi" => ["status" => 0, "idid" => "__ME__"]],
            ["name" => "待决定", "tip_type" => "1", "where_chehui" => ["status" => ['in',[3,7]]]],
            ["name" => "待裁决", "tip_type" => "1", "where_caijue" => ["c_status" => array("in", [1, 3])]],


        )
    );


    private $tag = [];


    private $_use_qrole = 0;

    private $_where = [];

    private $_where_roles = [];


    private $_runing_data = [];

    public function __construct()
    {

        $this->tags = $this->getTabs();

        $this->initTags($this->tags);

        $this->initBaseWhere();


    }


    private function keyword($keywords,&$node)
    {
        if(!$keywords) return $this;
        parse_str(urldecode($keywords), $s);

        $keyword = trim($s['sq_phone']);
        if (!$keyword) return $this;

        if (preg_match('/^1[3456789]\d{9}$/', $keyword)) {

            $node['where_users']["phone"] = $keyword;

        } elseif (preg_match('/^\d{15}$)|(^\d{17}([0-9]|X)$/isu', $keyword)) {

            $idid = IDcard::getIdId($keyword);
            if ($idid)
                $node['where_users']["idid"] = $idid;

        } elseif (strlen($keyword) == 5) {
            $node['where']['zno'] = $keyword;
        } elseif (is_numeric($keyword)) {
            $node['where']['id'] = (int)$keyword;
        } else {
            $node['where_users']["name"] = array("like", $keyword);
        }

        return $this;

    }


    public function initBaseWhere()
    {

        if ($this->_use_qrole == Constant::QX_ROLE_SHENQINGREN) {
            $this->_where_roles = array_merge($this->_where_roles, array("idid" => LoginUser::getIdid()));
        } else if ($this->_use_qrole == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {
            $this->_where_roles = array_merge($this->_where_roles, ["   ( r.idid=" . LoginUser::getIdid() . " and r.role=" . Constant::D_Role_ZhongCaiWei_GuanLiYuan . " )  or  (  r.idid=" . LoginUser::getRoleThIdId() . " and  d.sub_status=20 )"]);
        } else if ($this->_use_qrole == Constant::QX_ROLE_BEISHENQINGREN) {
            $this->_where_roles = array_merge($this->_where_roles, array("idid" => LoginUser::getIdid()));
            $this->_where = array_merge($this->_where, array("sub_status" => array(">=", 30)));
        } else if ($this->_use_qrole == Constant::QX_ROLE_ZHONGCAIYUAN) {
            $this->_where_roles = array_merge($this->_where_roles, array("idid" => LoginUser::getIdid(), "role" => Constant::D_Role_ZhongCaiYuan));
        } else if ($this->_use_qrole == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {
            $this->_where_roles = array_merge($this->_where_roles, ["  ( r.idid=" . LoginUser::getIdid() . " and r.role=" . Constant::D_Role_ZhongCaiWei_LiAnShenPi . " ) or  (  r.idid=" . LoginUser::getRoleThIdId() . " and  d.sub_status=22 )"]);
        }

        return $this;
    }

    private function initTags(&$item)
    {


        $colors = array("blue", "red", "yellow", "green");
        foreach ($item as $key => &$value) {


            $tag = "tag_" . count($this->tag);
            $value['tag'] = $tag;

            $value['bage_color'] = $colors[array_rand($colors)];
            $this->tag[$tag] = &$value;
            if ($value['nodes']) {
                $this->initTags($value['nodes']);
            }

        }

    }

    public function getList($tag, $start, $num,$keyword=null)
    {
        $node = $this->tag[$tag];

        $this->keyword($keyword,$node);
        list($this->_where, $this->_where_roles) = $this->mwhere($this->_where, $this->_where_roles, $node);
        $query = $this->getQuery($this->_where, $this->_where_roles);
        $count_query = $this->getQuery($this->_where, $this->_where_roles);


        $total = $count_query->count();

        $query->limit($start, $num);
        $list = $query->field("d.*,t.*")->order('d.id desc')->select();
        //echo $query->getLastSql();
        return array($total, $this->dealListData($list));


    }


    private function mwhere($where, $where_roles, $node, $where_user=[])
    {
        if ($node['where']) {
            $where = array_merge($where, $node['where']);
        }
        if ($node['where_roles']) {
            $where_roles = array_merge($where_roles, $node['where_roles']);
        }


        $in=[];
        $in_flag = false;
        if ($node['where_defence']) {
            $in = array_merge($in, $this->runingData("where_defence", $node['where_defence'], Db::name("dossier_defence")->where($this->rWhere($node['where_defence']))->column("dossier_id")));
            $in_flag = 1;
        }

        if ($node['where_question']) {
            $in = array_merge($in, $this->runingData("where_question", $node['where_question'], Db::name("dossier_question")->where($this->rWhere($node['where_question']))->column("dossier_id")));
            $in_flag = 1;
        }

        if ($node['where_dz']) {
            $in = array_merge($in, $this->runingData("where_dz", $node['where_dz'], Db::name("dz")->where($this->rWhere($node['where_dz']))->column("dossier_id")));
            $in_flag = 1;
        }
        if ($node['where_huibi']) {
            $node['where_huibi']['is_valid']=1;
            $in = array_merge($in, $this->runingData("where_huibi", $node['where_huibi'], Db::name("huibi")->where($this->rWhere($node['where_huibi']))->column("dossier_id")));
            $in_flag = 1;
        }
        if ($node['where_caijue']) {
            $in = array_merge($in, $this->runingData("where_caijue", $node['where_caijue'], Db::name("dossier_caijue")->where($this->rWhere($node['where_caijue']))->column("id")));
            $in_flag = 1;
        }


        if ($node['where_chehui']) {
            $in = array_merge($in, $this->runingData("where_chehui", $node['where_chehui'], Db::name("dossier_cancel")->where($this->rWhere($node['where_chehui']))->column("dossier_id")));
            $in_flag = 1;
        }

        if ($node['where_users']) {
            $in = array_intersect($in, $this->runingData("where_users", $where_user, Db::name("dossier_users")->where($this->rWhere($node['where_users']))->column("dossier_id")));
            $in_flag = 1;
        }
        //where_gxq_yy
        if ($node['where_gxq_yy']) {
            if($_GET['zhz']==935){
                dump($in);

            }
            $in = array_intersect($in, $this->runingData("where_gxq_yy", $where_user, Db::name("gxq_yy")->where($this->rWhere($node['where_gxq_yy']))->column("d_id")));
//            if($_GET['zhz']==935){
//
//                dump($in);
//
//            }
            $in_flag = 1;
        }

        if ($in_flag) {
            if (!$in)
                $where["id"] = 0;
        }
        if (count($in) > 0) {

            $where = array_merge($where, array("id" => array("in", array_unique($in))));

        }
        return [$where, $where_roles];
    }

    private function runingData($gruop, $data, $value)
    {
        $key = md5(http_build_query($data));

        if (!isset($this->_runing_data[$gruop][$key])) {
            $this->_runing_data[$gruop][$key] = $value;
        }

//        if($_GET['zhz']==935){
//            dump($this->_runing_data[$gruop][$key]);
//        }
        return $this->_runing_data[$gruop][$key];

    }

    private function rWhere($where, $tableas = "", &$more_where = [])
    {
        $tmp = [];
        foreach ($where as $key => $value) {
            if ($value === "__ME__") {
                $value = LoginUser::getIdid();
            }
            if ($value === "__YINHANG__") {
                $value = LoginUser::getRoleThIdId();
            }
            if (!is_int($key))
                $tmp[$tableas . $key] = $value;
            else {
                $more_where[] = $value;
            }
        }

        return $tmp;
    }

    private function getQuery($where, $where_roles)
    {


        $where = $this->rWhere($where, "d.", $morewhere);


        $where_roles = $this->rWhere($where_roles, "r.", $morewhere_roles);


        $query = Db::name("dossier")->alias("d")->join("dossier_time t", "t.id=d.id", "left");

        if ($where_roles || count($morewhere_roles)) {
            $query->join("dossier_roles r", "r.dossier_id=d.id", "right");

        }
        if ($where || $morewhere) {
            if ($where)
                $query->where($where);
            if ($morewhere) {
                foreach ($morewhere as $w) {
                    $query->where($w);
                }
            }


        }


        if ($where_roles || $morewhere_roles) {
            if ($where_roles)
                $query->where($where_roles);
            if ($morewhere_roles) {
                foreach ($morewhere_roles as $w) {

                    $query->where($w);
                }

            }
        }
        $query->distinct("d.id");
        return $query;


    }


    public function initTagCount()
    {


        $query = $this->getQuery($this->_where, $this->_where_roles);

        $sub_count = $query->field("count(1) as c_num")->group("d.sub_status")->selectOfIndex("sub_status");

        $count = [];
        foreach ($sub_count as $key => $value) {
            $count[substr($key . "", 0, 1)] += $value;
        }
        $count_all = 0;
        foreach ($sub_count as $key => $value) {
            $count_all += $value;
        }



        array_walk($this->tag, function (&$data) use ($count, $sub_count, $count_all) {


            if ($data['count_status']) {
                $data['num'] = array_sum(array_intersect_key($count, array_flip(explode(",", $data['count_status']))));
            } else if ($data['count_sub_status']) {
                $data['num'] = array_sum(array_intersect_key($sub_count, array_flip(explode(",", $data['count_sub_status']))));
            } else if ($data['count_all']) {
                $data['num'] = $count_all;
            } else {

                list($where, $where_roles) = $this->mwhere($this->_where, $this->_where_roles, $data);
                if($_GET['zhz']==935){



                }

                $query = $this->getQuery($where, $where_roles);
                $data['num'] = $query->count();
            }
            if (!$data['num']) {
                $data['tip_type'] = 0;
            }

        });



    }


    public function getWhereByTag($tag)
    {
        return $this->tag[$tag]['where'];
    }

    private function getTabs()
    {

        $btns = [];

        if (LoginUser::isZhongCaiWeiZhuBan()) {
            $this->_use_qrole = Constant::QX_ROLE_ZHONGCAIWEI_MISHU;
            $btns = $this->tags[Constant::QX_ROLE_ZHONGCAIWEI_MISHU];
        } else if (LoginUser::isShenQingFang()) {
            $btns = $this->tags[Constant::QX_ROLE_SHENQINGREN];
            $this->_use_qrole = Constant::QX_ROLE_SHENQINGREN;
        } else if (LoginUser::isBeiShenQingFang()) {
            $btns = $this->tags[Constant::QX_ROLE_BEISHENQINGREN];
            $this->_use_qrole = Constant::QX_ROLE_BEISHENQINGREN;
        } else if (LoginUser::isZhongCaiYuan()) {
            $btns = $this->tags[Constant::QX_ROLE_ZHONGCAIYUAN];
            $this->_use_qrole = Constant::QX_ROLE_ZHONGCAIYUAN;
        } else if (LoginUser::isZhongCaiLiAanShenPi()) {
            $btns = $this->tags[Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN];
            $this->_use_qrole = Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN;
        }

        return $btns;
    }

    public function getTabsShow()
    {
        return $this->tags;
    }

    public function dealListData($list)
    {
        $dids = array_column($list, "id");


        $dangshirens = Db::name("dossier_users")->whereIn("dossier_id", $dids)->group("dossier_id,role")->field("dossier_id,role,group_concat(name order by r_no asc) as title,count(*) as num")->select();
        $renyuan = array();

        foreach ($dangshirens as $dangshiren) {
            $renyuan[$dangshiren['dossier_id']][$dangshiren['role']] = $dangshiren;
        }
        $times = array_column(Db::name('dossier_time')->whereIn("id", $dids)->select(),'time30','id');

        list($defence,$question) = $this->remainingTime($dids);

        return array_map(function ($value) use ($renyuan,$times,$defence,$question) {

            $dangshiren = $renyuan[$value['id']];

            $value['sq_time'] = $this->time($value['addtime']);
            $value['sl_time'] = $this->time($value['time30']);

            $value['sq_string'] = $dangshiren[Constant::D_Role_ShenQingRen]['title'] . ($dangshiren[Constant::D_Role_ShenQingRen]['num'] > 1 ? (",共" . $dangshiren[Constant::D_Role_ShenQingRen]['num'] . "人") : "");
            $value['bsq_string'] = $dangshiren[Constant::D_Role_Bei_ShenQingRen]['title'] . ($dangshiren[Constant::D_Role_Bei_ShenQingRen]['num'] > 1 ? (",共" . $dangshiren[Constant::D_Role_Bei_ShenQingRen]['num'] . "人") : "");
            $value['status_string'] = Dossier::getStatus($value['status'],$value['sub_status']);

            if (!$value['status_string']) {
                $value['status_string'] = "-";
            }

            $value['sub_status_string'] = Dossier::getSubStatus($value['sub_status']);

//            if (! $value['sub_status_string'])
//            {
//                $value['sub_status_string']  = "-";
//            }

            if($defence[$value['id']]){
                $value['two_status'] = $defence[$value['id']];
            }
            if($question[$value['id']]){
                $value['two_status'] .= $question[$value['id']];
            }
            $value['two_status'] ? $value['two_status'] : $value['two_status'] = '---';

            if ((LoginUser::isZhongCaiWei() || LoginUser::isZhongCaiYuan()) && $value['sub_status_string']) {
                $value['status_string'] = $value['status_string'] . "(" . $value['sub_status_string'] . ")";
            }

            $value['status_string_color'] = "bg-" . Dossier::getStatusColor($value['status']);

            $value['zcw_name'] = '暂无';

            if (!$value['zcw_name']) {
                $value['zcw_name'] = "-";
            }
            $value['zno_title'] = DossierDoc::getInfoTitle($value['zno'],$times[$value['id']]);
            $value['zno'] = $value['zno'] ? DossierDoc::getZcNoByNo($value['zno'], $value['addtime']) : '---';
            return $value;
        }, $list);
    }

    private function time($time)
    {
        return $time ? date("Y-m-d H:i", $time) : "";
    }

    public function remainingTime($dids)
    {
        $remain_defence = $this->dealRemainTime(Db::name("dossier_defence")->where('status=2')->whereIn("dossier_id", $dids)->group("dossier_id")->field("dossier_id,group_concat(addtime) as defence_addtime,count(*) as num")->select(),'defence_addtime');
        $remain_question = $this->dealRemainTime(Db::name("dossier_question")->where('status=2')->whereIn("dossier_id",$dids)->group("dossier_id")->field("dossier_id,group_concat(addtime) as question_addtime,count(*) as num")->select(),'question_addtime','质证');

        return [array_column($remain_defence,'defence_time','dossier_id'),array_column($remain_question,'question_addtime','dossier_id')];
    }


    public function dealRemainTime($remain,$key,$reamrk='答辩',$time=5)
    {
        return  array_map(function($value) use($key,$time,$reamrk){

            $value[$key] = $value['num'].'个'.$reamrk.'剩余:'.implode(',',array_map(function($val)use($time){

                    $val = ceil(($val+86400*$time-time())/86400).'天';

                    return $val;
                },explode(',',$value[$key])));

            return $value;
        },$remain);
    }



    public function DealStatus($typeList, $sub_key_num, $role)
    {

        if (!$typeList) {
            return $typeList;
        }

        $colors = array("blue", "red", "yellow", "green");

        foreach ($typeList as $key => $value) {
            if ($sub_key_num[$key] == 0) {
                $sub_key_num[$key] = '';
            }
            $typeList[$key] = array("key" => $key, "name" => $value, "num" => $sub_key_num[$key], "bage_color" => $colors[array_rand($colors)]);

        }

        return $typeList;
    }


    public function dealSubList($role, $sublist)
    {
        $role_status = Dossier::getStatusForRole($role);

        foreach ($sublist as $key => $value) {
            if (!$role_status[$value['key']]) {
                unset($sublist[$key]);
            }
        }
        return $sublist;
    }

}