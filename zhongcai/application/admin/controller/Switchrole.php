<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/9
 * Time: 13:01
 */

namespace app\admin\controller;


use app\common\controller\Backend;

use think\Session;
use wslibs\wszc\idcard\IDcard;

class Switchrole  extends Backend{
  public function index(){
      $idcard = $this->request->get('idcard/s');
      $idid = IDcard::getIdId($idcard);
      Session::set('zc_admin_idid',$idid);
      echo 'success';
      dump(Session::get());
  }
}