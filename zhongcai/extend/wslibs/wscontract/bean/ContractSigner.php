<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/2/25
 * Time: 下午3:17
 */
namespace wslibs\wscontract\bean;

use think\Db;

class ContractSigner
{
    public $info = 0;
    public $auto = 0;

    public function __construct($id, $auto = -1)
    {
        $info = Db::name("ws_ht_user")->where("id", $id)->find();
        $this->info = $info;
        if ($auto != -1)  $this->info['is_auto'] = $auto;
    }

    public function isAuto()
    {
        return $this->info['is_auto'];
    }

    public function getQsType()
    {
        return $this->info['qs_type'];
    }

    public function getHtInfo()
    {
        $htInfo = Db::name("ws_ht")->where("id", $this->info['c_id'])->find();

        return $htInfo;
    }


    public function getSigner()
    {
        return new Signer($this->info['uid'], $this->info['u_type'], $this->info['id_code']);
    }

    public function getIs_Auto_value()
    {
        return $this->info['is_auto'];
    }
}