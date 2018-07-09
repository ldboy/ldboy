<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/10
 * Time: 下午7:37
 */

namespace app\admin\controller;


use app\common\controller\Backend;
use think\Cookie;
use think\Session;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\notice\Notice;

class Userlogin extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        parent::_initialize();
    }

    public function index()
    {
        $code = $_GET['code'];

        if(!$code){
            $this->error('code参数错误');
        }

        list($login_code,$user_info) = self::getCodeQuanXian($code,false);

        if(!$login_code){
            $this->assign('is_invalid',0);
            $user_info['phone_'] = '<span style="color: red">此处登录需要权限,没有权限无法登录</span>';
        }else{
            $this->assign('is_invalid',1);
            $user_info['phone_'] = '请使用此手机号'.substr_replace($user_info['phone'], '****', 3, 4).'发送验证码登录';
        }

        $this->assign('user_info',$user_info);
        $this->assign('code',$code);

        return $this->fetch();
    }

    public function code()
    {
        self::getCodeQuanXian($_POST['code']);
        //手机号检测  idid检测是不是你同一手机号
        echo ['code'=>1];
        exit;
    }
    public function checkCode($code)
    {
        //验证 验证码
    }
    public function login()
    {
        $code = $_POST['code'];
        self::getCodeQuanXian($_POST['code']);

        $id_card = $this->request->param('id_card/d');
        $phone = $this->request->param('phone/d');
        $yzm = $this->request->param('yzm/d');

        if(!$id_card || !$phone || !$yzm){
            $this->error('参数不能为空');
        }

        $preg_card='/^(\d{15}$|^\d{18}$|^\d{17}(\d|X|x))$/';

        if(!preg_match($preg_card,$id_card)){
            $this->error('身份证号格式错误');
        }

        $preg_phone='/^1[34578]\d{9}$/ims';

        if(!preg_match($preg_phone,$phone)){
            $this->error('手机号格式错误');
        }

        list($login_code,$user_info) = \wslibs\wszc\userlogin\UserLogin::getInfoByCode($code);

        $this->checkCode($yzm);

        if($id_card!=$user_info['id_num']){

            $this->error('身份证号匹配错误');
        }
        if($phone!=$user_info['phone']){

            $this->error('手机号匹配错误');
        }
        $url = $login_code['url'] ;

        $idid = IDcard::getIdId($id_card);
        Session::set('zc_admin_idid',$idid);

        $this->success('登陆成功',$url);
    }


    public function getCodeQuanXian($code,$is = true)
    {
        list($login_code,$user_info) = \wslibs\wszc\userlogin\UserLogin::getInfoByCode($code);

        if($is){
            if(!$login_code){
                $this->error('code权限错误或已失效');
            }
        }

        return [$login_code,$user_info];
    }

    /**
     * 注销登录
     */
    public function logout()
    {
        Session::delete("zc_admin_idid");
        Cookie::clear();
        $this->success(__('Logout successful'), 'Userlogin/index');
    }






    public function test()
    {

//        $res = Notice::changeLink('http://192.168.0.7/zhongcai/index.php/admin/userlogin/index?code=5ed00ed8a7023620073dad55605ffaec');
//        dump($res);
        $res =  Notice::addNotice(121);
//        $res1 =  Notice::sendSms('15201634344','您提交的您与2018年05月09日被申请人因新仲裁提交到仲裁委,查看详情请点击链接http://192.168.0.7/zhongcai/index.php/admin/userlogin/index?code=ac977a9d3cf76258f27640727725c5a6店铺申请已通过');
        dump($res);
//        dump($res1);
    }
}