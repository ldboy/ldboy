<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 19:13
 */

namespace app\admin\controller\dossier;

use \wslibs\wszc\User;
use app\common\controller\Backend;
use wslibs\wsform\Item;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\HuiBi;
use wslibs\wszc\LoginUser;
use wslibs\wszc\question\QuestionExpand;
use think\Db;
use wslibs\wszc\Dcancel;
use wslibs\wszc\Gxq;

class Subinfo extends Backend
{

    public function _initialize()
    {
        parent::_initialize();
    }

    private $_file_group = [];

    //质证
    public function question()
    {
        $qid = $this->request->param('qid/d');
        $is_phone = $this->request->param('is_phone/d');

        if (!$qid) {
//            $this->error('参数错误');
        }
        $info =  QuestionExpand::getOne($qid);
            // 查看就相当于处理了
//          QuestionExpand::AdminChuLiQues($qid,1);

        $dossier_id =$info['dossier_id'];
        $tableData = QuestionExpand::getItemList($qid);

        foreach ($tableData as $k => $v) {
            $v['dq_addtime'] = date('Y-m-d', $v['dq_addtime']);
            $v['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
            if ($v['status'] == 1) {
                $v['dq_finish_time'] = date('Y-m-d', $v['finish_time']);
                $v['status'] = '已完成';
            } else {
                $v['status'] = '未完成';
            }
            $tableData[$k] = $v;
        }
        $this->addFileGrou($dossier_id, Constant::FILE_GROUP_zhizheng, $qid);


        if(input("wl")==77){
            dump($tableData);
        }

        if ($info['status'] ==2)
        $tableData['button'] = $this->getBtn(
            url('dossier.cp/adminquestion', ['qid' => $_GET['qid'], 'ok' => 1,'is_phone'=>$is_phone]),
            url('dossier.cp/adminquestion', ['qid' => $_GET['qid'], 'ok' => 0,'is_phone'=>$is_phone])
        );

        return $this->displaySubInfo($tableData, 'question_table');
    }

    // 答辩
    public function defence()
    {
        $def_id = $this->request->param('def_id/d');
        $is_phone = $this->request->param('is_phone/d');

        $defenceInfo = DefenceExpand::getDefenceFind($def_id);

        $this->addFileGrou($defenceInfo['dossier_id'], Constant::FILE_GROUP_dabian, $def_id,$defenceInfo['zids']);
        if ($defenceInfo['status'] ==2)
        $defenceInfo['button'] = $this->getBtn(
            url('dossier.cp/admindabian', ['qid' => $def_id, 'ok' => 1,'is_phone'=>$is_phone]),
            url('dossier.cp/admindabian', ['qid' => $def_id, 'ok' => 0,'is_phone'=>$is_phone]),""
        );
        $defenceInfo['addtime'] = date("Y-m-d H:i:s",$defenceInfo['addtime']);
        $defenceInfo['real_name'] = Db::name("idcards")->where("id = ".$defenceInfo['idid'])->value("real_name");
        if(input("wl")==99){
            dump($defenceInfo);
        }

      

        return $this->displaySubInfo($defenceInfo, 'defence_table');
    }

    //撤回申请
    public function dcancel(){
        //根据类型判断 是发给主任 还是仲裁员
        $dca_id = $this->request->param('dca_id/d');
        $dcaInfo = Dcancel::getgetCancelById($dca_id);
        $dcaInfo['addtime'] = date("Y-m-d H:i:s",$dcaInfo['addtime']);

        $dRole = User::getDroleInDossier($dcaInfo['dossier_id'],LoginUser::getIdid());

        $btnHtml = '';
        if($dRole==Constant::D_Role_ZhongCaiWei_GuanLiYuan){
            // 主办
            if($dcaInfo['status']==2){
                $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing,$dca_id);
                if($dcaInfo['type']==2){// 发给主任

                    $btnHtml = $this->getBtnNew(
                         url('Cancel/canceladmin', ['dca_id' => $dcaInfo['id']]),
                         'btn btn-success btn-warning-tip',
                         '确定要发送给主任吗？',
                         '提示',
                         '发送至主任'
                     );
                 }elseif($dcaInfo['type']==3){// 发给仲裁员
                   
                     $btnHtml = $this->getBtnNew(
                         url('Cancel/canceladmin', ['dca_id' => $dcaInfo['id']]),
                         'btn btn-success btn-warning-tip',
                         '确定要发送给仲裁员吗？',
                         '提示',
                         '发送至仲裁员'
                     );
                 }
            }
            // 仲裁员已编写意见
            if($dcaInfo['status']==6){
                $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing,$dca_id);
                $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa,$dca_id);
                $btnHtml = $this->getBtnNew(
                    url('Cancel/canceladmin', ['dca_id' => $dcaInfo['id'],'ok'=>1]),
                    'btn btn-success btn-warning-tip',
                    '确定要让仲裁员签字吗？',
                    '提示',
                    '发送至仲裁员签字'
                );

                
                $btnHtml .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $btnHtml .= $this->getBtnNew(
                    url('Cancel/canceladmin', ['dca_id' => $dcaInfo['id'],'ok'=>0]),
                    'btn btn-warning btn-warning-tip',
                    '确定要让仲裁员重新编写决定意见吗？',
                    '提示',
                    '发送至仲裁员重新编写决定意见'
                );
            }

        }
        if($dRole==Constant::D_Role_ZhongCaiWei_LiAnShenPi){
            // 主任
            if($dcaInfo['type']==2&&$dcaInfo['status']==3){



                $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing_zhubanzf,$dca_id);
                $btnHtml = $this->getBtnNew(
                    url('Cancel/canceladmin1', ['dca_id' => $dcaInfo['id'],'ok'=>1]),
                    'btn btn-success btn-warning-tip',
                    '确定要处理并发送给当事人吗？',
                    '提示',
                    '同意并发送'
                );
                $btnHtml .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $btnHtml .= $this->getBtnNew(
                    url('Cancel/canceladmin1', ['dca_id' => $dcaInfo['id'],'ok'=>0]),
                    'btn btn-warning btn-warning-tip',
                    '确定退回至主办吗？',
                    '提示',
                    '退回至主办修改'
                );
            }
            if($dcaInfo['type']==3&&$dcaInfo['status']==4){
                $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa,$dca_id);
                $btnHtml = $this->getBtnNew(
                    url('Cancel/canceladmin1', ['dca_id' => $dcaInfo['id'],'ok'=>1]),
                    'btn btn-success btn-warning-tip',
                    '确定要处理并发送给当事人吗？',
                    '提示',
                    '同意并发送'
                );
                $btnHtml .= '&nbsp;&nbsp;&nbsp;&nbsp;';
                $btnHtml .= $this->getBtnNew(
                    url('Cancel/canceladmin1', ['dca_id' => $dcaInfo['id'],'ok'=>0]),
                    'btn btn-warning btn-warning-tip',
                    '确定要退回给仲裁员吗？',
                    '提示',
                    '打回给仲裁员重新编写决定意见'
                );
            }

        }
        if($dRole==Constant::D_Role_ZhongCaiYuan){
            // 仲裁员
            // FILE_GROUP_chehuishenqing_zhubanzf  借用 如果后期不一样了 可以单独配置
            if($dcaInfo['type']==3){
                if($dcaInfo['status']==3){
                    $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing_zhongcaiyuanchuli,$dca_id);
                    $btnHtml = $this->getBtnNew(
                        url('Cancel/cancelzcy', ['dca_id' => $dcaInfo['id']]),
                        'btn btn-success  btn-dialog',
                        '确定要处理吗？',
                        '提示',
                        '去处理'
                    );
                }elseif($dcaInfo['status']==7){
                    $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa,$dca_id);
                    $btnHtml = $this->getBtnNew(
                        url('Cancel/cancelzcy', ['dca_id' => $dcaInfo['id']]),
                        'btn btn-success btn-warning-tip',
                        '确定要签字吗？',
                        '提示',
                        '签字'
                    );
                }

            }
        }

        if(!$btnHtml){
            $this->addFileGrou($dcaInfo['dossier_id'],Constant::FILE_GROUP_chehuishenqing,$dca_id);
        }

        $dcaInfo['button'] = $btnHtml;

        return $this->displaySubInfo($dcaInfo , 'dcancel_table');
    }

    // 查看 披露 申请回避 详情 走这里
    public function piluhuibi()
    {

        if ($this->request->isPost()) {
            // 只有仲裁委可以操作
            $data = $this->request->param('row/a');
            $mater_id = $data['m_id'];
            $status = $data['status'];
            HuiBi::update($mater_id, ['status' => $status]);
            // 如果是受理 那么改卷宗的状态 记录日志
            $matter = HuiBi::getOne($mater_id);

            $logTypeArr = [
                1 => [
                    1 => DossierLog::LOG_TYPE_ACCEPT_PILU,
                    2 => DossierLog::LOG_TYPE_REFUSE_PILU,
                ],
                2 => [
                    1 => DossierLog::LOG_TYPE_ACCEPT_HUIBI,
                    2 => DossierLog::LOG_TYPE_REFUSE_HUIBI
                ]
            ];

            $logType = $logTypeArr[$matter['type']][$status];
//            if ($status == 1) {
//                Dossier::changeStatus($matter['dossier_id'], 7);
//                DossierLog::addLog($matter['dossier_id'], LoginUser::getIdid(), LoginUser::getUserName(), $logType);
//            } else {
//                DossierLog::addLog($matter['dossier_id'], LoginUser::getIdid(), LoginUser::getUserName(), $logType);
//            }
            $this->success('成功');
        } else {
            $mater_id = $this->request->param('m_id/d');
            $is_phone = $this->request->param('is_phone/d');
            $gid = $this->request->param('gid/d');
            $matter = HuiBi::getOne($mater_id,true);
            if(input('aa')==21){
                dump($matter);
            }
            // 未受理的 应该只有仲裁委可以看
            $this->addFileGrou($matter['dossier_id'], $gid, $mater_id);
           // $dRole = User::getDroleInDossier($matter['dossier_id'],LoginUser::getIdid());
            if(LoginUser::isZhongCaiWei()){
                if($matter['type']==2){ // 回避申请
                    // 判断状态
                    $html = '';
                    if($matter['status']==2&&LoginUser::isZhongCaiWeiZhuBan()){ // 主办操作
                        $html = $this->getBtnNew(
                            url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 1,'is_phone'=>$is_phone]),
                            'btn btn-success btn-warning-tip',
                            '确定要发送给主任吗？',
                            '提示',
                            '发送至主任'
                        );
                    }
                    if(in_array($matter['status'],[5,6])&&LoginUser::isZhongCaiLiAanShenPi()){ // 主任操作
                        $html = $this->getBtnNew(
                            url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 1,'is_phone'=>$is_phone]),
                            'btn btn-success btn-warning-tip',
                            '确定要审批吗？',
                            '提示',
                            '审批'
                        );
                    }
                    $matter['button'] = $html;
                }else{
                    if($matter['status']==2){
                        $matter['button'] = $this->getBtn(
                            url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 1,'is_phone'=>$is_phone]),
                            url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 0,'is_phone'=>$is_phone])
                        );
                    }
                }
            }
            
//            if($matter['status']==2){
//                $matter['button'] = $this->getBtn(
//                    url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 1]),
//                    url('dossier.cp/adminhuibipilu', ['qid' => $mater_id, 'ok' => 0])
//                );
//            }

            return $this->displaySubInfo($matter, 'huibi_table');
        }
    }
//$btn2show="hidden"
    private function getBtn($link1,$link2,$btn2show="",$btn1show=''){
        return '
    <a href="' . $link1 . '" class="btn btn-success btn-warning-tip '.$btn1show.'" data-tip="确定要转发吗？" data-title="提示">转发</a>
    <a href="' . $link2 . '" class="btn btn-yahoo btn-warning-tip '.$btn2show.'"   data-tip="确定要退回至提交人修改吗？" data-title="提示">建议修改</a>';
    }

    private function getBtnNew($link,$class,$tip,$title,$name){
        return '<a href="' . $link . '" class="'.$class.'" data-tip="'.$tip.'" data-title="'.$title.'">'.$name.'</a>';
    }

    public function zhengju(){

        $zid = $this->request->param('zid/d');
        $is_phone = $this->request->param('is_phone/d');
        if(!$zid){
            $this->error('参数错误');
        }
        $info = Dz::getDzInfo($zid);
        $info['addtime'] = date("Y-m-d H:i:s",$info['addtime']);


        $this->addFileGrou($info['dossier_id'],Constant::FILE_GROUP_zhengju_zhuanfa,$zid,$info['zids']);
        if ($info['status'] ==2&&LoginUser::isZhongCaiWeiZhuBan()){
            $info['button'] = $this->getBtn(
                url('dossier.cp/adminzhengju', ['qid' => $zid, 'ok' => 1,'is_phone'=>$is_phone]),
                url('dossier.cp/adminzhengju', ['qid' => $zid, 'ok' => 0,'is_phone'=>$is_phone])
            );
        }
       return $this->displaySubInfo($info,'zhengju_table');
    }

    public function piluhuibiView()
    {
        $form = new WsForm();
        $form->setMultiUrl("dma/index");
        $form->setAddUrl("dma/add");
        $form->setDelUrl("dma/del");
        $form->setEditUrl("dma/add");
        $form->setListUrl("");

        $form->addItem((new Item())->varName("m_id")->varTitle('')->inputType(InputType::hidden));
        $form->addItem((new Item())->varName("status")->varTitle("选择受理项")->inputType(InputType::radio)->required(true)->itemArr([1 => '同意', 2 => '拒绝']));
        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dossier/subinfo/piluhuibi");
    }


    public function gxqyy(){
        $gxid = $this->request->param('gxid/d');
        $is_phone = $this->request->param('is_phone/d');
        if(!$gxid) $this->error("参数错误");

        $gxqinfo = Gxq::getYyById($gxid);

        if($gxqinfo['status']>2){

            
            $this->addFileGrou($gxqinfo['d_id'],Constant::FILE_GROUP_gxq_dangshirenqingqiu,$gxid);
            $this->addFileGrou($gxqinfo['d_id'],Constant::FILE_GROUP_gxq_zhubanzhuanfa,$gxid);

        }else{

            $this->addFileGrou($gxqinfo['d_id'],Constant::FILE_GROUP_gxq_dangshirenqingqiu,$gxid);
        }

        if (LoginUser::isZhongCaiWeiZhuBan()) {
           if($gxqinfo['status'] ==2){
               $html = $this->getBtnNew(
                   url('Gxqyy/admingxqyy', ['gxid' => $gxid,'did'=>$gxqinfo['did'],'ok'=>1,'is_phone'=>$is_phone]),
                   'btn btn-success btn-warning-tip',
                   '确定要选择【有管辖权】吗？',
                   '提示',
                   '有管辖权'
               );
               $html .= '&nbsp;&nbsp;';
               $html .=  $this->getBtnNew(
                   url('Gxqyy/admingxqyy', ['gxid' => $gxid,'did'=>$gxqinfo['did'],'ok'=>0,'is_phone'=>$is_phone]),
                   'btn btn-yahoo  btn-warning-tip',
                   '确定要选择【没有管辖权】吗？',
                   '提示',
                   '没有管辖权'
               );
               $gxqinfo['button'] = $html;
           }elseif(in_array($gxqinfo['status'],[7,8])){
               $is_phone ==1 ? $html = $this->getBtnNew(
                   '',
                   'btn btn-success btn-warning-tip',
                   '确定要编辑管辖权异议决定书吗<br><br>请登录PC端操作<br><br>手机暂时无法编辑',
                   '提示',
                   '编辑管辖权异议决定书'
               ):
               $html = $this->getBtnNew(
                   url('admin/dossier.docedit/docview', ['id' => $gxqinfo['d_id'],'dmid'=>Constant::DOC_model_gxqyy_jueding,'extid'=>$gxid,'is_phone'=>$is_phone]),
                   'btn btn-success btn-warning-tip',
                   '确定要编辑管辖权异议决定书吗？',
                   '提示',
                   '编辑管辖权异议决定书'
               );
               $html .= '&nbsp;&nbsp;';
               $html .= $this->getBtnNew(
                   url('Gxqyy/admingxqyy', ['gxid' => $gxid,'did'=>$gxqinfo['did'],'ok'=>1,'is_phone'=>$is_phone]),
                   'btn btn-success btn-warning-tip',
                   '确定要发送至主任报批吗？',
                   '提示',
                   '报批'
               );
               $gxqinfo['button'] = $html;
           }
        }

        if(in_array($gxqinfo['status'],[3,4])&&LoginUser::isZhongCaiLiAanShenPi()){
           
            $html = $this->getBtnNew(
                url('Gxqyy/zhurencz', ['gxid' => $gxid,'did'=>$gxqinfo['did'],'ok'=>1,'is_phone'=>$is_phone]),
                'btn btn-success btn-warning-tip',
                '确定要同意并转发吗？',
                '提示',
                '同意'
            );
            $html .= '&nbsp;&nbsp;';
            $html .=  $this->getBtnNew(
                url('Gxqyy/zhurencz', ['gxid' => $gxid,'did'=>$gxqinfo['did'],'ok'=>0,'is_phone'=>$is_phone]),
                'btn btn-yahoo  btn-warning-tip',
                '确定要退回至主办吗？',
                '提示',
                '不同意'
            );
            $gxqinfo['button'] = $html;
        }

        return $this->displaySubInfo($gxqinfo,'gxqyy_table');
    }

    private function addFileGrou($did, $gid, $extid,$docIds='')
    {
        $this->_file_group[] = ["did" => $did, "gid" => $gid, "extid" => $extid,'docIds'=>$docIds];
    }

    private function getTableContent($tableData, $tpl)
    {
        $this->useLayout(false);
        $is_phone = $this->request->param('is_phone/d');
        $this->assign('is_phone', $is_phone);
        if(input("wl")==88){
            dump($is_phone);
        }
        return $this->fetch($tpl, ['tableContent' => $tableData]);
    }

    private function getListContent($listData)
    {
        $this->useLayout(false);
        return $this->fetch('subfiles', ['files' => $listData]);
    }

    private function displaySubInfo($tableData, $tpl)
    {
        $button = $tableData['button'];


        unset($tableData['button']);
        $this->assign('tablecontent', $this->getTableContent($tableData, $tpl));
        $listContent = [];
        foreach ($this->_file_group as $k => $v) {


            $listContent[] = Ddocs::getFilesHtml($this,$res = Ddocs::getFilesByGroup($v['did'], $v['gid'], $v['extid'],$v['docIds']),"材料",'','','',1,false,1);

        }

        if($_GET['zhz']==1){
            dump($res);
        }
        $this->useLayout($this->layout);
        $this->assign('listcontent', implode('<br/>', $listContent));
        $this->assign('button', $button);
        $is_phone = $this->request->param('is_phone/d');
        $this->assign('is_phone', $is_phone);

        $this->use_show_Js();
        return $this->fetch('subinfo');
    }

}