<?php
namespace dmconfig;

use think\Db;
class dmConfig
{
    private $status1 = [1,11,12,3];
    private $status2 = [];
    private $status3 = [];
    private $status4 = [];
    private $status5 = [];


    public function getDmConfig($status = 0){
        $map = [];
        switch ((int)$status) {
            case 1 : $map['id'] = ["in",$this->status1]; break;
            case 2 : $map['id'] = ["in",$this->status2]; break;
            case 3 : $map['id'] = ["in",$this->status3]; break;
            case 4 : $map['id'] = ["in",$this->status4]; break;
            case 5 : $map['id'] = ["in",$this->status5]; break;
        }

        $dmList = Db::name("dm")->field("id,model_name")->where($map)->select();

        return $dmList;
    }
}