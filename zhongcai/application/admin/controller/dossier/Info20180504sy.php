<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/3
 * Time: 上午11:32
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;

class Info extends Backend
{

    public function index()
    {
        $d_id = $this->request->param("id/d");
        if (!$d_id  ) {
            $this->error("参数错误");
        }

        $this->assign("d_id",$d_id);
       // var_dump(Ddocs::getFilesByGroup($d_id, 1));

        $this->assign("sqhtml",  $this->getFilesHtml($d_id,1,0,"申请文件","box-warning"));

         $this->assign("shouliwenjian",  $this->getFilesHtml($d_id,2,0,"受理文件","box-success"));
        $this->assign("zutingwenjian",  $this->getFilesHtml($d_id,4,0,"组庭文件","box-success"));
//        var_dump(Dossier::getDangShiRen($d_id));
        $this->assign("dangshiren",Dossier::getDangShiRen($d_id,Constant::getDangshirenRoles()));
        $this->assign("zhongcaiyuan",Dossier::getDangShiRen($d_id,Constant::D_Role_ZhongCaiYuan));
        $this->useLayout($this->layout);
       return $this->fetch();

    }

    private function getFilesHtml($d_id,$gid,$extid,$title,$class="")
    {
        $this->useLayout(false);
        return  $this->fetch("files",array("sqfiles"=> Ddocs::getFilesByGroup($d_id, $gid,$extid),"class"=>$class,"title"=>$title));
    }
}