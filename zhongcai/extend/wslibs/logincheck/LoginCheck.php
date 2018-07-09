<?php
namespace wslibs\logincheck;
use app\admin\library\Auth;
use think\Db;
use wslibs\wszc\idcard\IDcard;
use wslibs\wszc\LoginUser;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/15
 * Time: 上午10:46
 */
class LoginCheck
{
    private static $key = "zhangsanmaileyigexihongshi,quebuchi,kesile";

    public static function fromYh($data, &$emsg)
    {


        $_uid = (int)$data['uid'];
        $_ucode = $data['ucode'];//13042319861122193X

        $_step = (int)$data['step'];//步长
        $_stepnum = (int)$data['snum'];//步长取值数量     如果_step=4，_snum=3 则 13042319861122193X 应为 ****130****423****198****611***221****93X   ****为任意数字
        $_len = (int)$data['len'];
        $_role = (int)$data['role']; //    const Admin_Role_yinhang = 13  const Admin_Role_zhongcaiyuan = 12  Admin_Role_putongyonghu = 11; Admin_Role_zhongcaiwei = 10;Admin_Role_admin = 1;

        if ($data['_code'] != md5($_uid . $_ucode . $_step . $_stepnum . $_len . $_role . self::$key)) {

            $emsg = "这么高的墙头都想翻，摔死了咋办！";
            return false;
        }

        if (($idcard = self::getIdCard($_ucode, $_step, $_stepnum, $_len)) && ($idid = IDcard::getIdId($idcard))) {
            $result = Auth::instance()->loginByIdid($idid, $_role);

            if ($result === true) {
                return $result;
            } else {
                $emsg = "没有权限";
                return false;
            }
        } else {
            $emsg = $idcard." 您还没有没有注册哦";
            return false;
        }

    }

    private static function getIdCard($_ucode, $_step, $_stepnum, $_len)
    {
        $idcard = "";

        for ($i = $_step; $i < 100; $i = $i + $_step + $_stepnum) {

            $idcard = $idcard . substr($_ucode, $i, min($_len - strlen($idcard), $_stepnum));

            if (strlen($idcard) == $_len) return $idcard;
        }
        return false;
    }


    public function mkcode()
    {
        $idcard = "13042319861122193X";
        $_step = 3;
        $_stepnum = 3;
        $_len = strlen($idcard);


        $out = "";
        $uselen = 0;

        for ($i = 0; $i < 100; $i = $i + 1) {
            $out = $out . mt_rand(pow(10, $_step - 1), pow(10, $_step) - 1) . substr($idcard, $_stepnum * $i, $tlen = min($_len - ($_stepnum * $i), $_stepnum));

            $uselen = $uselen + $tlen;
            if ($uselen == $_len) return $out;
        }

        return "";
    }


    public static function reach($uid,$url='',$role=13)
    {


        $authcode= self::getAuthCode($uid,$role);

        if(!$authcode)   return false;

        $link =   'http://zcw.wszx.cc/admin/login/fromYh?url='.urlencode($url)."&".$authcode;

        return $link;
    }

    public static function getAuthCode($uid,$role=13)
    {
        if(!$uid)   return false;

        $idcards = Db::name('idcards')->where("id=".LoginUser::getIdid())->find();

        if(!$idcards['id_card'] ||!$idcards['real_name']) return false;

        $_step = rand(1,5);

        $_stepnum = rand(1,5);

        $idcard = self::whenGoToMkCode($idcards['id_card'],$_step,$_stepnum);


        $_len = strlen($idcards['id_card']);

        $_code  = md5($uid . $idcard  . $_step . $_stepnum . $_len . $role . self::$key);

        $link =   '_code='.$_code.'&uid='.$uid.'&step='.$_step.'&snum='.$_stepnum.'&len='.$_len.'&role='.$role.'&ucode='.$idcard;

        return $link;
    }

    public static function whenGoToMkCode($idcard,$_step,$_stepnum)
    {

        $_len = strlen($idcard);

        $out = "";
        $uselen = 0;

        for ($i = 0; $i < 100; $i = $i + 1) {
            $out = $out . mt_rand(pow(10, $_step - 1), pow(10, $_step) - 1) . substr($idcard,$_stepnum * $i, $tlen = min($_len -  ($_stepnum * $i), $_stepnum));

            $uselen = $uselen+$tlen;
            if ( $uselen== $_len) return $out;
        }

        return "";
    }


    public function decode()
    {

        echo $this->getIdCard("58513082542343919880361125722183293X", 3, 3, 18);
        exit;
    }
}