<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/3
 * Time: 上午8:45
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;
use think\Db;
use wslibs\wsform\GroupMoreItem;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DocAttr;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;

class Img extends Backend
{


    public function index()
    {
        $docid = $this->request->param("docid/d");
        $is_phone = $this->request->param("is_phone/d");
        if (!$docid) $this->error();

        $list = Dvalue::getDocFiles($docid, false);

        $this->assign("docid", $docid);

        $canedit = false;

        $info = Ddocs::getDocInfo($docid);
        $a_title = '上传证据';
        if($info['doc_model_id']==Constant::DOC_model_shenfenzhengjian){
            $a_title = '上传身份材料';
        }
        if ($this->request->param("edit")) {
            $canedit = $info['uid'] == LoginUser::getIdid();
        }
        $this->assign('is_phone',$is_phone);
        $this->assign('a_title',$a_title);
        $this->assign("canedit", $canedit);
        $this->assign("list", $list);
        $info['proof'] =  $info['des'];
        $this->assign("row", $info);

        if ($canedit) {
           // $this->use_form_Js();
        }
        if($is_phone){
            return $this->fetch("index_phone");
        }
        return $this->fetch("index");


    }


    public function editzj()
    {
        $zid = (int)$this->request->param("id/d");
        if (!$zid) $this->error("参数错误");
        $row = $this->request->param('row/a');
        $name = $row['name'];
        $proof = $row['proof'];


        if (!$name || !$proof) {
            $this->error('名称或事项不能为空');
        }


        $res =  Db::name("dr")->where(array("id"=>$zid))->update(["name"=>$name,"des"=>$proof]);



        $this->success('成功', "", array("alert" => 1, "wsreload" => 0));
    }

    public function mkform()
    {
        $form = new WsForm();
        $form->setMakeType(WsForm::Type_Form);
        $goup = new GroupMoreItem($form, "资料上传", "ziliao");
        $item = new Item();
        $item->varName("img")->varTitle("资料描述")->inputType(InputType::text);
        $goup->addItem($item->isUploadFile());
        $goup->addItem((new Item())->varName("title")->varTitle("资料描述")->inputType(InputType::textarea));

        $form->addItem($goup);
        $form->makeForm("wsdoc/img/add");
    }

    public function add()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error();

        if ($this->request->isPost()) {
            $data = $this->request->post("row/a");
            foreach ($data['ziliao']['img'] as $key => $value) {
                if ($value) {
                    //  DocAttr::addDocFile($docid,$data['ziliao']['title'][$key],$data['ziliao']['img'][$key],$data['ziliao']['img_id'][$key]);
//                    Dvalue::addFileToDoc($docid, Dvalue::mkFileValue(ltrim($data['ziliao']['img'][$key],"/"),$data['ziliao']['title'][$key]), $data['ziliao']['img_id'][$key],User::getLoginUid());
                    $img = explode(',', $data['ziliao']['img'][$key]);
                    foreach ($img as $k => $v) {
                        Dvalue::addFileToDoc($docid, Dvalue::mkFileValue(ltrim($v, "/"), $data['ziliao']['title'][$key]), $data['ziliao']['img_id'][$key], User::getLoginUid());
                    }
                }
            }
            $this->success("添加成功", "", array("alert" => 0, "wsreload" => 2));
        } else {

            $this->assign("ziliao_num", 1);
            return $this->fetch("add");
        }

    }


    public function adds()
    {
        $docid = $this->request->param("docid/d");
        $imgs = $this->request->param("imgs/a");
        $fids = $this->request->param("fids/a");

        foreach ($imgs as $k => $v) {
            if ($v)
                Dvalue::addFileToDoc($docid, ltrim($v, "/"), (int)$fids[$k], User::getLoginUid());
        }
        $this->success("添加成功", "", array("alert" => 0, "wsreload" => 2));
    }

    public function shanchu()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error();
        $ingid = $this->request->param("id/d");
        if (!$ingid) $this->error();

        Dvalue::delDocValueByid($ingid);
        $this->success("删除成功", "", array("alert" => 1, "wsreload" => 2));

    }
}