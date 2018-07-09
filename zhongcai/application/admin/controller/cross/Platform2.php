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
use wslibs\wszc\DossierUser;
use wslibs\wszc\Dvalue;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\User;


class Platform2 extends Backend
{
    protected $model = "dma";
    private $bsqr = ["zwr","gtjkr"];

    public function _initialize()
    {


        $this->noNeedLogin = ['*'];
        $this->use_action_js();

        parent::_initialize();

    }


    //http://192.168.0.7/zhongcai/admin.php/cross.Platform/crossJiGou
    public function crossJiGou()
    {

        $postData = $this->request->post();


        if(!$postData){
            echo -1;
            exit;
        }
        $data= [];
        $data['a_jgid'] = trim($postData['jgid']);
        $data['name'] = trim($postData['name']);
        $data['credit_code'] = trim($postData['credit_code']);
        $data['address'] = trim($postData['address']);
        $data['addtime'] = time();

        $province_id = Db::name('area')->where('name','like',"%{$postData['province']}%")->where('pid',0)->find()['id'];
        $city_id = Db::name('area')->where('name','like',"%{$postData['city']}%")->where('pid',$province_id)->find()['id'];

        $district = substr($postData['district'],0,(strlen($postData['district']))-3);
        $district_id = Db::name('area')->where('name','like',"%{$district}%")->where('pid',$city_id)->find()['id'];

        $data['province'] = $province_id;
        $data['city'] = $city_id;
        $data['district'] = $district_id;

        if($info = Db::name('third_client')->where('a_jgid',$data['a_jgid'])->find()){
             Db::name('third_client')->where('id',$info['id'])->update($data);
            echo 1;
        }else{
            echo $third_client_id = Db::name('third_client')->insertGetId($data);

            Db::name('idcards')->insert(['id_card'=>'third_client'.$third_client_id,'real_name'=>$data['name']]);



        }

        exit;
    }


    //添加银行机构用户
    //http://192.168.0.7/zhongcai/admin.php/cross.Platform/crossJiGouAdmin?phone=18333149995
    public function crossjigouuser()
    {
        if($this->request->isPost()) {


            $phone = $this->request->post('row/a')['phone'];

            if (!$phone) {
                $this->error('phone is missed');
                exit;
            }

            $returnData = json_decode($this->curlPost('http://zc.wszx.cc/ObtainInfo-getComUser?phone=' . $phone), true);

            if ($returnData['code'] - 0 < 0) {
                $this->error($returnData['code']);
                exit;
            }
            $postData = $returnData['msg'];

            $id = $this->addJgUser($postData,1,$phone);

            if ($id) {
                $this->success('success');

            } else {
                $this->error('error');
            }

            exit;
        }

        return $this->fetch('cross/platform/crossjigouuser');
    }


    //添加银行机构管理员
    public function crossjigouadmin()
    {
        if($this->request->isPost()){

            $phone = $this->request->post("row/a")['phone'];

            if(!$phone){
                $this->error('phone is missed');

                exit;
            }


            $returnData = json_decode($this->curlPost('http://zc.wszx.cc/ObtainInfo-getAdmin?phone='.$phone),true);

            if($returnData['code']-0<0){
                $this->error($returnData['code']);
                exit;
            }
            $postData = $returnData['msg'];

            $id = $this->addJgUser($postData,2,$phone);

            if($id){
                $this->success('success');
            }else{
                $this->error('error');
            }
            exit;
        }else{
            return $this->fetch('cross/platform/crossjigouadmin');
        }


    }


    private function addJgUser($postData,$role=1,$phone)
    {

        $thInfo = Db::name('third_client')->where("a_jgid",$postData['jgid'])->find();

        if(!$thInfo){
            $this->error('没有该机构');
            exit;
        }
        $idid = IDcard::getIdId(trim($postData['id_card']),trim($postData['real_name']));
        $userInfo = Db::name('third_user')
            ->where('idid',$idid)
            ->where('role',$role)
            ->find();

        if($userInfo){
            $this->error('该人员已添加');
            exit;
        }

        $data= [];
        $data['th_id'] = $thInfo['id'];
        $data['name'] = trim($postData['real_name']);
        $data['role'] = $role;
        $data['sex'] = $this->getSex(trim($postData['id_card']));
        $data['status'] = trim($postData['status']);
        $data['idid'] = $idid;
        $data['addtime'] = time();
        $data['phone'] = $phone;
        $data['role_name'] = '银行';

        return Db::name('third_user')->insertGetId($data);

    }

    private function curlPost($url){

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        $output = curl_exec($ch);

        curl_close($ch);

        return $output;
    }

    private function getSex($idcard)
    {

        return (substr($idcard, (strlen($idcard)==15 ? 14 : 16), 1) % 2)==0 ? '2' : '1';

    }



    //添加仲裁委人员
    public function crosszcwry()
    {

        if($this->request->isPost()){
            $da = $this->request->post('row/a');
            $phone = $da['phone'];
            $role = $da['role'];
            $th_id = $da['th_id'] ;
//            $th_id = 10;

            if(!$phone || !$role){
                $this->error('sorry');

                exit;
            }

            $returnData = json_decode($this->curlPost('http://zc.wszx.cc/ObtainInfo-userinfo?phone='.$phone),true);

            if($returnData['code']-0<0){
                $this->error($returnData['code']);
                exit;
            }

            $postData = $returnData['msg'];
            $idid = IDcard::getIdId(trim($postData['id_card']),trim($postData['real_name']));

            $info = Db::name('jigou_admin')->where('idid',$idid)->find();

            if($info){
                $this->error('已添加该用户');
                exit;
            }

            $roleName = [
                1=>'主办',
                2=>'立案审批',
                3=>'裁决审批',
            ];

            $data = [
                'name'=>$postData['real_name'],
                'role'=>$role,
                'sex'=>$this->getSex(trim($postData['id_card'])),
                'addtime'=>time(),
                'idid'=>$idid,
                'th_id'=>$th_id,
                'phone'=>$phone,
                'role_name'=>$roleName[$role]
            ];

            $id = Db::name('jigou_admin')->insertGetId($data);

            if($id){
                $this->success('success');
            }else{
                $this->error('error');
            }

        }

        $list = Db::name('jigou')->order('id desc')->select();
        $this->assign('zcwlist',array_column($list,'name','id'));

        return $this->fetch('cross/platform/crosszcwry');

    }




    public function j()
    {
        echo 456;
        $form = new WsForm();
        $form->setFormTitleTip("请填写资料");

        $item = new Item();
        $item->varName("phone")->varTitle("请输入手机号")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::radio);
        $item->itemArr([1=>"主办/秘书",2=>"主任"])->defaultValue(1);
        $form->addItem($item);

        $item = new Item();
        $item->varName("th_id")->varTitle("仲裁委id")->inputType(InputType::select);
        $item->itemArrValName("zcwlist");
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("cross/platform/crosszcwry");

    }



    public function kj()
    {
        echo 456;
        $form = new WsForm();
        $form->setFormTitleTip("请填写资料");

        $item = new Item();
        $item->varName("phone")->varTitle("请输入手机号")->inputType(InputType::text);
        $form->addItem($item);


        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::radio);
        $item->itemArr([1=>"普通",2=>"管理员"])->defaultValue(1);
        $form->addItem($item);


        $item = new Item();
        $item->varName("th_id")->varTitle("仲裁委id")->inputType(InputType::select);
        $item->itemArrValName("zcwlist");
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("cross/platform/crosszcy");

    }



    //添加仲裁员
    public function crosszcy()
    {

        if($this->request->isPost()){
            $da = $this->request->post('row/a');
            $phone = $da['phone'];
            $role = $da['role'];
            $jgid = $da['th_id'] ;

            if(!$phone || !$role){
                $this->error('sorry');

                exit;
            }


            $returnData = json_decode($this->curlPost('http://zc.wszx.cc/ObtainInfo-userinfo?phone='.$phone),true);

            if($returnData['code']-0<0){
                $this->error($returnData['code']);
                exit;
            }
            $postData = $returnData['msg'];

            $idid = IDcard::getIdId(trim($postData['id_card']),trim($postData['real_name']));


            $info = Db::name('third_user')->where('idid',$idid)->find();

            if($info){
                $this->error('已添加该用户');
                exit;
            }


            Db::startTrans();

            $data = [
                'name'=>$postData['real_name'],
                'role'=>$role,
                'addtime'=>time(),
                'sex'=>$this->getSex(trim($postData['id_card'])),
                'idid'=>$idid,
                'phone'=>$phone,
            ];

            $id = Db::name('zcy')->insertGetId($data);

            $jgzcy = Db::name('jigou_zcy')->insertGetId(['jg_id'=>$jgid,'zcy_id'=>$id,'addtime'=>time(),'status'=>1,'idid'=>$data['idid']]);

            if($id && $jgzcy){
                Db::commit();
                $this->success('success');
            }else{
                Db::rollback();
                $this->error('error');
            }
        }


        $list = Db::name('jigou')->order('id desc')->select();
        $this->assign('zcwlist',array_column($list,'name','id'));

        return $this->fetch('cross/platform/crosszcy');
    }

}