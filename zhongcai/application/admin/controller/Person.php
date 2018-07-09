<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/26
 * Time: 下午6:14
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use wslibs\wszc\datashow\PersonInfo;

class Person extends Backend
{
    public function index()
    {

        $this->use_action_js();

        if($_GET['zhz']==1){
            dump($person = PersonInfo::getPersonList());
        }

        if($this->request->isAjax()){
            $person = PersonInfo::getPersonList();

            $person = array("total" =>count($person), "rows" => $person);

            return json($person);
        }

        return $this->fetch();
    }

    public function initview()
    {

        $form = new WsForm();
        $form->setMultiUrl("person/index");

        $form->setListUrl("person/list");
        $form->addItem((new Item())->varName("id")->varTitle('idid')->inputType(InputType::text));
        $form->addItem((new Item())->varName("id_card")->varTitle("身份证号")->inputType(InputType::text));
        $form->addItem((new Item())->varName("real_name")->varTitle("姓名")->inputType(InputType::text));
        $form->addItem((new Item())->varName("num")->varTitle("仲裁案件数")->inputType(InputType::text));

        $form->setMakeType(WsForm::Type_Table);

        $form->makeForm("person/index");
    }
}