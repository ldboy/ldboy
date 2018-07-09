<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/22
 * Time: 上午10:24
 */

namespace app\admin\controller\wechat;


use app\common\controller\Backend;
use wslibs\wszc\btn\Btn;
use wslibs\wszc\publicnumber\mylist\Mydossier;
use wslibs\wszc\publicnumber\mylist\Myinform;
use wslibs\wszc\publicnumber\mylist\Mywaitdeal;

class Mylist extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
    }

    public function index()
    {
        $idid = $this->request->param('idid/d');

        if(!$idid) $this->error('IDID参数错误');

        $list = Mydossier::getMyDossierList($idid);

        $this->assign('info',$list);
        $this->assign('idid',$idid);
        $this->assign('count',count($list));

        return $this->fetch('wxcallback/usercenter/mydossier');
    }

    public function inform()
    {
        $idid = $this->request->param('idid/d');

        if(!$idid) $this->error('IDID参数错误');

        $list = Myinform::getInformList($idid);

        $this->assign('info',$list);
        $this->assign('count',count($list));

        return $this->fetch('wxcallback/usercenter/myinform');
    }

    public function waitdeal()
    {
        $idid = $this->request->param('idid/d');

        if(!$idid) $this->error('IDID参数错误');

        echo '<h1 style="color: green">开发中,敬请期待</h1>';
        exit;
    }































}