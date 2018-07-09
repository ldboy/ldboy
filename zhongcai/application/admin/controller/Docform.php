<?php
/**
 * Created by PhpStorm.
 * User: Lee
 * Date: 2018/5/2
 * Time: 9:15
 */
namespace app\admin\controller;

use app\common\controller\Backend;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;

use think\Db;
use think\Request;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use app\admin\controller\doccommon\DocCommon;
use app\admin\model\DocManager;



class Docform extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];

        return parent::_initialize();
    }


    public function index()
    {
        $dossier_id = $this->request->param('dossier_id/d');
        if(!$dossier_id){
            $this->error("缺少参数");
        }

        $dossierInfo = Db::name('dossier')->where('id',$dossier_id)->find();

        if(!$dossierInfo || $dossierInfo['status']-0==0){
            $this->error("信息不存在或卷宗已取消");
        }

        $list = Ddocs::getFilesByGroup($dossier_id,$dossierInfo['status']);

        foreach($list as $k=>$v){
            $list[$k]['editUrl'] = url("wsdoc/attr/index",['docid'=>$v['id']]);
            if($v['attr2_num']-0>0){
                $list[$k]['signUrl'] = url("",['cn'=>$v['doc_model_id']]);
            }

            if($v['create_type']==2){
                $list[$k]['uploadUrl'] = url("Evidence/upload",['ids'=>$v['id'],'attr_id'=>$v['doc_model_id']]);
            }

        }


        if($this->request->param('lee/d')==1){
            dump($list);
        }


        $this->assign('list',$list);

        return $this->fetch('docshow/list');
    }
}