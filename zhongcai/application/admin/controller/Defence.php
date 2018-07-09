<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018-05-05
 * Time: 8:53
 */

namespace app\admin\controller;
use app\common\controller\Backend;
use defence\DefenceExpand;
use defence\DefenceExt;
use dossier\DossierDoc;
use think\Session;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;
use wslibs\wszc\Dossier;

class Defence extends Backend
{
    private $table_url = 'zhongcai/index.php/admin/defence/showhtml?type=1';
    private $word_url = 'zhongcai/index.php/admin/defence/showhtml?type=3';

    public function index(){
        $dossier_id = input("dossier_id","","intval");
        $sqr = Dossier::getDangShiRen($dossier_id,1);
        $bsqr = Dossier::getDangShiRen($dossier_id,2);
        $this->assign('bdbr',$sqr);//被答辩人
        $this->assign('dbr',$bsqr);//答辩人
        $this->assign('date',date('Y年m月d日'));
        $this->assign('dossier_id',$dossier_id);
        return $this->fetch('defence/word/dabianshu');
    }
    public function add(){
        $dossier_id = input("dossier_id","","intval");
        if(!$dossier_id){
            $this->error('参数错误');
        }
        $shixiang = input('shixiang','','trim');
        $reason = input('reason','','trim');
        if(!$shixiang||!$reason){
            $this->error('参数错误');
        }
        // Session::get('admin_id')
        $res = DefenceExpand::addDefence($dossier_id,111,['shixiang'=>$shixiang,'reason'=>$reason]);
        if(!$res){
            $this->error('失败');
        }
        $this->success('成功');
    }
    public function indexback(){
        $dossier_id = input("dossier_id","","intval");
        $huanjie = $this->request->param('huanjie/d');

        if(!$dossier_id || !$huanjie){
            //$this->error("缺少参数");
        }

        $arr = DossierDoc::getConfig(3);

        foreach ($arr as $k => $v){
            if($v['weburl']){
                $arr[$k]['table_url'] = DossierDoc::$web_site_root.$v['weburl'];
            }else{
                $arr[$k]['table_url'] = DossierDoc::$web_site_root.$this->table_url."&dossier_id=".$dossier_id."&view=".$v['view']."&doc_type=".$v['doc_type'];
                $arr[$k]['word_url'] = DossierDoc::$web_site_root.$this->word_url."&dossier_id=".$dossier_id."&view=".$v['view']."&doc_type=".$v['doc_type'];
            }
        
            if(!DefenceExt::getDefence($dossier_id,$v['doc_type'])){
                DefenceExt::addDefence($dossier_id,1,session("zc_admin_uid"),$v['doc_type'],$v['name'],$arr[$k]['word_url']);
            }
        }

        $this->useLayout();

        if(input('sy')==1){
            echo 1111;
            dump($arr);
        }

        $this->assign("dossier_id",$dossier_id);
        $this->assign("doc_list",$arr);
        return $this->fetch("form");
    }


    public function showhtml(){
        $type = input("type","","intval");
        $view = input("view","","trim");
        $doc_type = input("doc_type","","intval");
        $dossier_id = input("dossier_id","","intval");

        $info = DefenceExt::getValue($dossier_id,$doc_type);

        if(input("sy")==1){
            dump($info);
        }

        if($info){
            $this->assign("row",$info);
        }

        $file = "";

        switch ($type) {
            case 1: $file = "/table/";break;
            case 3: $file = "/word/";break;
        }

        $this->assign("dossier_id",$dossier_id);
        $this->assign("doc_type",$doc_type);
        return $this->fetch("defence".$file.$view);
    }


    public function submitTable(){
        $data = $this->request->post();
        $dossier_id = $data['dossier_id'];
        $doc_type = $data['doc_type'];

        if(!$dossier_id || !$doc_type){
            $this->error("参数错误");
        }

        unset($data['dossier_id']);
        unset($data['doc_type']);

        $re = DefenceExt::submitTable($dossier_id,$doc_type,$data);

        if($re){
            $this->success("成功");
        }else{
            $this->error("失败或未修改");
        }
    }
}