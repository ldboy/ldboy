<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/10
 * Time: 22:13
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use think\Db;
use wslibs\wszc\User;

class Wswtest extends Backend{
  public function role(){
      $dossier_id = $this->request->get('d_id/d');
      dump('qx_role:'.User::getRoleInDossier($dossier_id));
      dump('login_uid:'.User::getLoginUid());
      dump('login_info:');
      dump(Db::name('idcards')->where('id',User::getLoginUid())->find());
  }
    public function test(){
        $str = '000000002';
        echo (int)$str;
        exit;
    }
}