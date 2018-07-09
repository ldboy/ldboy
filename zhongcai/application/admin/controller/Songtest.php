<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use dmconfig\dmConfig;
use think\Db;


use dossier\DossierDoc;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc\DocAttr;
use wslibs\wszc\DossierUser;






class Songtest extends Backend
{
    protected $model = "dma";
    private $bsqr = ["债务人","共同借款人（个人）"];

    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        $this->use_action_js();
        parent::_initialize();
    }

    public function songtest(){

        $data = json_decode($_POST['json'],true);


        //dump($data);die;

        $zc_jg_id = Db::name("third_client")->where("id = ".$data['third_jg_id'])->value("zc_jg_id");




        /*if(!$has = Db::name("dossier")->where("third_order_id = ".$data['third_order_id'])->find()){*/
            //新增卷宗
            $dossier_id = \wslibs\wszc\Dossier::add($zc_jg_id,$data['third_jg_id'],$data['third_order_id'],1,$data['title']);

            //加入申请人
            /*dump($data['sq']);die;*/
            $dossierUserF1 = new DossierUser();
            $dossierUserF1->setNameOrOrgname($data['sq']['sqr_f_name']);
            $dossierUserF1->setType(3);
            $dossierUserF1->setIdcardOrCreditno($data['sq']['sqr_idcard']);
            $dossierUserF1->setPhone($data['sq']['sqr_phone']);
            $dossierUserF1->setRole($data['sq']['role']);

            $applyUser = new DossierUser();
            $applyUser->setFaRen($dossierUserF1);
            $applyUser->setNameOrOrgname($data['sq']['sqr_name']);
            $applyUser->setType(2);
            $applyUser->setIdcardOrCreditno($data['sq']['sqr_credit_code']);
            $applyUser->setPhone($data['sq']['sqr_com_phone']);
            $applyUser->setAddress($data['sq']['sqr_address']);
            $applyUser->setRole($data['sq']['role']);



            $res = $applyUser->pushToDossier($dossier_id);


            //加入被申请人
            /*dump($data);*/
            foreach ($data as $k => $v){
                if(in_array($k,$this->bsqr)){
                    foreach ($v as $key => $val){
                        /*dump($val);die;*/
                        $applyUser = new DossierUser();
                        $applyUser->setNameOrOrgname($val['bsqr_name']);

                        $applyUser->setType(1);
                        $applyUser->setIdcardOrCreditno($val['bsqr_idcard']);
                        $applyUser->setPhone($val['bsqr_phone']);
                        $applyUser->setAddress($val['bsqr_address']);
                        $applyUser->setRole($val['bsqr_role']);

                        /*dump($applyUser);die;*/
                        $re = $applyUser->pushToDossier($dossier_id);
                    }
                }
            }

            dump($re);
        /*}else{
            echo "该业务正在被仲裁！";
        }*/


        die;
        echo 222222;
        die;




         
        $dmconfig = new dmConfig();

        $list = $dmconfig->getDmConfig(1);

        dump($list);
    }



    public function insertaa(){
        $testData = [
            'doc_id'=>1,
            'attr_id'=>1,
            'value'=>'4455',
            'status'=>1,
            'ext_id'=>1,
            'path'=>1,
            'createtime'=>time(),
        ];
        dump(Db::name('drav')->insert($testData));
    }


}