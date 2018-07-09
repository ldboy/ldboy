<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/3
 * Time: 上午11:32
 */
namespace app\admin\controller\dossier;


use app\common\controller\Backend;
use dossier\DossierDoc;
use wslibs\wszc\btn\Btn;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\defence\DefenceExpand;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DInfoValue;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\Dother;
use wslibs\wszc\Ds;
use wslibs\wszc\dtime\Dtime;
use wslibs\wszc\Dvalue;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\Gxq;
use wslibs\wszc\HuiBi;
use wslibs\wszc\LoginUser;
use wslibs\wszc\publicnumber\mylist\Mywaitdeal;
use wslibs\wszc\mes\Mes;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\User;
use wslibs\wszc\Dcancel;

class Info extends Backend
{

    private $role = [
        1 => '申请人',
        2 => '被申请人',
        3 => '申请人代理',
        4 => '被申请人代理',
        10 => '仲裁委',
        0 => '仲裁员'
    ];

    public function index()
    {
        $d_id = $this->request->param("id/d");
        if (!$d_id) {
            $this->error("参数错误");
        }
        $msgid = $this->request->param("msgid/d");
        if($msgid){
            (new Mes())->msg_chakan($msgid);
        }


        if (!(  in_array(   \wslibs\wszc\User::getRoleInDossier($d_id, LoginUser::getIdid()) ,array( Constant::QX_ROLE_ZHONGCAIWEI_MISHU,Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN,Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE,Constant::QX_ROLE_ZHONGCAIYUAN)))) {
            $this->redirect(url("dossier.info/view", array("id" => $d_id)));
            exit;
        }

        $this->assign("d_id", $d_id);

        $dangshiren = Dossier::getDangShiRen($d_id, Constant::getDangshirenRoles());

        foreach ($dangshiren as $k => $v){
            if($v['role'] == 3){
                unset($dangshiren[$k]);
            }
        }

        foreach ($dangshiren as $k => $v) {
            if($v['role']==2){
                if($v['r_no']-0>0){
                    $dangshiren[$k]['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$this->role[$v['role']];
                }else{
                    $dangshiren[$k]['role'] = $this->role[$v['role']];
                }
            }else{
                $dangshiren[$k]['role'] = $this->role[$v['role']];
            }
        }


        $this->assign('dsrnum',count($dangshiren));
        $this->assign("dangshiren", $dangshiren);


        $zhongcaiyuan = Db::name("arbitrator")->where("dossier_id = '$d_id' and status = 1")->order("id desc")->find();

        //仲裁员
        //$this->assign("dangshiren",Dossier::getDangShiRen($d_id,Constant::getDangshirenRoles()));
        $this->assign("zhongcaiyuan", $zhongcaiyuan);

        $q_role = User::getRoleInDossier($d_id);
        $d_num = 0;
        $q_num = 0;
        $dz_num = 0;
        $h_num = 0;
        $yy_num = 0;

        $d_num_total = 0;
        $q_num_total = 0;
        $dz_num_total = 0;
        $h_num_total = 0;
        $yy_total = 0;


        if (in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIWEI_MISHU,Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN))) {
            $role_qx = 1;
            if($q_role==Constant::QX_ROLE_ZHONGCAIWEI_MISHU){

                $role_qx = Constant::ZhongCaiWei_Role_ZhuBan;

            }elseif($q_role==Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN){

                $role_qx = $role_qx = Constant::ZhongCaiWei_Role_LianShenPi;
            }

            list($d_num,$d_num_total) = Mywaitdeal::getDbNum($d_id,$role_qx);

            list($q_num,$q_num_total) = Mywaitdeal::getZhiZNum($d_id,$role_qx);

            list($dz_num,$dz_num_total) = Mywaitdeal::getDzNum($d_id,$role_qx);

            list($h_num,$h_num_total) = Mywaitdeal::getHuiBiNum($d_id,$role_qx);

            list($yy_num,$yy_total) = Mywaitdeal::getGXQNum($d_id,$role_qx);
        }

        //判断是否有撤回申请
        if(Dcancel::getCancel($d_id)['status'] >= Dcancel::STATUS_WENJIANYITIJIAO){
            $this->assign('ch_num',1);
        }else{
            $this->assign('ch_num','');
        }
        $this->assign('d_num',$d_num?$d_num:'');
        $this->assign('d_num_total',$d_num_total?"[".$d_num_total."]":'');
        $this->assign('q_num',$q_num?$q_num:'');
        $this->assign('q_num_total',$q_num_total?"[".$q_num_total."]":'');
        $this->assign('dz_num',$dz_num?$dz_num:'');
        $this->assign('dz_num_total',$dz_num_total?"[".$dz_num_total."]":'');
        $this->assign('h_num',$h_num?$h_num:'');
        $this->assign('h_num_total',$h_num_total?"[".$h_num_total."]":'');
        $this->assign('yy_num',$yy_num?"[".$yy_num."]":'');
        $this->assign('yy_total',$yy_total?"[".$yy_total."]":'');

        $ywinfo = Db::name("dossier")->where("id = '$d_id'")->find();


        $ywinfo['time'] = Db::name('dossier_time')->where('id',$d_id)->find();

        $title_tip = DossierDoc::getInfoTitle($ywinfo['zno'],$ywinfo['time']['time30']);

        $this->assign('title_tip',$title_tip);
        foreach($ywinfo['time'] as $k=>$v){
            if($v){
                $key = $k.'_str';
                $ywinfo['time'][$key] = date('Y-m-d H:i:s',$v);
            }
        }
        $ywinfo['refuse'] = '';
        $ywinfo['status_int'] = $ywinfo['status'];
        if($ywinfo['status']==0){
            $ywinfo['refuse'] = Dvalue::getUniqueValueOfDossier($d_id,'Refuse');
        }

        //分配隐藏列表状态
        if ($ywinfo['status'] >= 3) {
            $this->assign("is_shouli", 1);
        }
        if ($ywinfo['status'] >= 5 && $ywinfo['status'] <= 10) {
            $this->assign("is_zcy", 1);
        }
        if ($ywinfo['status'] >= 4 && $ywinfo['status'] <= 10) {
            $this->assign("is_dbzz", 1);
        }


        //申请撤回列表
        $dcancellist = Dcancel::getCancel($d_id,true);
        $this->assign("dcancellist",$dcancellist);


        //业务状态
        $this->assign("YwStatus", $ywinfo['status']);


        $ywinfo['addtime'] = date("Y-m-d H:i:s", $ywinfo['addtime']);

        $ywinfo['shenqingr'] = Db::name("dossier_users")
            ->where("dossier_id", $d_id)
            ->where("role", 1)
            ->value("name");


        // 质证列表
        $questionList = QuestionExpand::getQuestionList($d_id);
        $this->assign('questionList', $questionList);

        // 答辩列表
        $defenceList = DefenceExpand::getDefenceList($d_id);

        foreach ($defenceList as $k=>$v){
            if($v['r_no']-0>0){
                $defenceList[$k]['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$this->role[$v['role']];
            }
        }


        $this->assign('defenceList', $defenceList);


        // 操作按钮

        $btnHtml = Btn::getBtnHtml($d_id, LoginUser::getIdid());


        $this->assign("btnList", $btnHtml);


        //披露/回避记录
        $piluHuibi = HuiBi::HuiBiList($d_id);

        $this->assign('piluHuibi', $piluHuibi);



        // 日志列表
        $logList = DossierLog::getLogs($d_id);
        $this->assign('logList', $logList);


        // 仲裁员数量
        $zcyNum = Db::name("arbitrator")->where("dossier_id = '$d_id'")->where('status', 1)->count();
        $this->assign("zcyNum", $zcyNum);


        // 日志数量
        $this->assign('logNum', "[".DossierLog::getLogNum($d_id)."]");


        $sub_status = Dossier::$arrSubStatus;

        $ywinfo['status'] = $sub_status[$ywinfo['sub_status']];
        $this->assign("jibenziliao", $ywinfo);


        // 证据列表
        $zhengJuList = Dz::getZhengJuList($d_id);

        foreach ($zhengJuList as $k=>$v){
            if($v['r_no']-0>0){
                $zhengJuList[$k]['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$this->role[$v['role']];
            }
        }
        $this->assign('zhengJuList', $zhengJuList);

        if ($q_role == Constant::QX_ROLE_ZHONGCAIYUAN)
        {
            $files = Ds::getFilesOfOne($d_id, [LoginUser::getIdid(),Db::name("jigou")->where("id",$ywinfo['zc_jg_id'])->value("idid")],Constant::zcyNotSee());

            $this->assign('is_zcy',1);
        }else{
            $files = Ds::getFilesOfOne($d_id, [LoginUser::getIdid(), LoginUser::getRoleThIdId()]);
        }


        $html = '';
        foreach ($files as $k => $v) {

            $html .= Ddocs::getFilesHtml($this, array_column($v['item'], 'doc_id'), $v['title'],"box-info",$v['time'],1);
        }


        $is_zhuban = LoginUser::isZhongCaiWeiZhuBan();

        if($is_zhuban){
            $this->assign("is_zhuban",1);
        }
        $is_zhuren = LoginUser::isZhongCaiLiAanShenPi();
        if($is_zhuren){
            $this->assign("is_zhuren",1);
        }
        $is_zcy = LoginUser::isZhongCaiYuan();
        if($is_zcy){
            $this->assign("is_zcy",1);
        }


        $gxqyyList = Gxq::getYyList($d_id);
        $this->assign('gxqyyList',$gxqyyList);


        $otherList = Dother::otherList($d_id);
        $this->assign('otherList',$otherList);


        $this->assign('filehtml',  Ddocs::getFilesLayout($this,$html));
        $this->assign('time_info',  Dtime::getTimeGroupForDid($d_id));

        $this->useLayout($this->layout);
        return $this->fetch();
    }


    public function view()
    {
        $d_id = $this->request->param("id/d");
        if (!$d_id) {
            $this->error("参数错误");
        }
        if ((  in_array(   \wslibs\wszc\User::getRoleInDossier($d_id, LoginUser::getIdid()) ,array( Constant::QX_ROLE_ZHONGCAIWEI_MISHU,Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN,Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE,Constant::QX_ROLE_ZHONGCAIYUAN)))) {
            $this->redirect(url("dossier.info/index", array("id" => $d_id)));
            exit;
        }

        $sub_status = Dossier::$arrSubStatus;
        $ywinfo = Dossier::getSimpleDossier($d_id);
        $ywinfo['status_int'] = $ywinfo['status'];
        $ywinfo['status'] = $sub_status[$ywinfo['sub_status']];
        $ywinfo['addtime'] = date("Y-m-d H:i:s", $ywinfo['addtime']);
        $ywinfo['time'] = Db::name('dossier_time')->where('id',$d_id)->find();
        foreach($ywinfo['time'] as $k=>$v){
            if($v){
                $key = $k.'_str';
                $ywinfo['time'][$key] = date('Y-m-d H:i:s',$v);
            }
        }
        $ywinfo['refuse'] = '';

        if($ywinfo['status']==0){
            $ywinfo['refuse'] = Dvalue::getUniqueValueOfDossier($d_id,'Refuse');
        }
        if(input("sys")==221){
            dump($ywinfo);
        }

        $sqr = Dossier::getDangShiRen($d_id, Constant::D_Role_ShenQingRen)[0];

        $ywinfo['sqr'] = $sqr['name'];

        if (input("sy") == 1) {
            dump($ywinfo);
        }


        $dangshiren = Dossier::getDangShiRen($d_id, Constant::getDangshirenRoles());
        foreach ($dangshiren as $k => $v){
            if($v['role'] == 3){
                unset($dangshiren[$k]);
            }
        }

        if(input('wsw')=='0608'){
            dump($dangshiren);
        }


        foreach ($dangshiren as $k => $v) {
            if($v['role']==2){
                if($v['r_no']){
                    $dangshiren[$k]['role'] = '第'.DInfoValue::num2Upper($v['r_no']).$this->role[$v['role']];
                }else{
                    $dangshiren[$k]['role'] = $this->role[$v['role']];
                }
            }else{
                $dangshiren[$k]['role'] = $this->role[$v['role']];
            }
        }


        $daohanglist = array();




        if (LoginUser::isRole(Constant::Admin_Role_yinhang))
        {
            $files = Ds::getFilesOfOne($d_id, [LoginUser::getIdid(),LoginUser::getRoleThIdId()]);
        }else{
            if(in_array(User::getDroleInDossier($d_id,LoginUser::getIdid()),[Constant::D_Role_Beo_ShenQingRen_FR,Constant::D_Role_Bei_ShenQingRen,Constant::D_Role_Bei_ShenQingRen_Dl])){


                $bsqr_com_idid = LoginUser::getBsqrComIdId($d_id);
                $idids = $bsqr_com_idid ? [LoginUser::getIdid(),$bsqr_com_idid] : [LoginUser::getIdid()];

                $files = Ds::getFilesOfOne($d_id,$idids);
            }else{
                $files = Ds::getFilesOfOne($d_id, [LoginUser::getIdid()]);
            }

        }




        if(input('wsw')==5069){
            dump($files);


            //dump(Ddocs::getFilesLayout($this,$html));
        }


        $html = '';
        foreach ($files as $k => $v) {
            $html .= Ddocs::getFilesHtml($this, array_column($v['item'], 'doc_id'), $v['title'],"box-info",$v['time'],1,0);
            $daohanglist[] = array("title" => $v['title']);
        }


        // 操作按钮

        // 操作按钮
        $btnHtml = Btn::getBtnHtml($d_id, LoginUser::getIdid());

        $this->assign("btnList", $btnHtml);
        $this->assign('time_info',  Dtime::getTimeGroupForDid($d_id));


        if(input('wsw')==506){
            dump(Dtime::getTimeGroupForDid($d_id));

            
            //dump(Ddocs::getFilesLayout($this,$html));
        }






        $this->assign('filehtml', Ddocs::getFilesLayout($this,$html,0));
        $this->assign('daohanglist', $daohanglist);
        $this->assign("dangshiren", $dangshiren);
        $this->assign("jibenziliao", $ywinfo);


        $this->useLayout($this->layout);
        return $this->fetch();
    }

    private function getFilesHtml($d_id, $gid, $extid, $title, $class = "")
    {
        return Ddocs::getGroupFilesHtml($this, $d_id, $gid, $extid, $title, $class);
    }
}