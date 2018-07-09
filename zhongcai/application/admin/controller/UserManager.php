<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Session;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;


class UserManager extends Backend
{

    protected $model = null;

    public function _initialize()
    {
        $this->noNeedRight = ['*'];
        $this->use_action_js();
        parent::_initialize();

    }
//-------------------------银行管理--------------------------------
    public function initjglist()
    {
        $form = new WsForm();

        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("Jigou/addjg");
        $form->setDelUrl("Jigou/deljg");
        $form->setEditUrl("Jigou/addjg");
        $form->setListUrl("user_manager/jglist");

        $item = new Item();
        $item->varName("name")->varTitle("机构名称")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("address")->varTitle("机构地址")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("tel")->varTitle("联系方式")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("zc_jg_id")->varTitle("仲裁机构id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("a_jgid")->varTitle("银行id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("usermanager/jglist");
    }
    public function initaddjgview()
    {
        $form = new WsForm();

        $item = new Item();
        $item->varName("name")->varTitle("机构名称")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("address")->varTitle("机构地址")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("tel")->varTitle("联系方式")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::select)->required(true);
        $item->itemArr([0=>"未启用",1=>"启用"]);
        $form->addItem($item);


        $item = new Item();
        $item->varName("zc_jg_id")->varTitle("仲裁机构id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("a_jgid")->varTitle("银行id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("UserManager/addjg");
    }
    //银行列表
    public function jglist()
    {
        if($this->request->isAjax()){

            $list = Db::name("third_client")->select();

            foreach ($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);

                if($v['status'] == 1){
                    $list[$k]['status'] = "启用";
                }else{
                    $list[$k]['status'] = "未启用";
                }
            }

            $result = array("total" => Db::name('third_client')->count(), "rows" => $list);

            return json($result);
        }else{
            $admin = Session::get('admin');
            $this->assign('admin_id',$admin['id']);
            return $this->fetch('usermanager/jglist');
        }

    }
    //编辑/添加银行
    public function addjg()
    {
        $id = $this->request->param('ids/i');
        if($this->request->isPost()){
            $param = $this->request->post("row/a");

            $data = [
                'name' => trim($param['name']),
                'address' => trim($param['address']),
                'tel' => trim($param['tel']),
                'status' => trim($param['status']),
                'zc_jg_id' => trim($param['zc_jg_id']),
                'a_jgid' => trim($param['a_jgid']),
            ];



            if($id){

                $str = "修改";
                $re = Db::name("third_client")->where("id = '$id'")->update($data);
            }else{
                $str = "添加";
                $re = Db::name("third_client")->insertGetId($data);
            }

            if($re){
                $this->success($str."成功" );
            }else{
                $this->error($str."失败");
            }
        }else{
            if($id){
                $info = Db::name("third_client")->where("id = '$id'")->find();
                $this->assign("row",$info);
            }
            return $this->fetch('usermanager/addjg');
        }
    }
    //删除银行
    public function deljg()
    {
        $id = $this->request->param('ids/i');

        $re = Db::name("third_client")->where("id = '$id'")->delete();

        if($re){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
//--------------------------银行人员管理-------------------------------

    public function initlist()
    {
        $form = new WsForm();

        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("user_manager/adduser");
        $form->setDelUrl("user_manager/deluser");
        $form->setEditUrl("user_manager/adduser");
        $form->setListUrl("user_manager/userlist");

        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("th_id")->varTitle("银行id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("usermanager/userlist");
    }

    public function initaddview()
    {
        $form = new WsForm();
        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("th_id")->varTitle("银行id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::select)->required(true);
        $item->itemArr([1=>"普通",2=>"管理员"]);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::select)->required(true);
        $item->itemArr([0=>"未启用",1=>"启用"]);
        $form->addItem($item);

        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("UserManager/adduser");
    }

    //银行人员列表
    public function userlist()
    {
        if($this->request->isAjax()){

            $list = Db::name("third_user")->select();

            foreach ($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);

                if($v['status'] == 1){
                    $list[$k]['status'] = "启用";
                }else{
                    $list[$k]['status'] = "未启用";
                }
            }

            $result = array("total" => Db::name('third_user')->count(), "rows" => $list);

            return json($result);
        }else{
            return $this->fetch('usermanager/userlist');
        }

    }
    //编辑/添加银行
    public function adduser()
    {
        $id = $this->request->param('ids/i');
        if($this->request->isPost()){
            $param = $this->request->post("row/a");

            $data = [
                'name' => trim($param['name']),
                'th_id' => trim($param['th_id']),
                'phone' => trim($param['phone']),
                'status' => trim($param['status']),
                'role' => trim($param['role']),
                'idid' => trim($param['idid']),
            ];



            if($id){

                $str = "修改";
                $re = Db::name("third_user")->where("id = '$id'")->update($data);
            }else{
                $str = "添加";
                $re = Db::name("third_user")->insertGetId($data);
            }

            if($re){
                $this->success($str."成功");
            }else{
                $this->error($str."失败");
            }
        }else{
            if($id){
                $info = Db::name("third_user")->where("id = '$id'")->find();
                $this->assign("row",$info);
            }
            return $this->fetch('usermanager/adduser');
        }
    }
    //删除银行
    public function deluser()
    {
        $id = $this->request->param('ids/i');

        $re = Db::name("third_client")->where("id = '$id'")->delete();

        if($re){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }



    //-------------------------仲裁委--------------------------------


    public function initzclist()
    {
        $form = new WsForm();

        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("user_manager/addzcadmin");
        $form->setDelUrl("user_manager/delzcadmin");
        $form->setEditUrl("user_manager/addzcadmin");
        $form->setListUrl("user_manager/zcadminlist");

        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("th_id")->varTitle("仲裁id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("addtime")->varTitle("添加时间")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("usermanager/zcadminlist");
    }

    public function initaddzcview(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("th_id")->varTitle("仲裁id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::select)->required(true);
        $item->itemArr([1=>"主办",2=>"立案审批",3=>'裁决审批']);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::select)->required(true);
        $item->itemArr([0=>"未启用",1=>"启用"]);
        $form->addItem($item);

        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("UserManager/addzcadmin");
    }

    //zc_jigou_admin表
    public function zcadminlist()
    {
        if($this->request->isAjax()){

            $list = Db::name("jigou_admin")->select();

            foreach ($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);

                if($v['status'] == 1){
                    $list[$k]['status'] = "启用";
                }else{
                    $list[$k]['status'] = "未启用";
                }
            }

            $result = array("total" => Db::name('jigou_admin')->count(), "rows" => $list);

            return json($result);
        }else{
            return $this->fetch('usermanager/zcadminlist');
        }

    }
    //编辑/添加
    public function addzcadmin(){
        $id = $this->request->param('ids/i');
        if($this->request->isPost()){
            $param = $this->request->post("row/a");
            $roleArr = [
                1=>'主办',
                2=>'立案审批',
                3=>'裁决审批',
            ];
            $data = [
                'name' => trim($param['name']),
                'th_id' => trim($param['th_id']),
                'phone' => trim($param['phone']),
                'status' => trim($param['status']),
                'role' => trim($param['role']),
                'idid' => trim($param['idid']),
                'addtime'=>time(),
                'role_name'=>$roleArr[trim($param['role'])]
            ];



            if($id){

                unset($data['addtime']);
                $str = "修改";
                $re = Db::name("jigou_admin")->where("id = '$id'")->update($data);
            }else{
                $str = "添加";
                $re = Db::name("jigou_admin")->insertGetId($data);
            }

            if($re){
                $this->success($str."成功");
            }else{
                $this->error($str."失败");
            }
        }else{
            if($id){
                $info = Db::name("jigou_admin")->where("id = '$id'")->find();
                $this->assign("row",$info);
            }
            return $this->fetch('usermanager/addzcadmin');
        }
    }
    //删除
    public function delzcadmin(){
        $id = $this->request->param('ids/i');

        $re = Db::name("jigou_admin")->where("id = '$id'")->delete();

        if($re){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }



    //--------------------------仲裁员-------------------------------


    public function initzcylist()
    {
        $form = new WsForm();

        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("user_manager/addzcy");
        $form->setDelUrl("user_manager/delzcy");
        $form->setEditUrl("user_manager/addzcy");
        $form->setListUrl("user_manager/zcylist");

        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("speciality")->varTitle("专业特长")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("address")->varTitle("地址")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("is_foreign")->varTitle("国籍")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("addtime")->varTitle("添加时间")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("usermanager/zcylist");
    }

    public function initaddzcyview(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("name")->varTitle("姓名")->inputType(InputType::text)->required(true);
        $form->addItem($item);

        $item = new Item();
        $item->varName("role")->varTitle("角色")->inputType(InputType::select)->required(true);
        $item->itemArr([1=>"普通",2=>"管理员"]);
        $form->addItem($item);



        $item = new Item();
        $item->varName("status")->varTitle("状态")->inputType(InputType::select)->required(true);
        $item->itemArr([0=>"未启用",1=>"启用"]);
        $form->addItem($item);

        $item = new Item();
        $item->varName("speciality")->varTitle("专业特长")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("address")->varTitle("地址")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("is_foreign")->varTitle("国籍")->inputType(InputType::text);
        $form->addItem($item);


        $item = new Item();
        $item->varName("idid")->varTitle("身份证id")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("addtime")->varTitle("添加时间")->inputType(InputType::text)->required(true);
        $form->addItem($item);



        $item = new Item();
        $item->varName("phone")->varTitle("手机号")->inputType(InputType::text)->required(true);
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("UserManager/addzcy");
    }

    //zc_jigou_admin表
    public function zcylist()
    {
        if($this->request->isAjax()){

            $list = Db::name("zcy")->select();

            foreach ($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);

                if($v['status'] == 1){
                    $list[$k]['status'] = "启用";
                }else{
                    $list[$k]['status'] = "未启用";
                }
            }

            $result = array("total" => Db::name('zcy')->count(), "rows" => $list);

            return json($result);
        }else{
            return $this->fetch('usermanager/zcadminlist');
        }

    }
    //编辑/添加
    public function addzcy(){
        $id = $this->request->param('ids/i');
        if($this->request->isPost()){
            $param = $this->request->post("row/a");

            $data = [
                'name' => trim($param['name']),
                'phone' => trim($param['phone']),
                'speciality' => trim($param['speciality']),
                'address' => trim($param['address']),
                'status' => trim($param['status']),
                'role' => trim($param['role']),
                'idid' => trim($param['idid']),
                'addtime'=>time(),
            ];



            if($id){

                unset($data['addtime']);
                $str = "修改";
                $re = Db::name("zcy")->where("id = '$id'")->update($data);
            }else{
                $str = "添加";
                $re = Db::name("zcy")->insertGetId($data);
            }

            if($re){
                $this->success($str."成功");
            }else{
                $this->error($str."失败");
            }
        }else{
            if($id){
                $info = Db::name("zcy")->where("id = '$id'")->find();
                $this->assign("row",$info);
            }
            return $this->fetch('usermanager/addzcy');
        }
    }
    //删除
    public function delzcy(){
        $id = $this->request->param('ids/i');

        $re = Db::name("zcy")->where("id = '$id'")->delete();

        if($re){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }








}