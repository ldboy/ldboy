<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/5
 * Time: 17:29
 * 卷宗详情
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use dossier\DossierDoc;
use think\Db;
use think\db\Query;
use think\Request;

class Dossierinfo extends Backend{
      public function _initialize(){
          parent::_initialize();
      }
    public function index(){
       $dossier_id = Request::instance()->param('id/d');
        if(!$dossier_id){
            $this->error('参数错误');
        }
        $this->assign('d_id',$dossier_id);
        $query = new Query();
        $query->name('dossier')->where('id',$dossier_id);
        // 基本信息
        $dossierInfo = Db::find($query);
//        $dossierInfo = Db::find(function($query)use($dossier_id){
//            $query->name('dossier')->where('id',$dossier_id);
//        });
        if(!$dossierInfo){
            $this->error('业务不存在');
        }
        $dossierInfo['addtime'] = date('Y-m-d H:i:s',$dossierInfo['addtime']);
        $this->assign('dossierInfo',$dossierInfo);
        //相关人员
        $userList = Db::name('dossier_users')->where("id",$dossier_id)->select();
        $this->assign('userList',$userList);
        // 各种文件
        $status = $dossierInfo['status'];
        // 受理文件
        $sldoc = [];
        if($status>=2){
            $sldoc = DossierDoc::getConfig(2);
        }
        $this->assign('sldoc',$sldoc);
        // 受理环节需要公布的证据 是 申请时提交的证据
        $slAttachment = Db::name('dossier_fujian')
            ->where('dossier_id',$dossier_id)
            ->where('type',1)
            ->select();
        $this->assign('slAttachment',$slAttachment);

        // 答辩质证文件
        $defence = [];
        if($status>=3){
            $defence = DossierDoc::getConfig(3);
        }
        $this->assign('defence',$defence);
        return $this->fetch();
    }
    // 获得申请资料 被申请资料
    private function getAttachment($dossier_id,$idid){
       $list = Db::name('dossier_fujian')
           ->where('dossier_id',$dossier_id)
           ->where('idid',$idid)
           ->select();
        return $list;
    }

}