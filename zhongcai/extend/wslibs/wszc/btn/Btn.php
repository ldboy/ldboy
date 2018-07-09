<?php
namespace wslibs\wszc\btn;

use think\Db;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\Constant;
use wslibs\wszc\Control;
use wslibs\wszc\Court;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\Dossier;
use wslibs\wszc\Dother;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\HuiBi;
use wslibs\wszc\LoginUser;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\User;
use wslibs\wszc\Dcancel;
use wslibs\wszc\Gxq;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/21
 * Time: 下午12:38
 */
class Btn
{
    public static function getBtnHtml($did, $idid,$isphone=false)
    {
        $info = Dossier::getSimpleDossier($did);
        $q_role = User::getRoleInDossier($did, $idid);

        $status = $info['status'];
        $sub_status = $info['sub_status'];

        if ($status == 1) //申请中
        {
            if (in_array($q_role, array(Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_fr))) {
                Control::addBtn(1, ["dossier_id" => $did]);//提交申请
            }
        }
        if (in_array($status, array(1, 2, 3))) {

        }

        if (in_array($q_role, array(Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_fr))) {
            if ($sub_status == 5) {
                Control::addBtn(1, ["dossier_id" => $did, "title" => "重新申请",]);//提交申请
            }

            if ($sub_status >= 10 && $sub_status <= 20) {
                Control::addBtn(12, ["id" => $did, "title" => "撤回"]);//提交申请
                Control::addBtn(28, ["id" => $did, "title" => "撤回修改", 'c_t' => 1]);//提交申请

            } else if ($sub_status > 20 && $sub_status < 40) {
                if(!Dcancel::getCancel($did)){
                    Control::addBtn(12, ["id" => $did]);//提交申请
                }
            }
        }


        if ($sub_status == 20 || $sub_status == 21)//受理中
        {
            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {


                Control::addBtn(2, ["id" => $did]);//受理申请审批
                Control::addBtn(4, ["id" => $did]);//拒绝

            }
        }

        if ($sub_status == 22) {


            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {


//                if ($did==172)
//                {
                Control::addBtn(29, ["id" => $did]);//立案审批
//                }else
//                Control::addBtn(17, ["id" => $did]);//立案审批
                Control::addBtn(26, ["id" => $did]);//拒绝立案审批

            }

        }
        if ($sub_status == 23) {


            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {


                Control::addBtn(102, ["title" => "立案文件整理发送中", "check_tag" => "d_sub_status", "check_value" => $did . "_" . "23"]);


            }

        }
        if($sub_status>23&&$sub_status<40){

            if(in_array($q_role,[Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_fr])){
                //变更仲裁请求
                Control::addBtn(30,[]);
            }elseif(in_array($q_role,[Constant::QX_ROLE_BEISHENQINGREN,Constant::QX_ROLE_BEI_SHENQINGREN_fr,Constant::QX_ROLE_BEISHENQINGREN_DL])){
                //  反请求
                Control::addBtn(31,[]);
            }
            
        }

        if($sub_status>23&&$sub_status<31){
            //管辖权异议 需要在答辩期完成
            if (in_array($q_role, array(Constant::QX_ROLE_BEISHENQINGREN, Constant::QX_ROLE_BEISHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_fr))) {
                $gxyyInfo = Gxq::hasYy($did);
                if(!$gxyyInfo||$gxyyInfo['status']<2){
                    Control::addBtn(32, array("id" => $did));
                }else{
                    if($gxyyInfo['status']==4){
                        Control::addBtn(32, array("id" => $did,'title'=>'管辖权异议（修改）'));
                    }
                }
            }
        }

        if ($sub_status == 35) {


            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {


                Control::addBtn(102, ["title" => "组庭文件整理发送中", "check_tag" => "d_sub_status", "check_value" => $did . "_" . "35"]);


            }

        }
//        if ($sub_status == 30) {
//            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {
//
//                Control::addBtn(3, ["id" => $did]);//跳转到发送文件
//
//            }
//        }



        if ($sub_status == 30) {
            if (in_array($q_role, array(
                Constant::QX_ROLE_BEISHENQINGREN,
                Constant::QX_ROLE_BEISHENQINGREN_DL,
                Constant::QX_ROLE_BEI_SHENQINGREN_fr,
                Constant::QX_ROLE_SHENQINGREN_DL,
                Constant::QX_ROLE_SHENQINGREN,
                Constant::QX_ROLE_SHENQINGREN_fr
            ))) {
                $daBianRenIdIds = [$idid];
                if(LoginUser::isRole(Constant::Admin_Role_putongyonghu)){
                    $daBianRenIdIds[] = LoginUser::getBsqrComIdId($did);
                }
                //判断是否能答辩
                if ($defence_info = DefenceExpand::IsCanDefence($did, $daBianRenIdIds, true)) {
                    if ($defence_info['matter']) {
                        Control::addBtn(5, array("id" => $defence_info['id'], 'title' => '答辩(修改)'));
                    } else {
                        Control::addBtn(5, array("id" => $defence_info['id']));
                    }
                }

                $dzInfo = Dz::getShenQingDzInfo($did, LoginUser::getIdid(), LoginUser::getUserName());
                if ($dzInfo && $dzInfo['status'] == 4) {
                    Control::addBtn(16, array("id" => $did, 'title' => '提交证据(修改)'));//提交证据
                } else {
                    Control::addBtn(16, array("id" => $did));//提交证据
                }
            }

            if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {
                Control::addBtn(101, array("id" => $did));//提交证据
            }
        }


        if ($sub_status == 31 || $sub_status == 32 || $sub_status == 30)//质证和答辩
        {


            if (in_array($q_role,
                array(
                    Constant::QX_ROLE_BEISHENQINGREN,
                    Constant::QX_ROLE_BEISHENQINGREN_DL,
                    Constant::QX_ROLE_BEI_SHENQINGREN_fr,
                    Constant::QX_ROLE_SHENQINGREN_DL,
                    Constant::QX_ROLE_SHENQINGREN,
                    Constant::QX_ROLE_SHENQINGREN_fr
                )
            )) {

                $usersRoles = [LoginUser::getIdid()];
                if (LoginUser::isRole(Constant::Admin_Role_yinhang)) {
                    $usersRoles[] = LoginUser::getRoleThIdId();
                }elseif(LoginUser::isRole(Constant::Admin_Role_putongyonghu)){
                    $usersRoles[] = LoginUser::getBsqrComIdId($did);
                }

                $qid = Db::name("dossier_question")
                    ->where("dossier_id = '$did'  and status < 2")
                    ->whereIn('idid', $usersRoles)
                    ->order('id desc')
                    ->value("id");
                if ($qid) {
                    Control::addBtn(7, array("qid" => $qid));
                }


            }

            //关于组庭
            if ((!DefenceExpand::isDefeceIng($did)) && in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN, Constant::QX_ROLE_ZHONGCAIWEI_MISHU))) {
                $findc = Db::name("court")->where("dossier_id", $did)->where("status", "<>", "0")->find();

                if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {

                    if ($findc) {
                        // 有披露 或者 有申请回避 才出现 重新指定仲裁员按钮
//                        $has = HuiBi::getHasSubmitByType($did,[1,2]);
                        if (self::isCanAgainCourt($did)) {
                            Control::addBtn(15, ["id" => $did, "again" => 1]);//重新组庭
                        }

                    } else {


                        if ($sub_status == 31) {
                            // Control::addBtn(100, ["title"=>"测试"],['data-msg'=>"测试"]);//组庭
                            Control::addBtn(9, ["id" => $did]);//组庭
                        }

                    }
                }
                if ($q_role == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {

                    if ($findc && $findc['status'] == 2) {
                        Control::addBtn(18, ["id" => $did, 'exid' => $findc['id'], "gid" => $findc['is_again'] ? 16 : 4]);//发送组庭文件
                    }
                }
            }


            if (in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIWEI_MISHU))) {

                $dbnum = Db::name("dossier_defence")->where("dossier_id", $did)->where("status", 2)->count();
                if ($dbnum > 0) {
                    Control::addBtn(22, array("id" => $did));//提交证据
                }
                $dbnum = Db::name("dossier_question")->where("dossier_id", $did)->where("status", 2)->count();
                if ($dbnum > 0) {
                    Control::addBtn(23, array("id" => $did));//提交证据
                }
                $dbnum = Db::name("dz")->where("dossier_id", $did)->where("status", 2)->count();
                if ($dbnum > 0) {
                    Control::addBtn(24, array("id" => $did));//提交证据
                }
                $dbnum = Db::name("huibi")
                    ->where("dossier_id", $did)
                    ->where("status", 2)
//                    ->where('type','<>',2)
                    ->count();

                if ($dbnum > 0) {
                    Control::addBtn(25, array("id" => $did));
                }

            }

            if (in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN))) {
                $findhuibi = Db::name("huibi")
                    ->where("dossier_id", $did)
                    ->whereIn("status", [5, 6, 10, 11])

                    ->order("status desc")->find();
                $dbnum = $findhuibi['status'];
                if ($dbnum >= 10) {



                    
                    Control::addBtn(102, ["title" => "决定书文件整理发送中", "check_tag" => "d_huibi", "check_value" => $findhuibi['id'] . "_" .$findhuibi['status']  ]);


                } else if (in_array($dbnum, [5, 6])) {
                    Control::addBtn(25, array("id" => $did));
                }
            }

        }

        if ($sub_status == 32)//已经组庭
        {


            //关于仲裁员
            if (in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIYUAN, Constant::QX_ROLE_BEISHENQINGREN, Constant::QX_ROLE_BEISHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_fr))) {

                $idids = [$idid];
                if (LoginUser::isRole(Constant::Admin_Role_yinhang)) {
                    $idids[] = LoginUser::getRoleThIdId();
                }
                $canhuibi = HuiBi::getCanHuibi($did, $idids);

                $id = $canhuibi['id'];
                if ($q_role == Constant::QX_ROLE_ZHONGCAIYUAN) {

                    if ($shengming = HuiBi::getCanShenMing($did, $idid)) {
                        Control::addBtn(13, ["id" => $shengming['id']]);//仲裁员申明
                    }

                    if ($canhuibi && !HuiBi::IsPiLu($did, $idids)) {
                        Control::addBtn(11, ["id" => $id]);
                    }

                } else {
                    if ($canhuibi) {
                        Control::addBtn(10, ["id" => $id]);
                    }
                }

            }


            //关于仲裁员结束


            //关于裁决
            if (in_array($q_role, array(Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE, Constant::QX_ROLE_ZHONGCAIYUAN))) {
                list($btn) = Dcaijue::getLoginUserBtn($did);
                if (input('aa')) {
                    dump($btn);
                }
                if ($q_role == Constant::QX_ROLE_ZHONGCAIYUAN) {
                    if ($btn['title']) {
                        if (self::isCanCaiJue($did)) {
                            Control::addBtn(20, ['title' => $btn['title'], "id" => $did]);
                        } else {
                            Control::addBtn(100, ["title" => $btn['title']], ['data-msg' => "当前状态不可操作"]);//组庭
                            //Control::addBtn(-1, ['title' => $btn['title'], "msg" => "有异议，故不能编写呢"]);
                        }
                    }
                } else {
                    if ($btn['title']) {
                        Control::addBtn(20, ['title' => $btn['title'], "id" => $did]);
                    }
                }
            }

            // 撤回处理的按钮
            //0 刚发起  1申请表单已提交 2文件已提交   3  主办已处理  4仲裁员已处理   5 主任以处理（已完成）   6拒绝撤回
            if (in_array($q_role, [Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN, Constant::QX_ROLE_ZHONGCAIYUAN])) {
                $isCancelApply = Dcancel::getCancel($did);
                if ($isCancelApply && $isCancelApply['status'] != 5) {
                    if (in_array($isCancelApply['status'], [2, 6]) && $q_role == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {
                        Control::addBtn(27, ['id' => $isCancelApply['id']]);
                    }
                    if (in_array($isCancelApply['status'], [3, 7]) && $q_role == Constant::QX_ROLE_ZHONGCAIYUAN) {
                        if ($isCancelApply['status'] == 7) {
                            Control::addBtn(27, ['id' => $isCancelApply['id'], 'title' => '决定书签字']);
                        } else {
                            Control::addBtn(27, ['id' => $isCancelApply['id']]);
                        }
                    }
                    if ($isCancelApply['status'] == 4 && $q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {
                        Control::addBtn(27, ['id' => $isCancelApply['id']]);
                    }

                }
            }

        }
        // 撤回处理的按钮 已受理 未组庭
        if ($sub_status >= 21 && $sub_status < 32) {
            if (in_array($q_role, [Constant::QX_ROLE_ZHONGCAIWEI_MISHU, Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN])) {
                $isCancelApply = Dcancel::getCancel($did);
                if ($isCancelApply['status'] == 2 && $q_role == Constant::QX_ROLE_ZHONGCAIWEI_MISHU) {
                    Control::addBtn(27, ['id' => $isCancelApply['id']]);
                }
                if ($isCancelApply['status'] == 3 && $q_role == Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN) {
                    Control::addBtn(27, ['id' => $isCancelApply['id']]);
                }
            }
            // 受理后 组庭前 没有申请过的 显示其他申请按钮
            if(in_array($q_role,Constant::getDangShiRenQroleArr())){
                if(!Dother::hasOtherApplay($did)){
                    Control::addBtn(35,['id'=>$did]);
                }
            }
        }

        // 管辖权异议处理
        if($sub_status<40){
            if($q_role==Constant::QX_ROLE_ZHONGCAIWEI_MISHU){
                if(Gxq::hasYy($did,[2,7,8],true)){
                    Control::addBtn(33,[]);
                }

                if(Dother::hasOtherApplay($did,1,true)){
                    Control::addBtn(36,['id'=>$did]);
                }
            }
            if($q_role==Constant::QX_ROLE_ZHONGCAIWEI_ZHUREN){
                 if(Gxq::hasYy($did,[3,4],true)){
                     Control::addBtn(33,['title'=>'管辖权异议审批']);
                 }
            }
        }

        return Control::getBtnHtml($isphone);
    }

    // 是否可以编写裁决书
    public static function isCanCaiJue($did)
    {
        $courtId = Court::getCourtId($did);
        if (!$courtId) {
            return false;
        }
        // 没有进行中的披露 或 同意的披露
        // 没有进行中的回避 或者 同意过的回避
        // 才可以裁决
        $piluHuibi = Db::name('huibi')
            ->whereIn('type', [1, 2])
            ->where('status', '>=', 2)
            ->where('status', '<>', 4)
            ->where('court_id', $courtId)
            ->find();
        if ($piluHuibi) {
            return false;
        }
        return true;
    }

    // 是否可以重新指定仲裁员
    public static function isCanAgainCourt($did)
    {
        // 有同意过披露
        // 有同意过的回避
        // 才可以重新指定仲裁员
        $courtId = Court::getCourtId($did);
        if (!$courtId) {
            return false;
        }
        $piluHuibi = Db::name('huibi')
            ->whereIn('type', [1, 2])
            ->where('status', 3)
            ->where('court_id', $courtId)
            ->find();
        if ($piluHuibi) {
            return true;
        }
        return false;
    }
}