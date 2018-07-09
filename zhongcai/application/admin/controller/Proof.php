<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-08
 * Time: 14:03
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use think\Db;

class Proof extends Backend
{
    public function _initialize()
    {
        $this->use_action_js();
        parent::_initialize();
    }


    public function index(){

        if($this->request->isPost()){
            $request = $this->request->post("row/a");

            $data = [
                'dossier_id' => $request['dossier_id'],
                'type' => 7,
                'path' => $request['path'],
                'des' => $request['value'],
                'idid' => session("zc_admin_idid"),
                'status' => 0
            ];

            //dump($data);die;

            $re = Db::name("dossier_fujian")->insertGetId($data);

            if($re){
                $this->success("上传成功");
            }else{
                $this->error("失败");
            }

        }else{
            $dossier_id = input("dossier_id","","intval");

            $this->assign("dossier_id",$dossier_id);
            return $this->fetch();
        }
    }
}