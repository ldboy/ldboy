<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;


class Jigou extends Backend
{
    protected $model = null;

    public function _initialize()
    {
        $this->use_action_js();
        parent::_initialize();

    }

    public function initaddview(){
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
        $item->varName("intro")->varTitle("简介")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("Jigou/add");
    }


    public function initlist(){
        $form = new WsForm();



        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("Jigou/add");
        $form->setDelUrl("Jigou/delete");
        $form->setEditUrl("Jigou/add");
        $form->setListUrl("Jigou/jglist");



        $item = new Item();
        $item->varName("id")->varTitle("ID")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("name")->varTitle("机构名称")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('address')->varTitle('机构地址')->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('tel')->varTitle("联系方式")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('status')->varTitle("状态")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('intro')->varTitle('简介')->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('total')->varTitle('总业务')->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('finish_num')->varTitle('成功业务')->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('failed_num')->varTitle('失败业务')->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName('addtime')->varTitle('添加时间')->inputType(InputType::text);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("Jigou/index");
    }

    public function add(){
        $id = $this->request->param('ids/i');
        if($this->request->isPost()){
            $param = $this->request->post("row/a");

            $data = [
                'name' => trim($param['name']),
                'address' => trim($param['address']),
                'tel' => trim($param['tel']),
                'status' => trim($param['status']),
                'intro' => trim($param['intro']),
                'addtime' => time()
            ];

            if($id){
                unset($data['addtime']);
                $str = "修改";
                $re = Db::name("jigou")->where("id = '$id'")->update($data);
            }else{
                $str = "添加";
                $re = Db::name("jigou")->insertGetId($data);
            }

            if($re){
                $this->success($str."成功");
            }else{
                $this->error($str."失败");
            }
        }else{
            return $this->fetch();
        }
    }


    public function jglist(){
        if($this->request->isAjax()){
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = Db::name("jigou")->select();

            foreach ($list as $k => $v){
                $list[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);

                if($v['status'] == 1){
                    $list[$k]['status'] = "启用";
                }else{
                    $list[$k]['status'] = "未启用";
                }
            }

            $result = array("total" => Db::name('jigou')->count(), "rows" => $list);

            return json($result);
        }else{
            return $this->fetch("index");
        }
    }


    public function delete(){
        $id = $this->request->param('ids/i');

        $re = Db::name("jigou")->where("id = '$id'")->delete();

        if($re){
            $this->success("删除成功");
        }else{
            $this->error("删除失败");
        }
    }
}