<?php

namespace app\admin\model;

use think\Model;

class Dma extends Model
{
    // 表名
    protected $name = 'dma';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
//        'type_text',
//        'sub_type_text'
    ];



    public function getTypeList()
    {
        return ['4' => __('Type 4')];
    }

    public function getSubTypeList()
    {
        return ['4' => __('Sub_type 4')];
    }


    public function getTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['type'];
        $list = $this->getTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }


    public function getSubTypeTextAttr($value, $data)
    {
        $value = $value ? $value : $data['sub_type'];
        $list = $this->getSubTypeList();
        return isset($list[$value]) ? $list[$value] : '';
    }




}
