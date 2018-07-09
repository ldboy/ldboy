<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/13
 * Time: 下午2:21
 */

namespace app\admin\controller\dossier;


use app\common\controller\Backend;

use dossier\DossierDoc;
use think\Db;
use userinfo\DossierUser;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dossier;
use wslibs\wszc\Drole;
use wslibs\wszc\LoginUser;
use wslibs\wszc\Dvalue;

class Lian extends Backend
{
    public function spshouli()
    {
        $d_id = $this->request->param("id/d");
        $is_phone = $this->request->param("is_phone/d");

        if (!LoginUser::isZhongCaiLiAanShenPi()) {
            return false;
        }

        if (!Dossier::isStatus($d_id, 22)) {
            $this->error("此状态不可修改");
        }


        //2018.6.21 新增代码
        $no = DossierDoc::create_zc_no();
        $re0 = Db::name('dossier')->where('id',$d_id)->update(['zno'=>$no]);
        //-------------------------------------------------------------------------------

        Drole::addRoleFromLoginUser($d_id, Constant::D_Role_ZhongCaiWei_LiAnShenPi);
        Dossier::changeStatus($d_id, [2, 23]);
        Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_lianshenpibiao,"zhurenShouliyijian","同意立案");


        DocContract::initContract(Ddocs::getOrInitFile($d_id, Constant::DOC_model_lianshenpibiao, 0)['id']);



        if($is_phone){
            $this->success("提交成功");
            exit;
        }

        $this->success("提交成功", "", ['alert' => 1, 'wsreload' => 1]);

        exit;

    }
}