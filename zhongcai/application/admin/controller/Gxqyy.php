<?php
namespace app\admin\controller;
use app\common\controller\Backend;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DocContract;
use wslibs\wszc\Gxq;
use wslibs\wszc\Constant;
use think\Db;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;
use wslibs\wszc\Ds;

use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc\qx\Qx;
use wslibs\wszc\User;


class Gxqyy extends Backend
{
    public function _initialize(){
        parent::_initialize();
        $this->use_action_js();
    }
    public function initindex(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("matter")->varTitle("请求事项")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("reason")->varTitle("事实及理由")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("Gxqyy/index");
    }


    public function index(){

        $did = $this->request->param("id/d");

        if($this->request->isPost()){
            $postdata = $this->request->post("row/a");
            $matter = $postdata['matter'];
            $reason = $postdata['reason'];
            //dump($postdata);dump($d_id);die;

            Db::startTrans();
            $re1 = Gxq::add($did,$matter,$reason);

            $re2 = Dvalue::saveUniqueValueByDocMode($did,Constant::DOC_model_gxqyy_sqs,'gxq_matter',$matter,$re1);
            $re3 = Dvalue::saveUniqueValueByDocMode($did,Constant::DOC_model_gxqyy_sqs,'gxq_reason',$reason,$re1);

            if($re1 && $re2 && $re3){
                Db::commit();
                $this->success("提交管辖权异议成功",url('dossier.cp/doclist', array("id" => $did, "gid" => Constant::FILE_GROUP_gxq_dangshirenqingqiu, "exid" => $re1)));
            }else{
                Db::rollback();
                $this->error("内容没有修改");
            }
        }else{
            $gxq_info = Gxq::getYjByDid($did);

            if($gxq_info){
                if($gxq_info['status'] == 1){
                    $this->redirect(url('dossier.cp/doclist', array("id" => $did, "gid" => Constant::FILE_GROUP_gxq_dangshirenqingqiu, "exid" => $gxq_info['id'])));
                }else if($gxq_info['status'] == 4){
                    $this->assign('row',$gxq_info);
                }else{
                    $this->success("您已提交了管辖权异议，不可重复操作");
                }
            }
            return $this->fetch();
        }
    }


    public function admingxqyy(){
        $ok = $this->request->param("ok/d");
        $gxid = $this->request->param("gxid/d");
        $is_phone = $this->request->param("is_phone/d");
        $gxinfo = Gxq::getYyById($gxid);
        $d_id = $gxinfo['d_id'];
        if(User::getDroleInDossier($d_id,LoginUser::getIdid())!=Constant::D_Role_ZhongCaiWei_GuanLiYuan){
            $this->error('没有权限');
        }
        if(in_array($gxinfo['status'],[7,8])){
            Gxq::changeYyStatus($gxid,$gxinfo['status']-4);
            $is_phone ==1 ? $this->success('报批成功，请等待主任审核',url('wechat.myinfo/guanxiaquan',['did'=>$d_id,'idid'=>LoginUser::getIdid()])):
            $this->success('报批成功，请等待主任审核');
        }
        if($gxinfo['status']!=2){
            $this->error('不可操作');
        }
        if($ok == 1){
            // 有管辖权
            Gxq::changeYyStatus($gxid,Gxq::status_7);
//            $msg = '有管辖权';
            $msg1 = '本会对该争议有管辖权。';
        } else {
            // 没有管辖权
//            $msg = '没有管辖权';
            $msg1 = '本会对该争议不具有管辖权。';
            Gxq::changeYyStatus($gxid,Gxq::status_8);
        }
//        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_zhubanyj',$msg,$gxid);
        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_addtime',date("Y年m月d日",strtotime($gxinfo['addtime'])),$gxid);
        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_zuizhongjueding',$msg1,$gxid);
        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_role',$gxinfo['role'].$gxinfo['name'],$gxid);
        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_sqr_reason',$gxinfo['reason'],$gxid);
        
//        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_gxqyy_jueding,'gxq_sqr_reason',$gxinfo['matter'],$gxid);

        $is_phone ==1 ? $this->success('操作成功',url('wechat.myinfo/guanxiaquan',['did'=>$d_id,'idid'=>LoginUser::getIdid()])):
        $this->success("操作成功");
        // 建议修改 预留功能
//            Gxq::acceptOrRefuse($gxid);
//            $docId = Ddocs::getOrInitFile($d_id,Constant::DOC_model_gxqyy_sqs,$gxid)['id'];
//            Ddocs::reSign($docId);
//            $this->success("操作成功");
    }

    public function zhurencz(){
        $ok = $this->request->param("ok/d");
        $gxid = $this->request->param("gxid/d");
        $gxinfo = Gxq::getYyById($gxid);
        $d_id = $gxinfo['d_id'];
        if(User::getDroleInDossier($d_id,LoginUser::getIdid())!=Constant::D_Role_ZhongCaiWei_LiAnShenPi){
            $this->error('没有权限');
        }
        if(!in_array($gxinfo['status'],[3,4])){
            $this->error('状态错误');
        }
        $docs = Ddocs::getFilesByGroup($d_id, Constant::FILE_GROUP_gxq_dangshirenqingqiu, $gxid);

        if($ok==1){ // 同意 自动盖章 发送至当事人 只发决定书
            $rurl = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id,Constant::DOC_model_gxqyy_jueding,$gxid)['id'],Constant::mkDmpUrl($d_id,Constant::FILE_GROUP_gxq_fasongsuoyou,$gxid));
            $this->redirect($rurl);
//            $this->redirect(url('dossier.cp/doclist', array("id" => $d_id, "gid" => Constant::FILE_GROUP_gxq_fasongsuoyou, "exid" => $gxid)));
        }else{ // 不同意 还给主办修改
            Gxq::changeYyStatus($gxid,Gxq::status_2);
            $this->success('操作成功');
        }

    }


}
