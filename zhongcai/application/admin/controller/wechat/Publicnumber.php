<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/21
 * Time: 下午2:38
 */


namespace app\admin\controller\wechat;

use app\common\controller\Backend;
use think\Session;
use wslibs\wszc\Constant;
use wslibs\wszc\publicnumber\BaseVar;
use wslibs\wszc\publicnumber\Config;
use wslibs\wszc\publicnumber\mylist\Mydossier;
use wslibs\wszc\publicnumber\mylist\Myinform;


class Publicnumber extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
    }

    public function getMyCode()
    {


        return \wslibs\wszc\publicnumber\PublicNumber::getMyCode();
    }

    public function usercenter()
    {
        if($_GET['code'] && $_GET['state']==BaseVar::WEIYI_CODE){

            $code = $_GET['code'];
            $state = $_GET['state'];

            if(!$code){
                $this->error('code参数错误');
            }

            list($access_token,$openID) = Config::getAccessTokenAndOpenid($code,$state);

            if($openID){
                Session::set('zhz_'.md5($code),$openID);
            }else{
                $openID = Session::get('zhz_'.md5($code));
                if(!$openID){
                    $this->error('页面超时,请重新登录');
                }
            }

            if(!$idcards = \wslibs\wszc\publicnumber\PublicNumber::checkOpenidExits($openID)){
                $this->error('请先绑定账号哦!');
            }

            $user_info = \wslibs\wszc\publicnumber\PublicNumber::addUserInfo($access_token,$openID,$idcards['id']);

            if(!$user_info){
                $this->error('user info 添加失败' );
            }

            if($user_info['errcode']){
                $this->error($user_info['errmsg']);
            }
            if(!$user_info){
                $this->error('get_user_info error');
            }

            $user_info['role_name'] = Constant::getRoleName($idcards['role']);
            $user_info['real_name'] = $idcards['real_name'];
            $user_info['id_card'] = $idcards['id_card'];

            $this->assign('info',$user_info);
            $this->assign('anjiancount',Mydossier::getMyDossierCount($idcards['id']));
            $this->assign('xiaoxicount',Myinform::getMyInformCount($idcards['id']));

            return $this->fetch('wxcallback/usercenter/index');
        }

        $this->error('小伙子,你这样横冲直撞的不好吧,有点太生猛了,送你到百度','http://www.baidu.com');
        exit;
    }

    public function user_info()
    {
        $openID = $this->request->param('openid/s');

        if(!$openID){
            header("Location:".\wslibs\wszc\publicnumber\PublicNumber::getMyCode());
            exit;
        }

        if(!$idcards = \wslibs\wszc\publicnumber\PublicNumber::checkOpenidExits($openID)){
            $this->error('请先绑定账号哦!');
        }

        $user_info = \wslibs\wszc\publicnumber\PublicNumber::getAccessUserInfo($openID);

        $user_info['role_name'] = Constant::getRoleName($idcards['role']);
        $user_info['real_name'] = $idcards['real_name'];
        $user_info['id_card'] = $idcards['id_card'];
        $user_info['sex'] = $user_info['sex'] ? (1 ?  '男' : '女' ): '未知';

        $this->assign('info',$user_info);

        return $this->fetch('wxcallback/usercenter/user_info');
    }

    public function site()
    {
        return $this->fetch('wxcallback/usercenter/site');
    }

    public function zczhinan()
    {
        return $this->fetch('wxcallback/usercenter/zczhinan');
    }
}