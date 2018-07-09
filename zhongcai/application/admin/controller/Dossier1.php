<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-02
 * Time: 13:50
 */
namespace app\admin\controller;

use wslibs\wsform\InputType;
use wslibs\wsform\WsForm;
use wslibs\wsform\Item;
use wslibs\wszc;
use app\common\controller\Backend;
use think\Db;
use \dossier\filemanager\FileManager;
use \dossier\dossierconfig\DossierConfig;
use \dossier\dossiermanager\DossierManager;

class Dossier extends Backend
{



    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        parent::_initialize();
    }

    public function addDossier(){

    }


    public function initAddDossier(){
        if($this->request->isPost()){

            $param = $this->request->post("row/a");

            $re = wszc\Dossier::add();

            if($re){
                $this->success("成功");
            }else{
                $this->error("失败");
            }

            dump($param);
        }else{
            $form = new WsForm();
            $item = new Item();
            $item->varName("third_jg_id")->varTitle("third_jg_id")->inputType(InputType::text)->required(true);
            $form->addItem($item);

            $item = new Item();
            $item->varName("third_order_id")->varTitle("third_order_id")->inputType(InputType::text)->required(true);
            $form->addItem($item);

            $form->setMakeType(WsForm::Type_Form);
            $form->makeForm("Dossier/add");

            return $form->display($this);
        }
    }


    public function create(){

        $data = $this->request->post();



        $thrid_id = trim($data['user_id']);
        $keyword = trim($data['keyword']);
        $zc_id = trim($data['arbitration_id']);
        $event_type = trim($data['event_type']);
        $arbitration_name = trim($data['arbitration_name']);

        if(!$thrid_id || !$zc_id  || !$arbitration_name){
            echo json_encode($this->getMsg('10010','缺少参数'));
            exit;
        }

        /*$thridInfo = FileManager::getThirdClientInfo($thrid_id);


        $zhongCaiInfo = FileManager::getZhongCaiInfo($zc_id);

        if($thridInfo['zc_jg_id']!=$zc_id || $zhongCaiInfo['name']!=$arbitration_name){
            echo json_encode($this->getMsg('10013','错误的仲裁机构'));
            exit;
        }

        $types = DossierConfig::getDossierType($event_type);

        if(!$types){
            echo json_encode($this->getMsg('10014','错误的案件类型'));
            exit;
        }*/

        /**
         * @internal param 仲裁机构 id $zc_jg_id
         * @internal param 银行 id $third_jg_id
         * @internal param 标题 title
         * @internal param 第三方机构唯一ID $third_order_id
         */

        Db::startTrans();
        $res = wszc\Dossier::add($zc_id,$thrid_id,5);

        $thirdRes = Db::name('third_client')->where("id = ".$thrid_id)->setInc('total');
        $zcRes = Db::name('jigou')->where("id = ".$zc_id)->setInc('total');

        if($res && $thirdRes && $zcRes){
            Db::commit();
            $code = '10000';
            $msg = array('resMsg'=>'SUCCESS','resInfo'=>array('dossier_id'=>$res));
        }else{
            Db::rollback();
            $code = '10015';
            $msg = '创建失败';
        }
        echo json_encode($this->getMsg($code,$msg));
        exit;
    }


    private function getMsg($code,$msg)
    {
        return array(
            'code'=>$code,
            'msg'=>$msg,
        );
    }

    public function test11(){
        echo json_encode($this->getMsg('10010','缺少参数'));
        die;
    }
}