<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/28
 * Time: 下午6:32
 */

namespace app\admin\controller\wechat;


use app\common\controller\Backend;
use think\Db;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Ds;
use wslibs\wszc\LoginUser;
use wslibs\wszc\publicnumber\mylist\Mywaitdeal;

class Casecailiao extends Backend
{
    private $idid = 0;

    public function __initialize()
    {
        $this->noNeedLogin = ['*'];
//
        parent::_initialize();
//
//
        $this->idid = $this->request->param('idid/d');
        if(!$this->idid) $this->error('参数错误');
        Mywaitdeal::cdMyDossierInfoLogin($this->idid);
    }

    public function index()
    {

        $d_id = $openID = $this->request->param('did/s');

        if(!$d_id){
            $this->error('did参数错误');
        }

        $files = Ds::getFilesOfOne($d_id, [$this->idid, LoginUser::getRoleThIdId()]);

        $html = [];

        foreach ($files as $k => $v) {

            $html[$v['title'].' '.$v['time']]= Ddocs::getFilesHtml($this, array_column($v['item'], 'doc_id'), $v['title'],"box-info",$v['time'],1,1,true);

        }

        $this->assign('filehtml', $html);

        return $this->fetch('wxcallback/usercenter/caseinfo');
    }
}