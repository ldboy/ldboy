<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/3/5
 * Time: ????9:49
 */
namespace wslibs\wscontract\contract;


use think\Db;
use wslibs\sign\YwSign;
use wslibs\wszc\Dvalue;

abstract class YwDocCommon extends ContractCommon
{

    public function getSignService()
    {
        // TODO: Implement getSignService() method.
        return 2;
    }

    public function isUserAutoSign($signer)
    {
        // TODO: Implement isUserAutoSign() method.
        if ($signer->isPersonal()) {
            return false;
        } else {
            return false;
        }
    }

    /**
     * SIGNATURE ?????????? 1
     * SIGNATURECODE ????????? 2
     * WRITTEN ??д???????? 3
     * WRITTENCODE ??д?????? 4
     * CODE ??????????
     * @param $signer
     */
    public function getUserSignType($signer)
    {
        // TODO: Implement getUserSignType() method.
        if ($signer->isPersonal()) {
            return 3;
        } else {
            return 1;
        }
    }

    public function getCreateType()
    {
        return 2;
    }

    public function isCreatedByPdf()
    {
        return $this->getCreateType() == 2;
    }

    public function isCreatedByTemplate()
    {
        return $this->getCreateType() == 1;
    }

    public function getPdfWebUrl()
    {

        $info = Db::name("dr")->where("c_no", $this->getNo())->find();

        return "http://124.239.196.194:8080/zhongcai/index.php/admin/wsdoc.show/index?docid=" . $info['id'] . "&pdf_user=aksjdflkajdflkajsdflkaddjflksafjlk";

    }

    public function getTemplateValues()
    {
        return array();
    }

    public function getTemplateId()
    {
        return 0;
    }


    public function onSignFinish($pdfbase64, $imagesurl)
    {
        // file_put_contents("pdf0.txt", "time:".time()."onSignFinish:1" . "\n", FILE_APPEND);


        // TODO: Implement onSignFinish() method.
        $info = Db::name("dr")->where("c_no", $this->getNo())->find();

        Db::name('dr')->where('id', $info['id'])->update(['has_sign' => 1]);

        //上传
        $shorturl = YwSign::getOrgDir($info['dossier_id'], true);

        $docimgsurl = $info['id'] . $info['doc_model_id'] . substr(time(), -3) . ".png";

        //   YwSign::saveFile($info['dossier_id'], $shorturl . $docimgsurl, file_get_contents($imagesurl[0]));
        $outurl = YwSign::saveFile($info['dossier_id'], $shorturl . $docimgsurl . ".pdf", base64_decode($pdfbase64));
        Dvalue::delPdf($info['id']);
        Dvalue::addPdfToDoc($info['id'], YwSign::getOrgDir($info['dossier_id']) . $docimgsurl . ".pdf", 0, 0);//$info['uid']
        $inarray = array();

        foreach ($imagesurl as $key => $value) {
            $tmpurl = $shorturl . $docimgsurl . ".$key.png";
            YwSign::saveFile($info['ywid'], $tmpurl, file_get_contents($value));
            Dvalue::delDocImgs($info['id']);
            Dvalue::addFileToDoc($info['id'], YwSign::getOrgDir($info['dossier_id']) . $docimgsurl . ".$key.png", 0, 0);//$info['uid']
            $inarray[] = $tmpurl;
        }


        // file_put_contents("pdf0.txt", "time:".time()."has_sign:1" . "\n", FILE_APPEND);


        return $outurl;

    }


    public function onOneSignFinish($ht_user_info)
    {

        $info = Db::name("dr")->where("c_no", $this->getNo())->find();
        Db::name('drs')->where('doc_id', $info['id'])->where("idid", $ht_user_info['uid'])->where("weizhi", $ht_user_info['id_code'])->update(['ok' => 1, "signtime" => time()]);
        return true;

        //对业务的操作

    }

    public function onCreatedContract()
    {

        echo 'world';
//        return Db::name("yw_docs")->where("c_no",$this->getNo())->update(array('ht_make_status' => 1));

    }
}