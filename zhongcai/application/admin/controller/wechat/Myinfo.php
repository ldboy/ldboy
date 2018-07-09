<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/27
 * Time: 上午11:42
 */

namespace app\admin\controller\wechat;


use app\common\controller\Backend;
use dossier\DossierDoc;
use think\Db;
use think\Request;
use wslibs\wszc\Constant;
use wslibs\wszc\Dcp;
use wslibs\wszc\Ddocs;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\DInfoValue;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\Dother;
use wslibs\wszc\Ds;
use wslibs\wszc\Dvalue;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\Gxq;
use wslibs\wszc\HuiBi;
use wslibs\wszc\LoginUser;
use wslibs\wszc\publicnumber\btnphone\Btnphone;
use wslibs\wszc\publicnumber\mylist\Mydossier;
use wslibs\wszc\publicnumber\mylist\Mywaitdeal;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\User;

class Myinfo extends Backend
{

    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        $idid = $this->request->param('idid/d');
//        $res = Mywaitdeal::cdMyDossierInfoLogin($idid);

//        if(!$res){
//
//            $this->error('手机无法查看,请到官网去查看信息');
//
//            exit;
//        }
    }

    public function daohang()
    {
        $idid = $this->request->param('idid/d');
        $did = $this->request->param('did/d');

        if (!$idid || !$did) $this->error('参数错误');

        $res = Mywaitdeal::cdMyDossierInfoLogin($idid);

        if(!$res){

            $this->error('您暂时无法再手机上查看'."\n".'请到官网去查看信息');

            exit;
        }
        $res1 = Mywaitdeal::isIdIdInDossier($idid,$did);

        if(!$res1){
            $this->error('该案件已被其他同事受理');
        }


        $list = Mywaitdeal::getNav($did,$idid,$res) ;

        $btnHtml = Btnphone::getBtnByPhone($did, $idid);
        $zno_str = DossierDoc::create_zc_no($did,true);
        $this->assign("zno_str",$zno_str?$zno_str:'待受理' );
        $this->assign("idid", $idid);
        $this->assign("did", $did);
        $this->assign("list", $list);
        $this->assign("btnList", $btnHtml);

        return $this->fetch('wxcallback/usercenter/daohang');
    }

    public function dabian()
    {
        $d_id = $this->request->param('did/d');
        $idid = $this->request->param('idid/d');
        // 答辩列表
        $defenceList = DefenceExpand::getDefenceList($d_id);

            foreach($defenceList as $key=>$value){
                $defenceList[$key]['link'] = WEB_SITE_ROOT.'admin/dossier.subinfo/defence?def_id='.$value['id'].'&is_phone=1';
            }


        $this->assign('idid', $idid);
        $this->assign('did', $d_id);
        $this->assign('defenceList', $defenceList);
        $this->assign('zhuban', 1);

        return $this->fetch('wxcallback/usercenter/dabian');
    }
    public function huibismpl()
    {
        $d_id = $this->request->param('did/d');
        //披露/回避记录
        $piluHuibi = HuiBi::HuiBiList($d_id);
        $this->assign('piluHuibi', $piluHuibi);

        return $this->fetch('wxcallback/usercenter/huibismpl');
    }
    public function zhizheng()
    {
        $d_id = $this->request->param('did/d');
        // 质证列表
        $questionList = QuestionExpand::getQuestionList($d_id);

            foreach($questionList as $key=>$value){
                $questionList[$key]['link'] = WEB_SITE_ROOT.'admin/dossier.subinfo/question?qid='.$value['id'].'&is_phone=1';
            }


        $this->assign('questionList', $questionList);
        $this->assign('zhuban', 1);

        return $this->fetch('wxcallback/usercenter/zhizheng');
    }

    public function guanxiaquan()
    {
        $d_id = $this->request->param('did/d');
        $idid = $this->request->param('idid/d');
        $gxqyyList = Gxq::getYyList($d_id);

        if($_GET['zhz']==1){
            dump($gxqyyList);
        }


            foreach($gxqyyList as $key=>$value){
                if(!$value['statusStr']){
                    $gxqyyList[$key]['link'] = '已处理';
                }
                $gxqyyList[$key]['link'] = WEB_SITE_ROOT.'admin/dossier.subinfo/gxqyy?gxid='.$value['id'].'&is_phone=1';
            }

        $this->assign('zhuban', 1);
        $this->assign('idid', $idid);
        $this->assign('did', $d_id);

        $this->assign('gxqyyList',$gxqyyList);

        return $this->fetch('wxcallback/usercenter/guanxiaquan');
    }

    public function otherlist()
    {
        $d_id = $this->request->param('did/d');
        $otherList = Dother::otherList($d_id);

        $this->assign('otherList',$otherList);
        return $this->fetch('wxcallback/usercenter/otherlist');
    }

    public function zjlist()
    {
        $d_id = $this->request->param('did/d');
        // 证据列表
        $zhengJuList = Dz::getZhengJuList($d_id);


        foreach($zhengJuList as $key=>$value){
            $zhengJuList[$key]['link'] = WEB_SITE_ROOT.'admin/dossier.subinfo/zhengju?zid='.$value['id'].'&is_phone=1';
        }

        $this->assign('zhengJuList', $zhengJuList);
        return $this->fetch('wxcallback/usercenter/zjlist');
    }

    public function loglist()
    {
        $d_id = $this->request->param('did/d');
        // 日志列表
        $logList = DossierLog::getLogs($d_id);
        $this->assign('logList', $logList);

        return $this->fetch('wxcallback/usercenter/loglist');
    }

    public function dangshiren()
    {

        $d_id = $this->request->param('did/d');

        if(!$d_id){
            $this->error('did参数错误');
        }

        $dangshiren = Mydossier::getDangShiRen($d_id);

        $this->assign("dangshiren", $dangshiren);
        return $this->fetch('wxcallback/usercenter/dangshiren');
    }


    public function zdzcy()
    {

        $d_id = $this->request->param("did/d");
        $idid = $this->request->param("idid/d");
        $is_phone = $this->request->param("is_phone/d");

        if(!$d_id){
            $this->error('id参数错误');
        }

        $again = (int)$this->request->param("again/d");

        if ($again) {
            $gid = Constant::FILE_GROUP_cxzhidingzhongcaiyuan;
        } else {
            $gid = Constant::FILE_GROUP_zhidingzhongcaiyuan;
        }

        $find = Db::name("court")->where("dossier_id = '$d_id' and  status<>0")->order("id desc")->find();


        if (!$again) {

            if ($find['status'] == 1) {
                $this->success("已经组庭了，但尚未完善组庭资", url('dossier.cp/doclist', array("id" => $d_id, "gid" => $gid, "exid" => $find['id'])));
            }
        }


        if ($is_phone==1) {
            $name = $this->request->param("name/s");

            if ($find) {
                if ($again == 1) {

                    Db::name("arbitrator")->where("dossier_id = '$d_id' and court_id={$find['id']} ")->update(["status" => 0]);
                    Db::name("court")->where("id = '{$find['id']}'")->update(['status' => 0]);
                    Db::name("huibi")->where("court_id = '{$find['id']}'")->update(['is_valid' => 0]);
                    DossierLog::addLog($d_id, User::getLoginUid(), LoginUser::getUserName(), DossierLog::LOG_TYPE_SET_UP_COURT);
                }
            }



            $re = Dcp::zuting($d_id, $name, $again);


            if ($re['code'] == 1) {


                $zcy_model = Constant::DOC_model_zhidingzhongcaiyuan;
                if ($again) {
                    $reason = $name = $this->request->post("reason/s");
                    $zcy_model = Constant::DOC_model_cxzhidingzhongcaiyuan;

                    Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_cxzhidingzhongcaiyuan, "AgainReason", $reason, $re['id']);

                }


                Ds::sendGroupFileToDocRole($d_id, $gid, Constant::D_Role_ZhongCaiWei_JiGou, [], $re['id']);


                Db::name("court")->where("id = " . $re['id'])->update(['status' => 2]);


                Dossier::changeStatus($d_id, [3, 35]);


                DocContract::initContract(Ddocs::getOrInitFile($d_id, $zcy_model, $re['id'])['id']);
                $this->success("提交成功");

            } else {
                $this->error($re['msg']);
            }
        } else {

            $dossierInfo = Dossier::getSimpleDossier($d_id);
            $hasChoose = Db::name('arbitrator')->field('zcy_uid')->where("dossier_id", $d_id)->selectOfIndex('zcy_uid');
            $zcyList = Db::name("jigou_zcy")
                ->field("zc.name,zc.id,zc.province,zc.city,zc.district,zc.total,zc.failed_num,zc.finish_num,a.name as province_name,aa.name as city_name,aaa.name as district_name,zc.avatar")
                ->alias("jz")
                ->join("zcy zc", "jz.zcy_id = zc.id", 'left')
                ->join("zc_area a", "zc.province = a.id", 'left')
                ->join("zc_area aa", "zc.city = aa.id", 'left')
                ->join("zc_area aaa", "zc.district = aaa.id", 'left')
                ->where('jz.jg_id', $dossierInfo['zc_jg_id'])
                ->whereNotIn('zc.id', $hasChoose)
                ->order('zc.finish_num desc,zc.total desc')
                ->paginate(20, false, ['query' => Request::instance()->param()]);
            $page = $zcyList->render();


            $thirdInfo = Db::name('third_client')->where('id', $dossierInfo['third_jg_id'])->find();

            $arr1 = [];
            $arr2 = [];
            $arr3 = [];

            foreach ($zcyList as $k => $v) {

                if ($v['province'] - $thirdInfo['province'] != 0) {
                    $arr1[$k] = $v;
                    $arr1[$k]['remind'] = '推荐';//'#007520';
                    $arr1[$k]['remind_show'] = 1;
                } elseif ($v['province'] - $thirdInfo['province'] == 0 && $v['city'] - $thirdInfo['city'] != 0) {
                    $arr2[$k] = $v;
                    $arr2[$k]['remind'] = '推荐';//'#fcbaba';
                    $arr2[$k]['remind_show'] = 1;
                } else {
                    $arr3[$k] = $v;
                    $arr3[$k]['remind'] = '�?';

                }


            }


            $zcyList_1 = array_merge(array_values($arr1), array_values($arr2), array_values($arr3));

            foreach ($zcyList_1 as $k => $v) {
                if ($v['avatar']) {
                    $zcyList_1[$k]['show'] = IMG_SITE_ROOT . $v['avatar'];
                }
                $zcyList_1[$k]['zd_url'] = 'http://zcw.wszx.cc/admin/wechat/myinfo/zdzcy?did='.$d_id.'&is_phone=1&name='.$v['id'].'&again='.$again;

                if (!$v['id']) {
                    unset($zcyList_1[$k]);
                }

            }

            $this->assign("did", $d_id);
            $this->assign("idid", $idid);
            $this->assign("page", $page);
            $this->assign("is_again", $again);
            $this->assign("zcyList", $zcyList_1);

            return $this->fetch('wxcallback/usercenter/zcylist');
        }
    }
}