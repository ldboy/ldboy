<?php
namespace wslibs\wszc;

use dossier\DossierDoc;
use think\Cache;
use think\Db;
use think\db\Query;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\question\QuestionExpand;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/9
 * Time: 上午9:37
 *
 * $d.info 卷宗表
 *
 *
 * $d.users.role[1]
 * $d.users.id[1]
 * $d.users.me
 *
 *
 *  $d.defence.this  //答辩 info
 *  $d.defence.all    //list
 *  $d.defence.me    //info
 *
 *
 * $d.question.this
 * $d.question.me //list
 * $d.question.all
 * $d.question.zid[zid]  //证据id list
 *
 *$d.me //当前用户
 *$d.uniquevalue //唯一变量
 *$d.morevalue //多变量
 *$d.dvalue.this.uniquevalue //唯一变量
 *$d.dvalue.this.morevalue //多变量
 *$d.dvalue.docid[1].uniquevalue //唯一变量
 *$d.dvalue.docid[1].morevalue //多变量
 *
 *$d.sign.imgs 图片
 * $d.sign.time.y 年月日
 * $d.sign.time.m 年月日
 * $d.sign.time.d 年月日
 *

 */
class DInfoValue
{

    private $info = array();
    private $dossier_id = 0;
    private $doc_id = 0;
    private $dossier_sub_status = 0;
    private $doc_model_id = 0;



    public function __construct($dossier_id, $docid)
    {
        $this->dossier_id = $dossier_id;
        $this->doc_id = $docid;
        $this->dossier_sub_status = Dossier::getSimpleDossier($this->dossier_id)['sub_status'];
        $this->doc_model_id = Db::name("dr")->where("id",$this->doc_id)->value("doc_model_id");
    }


    public static function getTemplateData($doc_id, $uid)
    {
        if (!$uid) {
            echo "uid is not ";
            exit;
        }

        $cacheKey = Constant::Cache_pre_dinfoValue.$doc_id.'_'.$uid;

        $cache = Cache::get($cacheKey);
        if($cache){
//            return $cache;
        }



        $docinfo = \think\Db::name("dr")->find($doc_id);


        $ywid = (int)$docinfo['exid'];

        $dinfo = new DInfoValue($docinfo['dossier_id'], $doc_id);

        $cacheGroupKey = Constant::Cache_pre_dinfoValue.$docinfo['dossier_id'];

        $out = ['users' => [], "defence" => [], "question" => []];

        $out['users']['all'] = $dinfo->getUsers();

        $out['users']['role'] = $dinfo->makeListArray($out['users']['all'], "role");
        $out['users']['id'] = $dinfo->makeUniqueArray($out['users']['all'], "idid");

        $out['me'] = $out['users']['me'] = $dinfo->makeUniqueArray($out['users']['all'], "idid", $uid);

        if ($dinfo->dossier_sub_status <= 32) {
            $out['defence']['all'] = $dinfo->getDefences(0);
        } else {
            $out['defence']['all'] = $dinfo->getDefences(2);
        }


        ($out['defence']['this'] = $dinfo->makeUniqueArray($out['defence']['all'], "id", $ywid)); //$ywid &&
        $out['defence']['me'] = $dinfo->makeUniqueArray($out['defence']['all'], "uid", $uid);

        $out['question']['all'] = $dinfo->getQuestion();
        $ywid && ($out['question']['this'] = $dinfo->makeUniqueArray($out['question']['all'], "id", $ywid));
        $out['question']['me'] = $dinfo->makeListArray($out['question']['all'], "uid", "", $uid);
//        $out['question']['zid'] = $dinfo->makeListArray($out['question']['all'], "evidence_id", "id");

        $allvalues = $dinfo->getDvalues();

        $dinfo->fenlei($allvalues, $out);

        $out['dvalue']['this'] = $dinfo->fenlei($dinfo->makeListArray($allvalues, "doc_id", "id", $doc_id));

        $tmp = $dinfo->makeListArray($allvalues, "doc_id");

        foreach ($tmp as $key => $item) {
            $out['dvalue']['docid'][$key] = $dinfo->fenlei($item);
        }
        $doctodocmodel = $dinfo->getModelIdsByDocids(array_keys($out['dvalue']['docid']));
        foreach ($tmp as $key => $item) {
            $out['dvalue']['doc_mode'][$doctodocmodel[$key]][] = $dinfo->fenlei($item);
        }
        if ($ywid) {

            $doctodocmodel = $dinfo->getModelIdsByDocids(array_keys($out['dvalue']['docid']), $ywid);
            foreach ($tmp as $key => $item) {
                $out['dvalue']['thisyw']['doc_mode'][$doctodocmodel[$key]][] = $dinfo->fenlei($item);
            }
        }


        $out['docs']['all'] = array_map(function ($value) use ($out) {

            $value['dvalue'] = $out['dvalue']['docid'][$value['id']];
            return $value;
        }, (Array)$dinfo->getDocs());
        $out['docs']['byid'] = $dinfo->makeUniqueArray($out['docs']['all'], "id");





        $out['docs']['zj']['all'] = array_filter($out['docs']['all'] , function ($value) {



            return $value['file_type'] == 1;

        });

        $out['docs']['zj']['thisyw'] = array_filter($out['docs']['zj']['all'], function ( $value) use ($ywid) {

            return $value['exid'] == $ywid;

        });
        $out['docs']['zj']['byywid'] =  $dinfo->makeListArray( $out['docs']['zj']['all'] ,"exid");





        $allzhengjushenqing = $dinfo->getzhengjuall();
        if($allzhengjushenqing)
        {
            $out['zj_sq']['all'] = array_map(function ($value) use ($out) {

                $value['docs'] = [];
                foreach (explode(",", $value['zids']) as $zid) {
                    $value['docs'][] = $out['docs']['byid'][$zid];
                }
                return $value;

            }, (Array)$dinfo->getzhengjuall());


            $ywid && ($out['zj_sq']['this'] = $dinfo->makeUniqueArray($out['zj_sq']['all'], "id", $ywid));

        }
        
        $out['thisHuibiYwUser'] = $dinfo->getThisHuiBiYwUser();
        $out['thisHuibiYwUserRole'] = $dinfo->getThisHuiBiYwUserRole();
        $out['thisQuestionYwUser'] = $dinfo->getThisQuestionUser();
        $out['thisSQhuibiUser'] = $dinfo->getThisSQhuibiUser();



        $out['zcy'] = $dinfo->getzcy();


        //$out['docs']['add']


        // $out['dvalue']['thisyw']['doc_mode'][5][0]['uniquevalue'][''];

        $dinfo->getDossierInfo($out,$docinfo);


        $out['jigou'] = $dinfo->getJiGou($out['info']['zc_jg_id']);
        $out['piluVal'] = HuiBi::getPiluVal($docinfo['dossier_id']);


        $out['sqr_zj'] = Dz::getZhengJuListByRole($docinfo['dossier_id'], Constant::D_Role_ShenQingRen);
        $out['bsqr_zj'] = Dz::getZhengJuListByRole($docinfo['dossier_id'], Constant::D_Role_Bei_ShenQingRen);
        $out['sqr_question'] = QuestionExpand::getQuestionListByRole($docinfo['dossier_id'], Constant::D_Role_ShenQingRen);
        $out['bsqr_question'] = QuestionExpand::getQuestionListByRole($docinfo['dossier_id'], Constant::D_Role_Bei_ShenQingRen);


        $out['jg_user'] = $dinfo->getJigouUser($dinfo->dossier_id);

        $out['PlHbSm'] = $dinfo->getPiluHuibiShengming($dinfo->dossier_id, $uid);
        $out['test_question']['me'] = $dinfo->getTestQuestion(true, null, $uid);
        /*$out['test_question']['bsqr'] = $dinfo->getTestQuestion(false, Constant::D_Role_ShenQingRen);
        $out['test_question']['sqr'] = $dinfo->getTestQuestion(false, Constant::D_Role_Bei_ShenQingRen);*/


        $out['test_zhengjulist']['sqr'] = $dinfo->getZhengJuList(Constant::D_Role_ShenQingRen);
        $out['test_zhengjulist']['bsqr'] = $dinfo->getZhengJuList(Constant::D_Role_Bei_ShenQingRen);
        $out['test_zhengjulist']['sqrall'] = $dinfo->getZhengJuList(Constant::D_Role_ShenQingRen, true);
        $out['test_zhengjulist']['bsqrall'] = $dinfo->getZhengJuList(Constant::D_Role_Bei_ShenQingRen, true);


        $out['time'] = $dinfo->getTime();
        $out['cjs'] = $dinfo->getCaiJueData();


        //-----------------------------------------------裁决书----------------------------------------------------------
        $dabianidids = array_column((array)Db::name("dossier_defence")->field("idid")->where("dossier_id",$dinfo->dossier_id)->where("status",">=",2)->select(),"idid");
        $zhizhengidids = array_column((array)Db::name("dossier_question")->field("idid")->where("dossier_id",$dinfo->dossier_id)->where("status",">=",2)->select(),"idid");
        $zhengju = array_column((array)Db::name("dz")->field("idid")->where("dossier_id",$dinfo->dossier_id)->where("status",">=",2)->select(),"idid");


        foreach ($out['users']['role'][Constant::D_Role_Bei_ShenQingRen] as $k => $v){
            $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Num'] = self::num2Upper($v['r_no']);

            if(in_array($v['idid'],(array)$dabianidids)){
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_dabian'] = 1;
            }else{
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_dabian'] = 0;
            }


            if(in_array($v['idid'],(array)$zhizhengidids)){
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_zhizheng'] = 1;
            }else{
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_zhizheng'] = 0;
            }


            if(in_array($v['idid'],(array)$zhengju)){
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_tjzj'] = 1;
            }else{
                $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Is_tjzj'] = 0;
            }



            $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Dabian'] = Db::name("dossier_defence")->field("idid")->where("dossier_id",$dinfo->dossier_id)->where("idid",$v['idid'])->where("status",2)->value("matter");

            $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['Zhizheng'] = $dinfo->getZhizhengYiJian($v['idid']);

            $out['users']['role'][Constant::D_Role_Bei_ShenQingRen][$k]['zhengju'] = $dinfo->getZhengJuList($v['role'],false,$v['idid']);
        }


        foreach ($out['users']['role'][1] as $k => $v){
            if(in_array($v['idid'],(array)$zhizhengidids)){
                $out['users']['role'][1][$k]['Is_zhizheng'] = 1;
            }else{
                $out['users']['role'][1][$k]['Is_zhizheng'] = 0;
            }
            $out['users']['role'][1][$k]['Zhizheng'] = $dinfo->getZhizhengYiJian($v['idid']);
        }



        //--------------------------------------------------------------------------------------------------------------


        //关于签字
        $dinfo->getSign($out);

        if ($_GET['ahaha']==11){
            dump($ywid);
            dump(Db::name("dm")->where("id",Db::name("dr")->where("id",$dinfo->doc_id)->value("doc_model_id"))->value("view_file"));
        }

        Cache::tag($cacheGroupKey)->set($cacheKey,$out);
        return $out;


    }

    public function getThisSQhuibiUser(){
        $hbid = Db::name("dr")->where("id",$this->doc_id)->value("exid");

        $idid = Db::name("huibi")->where("id",$hbid)->value("idid");

        if(!$idid){
            return -1;
        }

        return Db::name("dossier_users")->where("idid",$idid)->find();
    }


    /*public function getZhengJu($idid){
        $zhengju = Db::name("dr")->where("dossier_id",$this->dossier_id)->where("idid",$idid)->where("file_type",1)->select();

        $out = '';
        foreach ($zhengju as $k => $v){

        }
    }*/


    public function getThisQuestionUser(){
        $qid = Db::name("dr")->where("id = ".$this->doc_id)->value("exid");

        $idid = Db::name("dossier_question")->where("id = '$qid'")->value("idid");

        if(!$idid){
            return -1;
        }

        $userInfo = Db::name("dossier_users")->where("idid = '$idid'")->find();

        return $userInfo;
    }


    public function getZhizhengYiJian($idid){
        $q_id = Db::name("dossier_question")->where("dossier_id",$this->dossier_id)->where("idid",$idid)->value("id");
        $question_list = Db::name("dossier_question_list")
            ->alias("dql")
            ->join("dr doc","dql.evidence_id = doc.id")
            ->field("dql.*,doc.z_no")
            ->where("dql.q_id",$q_id)
            ->select();

        $out = "";
        foreach ($question_list as $k => $v){
            if($v['legal'] != QuestionExpand::DEFULE_STR || $v['relation'] != QuestionExpand::DEFULE_STR || $v['reality'] != QuestionExpand::DEFULE_STR || $v['other'] != QuestionExpand::DEFULE_STR){
                $out .= "对证据".$v['z_no']."：（".$v['title']."）";
                //合法性
                if($v['legal'] != QuestionExpand::DEFULE_STR){
                    $out .= "的合法性：".$v['legal']."、";
                }

                //关联性
                if($v['relation'] != QuestionExpand::DEFULE_STR){
                    $out .= "的关联性：".$v['relation']."、";
                }

                //真实性
                if($v['reality'] != QuestionExpand::DEFULE_STR){
                    $out .= "的真实性：".$v['reality']."、";
                }

                //具体理由
                if($v['other'] != QuestionExpand::DEFULE_STR){
                    $out .= "的具体理由：".$v['other']."、";
                }
            }
        }




        $out = mb_substr($out,0,-1);
        return $out;
    }


    public static function num2Upper($num){

        $arr = array(
            0=>'零',
            1=>'一',
            2=>'二',
            3=>'三',
            4=>'四',
            5=>'五',
            6=>'六',
            7=>'七',
            8=>'八',
            9=>'九',
            10=>'十',
            11=>'十一',
            12=>'十二',
            13=>'十三',
            14=>'十四',
            15=>'十五',
            16=>'十六',
            17=>'十七',
            18=>'十八',
            19=>'十九',
            20=>'二十',
            21=>'二十一',
            22=>'二十二',
            23=>'二十三',
            24=>'二十四',
            25=>'二十五',
            26=>'二十六',
            27=>'二十七',
            28=>'二十八',
            29=>'二十九',
            30=>'三十',
            31=>'三十一',
        );
        $num = (int)$num;

        return $arr[$num];
    }




    //--------------------------------------------------裁决书-----------------------------------------------------------
    public function getCaiJueData(){
        $out = [];
        $out['zcyInfo'] = Db::name("arbitrator")
            ->alias("arb")
            ->join("court cou","arb.court_id = cou.id")
            ->field("arb.name,cou.addtime")
            ->where("arb.dossier_id",$this->dossier_id)
            ->order("arb.id desc")
            ->find();

        $out['zcyInfo']['addtime'] = date("Y年m月d日",$out['zcyInfo']['addtime']);



        return $out;
    }
    //------------------------------------------------------------------------------------------------------------------




    public function getzcy(){
        $zcy = Db::name("arbitrator")->where("dossier_id",$this->dossier_id)->limit(2)->order("id desc")->select();
        $out['old_zcy'] = $zcy[1]['name'];
        $out['new_zcy'] = $zcy[0]['name'];

        if(!$out['old_zcy']){
            $out['old_zcy'] = $out['new_zcy'];
        }

        if(!$out['new_zcy']){
            $out['new_zcy'] = $out['old_zcy'];
        }

        return $out;
    }

    public function getThisHuiBiYwUser(){
        $dr_exid = Db::name("dr")->where("id",$this->doc_id)->where("doc_model_id",$this->doc_model_id)->value("exid");

        $idid = Db::name("huibi")->where("id",$dr_exid)->value("idid");

        return Db::name("idcards")->where("id",$idid)->value("real_name");
    }

    public function getThisHuiBiYwUserRole(){
        $dr_exid = Db::name("dr")->where("id",$this->doc_id)->where("doc_model_id",$this->doc_model_id)->value("exid");

        $idid = Db::name("huibi")->where("id",$dr_exid)->value("idid");

        if(!$idid){
            return "-1";
        }

        return Db::name("dossier_users")->where("idid = ".$idid)->value("role");
    }



    public function getzhengjuall()
    {
        return Db::name("dz")->where("dossier_id", $this->dossier_id)->select();

    }


    public function getDocs()
    {
        return Db::name("dr")->whereIn("dossier_id", $this->dossier_id)->select();
    }


    public function getTime()
    {
        $time = Db::name("dossier_time")->where("id = " . $this->dossier_id)->find();

        foreach ($time as $k => $v) {
            $time[$k] = date("Y年m月d日", $v);
        }

        unset($time['id']);

        return $time;
    }

    public function getModelIdsByDocids($ids, $exid = null)
    {
        if ($exid) {
            return Db::name("dr")->whereIn("id", $ids)->where("exid", $exid)->column("id,doc_model_id");
        } else {
            return Db::name("dr")->whereIn("id", $ids)->column("id,doc_model_id");
        }

    }

    public function getDocsByExtid($id, $jusgids = true)
    {
        $list = Db::name("dr")->where("dossier_id", $this->dossier_id)->where("exid", $id)->select()->toArray();
        if ($jusgids) {
            return array_column($list, "id");
        } else {
            return $list;
        }
    }


    public function getZhengJuList($Drole = null, $is_all = false , $idid = null)
    {
        $map = [];
        $map['dossier_id'] = $this->dossier_id;
        $map['file_type'] = 1;

        $realuid = Db::name("dr")->where("id = " . $this->doc_id)->value("uid");
        $map['uid'] = $realuid;

        if($idid){
            $map['uid'] = $idid;
        }

        $map['c_qrole'] = $Drole;

        if ($is_all) {
            unset($map['uid']);
        }

        $list = Db::name("dr")->where($map)->field("name,z_no,des,file_num,uid")->select();

        $out = '';
        foreach ($list as $k => $v) {
            $out .= "证据" . $v['z_no'] . "、" . $v['name'] . "（共" . $v['file_num'] . "页），欲证明" . $v['des'] . "；<br>";
        }

        if ($out) {
            $out = mb_substr($out, 0, -5);
            $out .= "。";
        }

        if ($Drole == Constant::D_Role_Bei_ShenQingRen) {
            $model_id = Constant::DOC_model_bsqrzhengjulist;
        } else {
            $model_id = Constant::DOC_model_sqrzhengjulist;
        }

        $zjlist = Db::name("dr")->where("dossier_id = " . $this->dossier_id . " and c_qrole = '$Drole' and doc_model_id = " . $model_id)->find();

        $return['list'] = $out;
        $return['addtime'] = date("Y年m月d日", $zjlist['addtime']);
        $return['tjr'] = Db::name("idcards")->where("id", $realuid)->value("real_name");

        return $return;
    }


    public function getTestQuestion($is_me = false, $role = null, $uid = null)
    {
        if ($this->info["test_question"]) {
            return $this->info["test_question"];
        }

        $map = [
            "dq.dossier_id" => ['eq', $this->dossier_id],
        ];

        if ($is_me) {
            $map['dq.idid'] = ['eq', $uid];
        }

        if ($role == Constant::D_Role_ShenQingRen) {
            $map['docr.c_qrole'] = Constant::D_Role_ShenQingRen;
        }

        if ($role == Constant::D_Role_Bei_ShenQingRen) {
            $map['docr.c_qrole'] = Constant::D_Role_Bei_ShenQingRen;
        }

        $question = Db::name("dossier_question")
            ->alias("dq")
            ->join("dossier_question_list dql", "dql.q_id = dq.id")
            ->join("dr docr", "dql.evidence_id = docr.id")
            ->where($map)
            ->field("dql.evidence_id,dql.title,dql.legal,dql.relation,dql.reality,dql.other,docr.z_no,docr.des,docr.name,docr.file_num")
            ->select();

        $str = "";
        $str1 = "";

        foreach ($question as $k => $v) {

            if($v['other'] == QuestionExpand::DEFULE_STR && $v['reality'] == QuestionExpand::DEFULE_STR && $v['relation'] == QuestionExpand::DEFULE_STR && $v['legal'] == QuestionExpand::DEFULE_STR){

                //$str1 .= "对证据" . $v['z_no'] . "《" . $v['name'] . "》未发表质证意见。";
                $str .= $v['z_no']."、";

            }else{
                $str1 .= "对证据" . $v['z_no'] . "《" . $v['name'] . "》的";

                if ($v['reality'] != QuestionExpand::DEFULE_STR) {
                    $str1 .= "真实性：" . $v['reality'] . "、";
                }

                if ($v['relation'] != QuestionExpand::DEFULE_STR) {
                    $str1 .= "关联性：" . $v['relation'] . "、";
                }

                if ($v['legal'] != QuestionExpand::DEFULE_STR) {
                    $str1 .= "合法性：" . $v['legal'] . "、";
                }

                if ($v['other'] != QuestionExpand::DEFULE_STR) {
                    $str1 .= "其他：" . $v['other'] . "、";
                }

                $str1 = mb_substr($str1, 0, -1);
                $str1 .= "；" . "<br>";

            }
        }
        
        if ($str1) {
            $str1 = mb_substr($str1, 0, -5);
            $str1 .= "。";
        }

        //$str1 .= "<br>对证据".mb_substr($str,0,-1)."未发表质证意见。";

        if(input("wl")==888){
            dump($str1);
        }

        $testQuestion['zhizhengyijian'] = $str1;
        return $testQuestion;
    }

    //新增的代码
    public function getPiluHuibiShengming($d_id, $uid)
    {
        $data = Db::name("huibi")->where("dossier_id = '$d_id' and idid = '$uid'")->select();

        $out = [];
        foreach ($data as $k => $v) {
            switch ($v['type']) {
                case 1 :
                    $out['pilu'][] = ['value' => $v['value'], 'addtime' => date("Y年m月d日", $v['addtime'])];
                    break;
                case 2 :
                    $out['huibi'][] = ['value' => $v['value'], 'addtime' => date("Y年m月d日", $v['addtime'])];
                    break;
                case 3 :
                    $out['shengming'][] = ['value' => $v['value'], 'addtime' => date("Y年m月d日", $v['addtime'])];
                    break;
            }
        }

        return $out;
    }


    public function getJigouUser($d_id)
    {
        $jg_id = Dossier::getSimpleDossier($d_id)['zc_jg_id'];

        $jg_user = Db::name("jigou_admin")->where("th_id = '$jg_id'")->select();

        $out = [];
        foreach ($jg_user as $k => $v) {
            switch ($v['role']) {
                case 1 :
                    $out['zhuban'][] = ['real_name' => $v['name'], 'idid' => $v['idid']];
                    break; //主办
                case 2 :
                    $out['lianzhuren'][] = ['real_name' => $v['name'], 'idid' => $v['idid']];
                    break; //立案主任
                case 3 :
                    $out['shenpizhuren'][] = ['real_name' => $v['name'], 'idid' => $v['idid']];
                    break; //裁决主任
            }
        }

        return $out;
    }


    public function getSign(&$out)
    {
        $out['sign']['imgs'] = Dvalue::getListValueOfDoc($this->doc_id, "signer_code");
        $out['sign']['time'] = Dvalue::getUniqueValueOfDoc($this->doc_id, "signer_time");
        $out['sign']['time']['string'] = date("Y") . '&nbsp;年&nbsp;' . date('n') . '&nbsp;月&nbsp;' . date('j') . '&nbsp;日';


        
    }

    public function getDossierInfo(&$out,$docInfo)
    {
        $out['info'] = Dossier::getSimpleDossier($this->dossier_id);
        $out['info']['zno_str'] = DossierDoc::getZcNoByNo($out['info']['zno'], $out['info']['addtime']);
        if($docInfo['file_type']==3){
            $out['info']['zno_str_1'] = DossierDoc::getZcNoByNo($out['info']['zno'].'-'.$docInfo['z_no'], $out['info']['addtime'],true);
        }elseif($docInfo['file_type']==4){
            $out['info']['zno_str_1'] = DossierDoc::getZcNoByNo($out['info']['zno'], $out['info']['addtime'],true);
        }else{
            $out['info']['zno_str_1'] = '';
        }

    }

    public function getJiGou($zc_jg_id)
    {

        $out['info'] = Db::name('jigou')->where('id', $zc_jg_id)->find();
        $out['arbitrator'] = Dossier::getZhongcaiyuan($this->dossier_id);

        return $out;
    }


    public function fenlei($allvalues, &$out = array())
    {
        $out['uniquevalue'] = $this->makeUniqueArray($allvalues, "var_name", null, function ($value) {
            return \wslibs\wszc\Dvalue::getValue($value);
        });
        $out['morevalue'] = $this->makeListArray($allvalues, "var_name", "", null, function ($value) {
            return \wslibs\wszc\Dvalue::getValue($value);
        });
        return $out;
    }

    public function getUsers()
    {
        if ($this->info["users"]) {
            return $this->info["users"];
        }
        $users = \wslibs\wszc\Dossier::getDangShiRen($this->dossier_id, 0);
        $users_1 = $users;
        unset($users_1[0]);
        $bie_shenqingren = array_column($users_1, 'name');

        return $this->info["users"] = array_map(function ($value) use ($bie_shenqingren) {
            $value['sex'] = $this->get_sex($value['id_num']);

            if(!$value['nation']){
                $value['nation'] =  "汉族";
            }else{
                $value['nation'] = $value['nation'] . "族";
            }

            
            $value['birthday'] = $this->get_birthday($value['id_num']) . "出生";

            if ($value['type'] == 2 && $value['role'] == 1) {
                $value['job'] = "该银行行长";
            }

            if ($value['type'] == 1) {
                $value['html_show'] = '姓名:' . $value['name'] . '、性别:' . $this->get_sex($value['id_num']) . '、出生年月:' . $this->get_birthday($value['id_num']) . '、民族:' . "汉族" ./*$value['minzu'] .*//* '、工作单位:' . $value['company'] .*/
                    '、身份证号:' . $value['id_num'] . /*'、邮政编码:' . $value['code'] . */
                    '、联系方式:' . $value['phone'];
            } else {
                $value['html_show'] = $value['name'] /*. '、法定代表人名称:' . $value['f_name'] */. '、联系方式:' . $value['f_phone'] . '、法定代表人身份证号:' . $value['f_id_card'];
            }
            $value['bei_apply_peo'] = implode('、', $bie_shenqingren);
            return $value;
        }, $users);
    }

    private function get_sex($idcard)
    {
        if (empty($idcard)) return null;
        $sexint = (int)substr($idcard, 16, 1);

        return $sexint % 2 === 0 ? '女' : '男';
    }

    private function get_birthday($idcard)
    {
        if (empty($idcard)) return null;
        $bir = substr($idcard, 6, 8);
        $year = (int)substr($bir, 0, 4);
        $month = (int)substr($bir, 4, 2);
        $day = (int)substr($bir, 6, 2);
        return $year . "年" . $month . "月" . $day . '日';
    }

    public function getDvalues()
    {
        if ($this->info["dvalues"]) {
            return $this->info["dvalues"];
        }
        return $this->info["dvalues"] = \think\Db::name("drv")->where("dossier_id", $this->dossier_id)->select();

    }

    public function getFiles()
    {
        if ($this->info["dvalues"]) {
            return $this->info["dvalues"];
        }
        return $this->info["dvalues"] = \think\Db::name("drv")->where("dossier_id", $this->dossier_id)->select();

    }


    public function getDefences($status)
    {
        if ($this->info["defence"]

        ) {
            return $this->info["defence"];
        }
        return $this->info["defence"] = DefenceExpand::getDefenceList($this->dossier_id, $status);
    }


    public function getQuestion()
    {
        if ($this->info["question"]) {
            return $this->info["question"];
        }
        return $this->info["question"] = array_map(function ($value) {
            $value['items'] = array_map(function ($value) {
                $value['html_show'] = "对《{$value['title']}》其真实性{$value['reality']}、合法性{$value['legal']}、关联性{$value['relation']}";
                return $value;
            }, (array)Db::name("dossier_question_list")->where("q_id", $value['id'])->select());
            return $value;
        }, (Array)\think\Db::name("dossier_question")->where("dossier_id", $this->dossier_id)->select());
    }


    public function makeUniqueArray($array, $key, $keyvalue = null, $value_key_or_function = null)
    {
        $out = [];
        foreach ($array as $value) {
            if ($keyvalue) {
                if ($keyvalue == $value[$key]) {
                    return self::valueKeyOrFunction($value, $value_key_or_function);
                }
            } else
                $out[$value[$key]] = self::valueKeyOrFunction($value, $value_key_or_function);
        }
        return $out;
    }


    public function makeListArray($array, $key, $listindex = "", $keyvalue = null, $value_key_or_function = null)
    {
        $out = [];
        foreach ($array as $value) {
            if ($keyvalue) {
                if ($keyvalue == $value[$key]) {
                    if ($listindex) {
                        $out[$value[$listindex]] = self::valueKeyOrFunction($value, $value_key_or_function);
                    } else
                        $out[] = self::valueKeyOrFunction($value, $value_key_or_function);
                }
            } else {
                if ($listindex) {
                    $out[$value[$key]][$value[$listindex]] = self::valueKeyOrFunction($value, $value_key_or_function);
                } else
                    $out[$value[$key]][] = self::valueKeyOrFunction($value, $value_key_or_function);
            }
        }
        return $out;
    }


    public static function valueKeyOrFunction($value, $callable)
    {
        if (is_callable($callable)) {
            return $callable($value);
        } else {
            return $value;
        }
    }


 
}