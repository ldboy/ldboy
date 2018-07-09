<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/11
 * Time: ����9:38
 */

namespace wslibs\wszc;


use EasyWeChat\Support\Log;
use think\Db;
use wslibs\wscontract\bean\Signer;
use wslibs\wscontract\WsContract;
use wslibs\wszc\caijue\Dcaijue;

class DocContract
{
    public static function initContract($docidOrInfo)
    {

        if (!is_array($info = $docidOrInfo))
            $info = Db::name("dr")->find((int)$docidOrInfo);
        WsContract::createContractOnLoction($info['c_class'], $info['c_no']);
        $users = self::getSignerFromDoc($info);

        if ($users ) {

            foreach ($users as $user) {


                WsContract::addSignerOfSigner($info['c_no'], $user, $info['dossier_id']);
            }

            if(input('lee')==4){

                dump($info);
                dump(WsContract::submitToService($info['c_no']));
                exit;
            }
            WsContract::submitToService($info['c_no']);

            return true;
        }

        return false;


    }

    public static function updateBackUrl($docinfo, $idid)
    {
        return WsContract::changeBackUrl($docinfo['c_no'], $idid, 0, $_SERVER['HTTP_REFERER']);
    }

    public static function getSignerFromDoc($docinfo)
    {
        $signers = array();
        $addzhogncaiwei = false;
        $addzhogncaiwei_sign_role = 0;


        if (in_array($docinfo['doc_model_id'], [Constant::DOC_model_caijueshu, Constant::DOC_model_chehuijuedingshu_zth])) {

            $users = Drole::getUsersByRole($docinfo['dossier_id'], Constant::D_Role_ZhongCaiYuan);


            if ($users) {
                $zhongcaiyuan = $users[0];

                $signers[] = array("c_idid" => $zhongcaiyuan['idid'], "uid" => $zhongcaiyuan['idid'], "u_type" => 1);
            }

            $addzhogncaiwei_sign_role = Constant::D_Role_ZhongCaiWei_LiAnShenPi;
            $addzhogncaiwei = true;
        } else if (in_array($docinfo['doc_model_id'], array_keys(Constant::getZhongCaiWeiSignDocIds()))) {
            $addzhogncaiwei_sign_role = Constant::getZhongCaiWeiSignDocIds()[$docinfo['doc_model_id']];
            $addzhogncaiwei = true;
        } else if (in_array($docinfo['doc_model_id'], [Constant::DOC_model_lianshenpibiao])) {


            $guanliyuans = Drole::getUsersByRole($docinfo['dossier_id'], Constant::D_Role_ZhongCaiWei_GuanLiYuan);
            if ($guanliyuans) {
                $guanliyuan = $guanliyuans[0];

                $signers[] = array("uid" => LoginUser::getIdid(), "u_type" => 1, "c_idid" => LoginUser::getIdid(), "next" => array(
                    "uid" => $guanliyuan['idid'], "u_type" => 1, "c_idid" => $guanliyuan['idid']

                ));
            }


        } else {

            $signers[] = array("uid" => LoginUser::getIdid(), "u_type" => 1, "c_idid" => LoginUser::getIdid());

        }


        if ($addzhogncaiwei) {

            $users = Drole::getUsersByRole($docinfo['dossier_id'], $addzhogncaiwei_sign_role);
            if ($users) {
                $zhongcaiwei = $users[0];

                $jgid = $zhongcaiwei['role_pid'];
                $jigouinfo = Db::name("jigou")->where("id", $jgid)->find();
                $signers[] = array("c_idid" => $jigouinfo['idid'], "uid" => $zhongcaiwei['idid'], "u_type" => 0, "role" => Constant::D_Role_ZhongCaiWei_JiGou, "fa" => array(
                    "uid" => $zhongcaiwei['idid'], "u_type" => 1, "c_idid" => $zhongcaiwei['idid']
                ));
            }

        }

        $out = array();
        Dvalue::delDocValue($docinfo["id"], "signer_code");
        Dvalue::delDocValue($docinfo["id"], "signer_time");
        Db::name("drs")->where("doc_id", $docinfo["id"])->delete();
        foreach ($signers as $uinfo) {
            $uid = $uinfo['c_idid'];
            $s = new Signer($uid, $uinfo['u_type'], 0);
            $s->backurl($_SERVER['HTTP_REFERER']);

            if ($uinfo['u_type'] == 0) {

                $stmp = new Signer($uinfo['fa']['c_idid'], 1);
                $stmp->backurl($_SERVER['HTTP_REFERER']);
                $s->setRole($uinfo['role'], $stmp);
            }

            $data = array("idid" => $uinfo['uid'], "weizhi" => 0, "doc_id" => $docinfo["id"], "c_idid" => $uinfo['c_idid']);
            if (!Db::name("drs")->where($data)->find())
                Db::name("drs")->insertGetId($data);


            Dvalue::addValue($docinfo["id"], "signer_code", $s->getUCode());


            if ($next = $uinfo['next']) {
                $n_s = new Signer($next['c_idid'], $next['u_type'], 0);

                $data = array("idid" => $next['uid'], "weizhi" => 0, "doc_id" => $docinfo["id"], "c_idid" => $next['c_idid']);
                if (!Db::name("drs")->where($data)->find())
                    Db::name("drs")->insertGetId($data);
                Dvalue::addValue($docinfo["id"], "signer_code", $n_s->getUCode());

                $s->addNext($n_s);
            }


            $out[] = $s;


        }
        Dvalue::saveUniqueValue($docinfo["id"], "signer_time", array("y" => date("Y"), "m" => date("m"), "d" => date("d"), "string" => date("Y-m-d")));
        return $out;
    }

    public static function autoSign($docinfo, $idid, $idcode = 0)
    {
        $find = Db::name("drs")->where("doc_id", $docinfo['id'])->where("idid", $idid)->where("ok", 0)->find();

        if (request()->param("test")) {
            var_dump($find);
            var_dump($docinfo['c_no'], $find['c_idid'], $idcode, 2);
        }


        if ($find) {
            WsContract::signContractByCnoAndUid($docinfo['c_no'], $find['c_idid'], $idcode, 2);
        }

    }

    public static function gotoAutoSignUrl($docid, $next_url, $next_ajax = 1)
    {
        return url("wsdoc.sign/signing", ["docid" => $docid, "next_url" => urlencode($next_url), "next_ajax" => $next_ajax]);
    }


    public static function onDocOneUserSign($doc_id, $drs_id)
    {

    }

    public static function onDocSignFinish($d_id, $doc_id, $doc_mod_id, $exid)
    {

        Log::error(func_get_args());
        if ($doc_mod_id == Constant::DOC_model_lianshenpibiao) {
            DocContract::initContract(Ddocs::getOrInitFile($d_id, Constant::DOC_model_tongzhishu, 0)['id']);


        } else if ($doc_mod_id == Constant::DOC_model_tongzhishu) {

            Dmp::doDmp($d_id, "2,13", 0, $msg);


        } else if ($doc_mod_id == Constant::DOC_model_zhidingzhongcaiyuan) {
            DocContract::initContract(Ddocs::getOrInitFile($d_id, Constant::DOC_model_zutingtzs, $exid)['id']);
        } else if ($doc_mod_id == Constant::DOC_model_cxzhidingzhongcaiyuan) {
            DocContract::initContract(Ddocs::getOrInitFile($d_id, Constant::DOC_model_againzuting, $exid)['id']);
        } else if ($doc_mod_id == Constant::DOC_model_zutingtzs) {

            Dmp::doDmp($d_id, Constant::FILE_GROUP_zuting, $exid, $msg);
        } else if ($doc_mod_id == Constant::DOC_model_againzuting) {

            Dmp::doDmp($d_id, Constant::FILE_GROUP_zuting_again, $exid, $msg);



        } else if ($doc_mod_id == Constant::DOC_model_huibi_huifu) {


            Db::name("huibi")->where("id", $exid)->setDec("status",7);
            Dmp::doDmp($d_id, Constant::FILE_GROUP_huibi_huifu, $exid, $msg);

        } 


    }
}