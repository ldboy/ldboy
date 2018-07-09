<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/9
 * Time: 上午10:05
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use wslibs\wszc\LoginUser;
use wslibs\wszc\mes\Mes;


class Inform extends Backend
{
    private $typeList = [];

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $type = (int)$this->request->param('type/d');

        $type = $type ? $type : 10;

        $this->assign('type',$type);

        $inform = new \wslibs\wszc\mes\Inform();

        $this->typeList = $inform->getStatus();

        $nums = $inform->getStatusCount();

        $this->typeList = $inform->DealStatus($this->typeList,$nums);

        $this->assign('typeList',$this->typeList);

        return $this->fetch();
    }

    public function dlist()
    {
        $status = $this->request->param("type/d");

        $limit = (int)($this->request->param("limit"));
        $start = (int)($this->request->param("offset"));

        $inform = new \wslibs\wszc\mes\Inform();

        $inform->addHere($status);

        $list = $inform->getList($start,$limit);

        return json(['rows' => $list, 'total' => count($list)]);

    }

    public function informlist()
    {

        list($num,$list) =  (new Mes())->addtype()->addIDId(LoginUser::getIdid())->msg_select();

        return ['new'=>$num,'newslist'=>$list];

    }

    public function dddlist()
    {
        list($num,$list) =  (new Mes())->addtype()->addIDId(LoginUser::getIdid())->msg_select();

        dump($list);
        return json(['new'=>$num,'newslist'=>$list]);
    }


}