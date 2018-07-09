<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/12
 * Time: 下午8:56
 */

namespace wslibs\wszc\datashow;


use think\Db;
use wslibs\wszc\Drole;

class InfoShow
{
    private $did = 0;

    private $info = [];
    private $roles = [];

    private $data = [];

    public function isNeedDanger($is_need=false)
    {
        if($is_need)
            $this->info['danger'] = true;

        return $this;
    }

    public function isNeedDaBian($is_need=false)
    {
        if($is_need)
            $this->info['dabian'] = true;

        return $this;
    }

    public function isNeedZhiZheng($is_need=false)
    {
        if($is_need){
            $this->info['zhizheng'] = true;
        }

        $this->info['zhizheng'] = false;
        return $this;
    }

    public function isNeedZCY($is_need=false)
    {
        if($is_need){
            $this->info['zcy'] = true;
        }

        $this->info['zcy'] = false;
        return $this;
    }

    public function isNeedDLog($is_need=false)
    {
        if($is_need){
            $this->info['dialog'] = true;
        }

        $this->info['dialog'] = false;
        return $this;
    }

    public function isNeedZuTing($is_need=false)
    {
        if($is_need){
            $this->info['zuting'] = true;
        }

        $this->info['zuting'] = false;
        return $this;
    }

    public function isNeedOperation($is_need=false)
    {
        if($is_need){
            $this->info['operation'] = true;
        }
        $this->info['operation'] = false;
        return $this;
    }



    public function getData($did)
    {
        $infoData = new InfoData();

        $this->did = $did;

        $this->roles = Drole::getRoleByDId($did);

        foreach($this->info as $key=>$value){

            $this->data[$key] = $infoData->{$key}($this->roles,$value);

        }

        return $this->dealData($this->data);
    }

    public function dealData($info)
    {
        return $info;
    }


}