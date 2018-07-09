<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/2/25
 * Time: 上午9:33
 * 合同表 ws_ht
 * id
 * c_no
 * name
 * class_name
 * qs_pingtai 签字平台
 * status 状态 1 创建 2签字中 3已经签订完成  0 取消
 * rnum 需要签字人数
 * snum 已经签字人数
 * time1
 * time2
 * time3
 * time0
 * create_type 创建方式  1 利用模板  2 利用pdf 文件
 * weburl pdf 对应的网址
 * pdf0  pdf 对应的要上传的模板地址（远程）。如要上传到第三方需要先下载
 * pdf1  最终签字后的合同pdf
 * template_id 模板的时候模板id
 *y
 *
 *
 *
 *
 *
 * 合同需要签字的人 ws_ht_user
 *
 * id
 * c_id 合同id
 * uid
 * u_type 0企业  1个人
 * status 0未签字 1 已签字
 * qs_time 签字时间
 * *is_auto  0手动 1自动
 * qs_type 这个和不同平台参数不一样
 *
 * 平台用户注册  ws_user
 *
 * id
 * uid
 * qs_pingtai 平台id
 * u_type  0企业  1个人
 *
 *
 */
namespace wslibs\wscontract;

use think\Db;
use wslibs\socketsend\SocketSend;
use wslibs\wscontract\bean\ContractSigner;
use wslibs\wscontract\bean\Signer;


class WsContract
{
    /**
     * @param $c_class 合同类名称
     * @param $c_no 合同编号
     */
    public static function createContractOnLoction($c_class, $c_no)
    {

        if ($find = Db::name("ws_ht")->where(" c_no='$c_no' ")->find()) {
            return $find['id'];
        }

        $class = ContractTools::getContractClass($c_class);
        if ($class) {
            $class->setNo($c_no);
            $data = array();
            $data['c_no'] = $c_no;
            $data['c_name'] = $class->getName();
            $data['class_name'] = $c_class;
            $data['qs_pingtai'] = $class->getSignService();
            $data['status'] = 1;
            $data['rnum'] = 0;
            $data['snum'] = 0;
            $data['time1'] = time();
            $data['create_type'] = $class->getCreateType();
            if ($class->isCreatedByPdf()) {
                $data['weburl'] = $class->getPdfWebUrl();
            }
            if ($class->isCreatedByTemplate()) {
                $data['template_id'] = $class->getTemplateId();
            }

            return Db::name("ws_ht")->insertGetId($data);
        }
        return 0;

    }


    public static function addSignerOfSigner($c_no, $signer, $ywid = 0)
    {

        $class = ContractTools::getContractClassByExistNo($c_no);

        $uid = $signer->uid;
        $u_type = $signer->getType();
        $id_code = $signer->id_code;
        if ($class) {

            //  var_dump($u_type);
            if ($signer->initInPingtai($class->getSignService())) {
                //   var_dump($u_type);

                if (!Db::name("ws_ht_user")->where("c_id", $class->getId())->where('uid', $uid)->where('u_type', $u_type)->where('id_code', $id_code)->find()) {
                    $data = array();
                    $data['c_id'] = $class->getId();
                    $data['c_no'] = $c_no;
                    $data['uid'] = $uid;
                    $data['u_type'] = $signer->getType();
                    $data['status'] = 0;
                    $data['qs_time'] = 0;
                    $data['is_auto'] = $class->isUserAutoSign($signer) ? 1 : 0;
                    $data['qs_type'] = $class->getUserSignType($signer);
                    $data['id_code'] = $id_code;
                    $data['f_uid'] = $signer->getComFuid();
                    $data['ywid'] = $ywid;
                    $data['backurl'] = $signer->backurl();
                    $data['auto_after'] = $signer->autoAfter();
                    $in_id = Db::name("ws_ht_user")->insertGetId($data);

                    if ($in_id) {

                        $gsret = Db::name("ws_ht")->where("id", $class->getId())->setInc('rnum');
                        if ($gsret) {


                            if ($nextSigners = $signer->nextSigners()) {
                                foreach ($nextSigners as $value) {
                                    $value->autoAfter($in_id);
                                    self::addSignerOfSigner($c_no, $value, $ywid);
                                }
                            }

                            return true;
//                            if ($signer->isCompany()) {
//                                return self::addSigner($c_no, $signer->getComFuid(), 1, $id_code);
//                            } else {
//                                return true;
//                            }

                        } else {
                            return false;
                        }

                    } else {
                        return false;
                    }
                }
                return true;

            } else {


                return false;
            }

        } else {
            return false;
        }
    }

    /**
     * @param $c_no
     * @param $uid  个人就是个人id  企业就是企业id
     * $u_type 0 企业 1 个人
     * @return bool
     */


    public static function addSigner($c_no, $uid, $u_type = 1, $id_code = "0", $ywid = 0, $backurl = "")
    {


        $signer = new Signer($uid, $u_type, $id_code);
        $signer->backurl($backurl);

        return self::addSignerOfSigner($c_no, $signer, $ywid);

    }


    public static function submitToService($c_no)
    {

        $class = ContractTools::getContractClassByExistNo($c_no);

        if ($class) {

            $created = false;
            if ($class->isCreatedByTemplate()) {
                $created = true;
                //第一步初始化第三方服务


            } else if ($class->isCreatedByPdf()) {

                if (trim($class->info("pdf0")) && $class->info("status") == 1) {

                    $created = true;
                } else if ($class->info("status") == 1) {
                    //去生成pdf，生成成功后，再次调取这个函数

                    $savepath = ContractConfig::makePdf0SavePath($class->info("id"));

                    SocketSend::html2pictureBackUrl($class->info("weburl"), $savepath, ContractConfig::getPdf0NoticeUrl($class->info("c_no")));

                    Db::name("ws_ht")->where("id", $class->getId())->update(["pdf0" => ContractConfig::getCunChuHtmlPre() . $savepath]);
                    return;
                }
            }



            if ($created) {
                $service = ContractTools::getServiceClass($class->getSignService());
                if ($service->createContract($class)) {

                    //此处做了修改
                    $class->onCreatedContract();

                    //加上生成pdf回调
                    Db::name("ws_ht")->where("id", $class->getId())->update(["time2" => time(), "status" => 2]);
                    //自动签署某一些人的签字
                    self::autoSign($class->getId(), 0);

                }


            }

        }
    }

    private static function autoSign($c_id, $id)
    {


        Db::name("ws_ht_user")->where("c_id", $c_id)->where("auto_after", $id)->update(['waiting' => 0]);
        $list = Db::name("ws_ht_user")->where("is_auto", 1)->where("c_id", $c_id)->where("waiting", 0)->where("auto_after", $id)->select();

        if(input('lee')==4){

            echo 1;
            dump($list);
        }


        foreach ($list as $value) {
            self::signContract($value['id']);
        }
        return $list;
    }

    public static function getContractStatus($c_no)
    {
        $class = ContractTools::getContractClassByExistNo($c_no);
        return Db::name("ws_ht")->where("id", $class->getId())->value("status");
    }

    public static function existContract($c_no)
    {
        return Db::name("ws_ht")->where(" c_no='$c_no' ")->find();


    }

    public static function isCanSign($c_no)
    {
        return self::getContractStatus($c_no) == 2;
    }

    public static function isCreateContract($c_no)
    {
        $class = ContractTools::getContractClassByExistNo($c_no);
        return (trim($class->info("pdf0")) && $class->info("status") == 1);
    }


    public static function signContractByCnoAndUid($c_no, $uid, $id_code = '0', $auto = -1)
    {
        $info = Db::name("ws_ht_user")->where("c_no", $c_no)->where("uid", $uid)->where("id_code", $id_code)->find();

        self::signContract($info['id'], $auto);
    }

    public static function signContract($id, $auto = -1)//自动将直接签署。这个函数如果是非自动签署必须在web中调用
    {

        $info = Db::name("ws_ht_user")->where("id", $id)->find();


        $class = ContractTools::getContractClassByExistNo($info['c_no']);

        if (!$class) exit;


        $service = ContractTools::getServiceClass($class->getSignService());
        $out = $service->signContract(new ContractSigner($id, $auto));



        if(input('lee')==4){

            echo 2222222;
            dump($out);

        }




        if (is_array($out)) {

            $url = $out['url'];
            $formData = "<form id='zqwssubmit' name='zqwssubmit' action='" . $url . "' method='post'>";
            $arr = $out['args'];
            foreach ($arr as $k => $val) {
                $formData = $formData . "<input type='hidden' name='" . $k . "' value='" . $val . "'/>";
            }
            $formData = $formData . "<input type='submit' value='确认' style='display:none;'></form><script>document.forms['zqwssubmit'].submit();</script>";
            echo $formData;
            exit;
        } else if ($out === true) {
            self::onSignContract($id);
        }
    }


    public static function onSignContract($id)//当某一个任签订谋一份合同的时候
    {


        $info = Db::name("ws_ht_user")->where("id", $id)/*->where("status",0)*/
        ->find();//



        if(input('lee')==4){
            echo '333333333';
            dump($info);
            exit;
        }



        if ($info) {
            Db::name("ws_ht_user")->where("id", $id)->update(["qs_time" => time(), "status" => 1]);
            Db::name("ws_ht")->where("id", $info['c_id'])->setInc('snum');


            $class = ContractTools::getContractClassByExistNo($info['c_no']);
            $class->onOneSignFinish($info);

            //签订完成后看看这个人是否对应某一个公司的法人


//            if (($info['u_type'] == 1) && ($company_qianding = M("ws_ht_user")->where("c_id={$info['c_id']} and status=0 and u_type=0 and f_uid={$info['uid']}")->find())) {
//                M("ws_ht_user")->where("id={$company_qianding['id']}")->data(array("is_auto" => 2))->save();
//                self::signContract($company_qianding['id']);
//                return;
//            }



            if (self::autoSign($info['c_id'], $id)) {
                return;
            }

            if(input('lee')==4){
                dump(Db::name("ws_ht_user")->where("c_id", $info['c_id'])->where("status", 0)->find());
                exit;
            }


            if (!Db::name("ws_ht_user")->where("c_id", $info['c_id'])->where("status", 0)->find()) {//

                $pdf1 = $class->onSignFinish(self::getContractPdfBase64($info['c_no']), self::getContractImagesUrl($info['c_no']));

                $data = array("time3" => time(), "status" => 3);
                if ($pdf1) {
                    $data['pdf1'] = ContractConfig::getCunChuHtmlPre() . $pdf1;
                }


                Db::name("ws_ht")->where("id", $info['c_id'])->update($data);
            }

        }


    }


    public static function getContractInfo($c_no)
    {
        $out = array();
        $out['info'] = Db::name("ws_ht")->where("c_no", $c_no)->find();
        $out['signer'] = Db::name("ws_ht_user")->where("c_no", $c_no)->select();
        $pingtai = $out['info']['qs_pingtai'];
        $uids = array_column($out['signer'], "uid");
        if ($uids) {
            $inpingtai = Db::name("ws_user")->where(" uid", "in", implode(",", $uids))->where("qs_pingtai", $pingtai)->column("uid");

            foreach ($out['signer'] as $key => $value) {
                if (in_array($value['uid'], $inpingtai)) {
                    $out['signer'][$key]['in_pingtai'] = 1;
                } else {
                    $out['signer'][$key]['in_pingtai'] = 0;
                }

            }
        }

        return $out;

    }


    public static function changeBackUrl($c_no, $uid, $id_code, $backurl)
    {

        return Db::name("ws_ht_user")->where("c_no", $c_no)->where("uid", $uid)->where("id_code", $id_code)->update(array("backurl" => $backurl));
    }

    public static function getContractPdfBase64($c_no)
    {
        $info = Db::name("ws_ht")->where("c_no", $c_no)->find();
        $service = ContractTools::getServiceClass($info['qs_pingtai']);
        return $service->getContractPdfBase64($c_no);
    }


    public static function getContractImagesUrl($c_no)
    {
        $info = Db::name("ws_ht")->where("c_no", $c_no)->find();
        $service = ContractTools::getServiceClass($info['qs_pingtai']);
        return $service->getContractImagesUrl($c_no);
    }

    /**
     * @param $c_no
     * @return mixed
     */
    public static function getContractPdfUrl($c_no)
    {

        $info = Db::name("ws_ht")->where("c_no", $c_no)->order('id desc')->find();

        $service = ContractTools::getServiceClass($info['qs_pingtai']?$info['qs_pingtai']:2);

        return $service->getContractPdfUrl($c_no);
    }

    /**
     * @param $c_no
     * @return mixed
     */
    public static function completionContract($c_no)
    {
        $info = Db::name("ws_ht")->where("c_no", $c_no)->find();

        $service = ContractTools::getServiceClass($info['qs_pingtai']?$info['qs_pingtai']:2);

        return $service->completionContract($c_no);
    }

    public static function signatureChange($file_url,$uid, $u_type = 1, $id_code = "0", $signService = 2)
    {


        
        $signer = new Signer($uid, $u_type, $id_code  );
        if ($signer->initInPingtai($signService)) {



            $service = ContractTools::getServiceClass($signService);
            return $service->signatureChange($signer,$file_url);
        }

        return false;
    }

}