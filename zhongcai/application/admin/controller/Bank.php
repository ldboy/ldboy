<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/18
 * Time: 下午3:31
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;

class Bank extends Backend
{
    public function initview()
    {
        $form = new WsForm();
        $form->setMultiUrl("bank/list");
        $form->setAddUrl("dossier/cp/addZJ");
        $form->addItem((new Item())->varName("id")->varTitle("银行ID")->inputType(InputType::text));
        $form->addItem((new Item())->varName("name")->varTitle("银行名称")->inputType(InputType::text));
        $form->addItem((new Item())->varName("address")->varTitle("银行地址")->inputType(InputType::text));
        $form->addItem((new Item())->varName("tel")->varTitle("电话")->inputType(InputType::text));
        $form->addItem((new Item())->varName("status")->varTitle("状态")->inputType(InputType::text));
        $form->addItem((new Item())->varName("a_jgid")->varTitle("银行系统ID")->inputType(InputType::text));
        $form->addItem((new Item())->varName("addtime")->varTitle("时间")->inputType(InputType::datetime));

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("bank/list");
    }
}