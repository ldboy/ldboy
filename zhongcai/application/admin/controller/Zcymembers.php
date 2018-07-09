<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/8
 * Time: 10:48
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use think\Db;
use think\Request;

class Zcymembers extends Backend{
    public function _initialize(){
        parent::_initialize();
    }
    // Zcymembers/index/dossier_id/1
    public function index(){
        $dossier_id = Request::instance()->param('dossier_id/d');
        // 找到机构id
        $jigou_id = Db::name('dossier')->where('id',$dossier_id)->value('zc_jg_id');
        $memberList = Db::name('jg_user')->where('jg_id',$jigou_id)->select();
        $this->assign('memberList',$memberList);
        return $this->fetch();
    }
}