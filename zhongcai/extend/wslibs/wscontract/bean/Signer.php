<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/2/25
 * Time: 上午10:36
 *  * id
 * uid
 * qs_pingtai 平台id
 * u_type  0企业  1个人
 */

namespace wslibs\wscontract\bean;

use think\Db;
use wslibs\wscontract\ContractTools;
use wslibs\wszc\Constant;

class Signer
{
    public $uid = 0;
    public $u_type = 1;
    const U_TYPE_GEREN = 1;
    const U_TYPE_COMPANY = 0;
    public $_com_info = array();
    public $id_code = "0";
    public $role = 0;
    public $role_faren = null;
    public $auto_after = 0;
    public $backurl = "";

    public $next =array();

    public function __construct($uid, $u_type = 1, $id_code = "0")
    {
        $this->uid = $uid;
        $this->u_type = $u_type;
        $this->id_code = $id_code;
    }

    public function setRole($role, Signer $faren)
    {
        $this->role = $role;
        $this->role_faren = $faren;
        return $this;
    }

    public function setIdCode($id_code)
    {
        $this->id_code = $id_code;
        return $this;
    }


    public function getUCode()
    {

        return ContractTools::getQianZiUcode($this->uid, $this->isCompany(), $this->id_code);
    }


    public function initInPingtai($qs_pingtai)
    {


        if (!Db::name("ws_user")->where("qs_pingtai", $qs_pingtai)->where("uid", $this->uid)->where("ucode", $this->getUCode())->find()) {


            $service = ContractTools::getServiceClass($qs_pingtai);

            if ($out = $service->initUser($this)) {

                $data = [];


                $data["uid"] = $this->uid;
                $data["qs_pingtai"] = $qs_pingtai;
                $data["u_type"] = $this->getType();
                $data['ucode'] = $this->getUCode();

                return Db::name("ws_user")->insertGetId($data);
            } else {
                return false;
            }


        } else {
            return true;
        }
    }


    public function getUserInfo()
    {

        $idCardInfo = Db::name('idcards')->where("id", $this->uid)->find();
        $idCardInfo['phone'] = "13888888888";
        // $idCardInfo =    array("real_name" => "asfd", "phone" => "asdf","id_card"=>"asdfsdf")
        return $idCardInfo;

    }

    public function getCompayInfo()
    {


        if ($this->role == Constant::D_Role_ZhongCaiWei_JiGou) {
            if (!$this->_com_info) {
                $tmp = Db::name("jigou")->where("idid", $this->uid)->find();

                $this->_com_info = array("com_name" => $tmp['name'], "credit_code" => $tmp['addtime'], "address" => $tmp['address']);
                $this->_com_info['f_info'] = $this->role_faren->getUserInfo();
                $this->_com_info["f_uid"] = $this->role_faren->uid;
            }
        } else

            if (!$this->_com_info) {
                $this->_com_info = Db::name("dossier_users")->where("id", $this->uid)->find();
                $this->_com_info['f_info'] = $idCardInfo = Db::name('idcards')->where("id ", $this->_com_info['idid'])->find();
            }


//        $this->_com_info = array("com_name" => "zhangsna", "credit_code" => "asdf", "address" => "asfasdf"
//        , "f_info" => array("real_name" => "asfd", "phone" => "asdf","f_uid")
//        );

        return $this->_com_info;
    }

    public function getComFuid()
    {

        if ($this->isPersonal()) return 0;
        return $this->getCompayInfo()['f_uid'];
    }

    public function getType()
    {
        return $this->u_type;
    }

    public function isPersonal()
    {
        return $this->getType() == 1;
    }

    public function isCompany()
    {
        return $this->getType() == 0;
    }

    public function autoAfter($set = false)
    {
        if ($set) {
            $this->auto_after = $set;
            return $this;
        }
        return $this->auto_after;
    }

    public function nextSigners()
    {
        return $this->next;
    }

    public function addNext(Signer $signer)
    {
        $this->next[]= $signer;
        return $this;
    }

    public function backurl($set = false)
    {
        if ($set) {
            $this->backurl = $set;
            return $this;
        }
        return $this->backurl;
    }

}