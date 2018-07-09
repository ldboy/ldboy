<?php

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Request;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use app\admin\controller\doccommon\DocCommon;
use app\admin\model\DocManager;

/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Dma extends Backend
{
    
    /**
     * Dma模型对象
     */
    protected $model = null;

    public function _initialize()
    {
        $this->use_action_js();
        parent::_initialize();
//        $this->model = model('Dma');
//        $this->view->assign("typeList", $this->model->getTypeList());
//        $this->view->assign("subTypeList", $this->model->getSubTypeList());
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

    /**
     * 生成模板
     */
    public function initview()
    {
        $form = new WsForm();
        $form->setMultiUrl("dma/index");
        $form->setAddUrl("dma/add");
        $form->setDelUrl("dma/del");
        $form->setEditUrl("dma/add");
        $form->setListUrl("dma/dmalist");
        $form->addItem((new Item())->varName("id")->varTitle('id')->inputType(InputType::text));
        $form->addItem((new Item())->varName("doc_model_id")->varTitle("文档模型id")->inputType(InputType::text));
        $form->addItem((new Item())->varName("type")->varTitle("类型")->inputType(InputType::text));
        $form->addItem((new Item())->varName("sub_type")->varTitle("二级类型")->inputType(InputType::text));
        $form->addItem((new Item())->varName("op")->varTitle("选项值")->inputType(InputType::text));
        $form->addItem((new Item())->varName("name")->varTitle("提示")->inputType(InputType::text));
        $form->addItem((new Item())->varName("flag")->varTitle("唯一标识")->inputType(InputType::text));
        $form->addItem((new Item())->varName("createtime")->varTitle("创建时间")->inputType(InputType::text));
        $form->addItem((new Item())->varName("gid")->varTitle("上级id")->inputType(InputType::text));
        $form->addItem((new Item())->varName("sync_id")->varTitle("同步id")->inputType(InputType::text));

        $form->setMakeType(WsForm::Type_Table);
//        $form->display($this);
        $form->makeForm("dma/dmalist");
    }

    public function addview(){
        $form = new WsForm();
        $form->setMultiUrl("dma/index");
        $form->setAddUrl("dma/add");
        $form->setDelUrl("dma/del");
        $form->setEditUrl("dma/add");
        $form->setListUrl("");

        $form->addItem((new Item())->varName("id")->varTitle('')->inputType(InputType::hidden));
        $form->addItem((new Item())->varName("doc_model_id")->varTitle("请选择文档模型")->inputType(InputType::select)->required(true)->itemArrValName('dmList'));
        $form->addItem((new Item())->varName("type")->varTitle("类型")->inputType(InputType::select)->required(true)->itemArrValName('typeList'));
        $form->addItem((new Item())->varName("sub_type")->varTitle("二级类型")->inputType(InputType::select)->required(true)->itemArrValName('subTypeList'));
        $form->addItem((new Item())->varName("op")->varTitle("选项值")->inputType(InputType::text));
        $form->addItem((new Item())->varName("name")->varTitle("提示")->inputType(InputType::text)->required(true));
        $form->addItem((new Item())->varName("flag")->varTitle("唯一标识")->inputType(InputType::text)->required(true));
        $form->addItem((new Item())->varName("sync_id")->varTitle("同步id")->inputType(InputType::text)->required(true));
        $form->addItem((new Item())->varName("gid")->varTitle("上级id")->inputType(InputType::select)->itemArrValName('gidArr'));

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("dma/add");
    }
    public function dmalist(){

        if (IS_AJAX)
        {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = Db::name('dma')
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list =  Db::name('dma')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();

            foreach($list as $k=>$v){
                $v['sub_type'] =  DocCommon::dmaSubTypeName($v['type'],$v['sub_type']);
                $v['type'] =  DocCommon::dmaTypeName($v['type']);
                $v['createtime'] = date('Y-m-d H:i:s',$v['createtime']);
                $list[$k] = $v;
            }

            $result = array("total" => $total, "rows" => $list);
            return json($result);
        }
        return $this->fetch();
    }

    public function add(){
        $id = Request::instance()->param('ids/i');

        if(IS_AJAX){

            $data = Request::instance()->post('row/a');
            if($id){
                $info = Db::name('dma')->where('id',$id)->update($data);
                $this->success('修改成功');
            }
            $data['createtime'] = time();
            $res = Db::name('dma')->insert($data);
            DocManager::setAttrNum($data['doc_model_id'],$data['type'],true);
            $this->success('成功');
        }

        if($id){
            $info = Db::name('dma')->where('id',$id)->find();
            $this->assign('row',$info);
        }
        // 如果有dmId 就查一条出来
        // 如果没有 就查多条出来
        $dmId = Request::instance()->param('dm_id/i');
        $dmList = $this->getDmList($dmId);
        $this->assign('dmList',$dmList);
        //typeList  subTypeList
        $this->assign('typeList',DocCommon::getDmaTypes());
        $this->assign('subTypeList',DocCommon::getDmaSubTypes());
        $this->assign('gidArr',$this->getGidList());
        return $this->fetch();
    }
    private function getDmList($dmId=0){
        $dmModel = Db::name('dm');
        if($dmId){
            $dmModel->where('id',$dmId);
        }
        $list = $dmModel->column('id,model_name');
        return $list;
    }
    private function getGidList(){
        $dmaModel = Db::name('dma');
        $dmaModel->where('type',1);
        $dmaModel->where('sub_type',0);
        $list = $dmaModel->column('id,name');
        array_unshift($list,'无');
        return $list;
    }

    public function del(){

        $id = Request::instance()->param('ids/i');
        $info = Db::name('dma')->where('id',$id)->find();
        Db::startTrans();
        $delRes = Db::name('dma')->delete($id);
        $res = DocManager::setAttrNum($info['doc_model_id'],$info['type']);
        if($delRes&&$res){
            Db::commit();
            $this->success('成功');
        }else{
            Db::rollback();
            $this->error('失败');
        }
    }

}
