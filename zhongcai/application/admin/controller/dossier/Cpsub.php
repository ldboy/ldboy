<?php
namespace app\admin\controller\dossier;
use app\common\controller\Backend;
use think\Db;
use wslibs\wszc\Ddocs;
use wslibs\wszc\LoginUser;
use wslibs\wszc\Constant;
use wslibs\wszc\Subbusiness\defence;
use wslibs\wszc\User;


/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/2
 * Time: 下午5:01
 */
class Cpsub extends Backend
{
    public function _initialize()
    {
//        $this->noNeedLogin = ['*'];
        parent::_initialize();
    }
    // 答辩 质证 披露 声明 申请回避 显示 和 提交表单
    public function subYw(){
        $subId = (int)$this->request->param("id/d");
        $sub_type = (int)$this->request->param("sub_type/d");
        $sub_sub_type = (int)$this->request->param("sub_sub_type/d");

        if(!$subId){
            $this->error('参数错误');
        }
        $parameter = $this->getSubParemeter($sub_type,$sub_sub_type);
        if(!$parameter){
            $this->error('参数错误');
        }
        $obj = new $parameter['class']();
//        $obj = new defence();
        $ywInfo = $obj->getOne($subId);
        if($ywInfo['idid']!=LoginUser::getIdid()){
            $this->error('没有权限');
        }
        if($ywInfo['status']>=2){
            $this->error('该业务已操作，请勿重复操作');
        }
        if($ywInfo['status']==1){
            $this->redirect(url('dossier.cp/doclist', array("id" => $ywInfo['dossier_id'], "gid" => $parameter['gid'], "exid" => $subId)));
        }
        if($this->request->isPost()){
            $postData = $this->request->param('row/a');
            $res = $obj->setTable($parameter['tableName'])->subToForm($subId,[],$postData);
            if($res){
                $this->success('操作成功',url('dossier.cp/doclist', array("id" => $ywInfo['dossier_id'], "gid" => $parameter['gid'], "exid" => $subId)));
            }else{
                $this->error('操作失败');
            }
        }else{
            if(!$parameter['tpl']){
                $this->redirect(url('dossier.cp/doclist', array("id" => $ywInfo['dossier_id'], "gid" => $parameter['gid'], "exid" => $subId)));
            }
            return $this->fetch($parameter['tpl']);
        }

    }
    // 提交到仲裁委  这里暂时还走cp
    public function subToZhongcaiwei(){

    }
    // 处理 sub 业务  仲裁委处理
    public function adminHandleSubYw(){
        $subId = (int)$this->request->param("id/d");
        $sub_type = (int)$this->request->param("sub_type/d");
        $sub_sub_type = (int)$this->request->param("sub_sub_type/d");
        $ok = (int)$this->request->param("ok/d");
        if(!$subId){
            $this->error('参数错误');
        }
        $parameter = $this->getSubParemeter($sub_type,$sub_sub_type);
        if(!$parameter){
            $this->error('参数错误');
        }
//        $obj = new $parameter['class']();
        $obj = new defence();
        $ywInfo = $obj->getOne($subId);
        if(User::getRoleInDossier($ywInfo['dossier_id'],LoginUser::getIdid())!=Constant::QX_ROLE_ZHONGCAIWEI_MISHU){
            $this->error('没有权限');
        }

        if($ywInfo['status']>=2){
            $this->error('该业务已操作，请勿重复操作');
        }
        if($ywInfo['status']==3){
            $this->redirect(url('dossier.cp/doclist', array("id" => $ywInfo['dossier_id'], "gid" => $parameter['gid'], "exid" => $subId)));
        }
        if($ok==1){
            $zids = Ddocs::getFilesByGroup($ywInfo['dossier_id'], Constant::FILE_GROUP_zhizheng, $subId);
            Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_zhizhengzhuanfa,$subId,array_column($zids,'id'));
            $obj->setTable($parameter['tableName'])->handle($subId,$ok);
            $this->redirect(url("dossier.cp/doclist", ['id' => $ywInfo['dossier_id'], 'gid' => $parameter['gid_zf'], 'exid' => $subId]));
        }
        $res = $obj->setTable($parameter['tableName'])->handle($subId,$ok);
        if($res){
            $this->success('拒绝成功');
        }else{
            $this->error('拒绝失败');
        }
    }
    // 调取模板 获取表明 获取扩展类名
    private function getSubParemeter($sub_type,$sub_sub_type=0){
        // 1 答辩 2质证 3 3.1披露 3.2申请回避 3.3声明
       $arr = [
           1=>['tpl'=>'defence/form','tableName'=>'dossier_defence','class'=>'defence','gid'=>Constant::FILE_GROUP_dabian,'gid_zf'=>Constant::FILE_GROUP_dabian_zhuanfa],
           2=>['tpl'=>'question/index','tableName'=>'dossier_question','class'=>'question','gid'=>Constant::FILE_GROUP_zhizheng,'gid_zf'=>Constant::FILE_GROUP_zhizhengzhuanfa],
           3=>[
               1=>['tpl'=>'dossier/cp/pilu','tableName'=>'huibi','class'=>'huibi','gid'=>Constant::FILE_GROUP_pilu,'gid_zf'=>Constant::FILE_GROUP_pilu_zhuanfa],
               2=>['tpl'=>'dossier/cp/huibi','tableName'=>'huibi','class'=>'huibi','gid'=>Constant::FILE_GROUP_huibi,'gid_zf'=>Constant::FILE_GROUP_huibi_huifu],
               3=>['tpl'=>'','tableName'=>'huibi','class'=>'huibi','gid'=>Constant::FILE_GROUP_shengming,'gid_zf'=>Constant::FILE_GROUP_shengming],
           ]
       ];
        if(!$sub_sub_type){
            return $arr[$sub_type];
        }
        return $arr[$sub_type][$sub_sub_type];
    }

}