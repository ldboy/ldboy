<?php
namespace app\admin\controller\dossier;

use app\admin\controller\Login;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\Constant;
use wslibs\wszc\Control;
use wslibs\wszc\Dcheck;
use wslibs\wszc\Dcp;
use wslibs\wszc\Ddocs;

use wslibs\wszc\Dmp;
use wslibs\wszc\Ds;
use wslibs\wszc\dtip\Dtip;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\LoginUser;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\User;

use think\Db;

use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;


use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;

use wslibs\wszc\Drole;


use wslibs\wszc\HuiBi;
use wslibs\wszc\Dvalue;


use wslibs\wszc\defence\DefenceExpand;


/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/2
 * Time: 下午5:01
 */
class Cp extends \app\common\controller\Backend
{

    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        parent::_initialize();
    }

    public function doclist()
    {
        $d_id = $this->request->param("id/d");

        $gids = explode(",", $this->request->param("gid"));

        $zgids = explode(",", $this->request->param("zgid"));
        $exid = (int)$this->request->param("exid");

        if (!$d_id || !$gids) {
            $this->error("参数错误");
        }


        if ($zgids) {
            foreach ($zgids as $key => $sgid) {
                $sgid = (int)$sgid;
                if ($sgid) {
                    Ddocs::initGroupFileFromSgid($gids[$key], $exid, $sgid);
                }
            }
        }

        $outlist = array();
        foreach ($gids as $gid) {
            list($title, $style) = array_values(Constant::getGroupInfo($gid));
            $is_add = Control::getAddZjButton($d_id, $gid);

            $outlist[] = ["title_tip"=>Dtip::getTip($d_id,$gid,$exid),"title" => $title, "tip" => "提示", "style" => $style, 'is_add' => $is_add, "list" => Ddocs::getFilesByGroup($d_id, $gid, $exid)];
        }


        if(input('wsw')==555){
            dump($outlist);
        }

        foreach ($outlist as $k => $v){
            foreach ($v['list'] as $key => $value){
                if($value['min_file_num'] == 0){
                    $v['list'][$key]['min_file_num_str'] = "";
                }else{
                    $v['list'][$key]['min_file_num_str'] = "（至少需要上传".$value['min_file_num']."个附件）";
                }
            }

            $outlist[$k] = $v;
        }









        $this->assign("list", $outlist);
        $this->assign("d_id", $this->request->param("id/d"));
        $this->assign("exid", $this->request->param("exid"));
        $this->assign("gid", $this->request->param("gid"));
        $this->assign("can_submit", Dcheck::checkSubmitFiles($d_id, $gids, $exid, User::getLoginUid()));

        $this->assign("btninfo", Dmp::getSubmitTip($d_id, $gids, $exid, User::getLoginUid()));
        return $this->fetch();
    }


    public function dmp()
    {
        $d_id = $this->request->param("id/d");

        $gids = array_filter(explode(",", $this->request->param("gid")));
        $exid = (int)$this->request->param("exid");
        $errMsg = '';
        Db::startTrans();
//        if (Dmp::doSubmit($d_id, $gids, $exid, User::getLoginUid(), $errMsg)) {
//            Db::commit();
//
//            $this->success("成功", url('dossier.info/index', ['id' => $d_id ]) , [ 'alert' => 1 , 'wsreload' => 4]);
//        } else {
//            Db::rollback();
//            $this->error("失败[$errMsg]");
//        }
    }

    public function add()
    {
        $this->assign("title", "申请仲裁");
        $d_id = $this->request->param('dossier_id/d');

        $dossierInfo = Dossier::getSimpleDossier($d_id);

        if($dossierInfo['status']==1 && $dossierInfo['sub_status']>10){

            header('Location: '.url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_shenqing, "exid" => 0)).'');
            exit;

        }else if($dossierInfo['status']>1){
            header('Location: '.url('dossier.info/index', array("id" => $d_id)).'');

            exit;
        }

        if ($this->request->isPost()) {


            $postdata = $this->request->post('row/a');

            if(!((int)$postdata['dispute_money'])){
                $this->error("金额不能为空");
            }
            if ($postdata['dossier_id']) {
                $editdata['zctitle'] = $postdata['zctitle'];
                $editdata['zhongcai'] = $postdata['zhongcai'];
                $editdata['request'] = $postdata['request'];
                $editdata['reasons'] = $postdata['reasons'];
                $editdata['dispute_money'] = $postdata['dispute_money'];

                $re = Dcp::editDossier($postdata['dossier_id'], $editdata);


                $this->success("", url('dossier.cp/doclist', array("id" => $postdata['dossier_id'], "gid" => Constant::FILE_GROUP_shenqing, "exid" => 0)));

                exit;


            }


        }

        if ($d_id) {
            $Users = Dossier::getDangShiRen($d_id, 2);
            $sqr_user = Dossier::getDangShiRen($d_id, 1);
            $middle = Dcp::getRow($d_id, $sqr_user, $Users);
            $this->assign("is_zc", 1);

        }


        $zhongCaiList = Db::name('jigou')->order('id desc')->column('name', 'id');

        $this->assign('zhongcailist', $zhongCaiList);
        $this->assign("bsqr_num", count($Users) ? count($Users) : 1);
        $this->assign('row', $middle);

        $this->assign('dossier_id', $d_id);
        return $this->fetch('wsdoc/applydoc/apply');
    }

    public function defence()
    {


        $defence_id = (int)$this->request->param("id/d");

        if (!$defence_id) {
            $this->error("cowu ");
        }
        /*dump($d_id);dump($this->getCurentUserId());die;*/

        $defence_info = Db::name("dossier_defence")->where("id = '$defence_id' ")->where("idid = " . $this->getCurentUserId())->find();
        if (!$defence_info) {
            $this->error("cowu ");
        }

        if ($defence_info['status'] >= 2) {
            $this->error("yitijiao");
        }

        /*dump($defence_info);*/


        if ($defence_info['status'] == 1) {
            //$this->error("你已在此卷宗答辩过，可在答辩申请中查看详情",url("dossier.info/index",['id' => $d_id]));
            $this->redirect(url('dossier.cp/doclist', array("id" => $defence_info['dossier_id'], "gid" => Constant::FILE_GROUP_dabian, "exid" => $defence_info['id'])));
        }

        if ($this->request->isPost()) {


            $data = $this->request->param("row/a");
            Db::startTrans();
            $re = DefenceExpand::submitDefence($defence_id, $this->getCurentUserId(), $data['matter'], $data['reason']);
            $defence_id = DefenceExpand::getCurrentDefenceId($defence_info['dossier_id']);

            if ($re) {
                Db::commit();
                $this->success("成功", url('dossier.cp/doclist', array("id" => $defence_info['dossier_id'], "gid" => Constant::FILE_GROUP_dabian, "exid" => $defence_id)));
            } else {
                Db::rollback();
                $this->error("失败");
            }

        } else {

            $this->use_form_Js();
            $this->assign("dossier_id", $defence_info['dossier_id']);
            return $this->fetch("defence/form");
        }
    }


    public function question()
    {

        $qid = $this->request->param("qid/d");
        if ($this->request->isPost()) {
            $data = $this->request->post("row/a");

            if (!$data['legal'] && !$data['relation'] && !$data['reality'] && !$data['other']) {
                $this->error("请至少填写一项");
            }

            $dossier_id = $data['dossier_id'];
            $evidence_id = $data['evidence_id'];

            $re = Dcp::doQuestion($qid, $dossier_id, $this->getCurentUserId(), $evidence_id, $data);

            if ($re) {
//                $this->success("质证成功", url('dossier.cp/doclist', array("id" => $dossier_id, "gid" => Constant::FILE_GROUP_zhizheng, "exid" => $evidence_id)));
                $this->success("质证成功",'',['wsreload'=>1,'alert'=>1]);//,url('wsdoc.show/viewquestion',['qid'=>$qid,'ref'=>''])






            } else {
                $this->error("质证失败");
            }

        } else {

            $dossier_id = input("id", "", "intval");
            $evidence_id = input("zid", "", "intval");
            if (!$dossier_id || !$evidence_id) {
                $this->error("参数错误");
            }
//            $questionInfo = QuestionExpand::getMy($dossier_id);
//            if($questionInfo){
//                if($questionInfo['status']==1){
//                    $this->error('你已经提交过质证，请进行下一步操作',url('dossier.cp/doclist',['id'=>$dossier_id,'gid'=>7,'exid'=>$questionInfo['id']]));
//                }elseif($questionInfo['status']==2){
//                  $this->error('你已经质证过了，请勿重复操作',url('dossier.info/index',['id'=>$dossier_id]));
//                }
//            }

            $QuestionDateil = QuestionExpand::getMyDateil($dossier_id, $evidence_id);

            if (input("sy") == 1) {
                dump($QuestionDateil);
            }


            $this->assign("row", $QuestionDateil);
            $this->assign("dossier_id", $dossier_id);
            $this->assign("evidence_id", $evidence_id);

            $this->use_form_Js();
            return $this->fetch("question/index");
        }
    }

    // dossier.cp/questionFinish
    public function questionfinish()
    {

        $qid = $this->request->param("qid/d");

        $dinfo = Db::name("dossier_question")->where("id = '$qid'")->find();
        $d_id = $dinfo['dossier_id'];
        if ($dinfo['status'] == 1) {
            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_zhizheng, "exid" => $qid)));
            //$this->success('提交成功', url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_zhizheng, "exid" => $qid)));
        }


        $res = Dcp::doQuestionFinish($qid, $d_id, $this->getCurentUserId());

        if ($res) {
            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_zhizheng, "exid" => $qid)));
            //$this->success('提交成功', url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_zhizheng, "exid" => $qid)));
        } else {
            $this->error('提交失败');
        }
    }

    public function getCurentUserId()
    {
        return User::getLoginUid();
    }


    //组庭
    public function courtinit()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("name")->varTitle("选择仲裁员")->inputType(InputType::select)->itemArrValName("zcyList")->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/court");
    }

    public function zuting()
    {
        $id = $this->request->param("id/d");
        $find = Db::name("court")->where("id = '$id'")->find();
        if ($find['status'] == 2) {

            if ($find['is_again']) {

                if (!$this->request->isPost()) {

                    return $this->fetch();
                } else {
                    $postdata = $this->request->post("row/a");
                    Dvalue::saveUniqueValueByDocMode($find['dossier_id'], Constant::DOC_model_zutingtzs, "AgainReason", $postdata['reason'], $id);
                    Dvalue::saveUniqueValueByDocMode($find['dossier_id'], Constant::DOC_model_zutingtzs, "is_again", 1, $id);
                    $this->redirect(url('dossier.cp/doclist', array("id" => $find['dossier_id'], "gid" => Constant::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan . ',' . Constant::FILE_GROUP_zuting, "exid" => $find['id'])));
                }

            } else $this->redirect(url('dossier.cp/doclist', array("id" => $find['dossier_id'], "gid" => Constant::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan . ',' . Constant::FILE_GROUP_zuting, "exid" => $find['id'])));
        } else {
            $this->error("错误");
        }
    }


    public function court()
    {
        $d_id = $this->request->param("id/d");
        $again = (int)$this->request->param("again/d");

        $gid = Constant::FILE_GROUP_zhidingzhongcaiyuan;
        $find = Db::name("court")->where("dossier_id = '$d_id' and  status<>0")->order("id desc")->find();


        if (!$again) {

            if ($find['status'] == 1) {
                $this->success("已经组庭了，但尚未完善组庭资料", url('dossier.cp/doclist', array("id" => $d_id, "gid" => $gid, "exid" => $find['id'])));
            }
        }


        if ($this->request->isPost()) {


            $postdata = $this->request->post("row/a");


            if ($find) {
                if ($again == 1) {

                    Db::name("arbitrator")->where("dossier_id = '$d_id' and court_id={$find['id']} ")->update(["status" => 0]);
                    Db::name("court")->where("id = '{$find['id']}'")->update(['status' => 0]);

                    Db::name("huibi")->where("court_id = '{$find['id']}'")->update(['is_valid' => 0]);


                    DossierLog::addLog($d_id, $this->getCurentUserId(), LoginUser::getUserName(), DossierLog::LOG_TYPE_SET_UP_COURT);


                }
            }


            $zcy_id = $postdata['name'];


            $re = Dcp::zuting($d_id, $zcy_id, $again);


            if ($re['code'] == 1) {

                if ($again) {
                    // Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_zutingtzs, "AgainReason", $postdata['reason'], $re['id']);
                    // Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_zutingtzs, "is_again", 1, $re['id']);
                    //Dvalue::saveUniqueValueToDossier()

                }
                $this->redirect( url('dossier.cp/doclist', array("id" => $d_id, "gid" => $gid, "exid" => $re['id'])));

            } else {
                $this->error($re['msg']);
            }
        } else {
            $zcyList = Db::name("jigou_zcy")
                ->field("zc.name,zc.id")
                ->alias("jz")
                ->join("zcy zc", "jz.zcy_id = zc.id")
                ->selectOfIndex("zc.id");

            /*$this->assign("d_id",$d_id);*/


            $this->assign("is_again", $again);
            $this->assign("zcyList", $zcyList);

            return $this->fetch();
        }
        /*dump($zcyList);*/
    }


    //披露、申请回避
    public function huibiinit()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("hb_value")->varTitle("请填写仲裁员回避申请理由")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/huibi");
    }

    public function piluinit()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("pl_value")->varTitle("请填写自行披露理由")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/pilu");
    }

    public function Huibi()
    {
        $id = $this->request->param("id/d");


        $info = HuiBi::getHuiBiInfo($id);
        $d_id = $info['dossier_id'];

        if ($info['status'] == 1) {
            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_huibi, "exid" => $id)));
        }

        if ($this->request->isPost()) {
            $postData = $this->request->post("row/a");

            $re = HuiBi::subtoForm($id, $postData);

            if ($re) {
                $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_huibi, "exid" => $id)));
            } else {
                $this->error("失败");
            }
        } else {
            return $this->fetch();
        }
    }

    public function Pilu()
    {
        $id = $this->request->param("id/d");



        $piluInfo = HuiBi::getHuiBiInfo($id);
        $d_id = $piluInfo['dossier_id'];


        if ($piluInfo['status'] == 1) {
            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_pilu, "exid" => $id)));
        }


        if ($this->request->isPost()) {
            $postData = $this->request->post("row/a");
            var_dump($postData);die;
            $postData['hb_value'] = $postData['pl_value'];
            $re = HuiBi::subtoForm($id, $postData);

            if ($re) {
                $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_pilu, "exid" => $id)));
                /*$this->success("成功", url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_pilu, "exid" => $id)));*/
            } else {
                $this->error("失败");
            }
        } else {
            return $this->fetch();
        }
    }


    public function shenming()
    {
        $id = $this->request->param("id/d");


        $piluInfo = HuiBi::getHuiBiInfo($id);
        $d_id = $piluInfo['dossier_id'];


        if ($piluInfo['status'] == 0) {
            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_shengming, "exid" => $id)));
        } else {
            $this->error("您已经声明过，不可重复操作");
        }
    }

    public function CaiJueinit()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("cj_value")->varTitle("请输入裁决内容")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/caijue");
    }


    public function statement()
    {
        $d_id = $this->request->param("id/d");

        if (Db::name("huibi")->where("type = " . HuiBi::TYPE_PILU . " and dossier_id = '$d_id' and idid = " . $this->getCurentUserId())->find()) {
            $this->error("您已经披露过，不可声明");
        } elseif (Statement::getMyStatement($d_id)) {
            $this->error("您已声明过，不可重复");
            //$this->success("即将跳转",url("dossier.cp/doclist",['id'=> $d_id , "gid" => Constant::FILE_GROUP_shengming]));
        } else {
            echo 1111;
            //header("loction : ".url("dossier.cp/doclist",['id'=> $d_id , "gid" => Constant::FILE_GROUP_shengming , "ref" => "addtabs"]));
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_shengming]));

        }
    }

    public function CaiJue()
    {
        $d_id = $this->request->param("id/d");

        $info = Dcaijue::getCaiJueInfo($d_id);
        if ($info['c_status'] > 4) {
            if ($info['c_status'] == 5)
                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue_fasongzhizhuren, "exid" => 0]));
            if ($info['c_status'] == 6)
                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue_shenpi . "," . Constant::FILE_GROUP_caijue_fasongsuoyouren, "exid" => 0]));
        }

        if ($this->request->isPost()) {
            $dataPost = $this->request->Post("row/a");

            Dcaijue::updateNeirong($d_id, $dataPost);

            $this->success("保存成功.",url('dossier.cp/caijue',['id'=>$d_id]),['alert'=>1]);

        } else {


            $this->assign('row', Dcaijue::getNeirong($d_id));
            $this->assign('did', $d_id);
            list($btn, $btn1, $btn2) = Dcaijue::getLoginUserBtn($d_id);
            $this->assign("btn1", $btn1);
            $this->assign("btn2", $btn2);
            $this->assign("cjsurl",Dcaijue::getCaiJueShuUrl($d_id));
            if(input('wl')==11){
                dump(Dcaijue::getCaiJueShuUrl($d_id));
            }

            $this->use_form_Js();
            return $this->fetch("caijue");
        }
    }

    // 发送至仲裁委主办
    public function caijuesend()
    {
        $d_id = $this->request->param("id/d");
        $info = Dcaijue::getCaiJueInfo($d_id);
        if ((!$this->request->isPost())) {

            if ($info['c_status'] == 2 || $info['c_status'] == 3) {
                return $this->fetch("caijuesend" . $info['c_status']);
            }
        }

        if ($this->request->isPost()) {
            $dataPost = $this->request->Post("row/a");
            $key = "shenpi_status_" . $info['c_status'];
            Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_caijueshenpi, $key, $dataPost['value']);
            if ($info['c_status'] == 3) {


                if (isset($dataPost['ok']) && !$dataPost['ok']) {


                    Dcaijue::updateStatus($d_id, 2);
                    $this->success("操作成功",url("dossier.info/index",array("id"=>$d_id)));


                }
            }
        }


       $res =  Dcaijue::autoSend($d_id);
        if($res){
            $this->success("操作成功",url("dossier.info/index",array("id"=>$d_id)));
        }else{
            $this->error("操作失败",url("dossier.info/index",array("id"=>$d_id)));
        }
    }

    // 案件审批意见
    public function anjianshenpi()
    {

    }

    // 预览裁决书
    public function view()
    {
        // http://192.168.0.7/zhongcai/index.php/admin/wsdoc.show/index?docid=1762
        $did = $this->request->param('did/d');
        if (!$did) {
            $this->error('参数错误');
        }
        $this->redirect(url('wsdoc.show/index', ['docid' => '']));
    }

    public function addElseZJ()
    {

    }

    public function initView()
    {
        $form = new WsForm();

        //  $form->setMultiUrl("dossier.cp/addElseZJ");
        //$form->setAddUrl("dossier/cp/addElseZJ");


        $form->addItem((new Item())->required(true)->varName("name")->varTitle("证据名称")->inputType(InputType::text));
        $form->addItem((new Item())->required(true)->varName("proof")->varTitle("证明事项")->inputType(InputType::textarea));

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/addzj");
    }

    public function addzj()
    {


        $gid = (int)$this->request->param("gid");//['gid'];
        $exid = (int)$this->request->param("exid");;//(int)$row['exid'];;
        $d_id = (int)$this->request->param("id");//(int)$row['id'];

        if (!($gid && $d_id)) {
            $this->error("参数错误");
        }

        if ($this->request->isPost()) {


            $row = $this->request->param('row/a');
            $name = $row['name'];
            $proof = $row['proof'];


            if (!$name || !$proof) {
                $this->error('名称或事项不能为空');
            }


            $res = Ddocs::addZhengjuFile($d_id, $gid, $name, $proof, $exid);
            if (!$res) {
                $this->error('添加失败');
            }


            $this->success('成功', "", array("alert" => 1, "wsreload" => 1));
        } else {
            $this->use_action_js();
            return $this->fetch();
        }

    }


    public function initspshouli()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("shenpiyijian")->varTitle("部门负责人意见")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/cp/spshouli");
    }


    public function spshouli()
    {
        $d_id = $this->request->param("id/d");
        /*if ($this->request->isPost()) {*/
            $postData = $this->request->post("row/a");
            $re = Dcp::lianshenpi($d_id, $postData);

            if ($re) {
                $this->success("成功",url('dossier.cp/doclist',['id' => $d_id, 'gid' => Constant::FILE_GROUP_lianshenpi_zhuren, 'exid' => '']));
            } else {
                $this->error("失败");
            }
        /*} else {
            $doc_id = Db::name("dr")->where("dossier_id = '$d_id' and doc_model_id = ".Constant::DOC_model_lianshenpibiao)->value("id");
            $data = Dvalue::getUniqueValueOfDoc($doc_id,"zhurenShouliyijian");

            $this->assign("zhurenShouliyijian",$data);
            $this->use_form_Js();
            return $this->fetch();
        }*/
    }


    public function adminquestion()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $info =  QuestionExpand::getOne($qid);
        $d_id = $info["dossier_id"];

        if ($ok == 1) {
            $zids = Ddocs::getFilesByGroup($d_id, Constant::FILE_GROUP_zhizheng, $qid);
            Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_zhizhengzhuanfa,$qid,array_column($zids,'id'));
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_zhizhengzhuanfa, 'exid' => $qid]));
        }

        $re = QuestionExpand::AdminChuLiQues($qid, $ok);

        if ($re) {
            $this->success("拒绝成功");
        } else {
            $this->error("拒绝失败");
        }
    }

    public function admindabian()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $defenceInfo = DefenceExpand::getDefenceFind($qid);
        $d_id = $defenceInfo['dossier_id'];


        if ($ok == 1) {
            Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_dabian_zhuanfa,$qid,$defenceInfo['zids']);
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_dabian_zhuanfa, 'exid' => $qid]));
        }

        $re = DefenceExpand::shouLiOrJuJue($qid, $ok);

        if ($re) {
            $this->success("拒绝成功");
        } else {
            $this->error("拒绝失败");
        }
    }

    public function adminhuibipilu()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $info = HuiBi::getOne($qid);
        $d_id = $info["dossier_id"];

        if ($ok == 1) {

            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => $info['type'] == 3 ? Constant::FILE_GROUP_shenming_zhuanfa : Constant::FILE_GROUP_pilu_zhuanfa, 'exid' => $qid]));
        }

        $re = DefenceExpand::shouLiOrJuJue($qid, $ok);

        if ($re) {
            $this->success("拒绝成功");
        } else {
            $this->error("拒绝失败");
        }
    }


    public function tijiaozhengju()
    {
        $did = $this->request->param("id/d");

        if (!$did) $this->error("错误");

        $zinfo = Dz::getShenQingDzInfo($did, LoginUser::getIdid(), LoginUser::getUserName());


        $this->redirect(url("dossier.cp/doclist", ['id' => $did, 'gid' => Constant::FILE_GROUP_tijiaozhengju, 'exid' => $zinfo['id']]));
    }

    public function adminzhengju()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $zinfo = Dz::getDzInfo($qid);
        $d_id = $zinfo['id'];

        if ($ok == 1) {
            $this->redirect(url("dossier.cp/doclist", ['id' => $zinfo['dossier_id'], 'gid' => Constant::FILE_GROUP_zhengju_zhuanfa, 'exid' => $zinfo['id'],"zgid"=>$zinfo['sgid']]));
        }

        $re = Dz::shouLiOrJuJue($qid, $ok);

        if ($re) {
            $this->success("拒绝成功");
        } else {
            $this->error("拒绝失败");
        }
    }
}