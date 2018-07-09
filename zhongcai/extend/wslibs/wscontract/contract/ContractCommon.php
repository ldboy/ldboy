<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/2/25
 * Time: 上午10:14
 */
namespace wslibs\wscontract\contract;

abstract class ContractCommon
{
    private $c_no = "";
    public $is_create = false;
    private $c_id = 0;
    private $c_info = array();

    public function setNo($no, $is_exist = false)
    {
        $this->c_no = $no;
        $this->is_create = $is_exist;
        return $this;
    }

    public function getNo()
    {
        return $this->c_no;
    }

    public function setInfo($info)
    {
        $this->c_no = $info['c_no'];
        $this->is_create = true;
        $this->c_info = $info;
        $this->c_id = $info['id'];
        return $this;
    }

    public function getId()
    {
        return $this->c_id;
    }

    public function info($key)
    {
        return $this->c_info[$key];
    }

    abstract public function getName();//获取名称

    abstract public function getSignService();//签字方式

    abstract public function isUserAutoSign($signer); //1手动 2 自动

    abstract public function getUserSignType($signer);//这个需要具体到某一个平台







    
    abstract public function getCreateType();


    abstract public function isCreatedByPdf();


    abstract public function isCreatedByTemplate();

    abstract public function onCreatedContract();

    abstract public function getPdfWebUrl();

    abstract public function getTemplateValues();

    abstract public function getTemplateId();
    abstract public function onSignFinish($pdfbase64,$imagesurl);
    abstract public function onOneSignFinish($ht_user_info);


}