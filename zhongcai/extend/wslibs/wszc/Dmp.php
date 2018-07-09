<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/9
 * Time: 下午7:21
 * 核心操作类 在文件签字后做处理
 *
 */


namespace wslibs\wszc;

use app\common\controller\Backend;
use think\Cache;
use think\Db;
use wslibs\run\WsExce;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\dmail\Dmail;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\notice\Dnotice;
use wslibs\wszc\notice\Notice;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\qx\Qx;
use wslibs\wszc\Statement\Statement;
use wslibs\wszc\Dcancel;
use wslibs\wszc\Dvalue;

class Dmp
{

    private static $errMsg = '';
    private $_did;
    private $_gid;
    private $_exid;
    private $_uid;
    private $_docs;


    public static function doDmp($d_id, $gids, $exid, &$errMsg)
    {

        if (!is_array($gids))
            $gids = array_filter(explode(",", $gids));

        Db::startTrans();
        if (Dmp::doSubmit($d_id, $gids, $exid, User::getLoginUid(), $errMsg)) {
            Db::commit();
            // 清除DInfoValue缓存
            Cache::clear(Constant::Cache_pre_dinfoValue . $d_id);
            // 清除dossier缓存
            Cache::clear(Constant::Cache_tag_pre_dossier . $d_id);
            self::doSend($d_id, $gids, $exid, User::getLoginUid());

            return true;
        } else {
            Db::rollback();
            return false;
        }
    }

    public static function getSubmitTip($did, $gids, $ext_id, $uid)
    {
        $btnArr = [];
        $gid = is_array($gids) ? $gids[0] : $gids;
        switch ($gid) {
            case 1:
                $btnArr['btn_title'] = '提交';
                $btnArr['btn_tip'] = '确定要将申请资料提交至石家庄仲裁委吗？此操作不可逆，请仔细核对！';
                break;
            case 2:
            case 13:
                $btnArr['btn_title'] = '发送';
                $btnArr['btn_tip'] = '确定要将资料发送至案件相关人员吗？此操作不可逆！';
                break;
            case 3:
                $btnArr['btn_title'] = '提交';
                $btnArr['btn_tip'] = '确定要提交答辩至石家庄仲裁委吗？此操作不可逆！';
                break;
            case 4:
                $btnArr['btn_title'] = '发送至当事人及仲裁员';
                $btnArr['btn_tip'] = '确定要将《组庭通知书》发送至当事人吗？此操作不可逆！';
                break;
            case 16:
                $btnArr['btn_title'] = '发送至当事人及仲裁员';
                $btnArr['btn_tip'] = '确定要将《重新组庭通知书》发送至当事人吗？此操作不可逆！';
                break;
            case 6:
                $btnArr['btn_title'] = '提交至石家庄仲裁委';
                $btnArr['btn_tip'] = '确定要将《回避申请书》发送至石家庄仲裁委吗？此操作不可逆！';
                break;

            case 7:
                $btnArr['btn_title'] = '提交至石家庄仲裁委';
                $btnArr['btn_tip'] = '确定要将《质证意见》发送至石家庄仲裁委吗？此操作不可逆！';
                break;

            case 33:
                $btnArr['btn_title'] = '提交至石家庄仲裁委';
                $btnArr['btn_tip'] = '确定要将《撤回仲裁请求申请书》发送至石家庄仲裁委吗？此操作不可逆！';
                break;
            default:
                $btnArr['btn_title'] = '确定';
                $btnArr['btn_tip'] = '确定要提交吗？';
        }
        return $btnArr;
    }

    private static function doSubmit($did, $gids, $ext_id, $uid, &$errMsg = '')
    {

        foreach ($gids as $gid) {
            $docs = Ddocs::getFilesByGroup($did, $gid, $ext_id);
            $dmp = new Dmp($did, $gid, $ext_id, $uid, $docs);


            //公共检测，比如文件是否完成

            if (!Dcheck::checkSubmitFiles($did, $gids, $ext_id, $uid)) {
                $errMsg = '请检查需要上传的证据是否都已上传';
                return false;
            }

            if(!((new Dauth($gid,$did))->CheckAuth())){
                $errMsg = "没有权限！";

                return false;
            }

            $check_fun = "check_" . $gid;
            if (method_exists($dmp, $check_fun)) {
                if (!$dmp->{$check_fun}()) {
                    $errMsg = "权限失败";
                    return false;
                }
            }

            $submit_fun = "submit_" . $gid;
            if (method_exists($dmp, $submit_fun)) {
                if (!$dmp->{$submit_fun}()) {
                    $errMsg ="操作失败";
                    return false;
                }


            }
        }
        return true;
    }


    private static function doSend($did, $gids, $ext_id, $uid)
    {
        foreach ($gids as $gid) {
            // $docs = Ddocs::getFilesByGroup($did, $gid, $ext_id);
            // Dnotice::whenDmpFinish($did, $gid, $ext_id, $uid, $docs);
            WsExce::dsend($did, $gid, $ext_id);
        }
    }

    public function __construct($did, $gid, $ext_id, $uid, $docs)
    {
        $this->_did = $did;
        $this->_gid = $gid;
        $this->_exid = $ext_id;
        $this->_uid = $uid;
        $this->_docs = $docs;
    }


    public function submit_33()
    {
        /*dump($this->_did);dump($this->_exid);dump($this->_gid);die;*/
        Dcancel::changeStatus($this->_did, Dcancel::STATUS_WENJIANYITIJIAO);
        $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        $sgid = Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        //Dcancel::saveCancel($this->_did,['sgid'=>$sgid]);
        Dvalue::saveUniqueValueToDossier($this->_did, "chehuiTime", date("Y年m月d日", time()));
        return true;
    }

    // 撤回申请 主办转发
    public function submit_34()
    {

        Dcancel::changeStatus($this->_did, Dcancel::STATUS_ZHUBANCHULI);
        $info = Dcancel::getgetCancelById($this->_exid);
        $sgid = 0;
        if ($info['type'] == 2) {
            $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        } elseif ($info['type'] == 3) {
            $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiYuan, [], $this->_exid);
        }
        Dcancel::saveCancel($this->_did, ['sgid' => $sgid]);
        return true;
    }

    // 撤回申请 主任转发
    public function submit_36()
    {
        Dcancel::changeStatus($this->_did, Dcancel::STATUS_ZHURENCHULI);
        $info = Dcancel::getgetCancelById($this->_exid);
        // 发给申请人
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);
        if ($info['type'] == 3) {// 发给被申请人
            Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [], $this->_exid);
        }
        Dossier::changeStatus($this->_did, 0);
        return true;
    }

    // 撤回申请 主办转发 组庭后
    public function submit_37()
    {
        return $this->submit_34();
    }

    // 撤回申请 仲裁员转发 组庭后
    public function submit_39()
    {
        // 这里应该发给主任盖章去
        Dcancel::changeStatus($this->_did, Dcancel::STATUS_ZHONGCAIYUANCHULI);
        $info = Dcancel::getgetCancelById($this->_exid);
        $sgid = 0;
        if ($info['type'] == 3) {
            $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        }
        Dcancel::saveCancel($this->_did, ['sgid' => $sgid]);
        return true;
    }

    // 组庭后的申请撤回 主任转发
    public function submit_41()
    {
        return $this->submit_36();
    }

    // 检测统一加时间限制

    public function check_1()
    {

        // 只有申请人可以操作申请 并且只有dossier状态是 1 时可以操作
        if (!in_array(User::getRoleInDossier($this->_did), [Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_DL])) {
            self::$errMsg = '没有权限';
            return false;
        }
        $dossierInfo = Dossier::getSimpleDossier($this->_did);
        if ($dossierInfo['status'] != Constant::DOSSIER_STATUS_GANGCHUANGJIAN) {
            self::$errMsg = '此状态下不可进行此项操作';
            return false;
        }
        return true;
    }

    public function check_2()
    { //check_2 与 check_13 是一起的  有一个即可
        // 如果不是管理员 也不是主任 那么不可以发送


//
//        if (!Dossier::isStatus($this->_did, 22)) {
//            self::$errMsg = '此状态下不可进行此项操作';
//            return false;
//        }
        if (!LoginUser::isZhongCaiLiAanShenPi()) {
            self::$errMsg = '没有权限';
            return false;
        }

        return true;
    }

    // 改变状态和添加日志
    private function changeAndLog($changeStatus, $logType)
    {
        $res = Dossier::changeStatus($this->_did, $changeStatus);
        if (!$res) {
            return false;
        }
        $name = LoginUser::getUserName();
        if (!$name) {
            return false;
        }
        $res1 = DossierLog::addLog($this->_did, $this->_uid, $name, $logType);
        if (!$res1) {
            return false;
        }
        return true;
    }

    // 申请提交 改变状态 加 添加时间  日志记录 文件添加权限
    public function submit_1()
    {
        // 发送短信
        Notice::sendToZhongCaiWei($this->_did);
        // 给自己  和  主办 发送文件
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou);
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid());
        $this->changeAndLog(2, DossierLog::LOG_TYPE_COMPLETE);
        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU], array_column($this->_docs, 'id'));
        return true;
    }

    // 受理 改变状态 给申请人发送文件
    public function submit_2()
    {




        
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen);
        Ds::sendGroupFileToDocRole($this->_did, Constant::FILE_GROUP_shouli_fagei_jigou, Constant::D_Role_ZhongCaiWei_JiGou);
        $this->changeAndLog(Dossier::makeStatus(3, 30), DossierLog::LOG_TYPE_SEND_ACCEPT_FILE);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));

        return true;
    }

    public function getZjIds()
    {
        $arr = array_column(array_filter($this->_docs, function ($val) {
            return $val['file_type'] == 1;
        }), 'id');
        return implode(',', $arr);
    }

    // 答辩 给仲裁委添加权限
    public function submit_3()
    {
        // 改答辩的状态
        Db::name('dossier_defence')->where('id', $this->_exid)->update(['status' => 2, "stime" => time(), "is_sign" => 1]);


        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU], array_column($this->_docs, 'id'));

        $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        DefenceExpand::addDefenceFile($this->_exid, $sgid, $this->getZjIds());

        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        return true;
    }

    //受理   给被申请人发送文件
    public function submit_13()
    {
        // 找到所有的被申请人
        // $res = DefenceExpand::addDefence($d_id,$v['idid']);
        $sgid = (int)Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen);
        $users = Drole::getUsersByRole($this->_did, Constant::D_Role_Bei_ShenQingRen);
        $third_id = Dossier::getSimpleDossier($this->_did)['third_jg_id'];
        foreach ($users as $k => $v) {
            $res = DefenceExpand::addDefence($this->_did, $v['idid']);
            if (!$res) {
                self::$errMsg = '添加答辩失败';
                return false;
            }

            $res = QuestionExpand::ManagerQuestion(
                $this->_did,
                $v['idid'],
                Constant::D_Role_Bei_ShenQingRen,
                array_column(array_filter($this->_docs, function ($val) {
                    return $val['file_type'] == 1;
                }), 'id'),
                $sgid,
                Db::name("dossier_users")->where("dossier_id",$this->_did)->where("role",1)->where("type",2)->value("idid")
            );

            if (!$res) {
                self::$errMsg = '添加质证失败';
                return false;
            }
        }


        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));


        return true;
    }

    // 组庭
    public function submit_4()
    {
        self::changeAndLog(Dossier::makeStatus(3, 32), DossierLog::LOG_TYPE_SET_UP_COURT);
        // 组庭状态改变
        Db::name('court')->where('id', $this->_exid)->update(['status' => 3]);
        Qx::addQxToDoc(
            [
                Constant::QX_ROLE_BEISHENQINGREN,
                Constant::QX_ROLE_SHENQINGREN,
                Constant::QX_ROLE_ZHONGCAIYUAN
            ], array_column($this->_docs, 'id'));
        $find = Db::name('arbitrator')->where('court_id', $this->_exid)->find();

        $zcyInfo = Db::name("jigou_zcy")
            ->alias("jz")
            ->field("zc.idid,jz.jg_id,zc.name")
            ->join("zcy zc", "jz.zcy_id = zc.id", "LEFT")
            ->where("zc.id = " . $find['zcy_uid'])
            ->find();
        Drole::delRoleByRole($this->_did, Constant::D_Role_ZhongCaiYuan);
        Drole::addRole($this->_did, $zcyInfo['idid'], $zcyInfo['name'], Constant::D_Role_ZhongCaiYuan, $find['zcy_uid'], $zcyInfo['jg_id']);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen_Dl, [], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen_Dl, [], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiYuan, [], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        HuiBi::createPiluHuiBi($this->_did, $this->_exid);
        return true;
    }


    // 申请人质证 改质证的发送文件状态  发送文件给仲裁委 仲裁员
    public function submit_7()
    {
        QuestionExpand::sendFileFinish($this->_did, $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU], array_column($this->_docs, 'id'));
        return true;
    }

    // 被申请人质证 改质证的发送文件状态 发送文件给仲裁委 仲裁员
    public function submit_8()
    {
//        QuestionExpand::sendFileFinish($this->_did,$this->_exid);
//        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN], array_column($this->_docs, 'id'));
        return true;
    }

    // 申请人对被申请人的证据质证
    public function submit_12()
    {
        QuestionExpand::sendFileFinish($this->_did, $this->_exid);
        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN], array_column($this->_docs, 'id'));
        return true;
    }

    // 被申请人对申请人的证据质证提交  改质证的状态
    public function submit_11()
    {
        QuestionExpand::sendFileFinish($this->_did, $this->_exid);
        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN], array_column($this->_docs, 'id'));
        return true;
    }

    // 仲裁员披露  文件添加权限 披露状态改变
    public function submit_14()
    {


        $sid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        HuiBi::subtoZhongCaiWei($this->_exid, $sid);
        Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ADMIN], array_column($this->_docs, 'id'));


        return true;
    }

    // 仲裁员签署裁决书
    public function submit_15()
    {
        Ds::sendGroupFileToDocRole($this->_did, Constant::FILE_GROUP_caijue, Constant::D_Role_ZhongCaiWei_JiGou);
        Ds::sendGroupFileToUid($this->_did, Constant::FILE_GROUP_caijue, LoginUser::getIdid());
        Dcaijue::autoSend($this->_did);
        return true;
    }

    public function submit_16()
    {
        // Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN, Constant::QX_ROLE_ADMIN], array_column($this->_docs, 'id'));
        return $this->submit_4();
    }

    // 给  立案审批  发送文件
    public function submit_17()
    {
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou);
        Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN], array_column($this->_docs, 'id'));
        Dossier::changeStatus($this->_did, Dossier::makeStatus(2, 22));
        return true;
    }

    // 仲裁员声明  声明后自动发送 不需要主办操作了
    public function submit_5()
    {


        $ids = Db::name("dr")->field("id")->where("dossier_id = " . $this->_did)->select();
        $ids = array_column($ids, "id");

        $sid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        HuiBi::subtoZhongCaiWei($this->_exid, $sid);
        Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ADMIN], array_column($this->_docs, 'id'));
        Dcaijue::updateStatus($this->_did, 1);
        Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIYUAN], $ids);

        // 声明转发
        return $this->submit_23();

    }

    public function submit_6()
    {
        Dvalue::saveUniqueValueByDocMode($this->_did, Constant::DOC_model_huibishenqing, "huibi_date", date("Y年m月d日"), $this->_exid);
        $zcy = Drole::getUsersByRole($this->_did,Constant::D_Role_ZhongCaiYuan);

    
        $sid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [$zcy[0]['idid']], $this->_exid);
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        HuiBi::subtoZhongCaiWei($this->_exid, $sid);
        Qx::addQxToDoc([Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ADMIN], array_column($this->_docs, 'id'));

        return true;
    }

    // 质证意见转发
    public function submit_18()
    {
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);
        $qinfo = QuestionExpand::getOne($this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [$qinfo['idid']], $this->_exid);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));
        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));
        QuestionExpand::AdminChuLiQues($this->_exid, 1);

//        Dmail::instance()->sendQuestionZhengJu($this->_did, $this->_exid);
        return true;
    }

    // 答辩转发
    public function submit_19()
    {
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);
        $qinfo = DefenceExpand::getDefenceFind($this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [$qinfo['idid']], $this->_exid);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));
        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));
        DefenceExpand::shouLiOrJuJue($this->_exid, 1);

        $users = Drole::getUsersByRole($this->_did, Constant::D_Role_ShenQingRen);
        foreach ($users as $k => $v) {


            $res = QuestionExpand::ManagerQuestion(
                $this->_did,
                $v['idid'],
                Constant::D_Role_ShenQingRen,
                array_column(array_filter($this->_docs, function ($val) {
                    return $val['file_type'] == 1;
                }), 'id'),
                0,
                $qinfo['idid']
            );

            if (!$res) {
                self::$errMsg = '添加质证失败';
                return false;
            }
        }
//        Dmail::instance()->sendDaBianZhengJu($this->_did, $this->_exid);
        return true;
    }


    public function submit_22()
    {
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);

        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [], $this->_exid);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));
        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));
        HuiBi::shouLiOrJuJue($this->_exid, 1);
        return true;
    }

    // 声明转发
    public function submit_23()
    {
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);

        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [], $this->_exid);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));
        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));
        HuiBi::shouLiOrJuJue($this->_exid, 1);
        return true;
    }

    public function submit_24()
    {

        Ds::sendGroupFileToDocRole($this->_did, Constant::FILE_GROUP_caijue_fasongzhizhuren, Constant::D_Role_ZhongCaiWei_CaiJueShenPi);
        return true;
    }

    // 答辩 给仲裁委添加权限
    public function submit_25()
    {
        // 改答辩的状态


        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU], array_column($this->_docs, 'id'));

        $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);

        Dz::subtoZhongCaiWei($this->_exid, $sgid, array_column($this->_docs, "id"));
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        return true;
    }


    public function submit_40()
    {

        Qx::addQxToDoc([Constant::QX_ROLE_ADMIN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU], array_column($this->_docs, 'id'));

        $sgid = Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $this->_exid);

        Dz::subtoZhongCaiWei($this->_exid, $sgid, array_column($this->_docs, "id"));
        Ds::sendGroupFileToUid($this->_did, $this->_gid, LoginUser::getIdid(), $this->_exid);
        return true;
    }


    public function submit_26()
    {
        $qinfo = Dz::getDzInfo($this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [$qinfo['idid']], $this->_exid);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [$qinfo['idid']], $this->_exid);
        Qx::addQxToDoc(Constant::QX_ROLE_SHENQINGREN, array_column($this->_docs, 'id'));
        Qx::addQxToDoc(Constant::QX_ROLE_BEISHENQINGREN, array_column($this->_docs, 'id'));
        Dz::shouLiOrJuJue($this->_exid, 1);

//        if ($qinfo['role'] == Constant::D_Role_Bei_ShenQingRen) {
//            $torole = Constant::D_Role_ShenQingRen;
//        } else {
//            $torole = Constant::D_Role_Bei_ShenQingRen;
//        }


        $users = Drole::getUsersByRole($this->_did, [Constant::D_Role_ShenQingRen,Constant::D_Role_Bei_ShenQingRen]);

        foreach ($users as $k => $v) {

            if($qinfo['idid']==$v['idid']){
                // 不给提交人质证机会
                continue;
            }
            $res = QuestionExpand::ManagerQuestion(
                $this->_did,
                $v['idid'],
                $v['role'],
                array_column(array_filter($this->_docs, function ($val) {
                    return $val['file_type'] == 1;
                }), 'id'), 0
                ,
                $qinfo['idid']
            );

            if (!$res) {
                self::$errMsg = '添加质证失败';
                return false;
            }
        }

//        Dmail::instance()->sendZhengjuZhuanfa($this->_did, $this->_exid);

        return true;
    }

    public function submit_27()
    {
        Ds::sendGroupFileToDocRole($this->_did, Constant::FILE_GROUP_caijue_fasongsuoyouren, Constant::D_Role_ShenQingRen, [], 0);

        Ds::sendGroupFileToDocRole($this->_did, Constant::FILE_GROUP_caijue_fasongsuoyouren, Constant::D_Role_Bei_ShenQingRen, [], 0);

        //  DocContract::autoSign(Ddocs::getOrInitFile($did, Constant::DOC_model_caijueshu, 0), LoginUser::getIdid(),0);
        return true;
    }

    public function submit_30()
    {
        Dossier::changeStatus($this->_did, 3);
        return true;
    }

    // 申请回避 回复
    public function submit_31()
    {
        $info = HuiBi::getOne($this->_exid);
        // 主办在 2 时操作  操作成功后 变成 5   6
        if ($info['status'] == 5 || $info['status'] == 6) { // 主办操作到这里
            // 此时不发给仲裁员
            $zcy = Drole::getUsersByRole($this->_did,Constant::D_Role_ZhongCaiYuan);
            Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [$zcy[0]['idid']], $this->_exid);
            Qx::addQxToDoc([
                Constant::QX_ROLE_ZHONGCAIWEI_MISHU,
                Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE,
            ],
                array_column($this->_docs, 'id'));
//            HuiBi::shouLiOrJuJue($this->_exid, 5);
        }

        // 主任在 5  6 时操作  操作成功后 变成 3  4
        if ($info['status'] == 3||$info['status']==4) { // 主任操作到这里
            
            Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ShenQingRen, [], $this->_exid);
            Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_Bei_ShenQingRen, [], $this->_exid);
            Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiYuan, [], $this->_exid);
            Qx::addQxToDoc([
                Constant::QX_ROLE_SHENQINGREN,
                Constant::QX_ROLE_BEISHENQINGREN,
            ],
                array_column($this->_docs, 'id'));
//            HuiBi::shouLiOrJuJue($this->_exid, 1);
        }

        return true;
    }

    // 管辖权异议  提交至仲裁委
    public function submit_42(){
        $info = Gxq::getYyById($this->_exid);
        if($info['status']!=1){
            return false;
        }
        // 发给主办
        $zcy = Drole::getUsersByRole($this->_did,Constant::D_Role_ZhongCaiYuan);
        Ds::sendGroupFileToDocRole($this->_did, $this->_gid, Constant::D_Role_ZhongCaiWei_JiGou, [$zcy[0]['idid']], $this->_exid);
        Gxq::subToZcw($this->_exid);
        return true;
    }

    public function submit_44(){
        Ds::sendGroupFileToDocRole($this->_did,Constant::FILE_GROUP_gxq_fasongsuoyou,[Constant::D_Role_Bei_ShenQingRen,Constant::D_Role_ShenQingRen,Constant::D_Role_ZhongCaiWei_JiGou],[],$this->_exid);
        $gxinfo = Gxq::getYyById($this->_exid);
        Gxq::changeYyStatus($this->_exid,$gxinfo['status']+2);
        return true;
    }

}