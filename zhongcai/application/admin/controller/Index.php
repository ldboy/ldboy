<?php

namespace app\admin\controller;

use app\admin\model\AdminLog;
use app\common\controller\Backend;
use think\Config;
use think\Db;
use think\Hook;
use think\Session;
use think\Validate;
use wslibs\wszc\Constant;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\LoginUser;
use wslibs\wszc\mes\Inform;

/**
 * 后台首页
 * @internal
 */
class Index extends Backend
{

    protected $noNeedLogin = ['login'];
    protected $noNeedRight = ['index', 'logout'];
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 后台首页
     */
    public function index()
    {
        //左侧菜单
       
        $tx_num = (new Inform())->addStatus(0)->getCount();


        $menulist = $this->auth->getSidebar([
            'dashboard' => 'hot',
            'inform/index' => $tx_num?$tx_num:'无',
            'addon'     => ['new', 'red', 'badge'],
            'auth/rule' => __('Menu'),
            'general'   => ['new', 'purple'],
                ], $this->view->site['fixedpage']);

        if($_GET['zhz']){
            dump($menulist);
            exit;
        }
        $action = $this->request->request('action');
        if ($this->request->isPost())
        {
            if ($action == 'refreshmenu')
            {
                $this->success('', null, ['menulist' => $menulist]);
            }
        }

        $admin = Session::get('admin');

        $admin_['name'] = LoginUser::getUserName();

        $site_ = [];

        if($admin['id']==Constant::Admin_Role_zhongcaiyuan){

            $site_['name'] = '仲裁管理系统';
            $admin_['nickname'] = LoginUser::getRoleRoleName() ? LoginUser::getRoleRoleName() : '仲裁员';

        }elseif($admin['id']==Constant::Admin_Role_putongyonghu){
            $site_['name'] = '仲裁管理系统';
            $admin_['nickname'] = LoginUser::getRoleRoleName() ? LoginUser::getRoleRoleName() : '当事人';
        }elseif($admin['id']==Constant::Admin_Role_admin){
            $site_['name'] = '文始征信仲裁后台';
            $admin_['nickname'] = LoginUser::getRoleRoleName() ? LoginUser::getRoleRoleName() : 'admin';

        }elseif($admin['id']==Constant::Admin_Role_yinhang){
            $site_  = Db::name('third_client')->where("id",LoginUser::getRoleThId())->find();
            $admin_['nickname'] = LoginUser::getRoleRoleName() ? LoginUser::getRoleRoleName() : '银行';
        }elseif($admin['id']==Constant::Admin_Role_zhongcaiwei){

            $admin_['nickname'] = LoginUser::getRoleRoleName() ;
            $site_  = Db::name('jigou')->where("id",LoginUser::getRoleThId())->find();
        }

        $this->assign('admin_', $admin_);
        $this->assign('site_', $site_);
        $this->view->assign('menulist', $menulist);
        $this->view->assign('title', __('Home'));
        if($_GET['zhz']){
            dump($site_);
            dump($admin_);
            dump(__('Home'));
            exit;
        }

        return $this->view->fetch();
    }

    /**
     * 管理员登录
     */
    public function login()
    {
        $url = $this->request->get('url', 'index/index');
        if ($this->auth->isLogin())
        {
            $this->success(__("You've logged in, do not login again"), $url);
        }
        if ($this->request->isPost())
        {
            $username = $this->request->post('username');
            $password = $this->request->post('password');
            $idcard = $this->request->post('idcard');
            $idid = IDcard::getIdId($idcard);
            Session::set('zc_admin_idid',$idid);
            $keeplogin = $this->request->post('keeplogin');
            $token = $this->request->post('__token__');
            $rule = [
                'username'  => 'require|length:3,30',
                'password'  => 'require|length:3,30',
                '__token__' => 'token',
            ];
            $data = [
                'username'  => $username,
                'password'  => $password,
                '__token__' => $token,
            ];
            if (Config::get('fastadmin.login_captcha'))
            {
                $rule['captcha'] = 'require|captcha';
                $data['captcha'] = $this->request->post('captcha');
            }
            $validate = new Validate($rule, [], ['username' => __('Username'), 'password' => __('Password'), 'captcha' => __('Captcha')]);
            $result = $validate->check($data);
            if (!$result)
            {
                $this->error($validate->getError(), $url, ['token' => $this->request->token()]);
            }
            AdminLog::setTitle(__('Login'));
            $result = $this->auth->login($username, $password, $keeplogin ? 86400 : 0);
            if ($result === true)
            {
               
                $this->auth->loginByIdid($this->auth->idid, Constant::Admin_Role_admin);
              $this->success(__('Login successful'), $url, ['url' => $url, 'id' => $this->auth->id, 'username' => $username, 'avatar' => $this->auth->avatar]);
            }
            else
            {
                $msg = $this->auth->getError();
                $msg = $msg ? $msg : __('Username or password is incorrect');
                $this->error($msg, $url, ['token' => $this->request->token()]);
            }
        }

        // 根据客户端的cookie,判断是否可以自动登录
        if ($this->auth->autologin())
        {
            $this->redirect($url);
        }
        $background = Config::get('fastadmin.login_background');
        $background = stripos($background, 'http')===0 ? $background : config('site.cdnurl') . $background;
        $this->view->assign('background', $background);
        $this->view->assign('title', __('Login'));
        Hook::listen("login_init", $this->request);
        return $this->view->fetch();
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        $this->auth->logout();
        $url = Session::get('referer');
        $url = $url ? $url : $this->request->url();

//        $this->success(__('Logout successful'), 'index/login');
        $this->success(__('Logout successful'), 'http://zc.wszx.cc/?url='. urlencode($url));
    }

}
