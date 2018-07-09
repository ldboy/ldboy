<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/9
 * Time: ����12:50
 */

namespace wslibs\wszc;

use wslibs\wszc\question\QuestionExpand;
use think\Db;
use app\common\controller\Backend;
use think\db\Query;
use wslibs\wszc\Dossier;
use wslibs\wsform\WsForm;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\GroupItem;
use wslibs\wsform\GroupMoreItem;
use wslibs\wszc\DossierLog;
use wslibs\wszc\DossierUser;
use wslibs\wszc\User;
use wslibs\wszc\Constant;


class Dcp extends Dossier
{
    public static function doDefences($uid, $dossier_id ,$matter ,$reason)
    {
        
        $find = Db::name("dossier_defence")
            ->where("dossier_id", $dossier_id)
            ->where("idid", $uid)->find();
        if ($find) {
          return true;
        }
        $res = Dossier::changeStatus($dossier_id,Constant::DOSSIER_STATUS_DEFENCE);

        $res2 = Db::name("dossier")->where("id = '$dossier_id'")->setInc("defence_num");
        $res3 = Db::name("dossier")->where("id = '$dossier_id'")->setInc("defence_num_dcl");

        Db::startTrans();
        $res0 = Db::name("dossier_defence")
            ->insertGetId(
                ["matter" => $matter,
                'reason' => $reason ,
                "addtime" => time(),
                "idid" => $uid,
                "dossier_id" => $dossier_id
                ]
            );

        $res1 = DossierLog::addLog($dossier_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_DEFENCE);
        
        if($res &&$res0 && $res1&& $res2 && $res3){
            Db::commit();
            return $res0;
        }else{
            Db::rollback();
            return false;
        }
    }

    /**
     * @param $dossier_id
     * @param $idid
     * @param $zid
     * @param $title
     * @param $dataFour
     * @return bool|int|string
     */
    public static function doQuestion($qid,$dossier_id,$idid,$zid,$dataFour)
    {

        $title = Db::name('dr')
            ->where('id',$zid)
            ->value('name');

        return QuestionExpand::addItem($qid,$dossier_id,$zid,$idid,$title,$dataFour['legal'],$dataFour['relation'],$dataFour['reality'],$dataFour['other']);
    }


    //点击质证总提交
    public static function doQuestionFinish($qid,$dossier_id,$idid){
        return QuestionExpand::submitQuestion($qid,$dossier_id,$idid);
    }

    //提交人员
    public static function AddDossierUser($dossier_id,$sqrData,$bsqrData)
    {

        return true;
        $applyUser = new DossierUser();

        if(strlen($sqrData['sqr_credit_code'])<8 || strlen($sqrData['sqr_f_name'])<5){

            $applyUser->setNameOrOrgname($sqrData['sqr_name']);

            $applyUser->setType(1);
            $applyUser->setIdcardOrCreditno($sqrData['sqr_idcard']);
            $applyUser->setPhone($sqrData['sqr_phone']);
            $applyUser->setAddress($sqrData['sqr_address']);
            $applyUser->setRole(1);

            $res = $applyUser->pushToDossier($dossier_id);

        }else{

            $dossierUserF1 = new DossierUser();
            $dossierUserF1->setNameOrOrgname($sqrData['sqr_f_name']);
            $dossierUserF1->setType(3);
            $dossierUserF1->setIdcardOrCreditno($sqrData['sqr_idcard']);
            $dossierUserF1->setPhone($sqrData['sqr_phone']);
            $dossierUserF1->setRole(1);


            $applyUser->setFaRen($dossierUserF1);
            $applyUser->setNameOrOrgname($sqrData['sqr_name']);
            $applyUser->setType(2);
            $applyUser->setIdcardOrCreditno($sqrData['sqr_credit_code']);
            $applyUser->setPhone($sqrData['sqr_com_phone']);
            $applyUser->setAddress($sqrData['sqr_address']);
            $applyUser->setRole(1);



            $res = $applyUser->pushToDossier($dossier_id);
        }


        if(!$res){

            return ['code'=>-1,'msg'=>'提交失败'];
        }

        $_uid = User::addUser($sqrData['sqr_phone']);
        if(!$_uid){
            return ['code'=>-2,'msg'=>'提交失败'];
        }

        $_userInfo = User::addUserInfo($_uid,User::getSex($sqrData['sqr_idcard']),strtotime(User::getBirthday($sqrData['sqr_idcard']))/*,$postdata['sqr_address']*/);

        if(!$_userInfo){
            return ['code'=>-3,'msg'=>'提交失败'];
        }



        $dossierUser = new DossierUser();


        foreach ($bsqrData as $k=>$v){
            if(strlen($v['bsqr_credit_code'])<8 || strlen($v['bsqr_f_name'])<5){
                //个人


                $dossierUser->setNameOrOrgname($v['bsqr_name']);
                $dossierUser->setType(1);
                $dossierUser->setIdcardOrCreditno($v['bsqr_idcard']);
                $dossierUser->setPhone($v['bsqr_phone']);
                $dossierUser->setRole(2);
                $dossierUser->setAddress($v['bsqr_address']);
                $res = $dossierUser->pushToDossier($dossier_id);


            }else{
                //公司

                $dossierUserF = new DossierUser();

                $dossierUserF->setNameOrOrgname($v['bsqr_f_name']);
                $dossierUserF->setType(3);
                $dossierUserF->setIdcardOrCreditno($v['bsqr_idcard']);
                $dossierUserF->setPhone($v['bsqr_phone']);
                $dossierUserF->setAddress($v['bsqr_address']);
                $dossierUserF->setRole(2);


                $dossierUser->setFaRen($dossierUserF);
                $dossierUser->setNameOrOrgname($v['bsqr_name']);
                $dossierUser->setType(2);
                $dossierUser->setIdcardOrCreditno($v['bsqr_credit_code']);
                $dossierUser->setPhone($v['bsqr_com_phone']);
                $dossierUser->setAddress($v['bsqr_address']);
                $dossierUser->setRole(2);



                $res = $dossierUser->pushToDossier($dossier_id);
            }



            if(!$res){
                return ['code'=>-4,'msg'=>'提交失败'];
            }

            $_uid = User::addUser($v['bsqr_phone']);
            if(!$_uid){
                return ['code'=>-5,'msg'=>'提交失败'];
            }

            $_userInfo = User::addUserInfo($_uid,User::getSex($v['bsqr_idcard']),strtotime(User::getBirthday($v['bsqr_idcard']))/*,$v['bsqr_address']*/);

            if(!$_userInfo){
                return ['code'=>-6,'msg'=>'提交失败'];
            }


        }

        return ['code'=>1,'msg'=>'提交成功'];

    }

    //编辑显示
    public static function getRow($dossier_id,$sqr_user,$Users)
    {
        $query = new Query();
        $query->name('dossier')
            ->alias('d')
            ->join('zc_jigou jg','d.zc_jg_id = jg.id','left')
            ->where('d.id',$dossier_id)
            ->field('d.*,jg.name as zc_jg_name ');
        $dossierInfo = Db::find($query);

        $wht = [];

        foreach ($Users as $k=>$v){


            $wht[$k]['bsqr_name'] = $v['name'];
            $wht[$k]['bsqr_f_name'] = $v['f_name']?$v['f_name']:'无';
            $wht[$k]['bsqr_address'] = $v['address']?$v['address']:'无';
            $wht[$k]['bsqr_phone'] = ($v['type']-2==0?$v['f_phone']:$v['phone']);

            $wht[$k]['bsqr_com_phone'] = ($v['type']-2==0?$v['phone']:'无');
            $wht[$k]['bsqr_credit_code'] = ($v['type']-2==0?$v['id_num']:'无');
            $wht[$k]['bsqr_idcard'] = ($v['type']-2==0?$v['f_id_card']:$v['id_num']);


        }


        $middle = [];
        foreach ($wht as $k=>$v){
            foreach ($v as $key=>$val){
                $middle['bsqr_num'][$key][$k] = $val;
            }
        }


        $middle['sqr_name'] = $sqr_user[0]['name'];
        $middle['sqr_f_name'] = $sqr_user[0]['f_name']?$sqr_user[0]['f_name']:'无';
        $middle['sqr_address'] = $sqr_user[0]['address']?$sqr_user[0]['address']:'无';
        $middle['sqr_phone'] = ($sqr_user[0]['type']-2==0?$sqr_user[0]['f_phone']:$sqr_user[0]['phone']);

        $middle['sqr_com_phone'] = ($sqr_user[0]['type']-2==0?$sqr_user[0]['phone']:'无');
        $middle['sqr_credit_code'] = ($sqr_user[0]['type']-2==0?$sqr_user[0]['id_num']:'无');
        $middle['sqr_idcard'] = ($sqr_user[0]['type']-2==0?$sqr_user[0]['f_id_card']:$sqr_user[0]['id_num']);

        $request_default = "（一）裁定被申请人(借款人)XXX偿还贷款本金XXX元及截止到XX年XX月XX日未偿还的利息XX、罚息XX元，合计XXX元；利息、罚息计算至实际执行之日止；"."\n"."（二）裁定被申请人（担保人）XXX、XXX对上述贷款本息承担连带保证责任/抵押责任/质押责任；"."\n"."（三）裁定申请人对三被申请人抵押物、质押物享有优先受偿权；"."\n"."（四）裁定本案仲裁费、律师费、保全费由上述三被申请人承担。"."\n"."（五）因     发生的纠纷。";





        $reason_default = '年   月   日，申请人与第一被申请人签署了编号为   第   号的《   借款合同》，合同项下的贷款金额为   元人民币；同日，第二被申请人与申请人签署了编号为   第   号的《   合同》约定第二被申请人对编号   ［   ］   号《   借款合同》项下债权承担连带保证责任。同日第三被申请人与申请人第三被申请人与申请人签署了编号为   第   号的《   抵押/质押合同》约定第三被申请人对编号   ［   ］   号《   借款合同》项下债权承担连带保证责任提供抵押/质押担保。

申请人与   被申请人于   年   月   日达成还款协议，约定借款、还款及担保事项，同时约定如有任何一期未足额偿还借款，申请人有权宣告提前收回全部贷款本金并按合同约定计收利息、罚息及复利。

上述合同及还款协议签订后，申请人按合同约定履行了放款义务，第一被申请人于   年   月开始未按期偿还欠款。截至     年     月     日借款人尚欠出借人借款本金     元，利息     元。';

        $middle['zctitle'] = $dossierInfo['title'];
        $middle['zhongcai'] = $dossierInfo['zc_jg_id'];
        $middle['request'] = $dossierInfo['request'] ? $dossierInfo['request'] : $request_default;
        $middle['reasons'] = $dossierInfo['reasons'] ? $dossierInfo['reasons'] : $reason_default;
        $middle['dispute_money'] = $dossierInfo['dispute_money'];


        return $middle;
    }

    public static function editDossier($dossier_id,$data)
    {
        $dossierData = [
            'title'=>$data['zctitle'],
            'zc_jg_id'=>$data['zhongcai'],
            'request'=>$data['request'],
            'reasons'=>$data['reasons'],
            'dispute_money'=>$data['dispute_money']
        ];
        Drole::delRoleByRole($dossier_id,Constant::D_Role_ZhongCaiWei_JiGou);
        $jigouInfo = Db::name('jigou')->where('id',$dossierData['zc_jg_id'])->find();
        Drole::addRole($dossier_id,$jigouInfo['idid'],$jigouInfo['name'],Constant::D_Role_ZhongCaiWei_JiGou,$jigouInfo['idid']);
        Dossier::changeStatus($dossier_id,Dossier::makeStatus(1,11));
        return Dossier::updata($dossier_id,$dossierData);
        /*echo Db::name("dossier")->getLastSql();die;*/
    }


    public static function zuting($dossier_id,$zcy_id,$again=0)
    {
        $info = Db::name('jigou_zcy')
            ->alias('z')
            ->join('zc_zcy zz','z.zcy_id = zz.id',"LEFT")
            ->where('zz.id',$zcy_id)
            ->field('z.*,zz.name')
            ->find();

        /*echo Db::name("jigou_zcy")->getLastSql();*/

        if(!$info){

            return ['code'=>-1,'msg'=>'仲裁员不存在'];

        }

        $cinfo = Db::name('arbitrator')
            ->alias('a')
            ->join('zc_court c','a.court_id = c.id')
            ->where('a.zcy_uid',$zcy_id)
            ->where('c.dossier_id',$dossier_id)
            ->where('a.status',1)
            ->find();
        if($cinfo){
            return ['code'=>-2,'msg'=>'已指定仲裁员'];
        }

        Db::startTrans();
        $time = time();

        $cRes = Db::name('court')->insertGetId(["is_again"=>$again,'dossier_id'=>$dossier_id,'addtime'=>$time,'director_id'=>User::getLoginUid(),'status'=>1]);

        $aRes = Db::name('arbitrator')->insertGetId(['court_id'=>$cRes,'name'=>$info['name'],'dossier_id'=>$dossier_id,'zcy_uid'=>$zcy_id]);

        if($aRes && $aRes){
            Db::commit();
            return ['code'=>1,'msg'=>'成功',"id"=>$cRes];
        }else{
            Db::rollback();
            return ['code'=>-3,'msg'=>'error'];
        }
    }

    public static function lianshenpi($d_id,$postData){
            if(!LoginUser::isZhongCaiLiAanShenPi()){
                return false;
            }
            /*$re = Dossier::changeStatus($d_id,3);*/
            //$re1 = DossierLog::addLog($d_id,LoginUser::getIdid(),LoginUser::getUserName(),DossierLog::LOG_TYPE_ACCEPT);
            $re2 = Dvalue::saveUniqueValueByDocMode($d_id,Constant::DOC_model_lianshenpibiao,"zhurenShouliyijian","同意立案");
            $re3 = Drole::addRoleFromLoginUser($d_id,Constant::D_Role_ZhongCaiWei_LiAnShenPi);
         return true;
    }

}