<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-08
 * Time: 21:16
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use question\QuestionExpand;
/*use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;*/
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dvalue;


class Question extends Backend
{
    const VAR_NAME = "Question";

    private $type = ['legal','relation','reality','other'];
    /*public function initview(){
        $form = new WsForm();
        $item = new Item();
        $item->varName("legal")->varTitle("合法性")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("relation")->varTitle("关联性")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("reality")->varTitle("真实性")->inputType(InputType::text);
        $form->addItem($item);

        $item = new Item();
        $item->varName("other")->varTitle("其他")->inputType(InputType::text);
        $form->addItem($item);


        $form->setMakeType(WsForm::Type_Form);
        $form->makeForm("question/index");
    }*/
    public function test(){
        $gid = input("gid","","intval");
        $arr = Ddocs::getFilesByGroup(28,$gid);
        dump(QuestionExpand::getQuestion(117,117,'',true));

        dump($arr);
    }


    public function index(){
        if($this->request->isPost()){
            $data = $this->request->post("row/a");

            if(!$data['legal'] && !$data['relation'] && !$data['reality'] && !$data['other']){
                $this->error("请至少填写一项");
            }


            $dossier_id = $data['dossier_id'];
            $evidence_id = $data['evidence_id'];

            foreach ($data as $k => $v){
                if(!in_array($k,$this->type)){
                    unset($data[$k]);
                }
            }

            $re = QuestionExpand::addQuestion($dossier_id,$evidence_id,session('zc_admin_uid'),$data);

            Ddocs::getFilesByGroup($dossier_id,7);
            foreach (Ddocs::getFilesByGroup($dossier_id,7) as $k => $v){
                if($v['name'] == "质证意见"){
                    //dump($dossier_id);dump($v['doc_model_id']);dump($re);die;
                    Dvalue::saveUniqueValueByDocMode($dossier_id,$v['doc_model_id'],self::VAR_NAME,$data,$re);
                }
            }

            if($re){
                $this->success("质证成功");
            }else{
                $this->error("质证失败");
            }

        }else{

            $dossier_id = input("dossier_id","","intval");
            $evidence_id = input("evidence_id","","intval");

            if(!$dossier_id || !$evidence_id){
                //$this->error("参数错误");
            }


            $this->assign("dossier_id",$dossier_id);
            $this->assign("evidence_id",$evidence_id);

            return $this->fetch();
        }
    }
}