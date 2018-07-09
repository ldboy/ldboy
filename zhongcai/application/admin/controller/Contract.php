<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/2/26
 * Time: 8:56
 */

//https://zhygz.wszx.cc/Wscontract-Wscontract_pdf0.html
namespace app\admin\controller;

use app\common\controller\Backend;
use wslibs\wscontract\WsContract;

class Contract extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function pdf0()
    {

        WsContract::submitToService($this->request->param('c_no'));

        if($this->request->param('refresh')==1){

            echo "1";
            exit;

        }
    }
    //http://192.168.0.7/zhongcai/admin.php/contract/gotoSign?c_no=555555555&uid=13&id_code=zhzc_13_zc_13
    public function gotoSign()
    {
 
        WsContract::signContractByCnoAndUid($this->request->param('c_no'),$this->request->param('uid'),$this->request->param('id_code'));
    }


//https://zhygz.wszx.cc/Wscontract-whenSign.html
    public function whenSign()
    {
        file_put_contents("pdf0.txt","notify:".$_GET['notify']."\n",FILE_APPEND);
        if($this->request->param('notify'))
        {

            WsContract::onSignContract($this->request->param('id'));
            exit;
        }

        if(strpos($_SERVER['HTTP_USER_AGENT'], 'iPhone') || strpos($_SERVER['HTTP_USER_AGENT'], 'iPad')){

//            echo "<script>alert('hello')</script>";
            echo "<script>apiready = function(){api.setScreenOrientation({orientation: 'portrait_up'});api.sendEvent({name: 'scsuccess'});api.closeWin();}</script>";





        }else{
            $url = "credCmd://?credCmd=1&_a=-1&_fname=_exit&_exit=1&_pre=1&_wsiphone=1&_window=-1&_type=function&function_name=&_nl=1";
            echo "<script>window.location.href='$url';</script>";
            echo "<script>window.opener=null;window.open('','_self');window.close();</script>";
        }

        exit;




//        $url = "credCmd://?credCmd=1&_a=-1&_fname=_exit&_exit=1&_pre=1&_wsiphone=1&_window=-1&_type=function&function_name=&_nl=1";
//        echo "<script>window.location.href='$url';</script>";
//        echo "<script>window.opener=null;window.open('','_self');window.close();</script>";
//        exit;


    }
}
