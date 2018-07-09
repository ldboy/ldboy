<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-09
 * Time: 17:39
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use dossier\DossierDoc;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dossier;
use think\Db;
use wslibs\wszc\DossierLog;
use wslibs\wszc\Drole;
use wslibs\wszc\LoginUser;
use wslibs\wszc\notice\Dnotice;
use wslibs\wszc\User;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\Dcancel;


use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;

use wslibs\wszc\Dvalue;
use wslibs\wszc\Ds;
use wslibs\wszc\qx\Qx;



class Receive extends Backend
{
    public function index(){
        $d_id = $this->request->param("id/d");

        $this->assign("d_id",$d_id);
        return $this->fetch();
    }

    public function initstatus(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("shouli")->varTitle("请输入受理意见")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("receive/changestatus");
    }

    public function agree(){
        $d_id = $this->request->param("id/d");
        $is_phone = $this->request->param("is_phone/d");
        if(!LoginUser::isZhongCaiWeiZhuBan()){
            $this->error("权限错误");
        }

        $dossier_info = Dossier::getSimpleDossier($d_id);

        if($dossier_info['status'] != 2){
            if($is_phone==1){
                $this->error("此状态下不可受理");
            }
            $this->error("此状态下不可受理",url('dossier.info/index',['id'=>$d_id]));
        }

        if(Dcancel::getCancel($d_id)['type'] == Dcancel::TYPE_WEISHOULI){
            Dcancel::saveCancel($d_id,['type'=>Dcancel::TYPE_YISHOULI]);
        }

//        $no = DossierDoc::create_zc_no();

        $re = Dossier::changeStatus($d_id,Dossier::makeStatus(2,22));

//        $re0 = Db::name('dossier')->where('id',$d_id)->update(['zno'=>$no]);
        $re1 = DossierLog::addLog($d_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_ACCEPT);

        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_lianshenpibiao,"Shouliyijian","符合立案条件，拟建议立案");

        $re3 = Drole::addRoleFromLoginUser($d_id,Constant::D_Role_ZhongCaiWei_GuanLiYuan);
        Ds::sendFileByDocids(array(Ddocs::getOrInitFile($d_id,Constant::DOC_model_lianshenpibiao,0,true)),Constant::D_Role_ZhongCaiWei_JiGou,"立案审批表文件");
        //Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN], array_column($this->_docs, 'id'));

        // 自动签字
//        $rurl = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id,Constant::DOC_model_tongzhishu,0)['id'],Constant::mkDmpUrl($d_id,"2,13",0));

        Dnotice::sendZhuRenFromZhuBan($dossier_info,$dossier_info['id'],0,0);


        if($re && $re1){
            $is_phone ? $this->success("已发送至领导，请等待领导审批")  :
            $this->success("已发送至领导，请等待领导审批",'',['alert'=>1,"wsreload"=>1]);
        }else{
            $this->error("操作失败");
        }

    }


    public function changestatus(){
        $d_id = $this->request->param("id/d");

        if($this->request->isPost()){



            /*dump($this->request->isPost());die;*/
            $postData = $this->request->Post("row/a");

            if(!LoginUser::isZhongCaiWeiZhuBan()){
                $this->error("权限错误");
            }

//            $qx = Constant::QX_ROLE_ADMIN + Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN;
//            $role = User::getRoleInDossier($d_id);
//            if(!($role & $qx)){
//                $this->error("没有权限",url('dossier.info/index',['id'=>$d_id]));
//            }
            
            $status = Dossier::getSimpleDossier($d_id)['status'];
            if($status >= 3){
                $this->success("您已受理",url('dossier.cp/doclist',['id'=>$d_id,'gid'=>'2,13']));
            }
            if($status != 2){
                $this->error("此状态下不可受理",url('dossier.info/index',['id'=>$d_id]));
            }

            $DosserUser = Dossier::getDangShiRen($d_id,Constant::D_Role_Bei_ShenQingRen);

            //添加答辩机会


            if(Dossier::getSimpleDossier($d_id)['sub_status'] == 21){
                Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_lianshenpibiao,"Shouliyijian",$postData['shouli']);
                //$this->redirect(url('dossier.cp/doclist',['id'=>$d_id,'gid'=>Constant::FILE_GROUP_lianshenpi , 'wsreload'=>2 ]));
                $this->success("操作成功",url('dossier.cp/doclist',['id'=>$d_id,'gid'=>Constant::FILE_GROUP_lianshenpi , 'wsreload'=>2 ]));
            }


            Db::startTrans();
            /*foreach ($DosserUser as $k => $v){
                $res = DefenceExpand::addDefence($d_id,$v['idid']);
                if(!$res){
                    Db::rollback();
                    $this->error("错误");
                }
            }*/

            $no = DossierDoc::create_zc_no();
            $re = Dossier::changeStatus($d_id,Dossier::makeStatus(2,21));
            $re0 = Db::name('dossier')->where('id',$d_id)->update(['zno'=>$no]);
            $re1 = DossierLog::addLog($d_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_ACCEPT);

            Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_lianshenpibiao,"Shouliyijian",$postData['shouli']);

            $re3 = Drole::addRoleFromLoginUser($d_id,Constant::D_Role_ZhongCaiWei_GuanLiYuan);

            if($re && $re1 && $re0 && $re3){
                Db::commit();
                $this->success("操作成功",url('dossier.cp/doclist',['id'=>$d_id,'gid'=>Constant::FILE_GROUP_lianshenpi , 'wsreload'=>2 ]));
                //$this->redirect(url('dossier.cp/doclist',['id'=>$d_id,'gid'=>Constant::FILE_GROUP_lianshenpi , 'wsreload'=>2 ]));
                //$this->success("受理成功",url('dossier.cp/doclist',['id'=>$d_id,'gid'=>Constant::FILE_GROUP_lianshenpi]),['confirm'=>1,"btn1"=>"立即发送","url2"=>url('dossier.info/index',['id'=>$d_id]),"btn2"=>"稍后发送","tip"=>"受理成功，是否现在将资料发送给相关人员？"]);
            }else{
                Db::rollback();
                $this->error("受理失败",url('dossier.info/index',['id'=>$d_id]));
            }
        }else{
            $info = Ddocs::getOrInitFile($d_id,Constant::DOC_model_lianshenpibiao);
            $Shouliyijian = Dvalue::getUniqueValueOfDoc($info['id'],"Shouliyijian");

            $this->assign("info",$Shouliyijian);

            if(input("sy")==1){
                dump($Shouliyijian);
            }

            $this->use_form_Js();
            return $this->fetch();
        }
    }
}