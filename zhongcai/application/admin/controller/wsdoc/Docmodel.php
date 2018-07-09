<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/17
 * Time: 上午8:39
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;

class Docmodel extends Backend
{
    public function index()
    {
        $moban = $this->request->param('moban/d');
        if($moban==1){
            return $this->fetch('request');
        }elseif($moban==2){
            return $this->fetch('reason');
        }else{
            $this->error('参数错误');
        }
    }
}