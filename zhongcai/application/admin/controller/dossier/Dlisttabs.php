<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/12
 * Time: ����1:37
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;

use wslibs\wszc\btn\DListTab;
use wslibs\wszc\Dcheck;

use wslibs\wszc\LoginUser;

class Dlisttabs extends Backend
{

    public function _initialize()
    {

        parent::_initialize();
    }

    public function index()
    {

        if (LoginUser::isZhongCaiYuan() && !Dcheck::checkArbitratorCanDo(LoginUser::getIdid())) {
            $this->redirect(url('cross.addfile/index', ['title' => '完善信息']));
            exit;
        }
        $dlisttabs = new DListTab();
        $dlisttabs->initTagCount();
        $this->assign("tabs", $dlisttabs->getTabsShow());

        if($_GET['zhz']==1){
            dump($dlisttabs->getTabsShow());
        }

        $tag = trim($this->request->param("type"));
        if(!$tag)
        {
            $tag = "tag_0";
        }
        $this->assign("type", $tag);
        return $this->fetch();


    }


    public function dlist()
    {

        $dlisttabs = new DListTab();
        $tag = trim($this->request->param("type"));


        if(!$tag)
        {
            $tag = "tag_0";
        }

         $keyword=$this->request->param('keywords');




        $limit = (int)($this->request->param("limit"));
        $start = (int)($this->request->param("offset"));



        list($total, $list) = $dlisttabs->getList($tag,$start,$limit,$keyword);
        return json(['rows' => $list, 'total' => $total]);
    }
}