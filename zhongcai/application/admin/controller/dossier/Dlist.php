<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/12
 * Time: ����1:37
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;
use EasyWeChat\Support\Log;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Dcheck;
use wslibs\wszc\Dossier;
use wslibs\wszc\LoginUser;

class dlistDlist extends Backend
{
    protected $noNeedLogin = ["*"];

    private  $dlist_mod = null;
    private  $caijue_num = 0;
    private  $typeList = [];
    private  $uid = 0;
    private  $ptyh = 0;
    private  $role = 0;
    public function _initialize()
    {
        if (LoginUser::isRole(Constant::Admin_Role_admin)) {

            $this->uid = 0;
            $this->role = Constant::Admin_Role_admin;
        } else if ( LoginUser::isZhongCaiWei()) {

            $this->uid = 0;
            if(LoginUser::isZhongCaiLiAanShenPi()){
                $this->role = Constant::D_Role_ZhongCaiWei_LiAnShenPi;
            }elseif(LoginUser::isZhongCaiWeiCaiJueShenpi()){
                $this->role = Constant::D_Role_ZhongCaiWei_CaiJueShenPi;
            }else{
                $this->role = Constant::D_Role_ZhongCaiWei_GuanLiYuan;
            }

        } elseif(LoginUser::isRole(Constant::Admin_Role_putongyonghu)){
            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_putongyonghu;
            $this->assign('ptyh',1);
            $this->ptyh = 1;
        }elseif(LoginUser::isZhongCaiYuan()){
            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_zhongcaiyuan;
        }else{
            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_yinhang;
        }


        $dlist = new \wslibs\wszc\Dlist($this->uid);

        if (LoginUser::isZhongCaiWei()) {

            $dlist->setZhongCaiwei(LoginUser::getRoleThId());

        }

        $this->caijue_num = $dlist->daiCaiJueNum($this->role);

        $this->dlist_mod = $dlist;

        if($_GET['zhz']==11){
            dump($this->typeList);
        }

        $this->typeList = Dossier::getStatusForRole($this->role);

        parent::_initialize();
    }

    public function index()
    {

        if(LoginUser::isZhongCaiYuan() && !Dcheck::checkArbitratorCanDo(LoginUser::getIdid())){
            $this->redirect(url('cross.addfile/index',['title'=>'完善信息']));
            exit;
        }


        $type = (int)$this->request->param('type/d');

        $type = $type ? $type : -1;

        $this->assign('type',$type);

        if($this->ptyh ==1){
            $sub_key_num = $this->dlist_mod->getSubStatusCount($this->ptyh);

        }else{

            $this->dlist_mod->addWhereAll('d.sub_status',['in',array_keys($this->typeList)]);
            $sub_key_num = $this->dlist_mod->getSubStatusCount();
        }

        foreach($this->typeList as $key=> $value){

            if(!$sub_key_num[$key]){
                $sub_key_num[$key] = 0;
            }

            if($this->role==Constant::D_Role_ZhongCaiWei_GuanLiYuan || $this->role==Constant::Admin_Role_yinhang){
                $sub_key_num[400] = $this->caijue_num;
            }elseif( $this->role==Constant::Admin_Role_zhongcaiyuan){
                $sub_key_num[403] = $this->caijue_num;
            }
        }

        array_sum($sub_key_num)  ? $sun = array_sum($sub_key_num) : $sun= '';

        $this->assign("total", $sun);


        $this->typeList = $this->dlist_mod->DealStatus($this->typeList,$sub_key_num,$this->role);

        if($_GET['zhz']==11){
            dump($this->typeList);
        }
        $this->assign("typeList", $this->typeList);

        return $this->fetch();


    }





    public function dlist()
    {
        $dlist = $this->dlist_mod ;

        $sq_phone = $this->request->param('keywords');
        $sq_phone = explode('=',urldecode($sq_phone));
        $sq_phone = $sq_phone[1];
        if($sq_phone){
            if(is_numeric($sq_phone)){
                if(strlen($sq_phone)==11){
                    if(preg_match('/^1[3456789]\d{9}$/',$sq_phone)){
                        $dlist->addWhereAll('du.phone',$sq_phone);

                    }else{
                        $this->error('手机号格式错误');
                    }
                }elseif(strlen($sq_phone)==18 || strlen($sq_phone)==15){
                    $preg_card='/^\d{15}$)|(^\d{17}([0-9]|X)$/isu';
                    if(preg_match($preg_card,$sq_phone)){
                        $dlist->addWhereAll('du.id_num',$sq_phone);

                    }else{
                        $this->error('身份证号格式错误');
                    }
                }elseif(strlen($sq_phone)==5){
                    $dlist->addWhereAll('d.zno',$sq_phone);
                }else{
                    $dlist->addWhereAll('d.id',$sq_phone);
                }
            }else{
                $dlist->addWhereAll('du.name',['like',"%$sq_phone%"]);
            }

        }

        $this->assign($this->_AS);

        $status = $this->request->param("type/d");

        if(!$status){
            $status = -1;
        }

        if($status>9){

            if($status==400 || $status==403){

                return json(['rows' => $dlist->daiCaiJueList($this->role),'total' =>$dlist->daiCaiJueNum($this->role)]);
            }

            if($status==230){
                $status = ['in',[30,31,32]];
            }
            $dlist->addWhereAll('d.sub_status',$status);
        }else{
            if ($status !== null && $status!==-1){
                $dlist->addWhereAll('d.sub_status',$status);
            }else{

                if($this->role!=Constant::Admin_Role_putongyonghu){
                    $dlist->addWhereAll('d.sub_status',['in',array_keys($this->typeList)]);
                }
            }

        }


        list($total, $list) = $dlist->getList();


        return json(['rows' => $list, 'total' => $total]);
    }
}