<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/17
 * Time: 上午8:22
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;

class Doc extends Backend
{
    //
    public function index()
    {
        $moban = $this->request->param('moban/d');
        if($moban==1){
            $this->fetch('request');
        }elseif($moban==2){
            $this->fetch('reason');
        }else{
            $this->error('参数错误');
        }

    }
}