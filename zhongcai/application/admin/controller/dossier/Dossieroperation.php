<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/7
 * Time: 下午4:15
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;
use defence\DefenceExt;
use dossier\DossierDoc;
use userinfo\DossierUser;
use think\Db;
use wslibs\wszc\Dossier;

class Dossieroperation extends Backend
{
    public function index()
    {
        $dossier_id = $this->request->param("dossier_id/d");

        if (!$dossier_id) $this->error('dossier参数错误');

        $list = DossierDoc::getConfig(2,10);

        $this->assign('list',$list);

        $this->assign('dossier_id',$dossier_id);

        return $this->fetch('dossier/dossieroperation/index');
    }

    public function accept()
    {
        $dossier_id = $this->request->param("dossier_id/d");

        $doc_type = 10;
        if (!$dossier_id) $this->error('dossier参数错误');
        if(!DefenceExt::getDefence($dossier_id,$doc_type)){
            Db::startTrans();

            $res = DefenceExt::addDefence($dossier_id,DefenceExt::SHOU_LI,session('zc_admin_uid'),$doc_type,'受理通知书','');
            if(!$res){
                Db::rollback();
                $this->error('受理失败');
            }
        }else{
            $this->error('已受理,请勿重复');
        }
        $zno = DossierDoc::create_zc_no();
        $res1 = Dossier::changeStatus($dossier_id,2,array('zno'=>$zno));
        if(!$res1){
            Db::rollback();
            $this->error('受理失败');
        }

        Db::commit();
        $this->success('受理成功');
    }

    public function no_accept()
    {
        $dossier_id = $this->request->param("dossier_id/d");

        $doc_type = 10;
        if (!$dossier_id) $this->error('dossier参数错误');

        if(DefenceExt::getDefence($dossier_id,$doc_type)){
            Db::startTrans();
            $res= DefenceExt::delDefence($dossier_id,$doc_type);
            if(!$res){
                Db::rollback();
                $this->error('拒绝失败');
            }
        }

        $res1 = Dossier::changeStatus($dossier_id,2);
        if(!$res1){
            Db::rollback();
            $this->error('拒绝失败');
        }
        $this->success('拒绝成功');
    }

    //通知书
    public function apply()
    {

        $dossier_id = $this->request->param("dossier_id/d");
        if (!$dossier_id) $this->error('dossier参数错误');
        $info = DossierUser::getInstance($dossier_id)->getUserInfo();
        $this->assign('info',$info);
        return $this->fetch('dossier/dossieroperation/apply_'. $this->get_dossier_common($dossier_id));

    }

    //申请人通知书
    public function note()
    {
        $dossier_id = $this->request->param("dossier_id/d");

        return $this->fetch('dossier/dossieroperation/note_'. $this->get_dossier_common($dossier_id));

    }
    //被申请人通知书
    public function note2()
    {
        $dossier_id = $this->request->param("dossier_id/d");

        return $this->fetch('dossier/dossieroperation/note2_'. $this->get_dossier_common($dossier_id));

    }

    //仲裁规则
    public function gui_ze()
    {

        $dossier_id = $this->request->param("dossier_id/d");

        return $this->fetch('dossier/dossieroperation/guize_'. $this->get_dossier_common($dossier_id));
    }

    private function get_dossier_common($dossier_id)
    {
        if (!$dossier_id) $this->error('dossier_id参数错误');

        $info = Db::name("dossier")->find($dossier_id);

        $zc_jg_id = $info['zc_jg_id'];

        $jg_info = Db::name("jigou")->find($zc_jg_id);

        if (!$jg_info) $this->error('机构不存在');

        return $zc_jg_id;
    }
}