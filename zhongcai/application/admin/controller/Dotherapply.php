<?php
namespace app\admin\controller;
use app\common\controller\Backend;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dother;
use wslibs\wszc\Gxq;
use wslibs\wszc\Constant;
use think\Db;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;
use wslibs\wszc\Ds;

use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc\qx\Qx;
use wslibs\wszc\User;

class Dotherapply extends Backend
{
    public function _initialize(){
        parent::_initialize();
        $this->use_action_js();
    }
    public function initindex(){
        $form = new WsForm();
        $item = new Item();
        // 模板里有js 重新生成模板可能导致覆盖
        $item->varName("matter")->varTitle("请选择申请类型")->inputType(InputType::radio)->required(true);
        $item->itemArr(array(1 => "申请调解", 2 => "申请中止",3=>'申请鉴定',4=>'其他'))->defaultValue(1);
        $form->addItem($item);

        $item = new Item();
        $item->varName("cont")->varTitle("请填写具体申请内容")->inputType(InputType::textarea)->required(true);
        $form->addItem($item);

        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("Dotherapply/index");
    }

    public function index(){

        $did = $this->request->param("id/d");
        $info = Dother::hasOtherApplay($did);
        if($info){
            $this->error('已经申请过了，请勿重复操作');
        }
        if($this->request->isPost()){
            $postdata = $this->request->post("row/a");
            if($postdata['type']==4&&!$postdata['cont']){
                $this->error("请填写具体申请内容");
            }
            $re1 = Dother::add($did,$postdata['type'],$postdata['cont']);
            if($re1){
                $this->success("您的申请已提交，我们将通过电子邮件及电话联系您，请及时查收！",'',['wsreload'=>2,'alert'=>1]);
            }else{
                $this->error("操作失败");
            }
        }else{
            $this->use_form_Js();
            return $this->fetch();
        }
    }

    public function other(){
        $id = $this->request->param("id/d");
        $info = Dother::getOtherInfo($id);
        if($info['status']==1&&LoginUser::isZhongCaiWeiZhuBan()){
            Dother::changeStatus($id,2);
        }
        $this->assign($info);
        return $this->fetch();
    }
}
