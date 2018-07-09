<?php


/**
 * Created by PhpStorm.
 * User: Lee
 * Date: 2018/5/5
 * Time: 10:16
 */
namespace app\admin\controller\wsdoc;

use app\common\controller\Backend;
use think\Db;
use think\db\Query;
use wslibs\wscontract\bean\Signer;
use wslibs\wscontract\ContractTools;
use wslibs\wscontract\WsContract;
use wslibs\wszc\Dossier;
use wslibs\wsform\WsForm;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\GroupItem;
use wslibs\wsform\GroupMoreItem;
use wslibs\wszc\DossierLog;
use wslibs\wszc\DossierUser;
use wslibs\wszc\MakePdf;
use wslibs\wszc\User;


class Applydoc extends Backend
{
    public function _initialize()
    {
        $this->noNeedLogin = ['*'];
        $this->use_action_js();
        return parent::_initialize(); // TODO: Change the autogenerated stub
    }

    public function initIndex()
    {
        $form = new WsForm();
        $form->setFormTitleTip("请填写资料");


        $item = new Item();
        $item->varName("zctitle")->varTitle("仲裁标题")->inputType(InputType::text);
        $form->addItem($item->required(true));


        $item = new Item();
        $item->varName("zhongcai")->varTitle("仲裁机构")->inputType(InputType::select);
        $item->itemArrValName("zhongcailist");
        $form->addItem($item);



        $item = new Item();
        $item->varName("request")->varTitle("请求事项")->inputType(InputType::content);
        $form->addItem($item->required(true));

        $item = new Item();
        $item->varName("reasons")->varTitle("事实和理由")->inputType(InputType::content);
        $form->addItem($item->required(true));



        $group = new GroupMoreItem($form, "被申请人信息", "bsqr_num");

        $item = new Item();
        $item->varName("bsqr_name")->varTitle("被申请人(公司/个人)名称")->inputType(InputType::text)->defaultValue("例：***有限公司/周**");
        $group->addItem($item->required(true));

        $item = new Item();
        $item->varName("bsqr_credit_code")->varTitle("统一社会信用代码(个人填：无)")->inputType(InputType::text);
        $group->addItem($item);

        $item = new Item();
        $item->varName("bsqr_f_name")->varTitle("法定代表人(个人填：无)")->inputType(InputType::text);
        $group->addItem($item);

        $item = new Item();
        $item->varName("bsqr_com_phone")->varTitle("联系方式(公司)(个人填：无)")->inputType(InputType::text);
        $group->addItem($item);
//
//        $item = new Item();
//        $item->varName("bsqr_sex")->varTitle("性别")->inputType(InputType::radio);
//        $item->itemArr(array(1 => "男", 2 => "女"))->defaultValue(1);
//        $group->addItem($item);

        $item = new Item();
        $item->varName("bsqr_idcard")->varTitle("身份证号")->inputType(InputType::text);
        $group->addItem($item);

        $item = new Item();
        $item->varName("bsqr_phone")->varTitle("手机号")->inputType(InputType::text);
        $group->addItem($item);


        $item = new Item();
        $item->varName("bsqr_address")->varTitle("地址(公司住所/个人住址)")->inputType(InputType::text);
        $group->addItem($item);

//
//
//        $item = new Item();
//        $item->varName("bsqr_minzu")->varTitle("民族")->inputType(InputType::text);
//        $group->addItem($item);


        $form->addItem($group);



        $group = new GroupItem($form, "申请人详情");

        $item = new Item();
        $item->varName("sqr_name")->varTitle("申请人(公司/个人)名称")->inputType(InputType::text)->defaultValue("例：***有限公司/周**");
        $group->addItem($item->required(true));

        $item = new Item();
        $item->varName("sqr_credit_code")->varTitle("统一社会信用代码(个人填：无)")->inputType(InputType::text);
        $group->addItem($item->required(false));

        $item = new Item();
        $item->varName("sqr_f_name")->varTitle("法定代表人(个人填：无)")->inputType(InputType::text);
        $group->addItem($item->required(false));


        $item = new Item();
        $item->varName("sqr_com_phone")->varTitle("联系方式(公司)(个人填：无)")->inputType(InputType::text);
        $group->addItem($item);


//        $item = new Item();
//        $item->varName("sqr_sex")->varTitle("性别")->inputType(InputType::radio);
//        $item->itemArr(array(1 => "男", 2 => "女"))->defaultValue(1);
//        $group->addItem($item->required(true));

        $item = new Item();
        $item->varName("sqr_idcard")->varTitle("身份证号")->inputType(InputType::text);
        $group->addItem($item->required(false));


        $item = new Item();
        $item->varName("sqr_phone")->varTitle("手机号")->inputType(InputType::text);
        $group->addItem($item);


        $item = new Item();
        $item->varName("sqr_address")->varTitle("地址(公司住所/个人住址)")->inputType(InputType::text);
        $group->addItem($item);

//
//        $item = new Item();
//        $item->varName("sqr_minzu")->varTitle("民族")->inputType(InputType::text);
//        $group->addItem($item);
//

        $form->addItem($group);

        $form->setMakeType(WsForm::Type_Form);

        $form->makeForm("wsdoc/Applydoc/apply");


    }
    public function apply()
    {

        $d_id = $this->request->param('dossier_id/d');


//        dump(Dossier::getDangShiRen($d_id,[1,2]));
        if($this->request->isAjax()) {

            if ($this->request->isPost()) {

                $postdata = $this->request->post('row/a');

                $dossierUserInfo = $postdata['bsqr_num'];
                unset($postdata['bsqr_num']);
                $mid = [];

                foreach ($dossierUserInfo as $k=>$v){

                    array_pop($v);
                    $mid[$k] = $v;
                }

                $arr = [];

                foreach ($mid as $k=>$v){
                    foreach ($v as $key=>$val){
                        $arr[$key][$k] = $val;
                    }
                }


                $uid = 1;
                $name = 1;

                Db::startTrans();

                if($d_id){
                    dump($postdata);dump($d_id);die;
                    $this->editDossier($d_id,$postdata);
                    DossierLog::addLog($d_id,$uid,$name,0);


                    $res = Db::name('dossier_users')->where('dossier_id',$d_id)->delete();

                    if(!$res){
                        Db::rollback();
                        $this->error('操作有误');
                    }

                    $dossier_id = $d_id;
                }else{

                    $zc_jg_id = intval($postdata['zhongcai']);
                    $title = trim($postdata['zctitle']);
                    $third_jg_id = session('admin.jg_id');
                    $third_order_id = 1;

                    $dossierData = [
                        'reasons'=>$postdata['reasons'],
                        'request'=>$postdata['request'],
                    ];



                    $dossier_id = Dossier::add($zc_jg_id, $third_jg_id, $third_order_id, $type = 1, $title);


                    DossierLog::addLog($dossier_id,$uid,$name,DossierLog::LOG_TYPE_CREATE);

                    if(!$dossier_id){
                        Db::rollback();
                        $this->error("添加失败!");
                    }
                    $result = Dossier::updata($dossier_id,$dossierData);

                    if(!$result){
                        Db::rollback();
                        $this->error("添加失败!!");
                    }

                }



                $returnArr = $this->AddDossierUser($dossier_id,$postdata,$arr);

                if($returnArr['code']-0<0){
                    Db::rollback();
                    $this->error($returnArr['code']);
                }else{

                    Db::commit();
                    $this->success($returnArr['msg']);
                }

            }
        }

        if($d_id){
            $Users = Dossier::getDangShiRen($d_id,2);
            $sqr_user = Dossier::getDangShiRen($d_id,1);
            $middle = $this->getRow($d_id,$sqr_user,$Users);
            $this->assign("is_zc",1);
        }

        if(input("sy")==1){
            dump($Users);
            dump($sqr_user);
            dump($middle);
        }


        $zhongCaiList = Db::name('jigou')->order('id desc')->column('name','id');

        $this->assign('dossier_id', $d_id);
        $this->assign('zhongcailist',$zhongCaiList);
        $this->assign("bsqr_num", count($Users)?count($Users):1);
        $this->assign('row',$middle);

        return $this->fetch();
    }


    //提交卷宗
    private function AddDossierUser($dossier_id,$sqrData,$bsqrData)
    {

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
    private function getRow($dossier_id,$sqr_user,$Users)
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


        $middle['zctitle'] = $dossierInfo['title'];
        $middle['zhongcai'] = $dossierInfo['zc_jg_id'];
        $middle['request'] = $dossierInfo['request'];
        $middle['reasons'] = $dossierInfo['reasons'];


        return $middle;
    }

    private function editDossier($dossier_id,$data)
    {
        $dossierData = [
            'title'=>$data['zctitle'],
            'zc_jg_id'=>$data['zhongcai'],
            'request'=>$data['request'],
            'reasons'=>$data['reasons'],
        ];

        Dossier::updata($dossier_id,$dossierData);


    }

    public function applyshow()
    {
        return $this->fetch();
    }

    private function addUser($dossier_id,$data)
    {

    }

    public function wt()
    {
        $dossier_id = Dossier::add(10,1,1,1);
        $dossierUserF = new DossierUser();

        $dossierUserF->setNameOrOrgname('王守位');
        $dossierUserF->setType(1);
        $dossierUserF->setIdcardOrCreditno('130406199105040915');
        $dossierUserF->setPhone('18531160171');
        $dossierUserF->setAddress('邯郸');
        $dossierUserF->setRole(1);

        $dossierUserF->pushToDossier($dossier_id);

        $dossierUserF = new DossierUser();

        $dossierUserF->setNameOrOrgname('李帆');
        $dossierUserF->setType(1);
        $dossierUserF->setIdcardOrCreditno('130185199106131818');
        $dossierUserF->setPhone('18332051721');
        $dossierUserF->setAddress('石家庄');
        $dossierUserF->setRole(2);

        $dossierUserF->pushToDossier($dossier_id);

        $dossierUserF = new DossierUser();

        $dossierUserF->setNameOrOrgname('郑洪志');
        $dossierUserF->setType(1);
        $dossierUserF->setIdcardOrCreditno('130425198903242030');
        $dossierUserF->setPhone('15201634344');
        $dossierUserF->setAddress('邯郸');
        $dossierUserF->setRole(2);

        $dossierUserF->pushToDossier($dossier_id);

        $dossierUserF = new DossierUser();

        $dossierUserF->setNameOrOrgname('宋杨');
        $dossierUserF->setType(1);
        $dossierUserF->setIdcardOrCreditno('130102199708280318');
        $dossierUserF->setPhone('13933863958');
        $dossierUserF->setAddress('石家庄');
        $dossierUserF->setRole(2);

        $dossierUserF->pushToDossier($dossier_id);

        echo $dossier_id;

    }

    public function wtf()
    {



            $info = Db::name("dr")->find(input('id'));
        $users = self::getSignerFromDoc($info);

        if ($users) {
            WsContract::createContractOnLoction($info['c_class'], $info['c_no']);
            foreach ($users as $user) {


                WsContract::addSigner($info['c_no'], $user->uid, $user->getType(), $user->id_code, $info['dossier_id'],$_SERVER['HTTP_REFERER']);
            }
            WsContract::submitToService($info['c_no']);

            return true;
        }

        return false;


        $docid = $this->request->param('docid');
        MakePdf::index($docid);

        $info = Db::name('dr')->where('id',$docid)->find();

        $s_id_code = ContractTools::getUserIdCode(1,$info['uid']) ;//($info['type']-2==0 ? ContractTools::getCompanyIdCode(1) : ContractTools::getUserIdCode(1,$info['id']) );


        $this->success('success',url('contract/gotoSign',['c_no'=>$info['c_no'],'uid'=>$info['uid'],'id_code'=>$s_id_code]),'',5);




        exit;

        WsContract::createContractOnLoction('Repayment', '555555555');
        WsContract::addSigner('555555555', 13,  1,'zc_13');

        WsContract::submitToService('555555555');
    }



    public function getSignerFromDoc($docinfo)
    {
        return array(new Signer($docinfo['uid'], 1, 0));
    }


}