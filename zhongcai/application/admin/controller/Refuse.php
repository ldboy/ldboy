<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/12
 * Time: 11:43
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc\Dvalue;

class Refuse extends Backend{
    public function _initialize(){
        parent::_initialize();
    }


    public function init(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("refuse_value")->varTitle("请填写拒绝原因")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("refuse/index");
    }

    public function index(){

        /*$this->success("测试ajax拒绝");
        exit;*/

        $dossier_id = $this->request->param('id/d');
        $is_phone = $this->request->param('is_phone/d');
        if($this->request->isPost()){
            $refuse = $this->request->param("msg");
             
            // 仲裁委 状态是2 可拒绝
            $qx = Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN + Constant::QX_ROLE_ADMIN + Constant::QX_ROLE_ZHONGCAIWEI_MISHU;
            $role = User::getRoleInDossier($dossier_id);
            if(!($role & $qx)){
                $this->error('没有权限');
            }
            $status = Dossier::getSimpleDossier($dossier_id)['status'];
            if($status!=2){
                $this->error('此状态下不可拒绝');
            }
            // 改状态 加 log
            Db::startTrans();
            $re2 = Dvalue::saveUniqueValueToDossier($dossier_id,"Refuse",$refuse);
            $res = Dossier::changeStatus($dossier_id,Dossier::makeStatus(0,5));

            $res1 = DossierLog::addLog($dossier_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_REFUSE,LoginUser::getRoleThId());
            if($res&&$res1&&$re2){
                Db::commit();
                $is_phone ? $this->success('操作成功')  :
                $this->success('操作成功','',['alert'=>1,'wsreload'=>1]);
            }else{
                Db::rollback();
                $this->error('失败');
            }
        }else{

            return $this->fetch();

        }


    }

    public function reapply(){
        $id = $this->request->param('dossier_id');
        $info = Dossier::getSimpleDossier($id);
        if($info['sub_status']!=5){
            $this->error('状态错误');
        }
        $doc_id = Ddocs::getOrInitFile($id,Constant::DOC_model_shengqingshu);
        Ddocs::reSign($doc_id);
        Dossier::changeStatus($id,Dossier::makeStatus(1));
        $this->redirect(url('dossier.cp/add',['dossier_id'=>$id,'title'=>'申请仲裁']));
    }


    /*public function refuse(){
        $dossier_id = $this->request->param('id/d');
        // 仲裁委 状态是2 可拒绝
        $qx = Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN+Constant::QX_ROLE_ADMIN;
        $role = User::getRoleInDossier($dossier_id);
        if(!($role&$qx)){
            $this->error('没有权限');
        }
        $status = Dossier::getSimpleDossier($dossier_id)['status'];
        if($status!=2){
            $this->error('此状态下不可拒绝');
        }
        // 改状态 加 log
        Db::startTrans();
        $res = Dossier::changeStatus($dossier_id,0);
        $res1 = DossierLog::addLog($dossier_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_REFUSE,LoginUser::getRoleThId());
        if($res&&$res1){
          Db::commit();
            $this->success('成功');
        }else{
            Db::rollback();
            $this->error('失败');
        }
    }*/
}