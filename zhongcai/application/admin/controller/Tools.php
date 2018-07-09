<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/26
 * Time: 上午10:36
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use app\common\library\Email;
use Cron\CronExpression;
use think\console\command\optimize\Schema;
use think\Db;
use wslibs\cunchuio\CunChuIO;

use wslibs\run\WsExce;
use wslibs\wscontract\WsContract;
use wslibs\wsform\GroupItem;
use wslibs\wsform\GroupMoreItem;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use wslibs\wszc\btn\DListTab;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\dmail\Dmail;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dossier;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;


class Tools extends Backend
{





    protected $noNeedLogin = ['*'];

    public function dlisttabs()
    {
        $listtab = new DListTab();
       // var_dump($listtab->getList("tag_1"));
        var_dump($listtab->initTagCount());
       return "";
    }
    public function e()
    {

     // WsExce::dsend(50,13,0);
        return "";
    }
    public function optimize()
    {
        WsExce::exce("php think optimize:schema");
       // WsExce::exce("php think optimize:schema");
       // WsExce::exce("php think optimize:schema");
    }

    public function min()
    {
        WsExce::exceSync("node -v");
        WsExce::exceSync("php think min -m backend -r js");

    }

    public function autosign()
    {
        DocContract::autoSign(Ddocs::getOrInitFile(7, Constant::DOC_model_caijueshu, 0), LoginUser::getIdid(),0);
        exit;
    }

    public function resign()
    {
        $id = $this->request->param("id/d");
        if($id)
        {
            $docinfo= array();
            $docinfo['c_no'] = time();
            $docinfo['has_sign'] = 0;
            echo  Db::name("dr")->where("id", $id)->update($docinfo);
            echo Db::name("drs")->where("doc_id",$id)->delete();
        }

       exit;
    }

    public function dvalue()
    {

        $list = Ddocs::getFilesByGroup(656, 13);

        $mail = Dmail::instance();

        $mail->setTitle("来自石家庄仲裁委的邮件")->setHtmlTitle("您好，张三");
        $mail->setHtmlContent("您与某某的协议被申请人申请仲裁");
        foreach ($list as $value) {
            $mail->addDocFile($value['id'], $value['name']);
        }
        $mail->addUser("475511896@qq.com", "尹春芝");
       $mail->addUser("1725110348@qq.com", "任立荣");
        $mail->sendTemplate("t1");
        exit;


        $docinfo = Db::name("dr")->where("id", 2214)->find();
        $docinfo['c_no'] = "dfasddddfdssfdd";
        $docinfo['ha_sign'] = 0;
//        Db::name("drs")->where("doc_id",2214)->delete();
//        if (DocContract::initContract($docinfo)) {
//            return json(array("ok" => 1, "msg" => "文档正在生成中，请稍等"));
//        } else {
//            return json(array("ok" => 0, "msg" => "合同生成失败"));
//        }


        echo url('contract/gotoSign', ['c_no' => $docinfo['c_no'], 'uid' => 29, 'id_code' => 0]);

        exit;

        Ddocs::addZhengjuFile(561, 1, "测试的证据");
        exit;
        $_SESSION['aaa'] = 1;
        var_dump($_SESSION['aaa']);
        //sessio"a","ddddd");
        // var_dump(session("a"));
        // Dvalue::saveUniqueValue(798, "signer_time", array("y" => date("Y"), "m" => date("m"), "d" => date("d"), "string" => date("Y年m月d日")));
        //var_dump(WsContract::onSignContract(12));
        // LoginUser::isLogin();
        // CunChuIO::uploadImageFile("renlirong.jpeg","renlirong.jpeg","png");


    }

    public function initview()
    {
        $form = new WsForm();
        $form->setFormTitleTip("请填写资料");


        $form->addItem((new Item())->varName("xingming")->varTitle("姓名")->inputType(InputType::text)->defaultValue("好的額"));
        $item = new Item();
        $item->varName("xingming22")->varTitle("姓名333")->inputType(InputType::radio);
        $item->itemArr(array(1 => "不錯", 2 => "可以"))->defaultValue(1);
        $form->addItem($item);

        $item = new Item();
        $item->varName("diqu")->varTitle("地区")->inputType(InputType::citypicker);

        $form->addItem($item);

        $item = new Item();
        $item->varName("jianjie")->varTitle("简介")->inputType(InputType::textarea);

        $form->addItem($item->required(true));

        $item = new Item();
        $item->varName("neirong")->varTitle("内容")->inputType(InputType::content);

        $form->addItem($item->required(true));

        $item = new Item();
        $item->varName("neirong1")->varTitle("时间")->inputType(InputType::datetime);


        $group = new GroupMoreItem($form, "附件列表", "shenqingren", "fujiannum");

        $item = new Item();
        $item->varName("img")->varTitle("图片")->isUploadFile(true);
        $group->addItem($item->required(true));

        $item = new Item();
        $item->varName("neirong2dd")->varTitle("附件时间")->inputType(InputType::text);
        $group->addItem($item);


        $form->addItem($group);


        $group = new GroupItem($form, "申请人详情", "shenpingren");

        $item = new Item();
        $item->varName("youxiao")->varTitle("是否有效")->inputType(InputType::switch_field);
        $group->addItem($item->required(true));
        $item = new Item();
        $item->varName("xiai")->varTitle("喜爱")->inputType(InputType::select);


        //$item->itemArr(array("0" => "篮球", "乒乓球"));
        $item->itemArrValName("xiailist");

        $group->addItem($item->required(true));

        $form->addItem($group);
        // $this->assign("namelist", array("2" => "上海", 3 => "動靜"));
        $form->setMakeType(WsForm::Type_Form);
        // $form->makeForm("Tools/index");


//        $this->assign("row",array("xingming"=>"張三"));


        $this->assign("fujiannum", 5);
        $data['neirong2dd'][] = 1;
        $data['neirong2dd'][] = 2;
        $data['neirong2dd'][] = 3;
        $data['neirong2dd'][] = 4;
        $data['neirong2dd'][] = 5;
        $this->assign("row", $data);
        $this->assign(array("a" => array("b" => array(2 => 5))));
        $this->assign("rindex", 2);

        $this->assign("xiailist", array("ping", "zu", "lan"));

        return $form->display($this);

    }

    public function initview1()
    {
        $form = new WsForm();


        $form->setMultiUrl("Tools/list1");
        $form->setAddUrl("http://wwww.baidu.com");
        $form->setDelUrl("http://wwww.baidu.com");
        $form->setEditUrl("http://wwww.baidu.com");
        $form->setAddUrl("http://wwww.baidu.com");
        $form->setListUrl("Tools/list1");


        $form->addItem((new Item())->varName("id")->varTitle("ID")->inputType(InputType::text));
        $form->addItem((new Item())->varName("xingming")->varTitle("姓名")->inputType(InputType::text));

        $form->setMakeType(WsForm::Type_Table);


        $this->display($form->makeForm());
    }

    public function index()
    {


        if ($this->request->isAjax()) {
            $this->success("成功");
        } else {
            $this->use_action_js();
            $this->assign("fujiannum", 5);
            $data['neirong2dd'][] = 1;
            $data['neirong2dd'][] = 2;
            $data['neirong2dd'][] = 3;
            $data['neirong2dd'][] = 4;
            $data['neirong2dd'][] = 5;
            $this->assign("row", $data);
            $this->assign(array("a" => array("b" => array(2 => 5))));
            $this->assign("rindex", 2);

            $this->assign("xiailist", array("ping", "zu", "lan"));

            return $this->fetch();
        }

    }

    public function list1()
    {


        if (IS_AJAX) {

            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $list = [];

            for ($i = $offset; $i < $offset + $limit; $i++) {
                $list[] = array("id" => $i, "xingming" => "mingcheng" . $i);
            }

            $result = array("total" => 500, "rows" => $list);

            return json($result);

        }
        return $this->fetch("list1");
    }


    public function adddo()
    {
        // var_dump(Dossier::add(1,1,1,1,"新仲裁"));

        // var_dump(Ddocs::getDocModeInfo("5,6"));
        var_dump(Ddocs::getFilesByGroup(7, 1));
    }
}