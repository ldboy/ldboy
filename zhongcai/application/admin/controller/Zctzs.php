<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5
 * Time: 18:53
 */

namespace app\admin\controller;


use app\common\controller\Backend;

class Zctzs extends Backend{
   public function _initialize(){
       parent::_initialize();
   }
    public function index(){
       $dossier_id = request()->instance()->param('id/d');
        return $this->fetch();
    }
}