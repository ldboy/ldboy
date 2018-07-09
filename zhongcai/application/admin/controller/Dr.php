<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/2
 * Time: 13:30
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use think\Db;
use think\Request;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;

class Dr extends Backend {
    public function _initialize(){
        $this->use_action_js();
        parent::_initialize();
    }
    public function drlistView(){
     $form = new WsForm();
        $form->setAddUrl('');
        $form->setDelUrl('');
        $form->setEditUrl('');
        $form->setMultiUrl('');
        $form->setListUrl('Dr/drlist');

        $form->addItem((new Item())->varName('id')->varTitle('id')->inputType(InputType::text));
        $form->addItem((new Item())->varName('name')->varTitle('文档名称')->inputType(InputType::text));
        //$form->addItem((new Item())->varName('dm_name')->varTitle('模型名称')->inputType(InputType::text));
        $form->addItem((new Item())->varName('addtime')->varType('datetime')->varTitle('创建时间')->inputType(InputType::text));
        $form->addItem((new Item())->varName('create_type')->varTitle('创建方式')->inputType(InputType::text));
        $form->addItem((new Item())->varName('exid')->varTitle('扩展id')->inputType(InputType::text));
        $form->addItem((new Item())->varName('dossier_id')->varTitle('卷宗id')->inputType(InputType::text));

        $form->addItem((new Item())->varName('attr1_num')->varTitle('表单属性个数')->inputType(InputType::text));
        $form->addItem((new Item())->varName('attr2_num')->varTitle('签字属性个数')->inputType(InputType::text));
        $form->addItem((new Item())->varName('attr3_num')->varTitle('其它属性个数')->inputType(InputType::text));
        $form->addItem((new Item())->varName('attr1_success')->varTitle('表单属性提交个数')->inputType(InputType::text));
        $form->addItem((new Item())->varName('attr2_success')->varTitle('签字属性提交个数')->inputType(InputType::text));
        $form->addItem((new Item())->varName('attr3_success')->varTitle('其它属性提交个数')->inputType(InputType::text));

        $form->setMakeType(WsForm::Type_Table);
        $form->makeForm('Dr/drlist');

    }
  public function drlist(){
      $dossier_id = input('dossier_id',0,'intval');
      if(!$dossier_id){
          $this->error('参数错误');
      }
      $this->assign('dossier_id',$dossier_id);
//      $list = Db::name('dr')
//          ->where('dossier_id',$dossier_id)
//          ->select();

      if (IS_AJAX)
      {
          list($where, $sort, $order, $offset, $limit) = $this->buildparams(['dossier_id'=>$dossier_id]);
          $total = Db::name('dr')
              ->where($where)
              ->order($sort, $order)
              ->count();

          $list =  Db::name('dr')
              ->where($where)
              ->order($sort, $order)
              ->limit($offset, $limit)
              ->select();


          $result = array("total" => $total, "rows" => $list);

          return json($result);
      }
      return $this->fetch();
  }
}