<?php

namespace app\admin\controller\cross;

use app\common\controller\Backend;
use dmconfig\dmConfig;
use think\Db;


use dossier\DossierDoc;
use wslibs\logincheck\LoginCheck;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc\DocAttr;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierUser;
use wslibs\wszc\Dvalue;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\User;


class Platform extends Backend
{
    protected $model = "dma";
    private $bsqr = ["zwr","gtjkr"];

    public function _initialize()
    {


        $this->noNeedLogin = ['*'];
        $this->use_action_js();
        $res = LoginCheck::fromYh($this->request->param(),$msg);
        if(!$res){
            $this->error($msg);
        }
        parent::_initialize();

    }


    public function getIdid($idcard)
    {
        return Db::name("idcards")->where(["id_card "=>$idcard])->value("id");
    }
    public function getdossierid()
    {
        $ywid = $_GET['ywid'];

        echo  json_encode(['code'=>2,'msg'=>'no happened','status'=>0]);
        exit;
        if(!$ywid){
            echo  json_encode(['code'=>-1,'msg'=>'no find ywid','status'=>0]);
            exit;
        }
        $has = Db::name("dossier")->where("status > 0 and third_order_id = ".$ywid)->order('id desc')->find();

        if($has){
            echo  json_encode(['code'=>1,'msg'=>['dossier_id'=>$has['id']],'status'=>$has['status']]);
            exit;
        }else{
            echo  json_encode(['code'=>2,'msg'=>'no happened','status'=>0]);
            exit;
        }


    }


    public function newindex(){

        $data = json_decode($_POST['people'],true);
        $filesData = json_decode($_POST['files'],true);

        if(!$data || !$filesData){
            echo json_encode(["str"=>"缺少文件","code"=>1011]);

            exit;
        }


        $thirdInfo = Db::name("third_client")->where("a_jgid",$data['third_jg_id'])->find();
        
        $zc_jg_id = $thirdInfo['zc_jg_id'];

        Db::startTrans();
        //建立卷宗
        $dossier_id = \wslibs\wszc\Dossier::add($zc_jg_id, $thirdInfo['id'], $data['third_order_id'], 1, '借款合同' ,$data['hk_sign_time']);//$data['title']

        $caijue = Db::name('dossier_caijue')->insert(['id'=>$dossier_id,'doc_id'=>0,'c_status'=>0,'c_idid'=>0,'c_role'=>0]);


        if (!$caijue) {
            Db::rollback();
            echo json_encode(["str"=>"卷宗文档生成失败","code"=>101]);

            exit;
        }

        if (!$dossier_id) {
            Db::rollback();
            echo json_encode(["str"=>"卷宗生成失败","code"=>101]);

            exit;
        }


        $applyUser = new DossierUser();
        //建立申请人
        $dossierUserF1 = new DossierUser();
        $dossierUserF1->setNameOrOrgname($data['sqr']['f_name']);
        $dossierUserF1->setType(3);
        $dossierUserF1->setIdcardOrCreditno($data['sqr']['f_id_card']);
        $dossierUserF1->setPhone($data['sqr']['f_phone']);
        $dossierUserF1->setEmail($data['sqr']['f_email']);
        $dossierUserF1->setRole(19);
        $dossierUserF1->setAddress($data['sqr']['f_address']);
        $dossierUserF1->setNation($data['sqr']['f_nation']);
        $dossierUserF1->setJob($data['sqr']['job']);


        $applyUser->setFaRen($dossierUserF1);
        $applyUser->setNameOrOrgname($data['sqr']['com_name']);
        $applyUser->setType(2);
        $applyUser->setIdcardOrCreditno($data['sqr']['credit_code']);
        $applyUser->setPhone($data['sqr']['com_phone']);
        $applyUser->setAddress($data['sqr']['com_address']);
        $applyUser->setEmail($data['sqr']['com_email']);
        $applyUser->setRole(1);

        $res = $applyUser->pushToDossier($dossier_id);

        if (!$res) {
            Db::rollback();
            echo json_encode(["str"=>"添加申请人失败","code"=>102]);
            exit;
        }


        //建立申请人

        if($data['sqr']['wt_name']){
            $applyUserW = new DossierUser();
            $applyUserW->setNameOrOrgname($data['sqr']['wt_name']);
            $applyUserW->setType(1);
            $applyUserW->setIdcardOrCreditno($data['sqr']['wt_id_card']);
            $applyUserW->setPhone($data['sqr']['wt_phone']);
            $applyUserW->setRole(3);
            $applyUserW->setAddress($data['sqr']['wt_address']);
            $applyUserW->setEmail($data['sqr']['wt_email']);
            $applyUserW->setNation($data['sqr']['wt_nation']);

            $wres = $applyUserW->pushToDossier($dossier_id);


            if (!$wres) {
                Db::rollback();
                echo json_encode(["str"=>"添加委托人失败","code"=>102]);
                exit;
            }
        }


        //建立被申请人
        $jkr = $data['bsqr']['jkr'];
        $gtjkr = $data['bsqr']['gtjkr'];
        $danbaoren = $data['bsqr']['danbaoren'];
        $zhiyaren = $data['bsqr']['zhiyaren'];
        $diyaren = $data['bsqr']['diyaren'];


        $all_num = 0;
        if($jkr){
            $all_num += count($jkr);
        }
        if($gtjkr){
            $all_num += count($gtjkr);
        }
        if($danbaoren){
            $all_num += count($danbaoren);
        }
        if($zhiyaren){
            $all_num += count($zhiyaren);
        }
        if($diyaren){
            $all_num += count($diyaren);
        }

        DossierUser::$bsqr_num = $all_num;

        if($jkr){
            $this->pushqian($dossier_id,$jkr);
        }

        if($gtjkr){
            $this->pushqian($dossier_id,$gtjkr);
        }

        if($danbaoren){
            $this->pushqian($dossier_id,$danbaoren);
        }

        if($zhiyaren){
            $this->pushqian($dossier_id,$zhiyaren);
        }
        if($diyaren){
            $this->pushqian($dossier_id,$diyaren);
        }



        $datas = $filesData;

        if($datas){
            foreach ($datas as $k=>$v){

                $res = Dvalue::addFileToDocByDocMode($dossier_id, $v['mod_id'],Dvalue::mkFileValue($v['val'],$v['describe']),User::getLoginUid()-0>0?User::getLoginUid():13);

                if(!$res){
                    Db::rollback();
                    echo json_encode(["str"=>"提交资料失败","code"=>104]);
                    exit;
                }
            }
        }



        Db::commit();



//            file_get_contents('http://zc.wszx.cc/dossiertest-getDossiderId?ywid='.$data['third_order_id'].'&dossier_id='.$dossier_id);

        echo json_encode(["str"=>"成功","code"=>100,"url"=>url('dossier/cp/add',['dossier_id'=>$dossier_id]),"dossier_id"=>$dossier_id]);
        exit;
        /*dump($data);die;*/
//        }else{
//            echo json_encode(["str"=>"该业务正在被仲裁","code"=>104]);
//            exit;
//        }
    }


    private function pushqian($dossier_id,$jkr)
    {
        foreach ($jkr as $k=>$v){
            if(!$v['is_com']){
                $this->pushuser($dossier_id,$v,1,2);
            }else{
                $this->pushuser($dossier_id,$v,3,20,1,2,2);
            }

        }
    }


    private function pushuser($dossier_id,$userArr,$type,$role,$is_com=0,$comType=0,$comRole=0)
    {


        if(!$is_com){
            $applyUser = new DossierUser();
            $applyUser->setNameOrOrgname($userArr['real_name']);

            $applyUser->setType($type);
            $applyUser->setIdcardOrCreditno($userArr['id_card']);
            $applyUser->setPhone($userArr['u_phone']);
            $applyUser->setAddress($userArr['local']?$userArr['local']:$userArr['address']);
            $applyUser->setEmail($userArr['u_email']);
            $applyUser->setNation($userArr['nation']);
            $applyUser->setRole($role);

            $re = $applyUser->pushToDossier($dossier_id);

            if (!$re) {
                Db::rollback();
                echo json_encode(["str"=>"添加人员失败","code"=>103]);
                exit;
            }
        }else{


            $applyUserB = new DossierUser();
            //建立申请人
            $dossierUserBF1 = new DossierUser();
            $dossierUserBF1->setNameOrOrgname($userArr['f_name']);
            $dossierUserBF1->setType($type);
            $dossierUserBF1->setIdcardOrCreditno($userArr['f_id_card']);
            $dossierUserBF1->setPhone($userArr['f_phone']);
            $dossierUserBF1->setRole($role);
            $dossierUserBF1->setAddress($userArr['f_address']);
            $dossierUserBF1->setEmail($userArr['f_email']);
            $dossierUserBF1->setNation($userArr['nation']);
            $dossierUserBF1->setJob($userArr['job']);

            $applyUserB->setFaRen($dossierUserBF1);
            $applyUserB->setNameOrOrgname($userArr['com_name']);
            $applyUserB->setType($comType);
            $applyUserB->setIdcardOrCreditno($userArr['credit_code']);
            $applyUserB->setPhone($userArr['phone']);
            $applyUserB->setAddress($userArr['address']);
            $applyUserB->setEmail($userArr['email']);
            $applyUserB->setRole($comRole);

            $resB = $applyUserB->pushToDossier($dossier_id);

            if (!$resB) {
                Db::rollback();
                echo json_encode(["str"=>"添加人员失败","code"=>104]);
                exit;
            }

        }



    }



}