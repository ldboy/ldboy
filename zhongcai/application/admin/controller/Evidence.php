<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/27
 * Time: 15:44
 */

namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use think\Request;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;

class Evidence extends Backend {

    public function _initialize()
    {
        $this->use_action_js();
        parent::_initialize();
    }

    // 证据列表模板
    public function indexView(){
        $form = new WsForm();
        $form->setMultiUrl("Evidence/index");
        $form->setAddUrl("");
        $form->setDelUrl("");
        $form->setEditUrl("Evidence/upload");
        $form->setListUrl("Evidence/index");

        $form->addItem((new Item())->varName("id")->varTitle("ID")->inputType(InputType::text));
        $form->addItem((new Item())->varName("doc_id")->varTitle("文档id")->inputType(InputType::text));
        $form->addItem((new Item())->varName("attr_id")->varTitle("属性id")->inputType(InputType::text));
        $form->addItem((new Item())->varName("value")->varTitle("描述")->inputType(InputType::textarea));
        $form->addItem((new Item())->varName("status")->varTitle("状态")->inputType(InputType::text));
        $form->addItem((new Item())->varName("ext_id")->varTitle("操作人")->inputType(InputType::text));
        $form->addItem((new Item())->varName("path")->varTitle("文件")->inputType(InputType::text));


        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm("Evidence/index");
    }
    // 证据列表
  public function index(){
      if (IS_AJAX)
      {
          list($where, $sort, $order, $offset, $limit) = $this->buildparams();
          //取数据
          $total = Db::name('drav')
              ->where($where)
              ->order($sort, $order)
              ->count();

          $list =  Db::name('drav')
              ->where($where)
              ->order($sort, $order)
              ->limit($offset, $limit)
              ->select();

          $result = array("total" => $total, "rows" => $list);
          return json($result);
      }
      return $this->fetch();
  }
    // 查看详情模板
    public function detailView(){

    }
    // 查看详情
    public function detail(){

    }
    // 生成上传模板
    public function uploadView(){

        $form = new WsForm();
        $form->setMultiUrl("Evidence/upload");
        $form->setAddUrl("");
        $form->setDelUrl("");
        $form->setEditUrl("");
        $form->setListUrl("Evidence/upload");

        $form->addItem((new Item())->varName("doc_id")->varTitle("")->inputType(InputType::hidden));
        $form->addItem((new Item())->varName("attr_id")->varTitle("")->inputType(InputType::hidden));
        $form->addItem((new Item())->varName("value")->varTitle("描述")->inputType(InputType::textarea)->required(true));
        $form->addItem((new Item())->varName("path")->varTitle("文件")->isUploadFile(true));

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("Evidence/upload");
    }
    // 接收上传参数 存数据库
    public function upload(){
        // 接收 doc_id  attr_id
        // 编辑的时候  接收 id
        $id = Request::instance()->param('ids/i');
        $doc_id = Request::instance()->param('doc_id/i');
        $attr_id = Request::instance()->param('attr_id/i');
        if(!$id){
            if(!$doc_id||!$attr_id){
//                $this->error('参数错误');
            }
        }
        $row = [
            'doc_id'=>$doc_id,
            'attr_id'=>$attr_id,
        ];
        $this->assign('row',$row);

       if(IS_AJAX){

           $data = Request::instance()->post('row/a');
           if(!$data['value']){
               $this->error('描述不能为空');
           }
           if(!$data['path']){
               $this->error('上传文件不能为空');
           }
           if(!$data['doc_id']){
               $this->error('文档id不能为空');
           }
           if(!$data['attr_id']){
               $this->error('属性id不能为空');
           }
           // 数据入库 或更新
           if($id){
              Db::name('drav')->where('id',$id)->update($data);
           }else{
               $data['status'] = 1;
               $data['ext_id'] = 0;
               $data['createtime'] = time();

              Db::name('drav')->insert($data);
           }
           $this->success('成功');
       }
        if($id){
            $info = Db::name('drav')->where('id',$id)->find();
            $this->assign('row',$info);
        }
        return $this->fetch();
    }



}