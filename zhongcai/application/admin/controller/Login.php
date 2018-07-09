<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/11
 * Time: 下午2:19
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use wslibs\logincheck\LoginCheck;
use wslibs\wszc\Constant;
use wslibs\wszc\idcard\IDcard;

class Login extends Backend
{
    protected $noNeedLogin = ["fromYh", "testlogin", "mkcode", "decode"];


    public function fromYh()
    {
        $data = $this->request->param();

        if (LoginCheck::fromYh($data, $msg)) {

            $_url = urldecode($data['url']);


            if ($_url)

                $this->redirect($_url);
            else {
                $this->redirect(url("index/index"));

            }
        } else {
            $this->error($msg);
        }

    }


}