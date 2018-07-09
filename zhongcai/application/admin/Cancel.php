<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/12
 * Time: 9:52
 */

namespace app\admin\controller;

use app\common\controller\Backend;
use wslibs\wszc\Constant;
use wslibs\wszc\Dmp;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;
use wslibs\wszc\Dcancel;
use wslibs\wszc\Dvalue;

use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;

use wslibs\wszc\Ddocs;
use think\Db;

class Cancel extends Backend
{

    public function _initialize()
    {

        parent::_initialize();
    }


    public function initcancel()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("chehuiyuanyin")->varTitle("请填写撤回原因")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("chehuishishijiliyou")->varTitle("请填写撤回事实及理由")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("cancel/cancel");
    }

    public function initcancels()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("zhubanchehuiyijian")->varTitle("请填写批准撤回意见")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("cancel/cancels");
    }


    public function cancel()
    {
        $dossier_id = $this->request->param('id/d');
        $d_sub_status = Dossier::getSimpleDossier($dossier_id)['sub_status'];
        $is_cancel = Dcancel::getCancel($dossier_id);


        if ($is_cancel['status'] > 0) {
            if ($is_cancel['status'] == Dcancel::STATUS_ZHURENCHULI) {
                $this->success('您发起的仲裁请求已成功撤回', url("dossier.info/view", ['id' => $dossier_id, 'alert' => 1]));
            }

            if ($is_cancel['status'] >= Dcancel::STATUS_WENJIANYITIJIAO) {
                $this->success('您发起的仲裁撤回请求已发出，请等待审批', url("dossier.info/view", ['id' => $dossier_id, 'alert' => 1]));
            }

            if ($is_cancel['status'] == Dcancel::STATUS_BIAODANYITIJIAO) {
                $this->redirect(url("dossier.cp/doclist", ['id' => $dossier_id, 'gid' => Constant::FILE_GROUP_chehuishenqing, 'exid' => $is_cancel['id']]));
            }
        }


        if ($this->request->isPost()) {
            $postdata = $this->request->post("row/a");

            $re2 = Dvalue::saveUniqueValueByDocMode($dossier_id, Constant::DOC_model_chehuishenqingshu, "chehuiyuanyin", $postdata['chehuiyuanyin'], $is_cancel['id']);
            $re3 = Dvalue::saveUniqueValueToDossier($dossier_id, "chehuishishijiliyou", $postdata['chehuishishijiliyou']);
            $re4 = Dcancel::saveCancel($dossier_id, ['shixiang' => $postdata['chehuiyuanyin']]);

            if ($is_cancel['type'] == 1) {// 未受理时 撤回
                /*$re1 = Dossier::changeStatus($dossier_id, 0);
                  $re2 = DossierLog::addLog($dossier_id, LoginUser::getIdid(), LoginUser::getUserName(), DossierLog::LOG_TYPE_GIVE_UP);*/
                $re3 = Dcancel::changeStatus($dossier_id, Dcancel::STATUS_ZHURENCHULI);
                if ($re3) {
                    $this->success('撤回成功', url("dossier.info/view", ['id' => $dossier_id, 'alert' => 1]));
                } else {
                    $this->error('撤回失败', url("dossier.info/view", ['id' => $dossier_id, 'alert' => 1]));
                }
            }
            /*
             *type = 2 已受理 撤回
             *type = 3 已组庭 撤回 */

            Db::startTrans();
            $re1 = Dcancel::changeStatus($dossier_id, Dcancel::STATUS_BIAODANYITIJIAO);
            if ($re1 && $re2 && $re3 && $re4) {
                Db::commit();
                $this->redirect(url("dossier.cp/doclist", ['id' => $dossier_id, 'gid' => Constant::FILE_GROUP_chehuishenqing, 'exid' => $is_cancel['id']]));
            } else {
                Db::rollback();
                $this->error('错误', url("dossier.info/view", ['id' => $dossier_id, 'alert' => 1]));
            }
        } else {
            $info = Dcancel::addCancel($dossier_id, $d_sub_status);
            if ($info['idid'] != LoginUser::getIdid()) {
                // 应该不会出现多个申请人的情况 此处备用
                $this->error('其他人已经申请过撤销了');
            }
            return $this->fetch();
        }
    }

    // 主任操作
    public function canceladmin1()
    {
        $dac_id = $this->request->param('dca_id/d');
        $ok = $this->request->param('ok/d');

        $dacInfo = Dcancel::getgetCancelById($dac_id);

        $d_id = $dacInfo['dossier_id'];

        if ($ok == 1) {
            // 同意 自动盖章并发送
            //如果只转发 撤回决定书 使用这个 FILE_GROUP_chehuishenqing_zhuren_zf
            //如果转发撤回申请书 和 决定书 使用这个 FILE_GROUP_chehuishenqing_zhubanzf
            $url = '';
            if($dacInfo['type']==2){
                $url = DocContract::gotoAutoSignUrl(
                    Ddocs::getOrInitFile($d_id, Constant::DOC_model_chehuijuedingshu_ztq, $dac_id)['id'],
                    Constant::mkDmpUrl($d_id, Constant::FILE_GROUP_chehuishenqing_zhuren_zf, $dac_id));
            }elseif($dacInfo['type']==3){
                $url = DocContract::gotoAutoSignUrl(
                    Ddocs::getOrInitFile($d_id, Constant::DOC_model_chehuijuedingshu_zth, $dac_id)['id'],
                    Constant::mkDmpUrl($d_id, Constant::FILE_GROUP_chehuishenqing_zhuren_zf_zth, $dac_id));
            }
            $this->redirect($url);
        } else {
            $msg = '';
            // 不同意 打回给主办  或 仲裁员
            if ($dacInfo['type'] == 2) {
                // 打回给主办
                Dcancel::changeStatus($dacInfo['dossier_id'], Dcancel::STATUS_WENJIANYITIJIAO);
                $msg = '已打回给主办';
            } elseif ($dacInfo['type'] == 3) {
                // 打回给仲裁员
                Ddocs::reSign(Ddocs::getOrInitFile($d_id, Constant::DOC_model_chehuijuedingshu_zth, $dac_id)['id']);
                Dcancel::changeStatus($dacInfo['dossier_id'], Dcancel::STATUS_ZHUBANCHULI);
                $msg = '已打回给仲裁员';
            }
            $this->success($msg, url('dossier.info/index', ['id' => $dacInfo['dossier_id']]));
        }
    }

    // 主办操作
    public function canceladmin()
    {
        $dac_id = $this->request->param('dca_id/d');
        $dacInfo = Dcancel::getgetCancelById($dac_id);
        $d_id = $dacInfo['dossier_id'];

        if ($this->request->isPost()) {
            $postdata = $this->request->Post("row/a");
            //dump($postdata);
            $re = Dvalue::saveUniqueValueToDossier($d_id, "zhubanchehuiyijian", $postdata['zhubanchehuiyijian']);

            if ($re) {
                $zids = Ddocs::getFilesByGroup($d_id, Constant::FILE_GROUP_chehuishenqing, $dac_id);
                Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_chehuishenqing_zhubanzf, $dac_id, array_column($zids, 'id'));
                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_chehuishenqing_zhubanzf, 'exid' => $dac_id]));
            } else {
                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_chehuishenqing_zhubanzf, 'exid' => $dac_id]));
            }

        } else {
            // 发给主任的需要主办写决定意见 发给仲裁员的  不需要主办写决定意见
            if ($dacInfo['type'] == 2) {
                $val = Dvalue::getUniqueValueOfDossier($d_id, 'zhubanchehuiyijian');
                $row = [
                    'zhubanchehuiyijian' => $val
                ];
                $this->assign('row', $row);
                return $this->fetch("cancels");
            } elseif ($dacInfo['type'] == 3) {
                // 直接转发给仲裁员
                $errMsg = '';
                $res = Dmp::doDmp($dacInfo['dossier_id'], Constant::FILE_GROUP_chehuishenqing_zhuban_zf_zth, $dacInfo['id'], $errMsg);
                if ($res) {
                    $this->success('发送成功', url('dossier.info/index', ['id' => $d_id]));
                } else {
                    $this->error($errMsg);
                }
            }
        }
    }

    // 仲裁员操作
    public function cancelzcy()
    {
        $dac_id = $this->request->param('dca_id/d');
        $dacInfo = Dcancel::getgetCancelById($dac_id);
        $d_id = $dacInfo['dossier_id'];
        if ($dacInfo['status'] != 3) {
            $this->error('此状态下不允许操作');
        }
        if ($this->request->isPost()) {
            $postdata = $this->request->Post("row/a");
            //dump($postdata);
            // 因为跟主办用的是一个模板  所以提交过来的字段是 zhubanchehuiyijian
            $re = Dvalue::saveUniqueValueToDossier($d_id, "zcychehuiyijian", $postdata['zhubanchehuiyijian']);

//                $zids = Ddocs::getFilesByGroup($d_id, Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa, $dac_id);
//                Ddocs::initGroupFileFromDocids(Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa, $dac_id, array_column($zids, 'id'));
             // $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, 'gid' => Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa, 'exid' => $dac_id]));

                $url = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id, Constant::DOC_model_chehuijuedingshu_zth, $dac_id)['id'], Constant::mkDmpUrl($d_id, Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa, $dac_id));
                $this->redirect($url);



        } else {
            // 发给主任的需要主办写决定意见 发给仲裁员的  不需要主办写决定意见
            if ($dacInfo['type'] == 3) {
                $val = Dvalue::getUniqueValueOfDossier($d_id, 'zcychehuiyijian');
                $row = [
                    'zcychehuiyijian' => $val
                ];
                $this->assign('row', $row);
                return $this->fetch("cancels");
            }
        }
    }













































    // 暂定没受理之前都可以撤销

    /*if(!$dossier_id){
      $this->error('参数错误');
    }
    // 申请人 仲裁委受理之前 可以撤销
     $cancelQx = Constant::QX_ROLE_SHENQINGREN;
     if(!(User::getRoleInDossier($dossier_id)&$cancelQx)){
       $this->error('没有权限');
     }
    $dossierInfo = Dossier::getSimpleDossier($dossier_id);
    if($dossierInfo['status']>2){
        $this->error('此状态下不可撤销');
    }
    $res = Dossier::changeStatus($dossier_id,0);
    $res1 = Dossier::updata($dossier_id,['is_valid'=>0]);
    DossierLog::addLog($dossier_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_GIVE_UP);
    $this->success('撤销成功',url("dossier.dlist/index"));*/
}