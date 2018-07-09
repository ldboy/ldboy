<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/1
 * Time: 上午9:10
 */

namespace app\admin\controller;


use app\common\controller\Backend;

use think\Db;
use wslibs\wsform\WsForm;
use wslibs\wszc\publicnumber\Config;
use wslibs\wszc\publicnumber\MsgCrypt;
use wslibs\wszc\publicnumber\PublicNumber;
use wslibs\wszc\publicnumber\ReplyMsg;
use wslibs\wszc\publicnumber\Token;

class Wxcallback  extends Backend
{

    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
    }
    public function index()
    {
        $xml = file_get_contents("php://input");
        file_put_contents('test.txt',$xml);
        file_put_contents('test1.txt',Config::arrayToXml($_GET));

        if($_GET['signature'] && !$xml){
            echo  (new Token())->valid($_GET['signature'],$_GET['timestamp'],$_GET['nonce'],$_GET['echostr']) ;
            exit;
        }


        if($xml){
            $this->xmlDeal($xml,$_GET);
            exit;
        }

        if($_GET['code'] && $_GET['state']){

            $code = $_GET['code'];
            $state = $_GET['state'];
//
//            if($state>99999){
//                 算了  写个新的吧
//            }

            if(!$code){
                $this->error('code参数错误');
            }

            Config::getDbAccessToken(true,$code,$state);

            PublicNumber::get_access_token($code,$state);

            $openID = Config::getOpenidByState($state);

            return $this->addCallBack($openID);
        }


    }

    public function xmlDeal($xml,$get)
    {

        //安全模式
//        (new MsgCrypt())->decryptMsg($get["msg_signature"],$get["timestamp"],$get["nonce"],$xml,$msg);
//
//        $msgReply = ReplyMsg::replyMsg($msg);
//
//        if($msgReply=='success'){
//            echo $msgReply;
//            exit;
//        }
//
//        (new MsgCrypt())->encryptMsg($msgReply,$get["timestamp"],$get["nonce"],$msg);
//
//        echo $msg;
//
//        exit;

        //明文模式
        $res = ReplyMsg::replyMsg($xml);
        if($res=='success'){
            echo $res;
            exit;
        }
        echo Config::arrayToXml($res);
    }


    public function addCallBack($openID)
    {
        $this->assign('openid',$openID);
//        if(Db::name("idcards")->where("openid='$openID'")->find()){
//            $this->success('此微信号已被绑定喽!');
//        }
        return $this->fetch('wxcallback/add');
    }

    public function bind()
    {
        $phone = $_POST['phone'];
        $pass = $_POST['pass'];
        $openid = $_POST['openid'];

        if(Db::name("idcards")->where("openid='$openid'")->find()){
            $this->success('此微信号已被绑定喽!');
        }

        if(!$phone || !$pass){
            $this->error('用户名或密码不能为空');
        }

        $url = 'http://zc.wszx.cc/dossier-getcode?phone='.$phone.'&pass='.$pass;
        $res = file_get_contents($url);

        $result = \GuzzleHttp\json_decode($res,true);

        if($result['code']==100){
            if($res = PublicNumber::bindOpenid($openid,$result['result']['id_card'])>0){
                $this->success('绑定成功');
            }else{
                $res==-1 ? $this->error('账号不存在') : $this->error('此账号已被绑定');
            }

        }else{
            $this->error($result['msg']);
        }


    }

    public function info()
    {
        $openid = $this->request->param('openid/s');
        header("Location:".PublicNumber::getMyCode());
        exit;
    }

    public function getCode()
    {
        return PublicNumber::getCode();
    }
    public function getMyCode()
    {
        return PublicNumber::getMyCode();
    }

    public function createmenu()
    {
        dump(PublicNumber::getButton());

        dump(PublicNumber::createMenu());
    }




}