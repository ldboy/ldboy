<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/12
 * Time: ����1:37
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;
use EasyWeChat\Support\Log;
use wslibs\wszc\Constant;
use wslibs\wszc\Dossier;
use wslibs\wszc\LoginUser;

class Dlist extends Backend
{
    protected $noNeedLogin = ["*"];

    private  $dlist_mod = null;
    private  $uid = 0;
    private  $role = 0;
    public function _initialize()
    {
        if (LoginUser::isRole(Constant::Admin_Role_admin)) {

            $this->uid = 0;
            $this->role = Constant::Admin_Role_admin;
        } else if ( LoginUser::isRole(Constant::Admin_Role_zhongcaiwei)) {

            $this->uid = 0;
            $this->role = Constant::Admin_Role_zhongcaiwei;
        } elseif(LoginUser::isRole(Constant::Admin_Role_putongyonghu)){

            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_putongyonghu;
        }elseif(LoginUser::isRole(Constant::Admin_Role_zhongcaiyuan)){
            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_zhongcaiyuan;
        }else{
            $this->uid = LoginUser::getIdid();
            $this->role = Constant::Admin_Role_yinhang;
        }

        $dlist = new \wslibs\wszc\Dlist($this->uid);

        if ($this->role==Constant::Admin_Role_zhongcaiwei) {

            $dlist->setZhongCaiwei(Constant::Admin_Role_zhongcaiwei);
        }
        $this->dlist_mod = $dlist;

        parent::_initialize();
    }

    public function index()
    {
      
        $typeList = Dossier::getAllStatus($this->role);

        $keynum = $this->dlist_mod->getStatusCount();
        $this->assign("total", array_sum($keynum));
        $colors = array("blue", "red", "yellow");
        foreach ($typeList as $key => $value) {
            $typeList[$key] = array("key" => $key, "name" => $value, "num" => $keynum[$key], "bage_color" => $colors[array_rand($colors)]);
        }
        $this->assign("typeList", $typeList);
        return $this->fetch();
    }


    public function search()
    {

        $this->dlist();

        $this->fetch('index');
    }

    public function dlist()
    {

        $dlist = $this->dlist_mod ;
        $id= $this->request->param("id/d");
        $zno = $this->request->param("zno/d");
        $sq_phone = $this->request->param("sq_phone/d");
        $id_num = $this->request->param("id_num/d");
        if($id){
            if(is_numeric($id)){
                $dlist->addWhereAll('d.id',$id);
                $this->_AS['id'] = $id;
            }else{
                $this->error('id参数格式错误');
            }
        }


        if($zno){
            if(is_numeric($zno)){
                $dlist->addWhereAll('d.zno',$zno);
                $this->_AS['zno'] = $zno;
            }else{
                $this->error('请输入第 号中间的数字哦');
            }

        }
        if($sq_phone){
            if(preg_match('/^1[3456789]\d{9}$/',$sq_phone)){
                $dlist->addWhereAll('du.phone',$sq_phone);
                $this->_AS['sq_phone'] = $sq_phone;
            }else{
                $this->error('手机号格式错误');
            }
        }
        if($id_num){
            $preg_card='/^\d{15}$)|(^\d{17}([0-9]|X)$/isu';
            if(preg_match($preg_card,$id_num)){
                $dlist->addWhereAll('du.id_num',$id_num);
                $this->_AS['id_num'] = $id_num;
            }else{
                $this->error('身份证号格式错误');
            }
        }
        $this->assign($this->_AS);

        $status = $this->request->param("type/d");

        if ($status !== null){
            $dlist->setStatus($status);
        }


        list($total, $list) = $dlist->getList();

        $this->assign($this->_AS);

        return json(['rows' => $list, 'total' => $total]);
    }
}