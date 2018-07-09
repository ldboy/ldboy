<?php
namespace app\admin\controller\dossier;

use app\admin\controller\Login;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\Constant;
use wslibs\wszc\Control;
use wslibs\wszc\Dcheck;
use wslibs\wszc\Dcp;
use wslibs\wszc\Ddocs;

use wslibs\wszc\DInfoValue;
use wslibs\wszc\Dmp;
use wslibs\wszc\DocContract;
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
        $is_phone = $this->request->param("is_phone/d");

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

        $cuid = LoginUser::getIdid();
        $outlist = array();
        foreach ($gids as $gid) {
            list($title, $style) = array_values(Constant::getGroupInfo($gid));
            $is_add = in_array(Constant::DOC_model_qitazhengju, Constant::getGroupFileMode($gid)) || in_array($gid, Constant::getNeedZhengjuGroup());
            $doclist = Ddocs::getFilesByGroup($d_id, $gid, $exid);

            if(input('wt')==3){
                dump($doclist);
            }

            if ($is_add) {

                foreach ($doclist as $value) {
                    if ($value['file_type'] == 1 && ($value['uid'] != $cuid)) {
                        $is_add = false;
                        break;
                    }
                }
            }


            $outlist[] = [
                'proposal'=>Dtip::getProposal($d_id, $gid, $exid),
                "title_tip" => Dtip::getTip($d_id, $gid, $exid,'',$is_phone),
                "title" => $title,
                "tip" => "提示",
                "style" => $style,
                'is_add' => $is_add,
                "list" => $doclist
            ];
        }


        if (input('wsw') == 555) {
            dump($outlist);
        }

        foreach ($outlist as $k => $v) {
            foreach ($v['list'] as $key => $value) {
                if ($value['min_file_num'] == 0) {
                    $v['list'][$key]['min_file_num_str'] = "";
                } else {
                    $v['list'][$key]['min_file_num_str'] = "（至少需要上传" . $value['min_file_num'] . "个附件）";
                }
            }

            $outlist[$k] = $v;
        }


        if(input('lee')==123){
            dump(Dcheck::checkSubmitFiles($d_id, $gids, $exid, User::getLoginUid()));

        }


        $this->assign("list", $outlist);
        $this->assign("is_phone", $is_phone);
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
        $is_phone = $this->request->param("is_phone/d");

        $gids = array_filter(explode(",", $this->request->param("gid")));
        $exid = (int)$this->request->param("exid");
        $errMsg = '';

        if (Dmp::doDmp($d_id, $gids, $exid, $errMsg)) {

            $is_phone ==1 ?$this->success('成功',url('wechat.myinfo/daohang',['did'=>$d_id,'idid'=>LoginUser::getIdid()])) :
            $this->success("成功", url('dossier.info/index', ['id' => $d_id]), ['alert' => 1, 'wsreload' => $this->request->param("auto")?0:0]);
        } else {

            $this->error("失败[$errMsg]");
        }
    }


    public function add()
    {
        $this->assign("title", "申请仲裁");
        $d_id = $this->request->param('dossier_id/d');
        if (!$d_id) {
            $d_id = $this->request->post('row/a')['dossier_id'];
        }

        $dossierInfo = Dossier::getSimpleDossier($d_id,true);


        if ($dossierInfo['status'] == 1 && $dossierInfo['sub_status'] > 10) {

            header('Location: ' . url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_shenqing, "exid" => 0)) . '');
            exit;

        } else if ($dossierInfo['status'] != 1) {

            $this->redirect(url('dossier.info/index', array("id" => $d_id)));

            exit;
        }

        if ($this->request->isPost()) {



            $postdata = $this->request->post('row/a');


            
            if ($postdata['dossier_id']) {
//                $editdata['zctitle'] = $postdata['zctitle'];
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

            $str = '';

            if(count($Users)>1){
                foreach ($Users as $k=>$v){

                    $str .= '第'.DInfoValue::num2Upper($v['r_no']).'被申请人：'.$v['name'].';';
                }

                $str = rtrim($str,';');
                $str = $str.'。';
            }



            if(input('lee')==32){
                dump($str);
            }


            $this->assign('str',$str);
            $sqr_user = Dossier::getDangShiRen($d_id, 1);
            $middle = Dcp::getRow($d_id, $sqr_user, $Users);
            $this->assign("is_zc", 1);

        }


        $zhongCaiList = Db::name('jigou')->where('status',1)->order('id desc')->column('name', 'id');

        $this->assign('zhongcailist', $zhongCaiList);
        $this->assign("bsqr_num", count($Users) ? count($Users) : 1);
        $this->assign('row', $middle);

        if(input("sy")==881){
            dump($middle);
        }

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
            $doc_id = Ddocs::getOrInitFile($defence_info['dossier_id'],Constant::DOC_model_dabianshu, $defence_id)['id'];

            $proposal = Dvalue::getUniqueValueOfDoc($doc_id,'defence_proposal',$defence_id);
            $this->assign('proposal',$proposal);
            $this->assign("dossier_id", $defence_info['dossier_id']);
            $this->assign("sample", DefenceExpand::getSample($defence_id));
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
                $this->success("质证成功", '', ['wsreload' => 1, 'alert' => 1]);//,url('wsdoc.show/viewquestion',['qid'=>$qid,'ref'=>''])


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

            $QuestionDateil = QuestionExpand::getMyDateil($qid, $evidence_id);

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


        $arr = explode(",", $dinfo['zids']);


        $list = array_map(function ($value) {
            $value['real_name'] = User::getUserInfoByIdid($value['uid'])['real_name'];
            $value['imgs'] = Dvalue::getDocFiles($value['id'], false);
            return $value;
        }, Ddocs::getFilesByDocIds($arr));

        //dump($list);die;


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
                    $this->redirect(url('dossier.cp/doclist', array("id" => $find['dossier_id'], "gid" => Constant::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan . ',' . Constant::FILE_GROUP_zuting_again, "exid" => $find['id'])));
                }

            } else {
                $this->redirect(url('dossier.cp/doclist', array("id" => $find['dossier_id'], "gid" => Constant::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan . ',' . Constant::FILE_GROUP_zuting, "exid" => $find['id'])));
            }
        } else {
            $this->error("错误");
        }
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
            $box = $this->request->post('liyou_box/a');
            $liyou = $this->request->post("liyou/a");

            $last = array_pop($liyou);
            $str = "";
            foreach ($box as $key => $value) {
                if ($value == "on") $str .= $liyou[$key]."、";
            }

            $last ? $postData['hb_value'] = $str.$last : $postData['hb_value'] = mb_substr($str,0,-1);

            $re = HuiBi::subtoForm($id, $postData);
            
            if ($re) {

                Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_huibishenqing, "huibi_value", $postData['hb_value'], $id);
                //Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_huibishenqing, "liyou", $liyou, $id);
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
            $box = $this->request->post('liyou_box/a');
            $liyou = $this->request->post("liyou/a");

            $last = array_pop($liyou);
            $str = "";
            foreach ($box as $key => $value) {
                if ($value == "on") $str .= $liyou[$key]."、";
            }

            $last ? $postData['hb_value'] = $str.$last : $postData['hb_value'] = mb_substr($str,0,-1);
            
            $re = HuiBi::subtoForm($id, $postData);

            if ($re) {

                Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_pilushu, "pilu_value", $postData['hb_value'], $id);
                //Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_pilushu, "liyou", $liyou, $id);
                $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_pilu, "exid" => $id)));
                /*$this->success("成功", url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_pilu, "exid" => $id)));*/
            } else {
                $this->error("失败");
            }
        } else {
            $doc_id = Ddocs::getOrInitFile($d_id, Constant::DOC_model_pilushu, $id)['id'];
            $proposal = Dvalue::getUniqueValueOfDoc($doc_id,'piluhuibi_proposal',$id);
            $this->assign('proposal',$proposal);
            return $this->fetch();
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

        $postData = $this->request->post("row/a");
        $re = Dcp::lianshenpi($d_id, $postData);
      //  $re1 = Dossier::changeStatus($d_id, 3);



        

        if ($re ) {



            

            $rurl = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id,Constant::DOC_model_lianshenpibiao,0)['id'].",".Ddocs::getOrInitFile($d_id,Constant::DOC_model_tongzhishu,0)['id'],Constant::mkDmpUrl($d_id,"2,13",0));

            $this->success("", $rurl, ['alert' => 0, 'wsreload' => 1,"dialog"=>1,"dialogtitle"=>"请稍等"]);
        } else {
            $this->error("失败");
        }

    }

    /*public function spshouli()
    {
        $d_id = $this->request->param("id/d");
        if ($this->request->isPost()) {
            $postData = $this->request->post("row/a");
            $re = Dcp::lianshenpi($d_id, $postData);

            if ($re) {
                $this->success("成功",url('dossier.cp/doclist',['id' => $d_id, 'gid' => Constant::FILE_GROUP_lianshenpi_zhuren, 'exid' => '']));
            } else {
                $this->error("失败");
            }
        } else {
            $doc_id = Db::name("dr")->where("dossier_id = '$d_id' and doc_model_id = ".Constant::DOC_model_lianshenpibiao)->value("id");
            $data = Dvalue::getUniqueValueOfDoc($doc_id,"zhurenShouliyijian");

            $this->assign("zhurenShouliyijian",$data);
            $this->use_form_Js();
            return $this->fetch();
        }
    }*/


    public function adminquestion()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $is_phone = $this->request->param("is_phone/d");
        $info = QuestionExpand::getOne($qid);
        $d_id = $info["dossier_id"];

        if ($ok == 1) {
            $zids = Ddocs::getFilesByGroup($d_id, Constant::FILE_GROUP_zhizheng, $qid);
            Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_zhizhengzhuanfa, $qid, array_column($zids, 'id'));
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_zhizhengzhuanfa, 'exid' => $qid,'is_phone'=>$is_phone]));
        }

        if($this->request->isPost()){
            $data = $this->request->post('row/a');

            Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_zhizhengyijian,'question_proposal',$data['proposal'],$qid);
            $doc_id = Ddocs::getOrInitFile($d_id,Constant::DOC_model_zhizhengyijian,$qid);
            $re = QuestionExpand::AdminChuLiQues($qid, $ok,$doc_id);
            if ($re) {
                $data['is_phone'] == 1 ? $this->success('操作成功',url('admin/wechat.myinfo/zhizheng',['did'=>$d_id,'idid'=>LoginUser::getIdid()])) :
                $this->success("操作成功",url('dossier.info/index',['id'=>$d_id]));
            } else {
                $this->error("操作失败");
            }
        }else{
            $doc_id = Ddocs::getOrInitFile($d_id,Constant::DOC_model_zhizhengyijian, $qid)['id'];
            $proposal = Dvalue::getUniqueValueOfDoc($doc_id,'question_proposal',$qid);
            $this->assign('proposal',$proposal);
            $this->assign('is_phone',$is_phone);
            $this->use_form_Js();
           return $this->fetch('dossier/subinfo/proposal');
        }
    }

    public function admindabian()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $is_phone = $this->request->param("is_phone/d");
        $defenceInfo = DefenceExpand::getDefenceFind($qid);
        $d_id = $defenceInfo['dossier_id'];


        if ($ok == 1) {
            Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_dabian_zhuanfa, $qid, $defenceInfo['zids']);
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_dabian_zhuanfa, 'exid' => $qid,'is_phone'=>$is_phone]));
        }

        if($this->request->isPost()){
            $data = $this->request->post('row/a');
            Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_dabianshu,'defence_proposal',$data['proposal'],$qid);
            $docId = Ddocs::getOrInitFile($d_id,Constant::DOC_model_dabianshu,$qid);
            $re = DefenceExpand::shouLiOrJuJue($qid, $ok,$docId);
            if ($re) {
                $data['is_phone'] == 1 ? $this->success('操作成功',url('admin/wechat.myinfo/dabian',['did'=>$d_id,'idid'=>LoginUser::getIdid()])) :

                    $this->success("操作成功",url('dossier.info/index',['id'=>$d_id]));
            } else {
                $this->error("操作失败");
            }
        }else{
//            $doc_id = Ddocs::getOrInitFile($d_id,Constant::DOC_model_dabianshu, $qid)['id'];
//            $row = [];
//            $row['proposal'] = Dvalue::getUniqueValueOfDoc($doc_id,'proposal',$qid);
//            $this->assign('row',$row);
            $this->assign('is_phone',$is_phone);
            $this->use_form_Js();
            return $this->fetch('dossier/subinfo/proposal');
        }

    }


    public function shenming()
    {
        $id = $this->request->param("id/d");


        $piluInfo = HuiBi::getHuiBiInfo($id);
        $d_id = $piluInfo['dossier_id'];


        if ($piluInfo['status'] == 0) {
            $rurl = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id,Constant::DOC_model_shengmingshu,$id)['id'],Constant::mkDmpUrl($d_id,Constant::FILE_GROUP_shengming,$id));

            $this->redirect($rurl);

           // $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_shengming, "exid" => $id)));
        } else {
            $this->error("您已经声明过，不可重复操作");
        }
    }


    public function adminhuibipilu()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $info = HuiBi::getOne($qid);
        $d_id = $info["dossier_id"];

        if(!LoginUser::isZhongCaiWei()){
            $this->error('没有权限');

        }
        if ($ok == 1) {

            if ($this->request->isPost()) {
                $data = $this->request->param('row/a');


                if($info['status'] == 2){
                    $jdyijianMsg = '不同意回避申请';
                    if($data['jdyijian']){
                        $jdyijianMsg = '拟同意回避申请';
                    }
                    $status = $data['jdyijian']?5:6;
                    HuiBi::shouLiOrJuJue($qid, $status);
                    Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_huibi_huifu, 'jdyijian', $jdyijianMsg, $qid);
                    
                    $this->success('操作成功',url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_huibi_huifu, 'exid' => $qid]));
                }

                if($info['status'] == 5||$info['status']==6){
                    $zhurenjdyijian = '不同意';
                    if($data['zhurenshenpiyijian']){
                        $zhurenjdyijian = '同意';
                    }

                    if($data['zhurenshenpiyijian']){
                        $status =  $info['status']==5?3:4;
                    }else{
                        $status = 2;
                    }

                    Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_huibi_huifu, 'zhurenjdyijian', $zhurenjdyijian, $qid);
                    if($status!=2){

                        HuiBi::shouLiOrJuJue($qid, $status+7);
                        DocContract::initContract(Ddocs::getOrInitFile($d_id, Constant::DOC_model_huibi_huifu, $qid)['id']);
                        $this->success("提交成功", "", ['alert' => 1, 'wsreload' => 1]);


//
//                        $url = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id, Constant::DOC_model_huibi_huifu, $qid)['id'], Constant::mkDmpUrl($d_id, Constant::FILE_GROUP_huibi_huifu, $qid));
//
//
//
//
//                        $this->redirect($url);
                    }else{
                        HuiBi::shouLiOrJuJue($qid, $status);
                        $this->success('操作成功',url('dossier.info/index',['id'=>$d_id]));
                    }
                }
            }


            $gidArr = [
                1 => Constant::FILE_GROUP_pilu_zhuanfa,
                2 => Constant::FILE_GROUP_huibi_huifu,
                3 => Constant::FILE_GROUP_shenming_zhuanfa,
            ];
            // 如果是 申请回避的处理 需要输入决定意见
            if ($info['type'] == 2) {
                $doc_id = Db::name("dr")
                    ->where("dossier_id = '$d_id' and doc_model_id = " . Constant::DOC_model_huibi_huifu)
                    ->where("exid",$qid)
                    ->value("id");
                $val = Dvalue::getUniqueValueOfDoc($doc_id, 'jdyijian', $qid);
                $val1 = Dvalue::getUniqueValueOfDoc($doc_id, 'zhurenjdyijian', $qid);
                if (input('wsw') == 26) {
                    dump($doc_id);
                    dump($val);
                    dump($val1);
                }
                $this->assign('status', $info['status']);
                $this->assign('isZhongCaiWei', LoginUser::isZhongCaiWei());
                $this->assign('jdyijian', $val);
                $this->assign('zhurenjdyijian', $val1);



                
                $this->use_form_Js();
                return $this->fetch('dossier/cp/huibijueding');
            }
            $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => $gidArr[$info['type']], 'exid' => $qid]));
        }else{

            $mod_id = 0;
            if($info['type']==2){
                $mod_id = Constant::DOC_model_huibi_huifu;
            }elseif($info['type']==1){
                $mod_id = Constant::DOC_model_pilushu;
            }elseif($info['type']==3){
                $mod_id = Constant::DOC_model_shengmingshu;
            }

            if($this->request->isPost()){
                $data = $this->request->post('row/a');
                Dvalue::saveUniqueValueByDocMode($d_id,$mod_id,'piluhuibi_proposal',$data['proposal'],$qid);
                $re = HuiBi::shouLiOrJuJue($qid, $ok);
                if ($re) {
                    $this->success("操作成功",url('dossier.info/index',['id'=>$d_id]));
                } else {
                    $this->error("操作失败");
                }
            }else{
                $doc_id = Ddocs::getOrInitFile($d_id,$mod_id, $qid)['id'];
                $row = [];
                $row['proposal'] = Dvalue::getUniqueValueOfDoc($doc_id,'piluhuibi_proposal',$qid);
                $this->assign('row',$row);
                return $this->fetch('dossier/subinfo/proposal');
            }

        }


    }

    // 申请回避的处理 单独出来了
    public function adminHuibi()
    {

    }


    public function tijiaozhengju()
    {
        $did = $this->request->param("id/d");

        //dump(LoginUser::getRole());die;

        if (!$did) $this->error("错误");

        $zinfo = Dz::getShenQingDzInfo($did, LoginUser::getIdid(), LoginUser::getUserName());

        if(LoginUser::getRole() == Constant::D_Role_ShenQingRen || LoginUser::getRole() == 13){
            $this->redirect(url("dossier.cp/doclist", ['id' => $did, 'gid' => Constant::FILE_GROUP_tijiaozhengju_sqr, 'exid' => $zinfo['id']]));
        }else{
            $this->redirect(url("dossier.cp/doclist", ['id' => $did, 'gid' => Constant::FILE_GROUP_tijiaozhengju_bsqr, 'exid' => $zinfo['id']]));
        }
    }

    public function spjujue()
    {
        $did = $this->request->param("id/d");
        $is_phone = $this->request->param("is_phone/d");

        //dump($did);exit;
        $re = Dossier::changeStatus($did, Dossier::makeStatus(2, 21));

        if ($re) {
            $is_phone ? $this->success("拒绝立案成功") :
            $this->success("拒绝立案成功", url("dossier.info/index", ['id' => $did]));
        }
        $is_phone ? $this->error("拒绝立案失败") :
        $this->error("拒绝立案失败", url("dossier.info/index", ['id' => $did]));
    }

    public function adminzhengju()
    {
        $qid = $this->request->param("qid/d");
        $ok = $this->request->param("ok/d");
        $is_phone = $this->request->param("is_phone/d");

        $zinfo = Dz::getDzInfo($qid);
        $d_id = $zinfo['dossier_id'];

        if ($ok == 1) {
            $this->redirect(url("dossier.cp/doclist", ['id' => $zinfo['dossier_id'], 'gid' => Constant::FILE_GROUP_zhengju_zhuanfa, 'exid' => $zinfo['id'], "zgid" => $zinfo['sgid'],'is_phone'=>$is_phone]));
        }


        if($this->request->isPost()){
            $data = $this->request->post('row/a');
            Dvalue::saveUniqueValueToDossier($d_id,'zj_proposal_'.$qid,$data['proposal']);
            $re = Dz::shouLiOrJuJue($qid, $ok);
            if ($re) {
                $data['is_phone'] == 1 ? $this->success('操作成功',url('wechat.myinfo/zjlist',['did'=>$d_id,'idid'=>LoginUser::getIdid()]))  :
                $this->success("操作成功",url('dossier.info/index',['id'=>$d_id]));
            } else {
                $this->error("操作失败");
            }
        }else{
            $proposal = Dvalue::getUniqueValueOfDossier($d_id,'zj_proposal_'.$qid);
            $this->assign('proposal',$proposal);
            $this->assign('is_phone',$is_phone);
            return $this->fetch('dossier/subinfo/proposal');
        }

    }

    public function ajaxDefence(){
        $defence_id = $_POST['defence_id'];
        $matter = $_POST['matter'];

        $re = Db::name("dossier_defence")->where("id = " . $defence_id)->update(['matter'=>$matter]);

        if($re){
            echo 1;
        }else{
            echo 2;
        }
    }
}