<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/3
 * Time: 上午8:08
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;
use think\Db;
use wslibs\wszc\DocAttr;

class Show extends Backend
{
    public function index()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error();


        $info = Db::name("dr")->find($docid);

        $doc_mod_id = $info['doc_model_id'];
        $modeinfo = Db::name("dm")->find($doc_mod_id);
        if (!$modeinfo) $this->error();
        $fun = "show" . $modeinfo['type'] . "_" . $modeinfo['create_type'];
        return $this->{$fun}($info, $modeinfo);


    }

    private function show1_1($info, $modeinfo)
    {
        $this->useLayout(false);
        $content =  $this->display($modeinfo['view_content'], DocAttr::getFormAttr($info['id']));
        $this->assign("content",$content);
       return $this->fetch("show11");

    }

    private function show1_2($info, $modeinfo)
    {
        return "文档结果需要上传的图片";
    }

    private function show2_1($info, $modeinfo)
    {
        return "上传的图片";
    }

    private function show2_2($info, $modeinfo)
    {
        return "上传的图片";
    }

}