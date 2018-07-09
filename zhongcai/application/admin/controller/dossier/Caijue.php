<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/26
 * Time: 下午2:52
 */

namespace app\admin\controller\dossier;

use app\common\controller\Backend;
use EasyWeChat\Staff\Session;
use think\Db;
use wslibs\socketsend\SocketSend;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\divedit\DivEdit;
use wslibs\wszc\DocContract;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;

class Caijue extends Backend
{
    protected $noNeedLogin = ['yulanok'];

    public function css()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error("参数错误");
        Dvalue::saveUniqueValue($docid, "doccss", ["letterSpacing" => $this->request->param("zijianjue") . "px", "lineHeight" => $this->request->param("hangjianju")]);
        Dvalue::saveUniqueValue($docid, "doccssvalue", ["letterSpacing" => $this->request->param("zijianjue"), "lineHeight" => $this->request->param("hangjianju")]);
        $this->success("保存成功.", "", array("alert" => 0, "wsreload" => 2, "in_this" => 1));
    }

    public function yulanok()
    {
        $docid = $this->request->param("docid/d");
        Dvalue::saveUniqueValue($docid, "ruing_tag", 2);
        echo "success";
        exit;
    }

    public function yulan()
    {
        $docid = $this->request->param("docid/d");

        $cjsurl = url("wsdoc.show/index", ['view_pdf' => 1, 'docid' => $docid]);
        $name = "yulan/".$docid . "_" . time() . ".pdf";
        Dvalue::saveUniqueValue($docid, "ruing_tag", 1);
        Dvalue::saveUniqueValue($docid, "ruing_yulan_url", $name);
        SocketSend::html2pictureBackUrl(WEB_DOMAIN_ROOT.$cjsurl."&pdf_user=aksjdflkajdflkajsdflkaddjflksafjlk", $name, WEB_DOMAIN_ROOT.url("dossier.caijue/yulanok", ['docid' => $docid]));
        $this->success("成功");

    }

    public function yulancheck()
    {


        $docid = $this->request->param("docid/d");
        $tag = Dvalue::getUniqueValueOfDoc($docid, "ruing_tag");
        if ($tag == 2) {
            return json(['ok' => 2, 'url' => IMG_SITE_ROOT ."sign/gongzheng/". Dvalue::getUniqueValueOfDoc($docid, "ruing_yulan_url")]);
        } else if ($tag == 1) {
            return json(['ok' => 1]);
        }
        return json(['ok' => 0]);
    }

    public function divedit()
    {
        $docid = $this->request->param("docid/d");
        $divid = $this->request->param("divid/d");
        $val0 = $this->request->param("val0");
        $val1 = $this->request->param("val1");
        $divtitle = $this->request->param("divtitle");

        $info = Ddocs::getDocInfo($docid);
        if ($val1) {
//            var_dump($docid);
//            var_dump($divid);
//            var_dump($val0);
//            var_dump($val1);
            $rid = DivEdit::add($docid, LoginUser::getIdid(), LoginUser::getUserName(), $divtitle, \wslibs\wszc\User::getRoleInDossier($info['dossier_id'], LoginUser::getIdid()), $divid, $val0, $val1);
            if ($rid) {


                Db::name("dossier_caijue")->where("id", $info['dossier_id'])->update(['c_edit' => 1]);


                $this->success("保存成功.", "", array("alert" => 0, "wsreload" => 2, "in_this" => 1));

            }
        } else {
            $this->use_form_Js();
            $this->assign("val0", $val0);
            return $this->fetch();
        }

    }

    public function caijueview()
    {
        $d_id = $this->request->param("id/d");
        $doc_id = Db::name("dr")->where("dossier_id = '$d_id' and doc_model_id = " . Constant::DOC_model_caijueshu)->value("id");

        $caijueinfo = Dcaijue::getCaiJueInfo($d_id);

        $data = DivEdit::getList($doc_id);
        foreach ($data as $k => $v) {
            $data[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
        }


//        if ($info['c_status'] > 4) {
//            if ($info['c_status'] == 5)
//                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue_fasongzhizhuren, "exid" => 0]));
//            if ($info['c_status'] == 6)
//                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue_shenpi . "," . Constant::FILE_GROUP_caijue_fasongsuoyouren, "exid" => 0]));
//        }


        $caijueshu = Dcaijue::getCaiJueShu($d_id);

        $dh_content = json_decode(Dvalue::getUniqueValueOfDoc($doc_id, 'dh_content'), true);

        if (input('lee') == 23) {
            dump($dh_content);
        }

        $cjsurl = url("wsdoc.show/index", ['view_pdf' => 1, 'docid' => $caijueshu['id']]);
        $doc_id = $caijueshu['id'];
        $this->assign("view_url", $cjsurl);
        $this->assign("docid", $doc_id);
        $this->assign("logList", $data);

        $this->use_form_Js();

        $this->assign('did', $d_id);
        list($btn, $btn1, $btn2, $btn3, $btn4) = Dcaijue::getLoginUserBtn($d_id);
        $this->assign("btn1", $btn1);
        $this->assign("btn2", $btn2);
        $this->assign("btn3", $btn3);
        $this->assign("btn4", $btn4);
        $this->assign("cjsurl", $cjsurl);
        $this->assign("dh_content", $dh_content);

        $css = Dvalue::getUniqueValueOfDoc($doc_id, "doccssvalue");

        if (!$css) {

            $css = ['letterSpacing' => "0", "lineHeight" => "1.8"];


        }


        $this->assign("css", $css);
        $this->assign("xianyouurl", ($f_file=Dvalue::getUniqueValueOfDoc($doc_id, "ruing_yulan_url"))?(IMG_SITE_ROOT ."sign/gongzheng/".$f_file):"" );
        $this->assign("editcount", json_encode(DivEdit::getEditCount($doc_id)));
        $this->assign("edit_css", LoginUser::isZhongCaiWeiZhuBan() && $caijueinfo['c_status'] == 2 ? 1 : 0);
        return $this->fetch();

    }



    public function show()
    {
        $d_id = $this->request->param("cid/d");
        $data = Db::name("dre")->where("id", $d_id)->find();
        $data['addtime'] = date("Y-m-d H:i:s", $data['addtime']);
        $this->assign("vo", $data);
        return $this->fetch('caijueshow');

    }

    public function allshow()
    {
        $d_id = $this->request->param("id/d");
        $div_id = $this->request->param("divid/d");

//        var_dump($d_id);

        $info = Dcaijue::getCaiJueInfo($d_id);
        $doc_id = Db::name("dr")->where("dossier_id = '$d_id' and doc_model_id = " . Constant::DOC_model_caijueshu)->value("id");

        $data = DivEdit::getList($doc_id, null, $div_id ? $div_id : -1);
        foreach ($data as $k => $v) {
            $data[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
        }
        $this->assign("data", $data);
        return $this->fetch('caijueallshow');
    }

    public function CaiJue()
    {
        $d_id = $this->request->param("id/d");

        $info = Dcaijue::getCaiJueInfo($d_id);
        if ($info['c_status'] > 4) {
            if ($info['c_status'] == 5)
                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue_fasongzhizhuren, "exid" => 0]));
//            if ($info['c_status'] == 6)
//                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" =>  Constant::FILE_GROUP_caijue_fasongsuoyouren, "exid" => 0]));
        }

        if ($this->request->isPost()) {
            $dataPost = $this->request->Post("row/a");

            Dcaijue::updateNeirong($d_id, $dataPost);

            $this->success("保存成功.", url('dossier.cp/caijue', ['id' => $d_id]), ['alert' => 1]);

        } else {


            $this->assign('row', Dcaijue::getNeirong($d_id));
            $this->assign('did', $d_id);
            list($btn, $btn1, $btn2) = Dcaijue::getLoginUserBtn($d_id);
            $this->assign("btn1", $btn1);
            $this->assign("btn2", $btn2);
            $this->assign("cjsurl", Dcaijue::getCaiJueShuUrl($d_id));
            if (input('wl') == 11) {
                dump(Dcaijue::getCaiJueShuUrl($d_id));
            }

            $this->use_form_Js();
            return $this->fetch("caijue");
        }
    }


    public function dahui()
    {
        $d_id = $this->request->param("id/d");
        $msg = $this->request->param('msg/s');

//        saveUniqueValueByDocMode
        $info = Dcaijue::getCaiJueInfo($d_id);
        if (!in_array($info['c_status'], array(2, 4))) {
            $this->error();
        } else {

            $doc_id = Db::name("dr")->where("dossier_id = '$d_id' and doc_model_id = " . Constant::DOC_model_caijueshu)->value("id");

            $dh_content = json_decode(Dvalue::getUniqueValueOfDoc($doc_id, 'dh_content'), true);


            if (!$dh_content) {
                $dh_content = [];
            }


            $dh_content[] = $msg;
            Dvalue::saveUniqueValueByDocMode($d_id, 28, 'dh_content', json_encode($dh_content));

            Dcaijue::dahui($d_id);
            $this->success("操作成功", "", ['alert' => 1, "wsreload" => 1]);
        }

    }


    // 发送至仲裁委主办
    public function caijuesend()
    {
        $d_id = $this->request->param("id/d");
        $info = Dcaijue::getCaiJueInfo($d_id);

        if ($info['c_status'] > 2) {
            if ($info['c_status'] == 3) {
                $rurl = DocContract::gotoAutoSignUrl(Ddocs::getOrInitFile($d_id, Constant::DOC_model_caijueshu, 0)['id'], Constant::mkDmpUrl($d_id, Constant::FILE_GROUP_caijue, 0));
                $this->redirect($rurl);
            }

            //$this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" => Constant::FILE_GROUP_caijue, "exid" => 0]));
//            if ($info['c_status'] == 6)
//                $this->redirect(url("dossier.cp/doclist", ['id' => $d_id, "gid" =>  Constant::FILE_GROUP_caijue_fasongsuoyouren, "exid" => 0]));
//
        }

//        if ((!$this->request->isPost())) {
//
//            if ($info['c_status'] == 2  ) {
//                $this->use_form_Js();
//                return $this->fetch("caijuesend" . $info['c_status']);
//            }
//        }
//
//        if ($this->request->isPost()) {
//            $dataPost = $this->request->param("row/a");
//            $key = "shenpi_status_" . $info['c_status'];
//            Dvalue::saveUniqueValueByDocMode($d_id, Constant::DOC_model_caijueshenpi, $key, $dataPost['value']);
//            if ($info['c_status'] == 3) {
//
//
//                if (isset($dataPost['ok']) && !$dataPost['ok']) {
//
//
//                    Dcaijue::updateStatus($d_id, 2);
//                    $this->success("操作成功", '',['alert'=>1,'wsreload'=>2]);
//
//
//                }
//            }
//        }


        $res = Dcaijue::autoSend($d_id);
//        if ($res) {
//            $this->success("操作成功", url("dossier.info/index", array("id" => $d_id)), ['alert' => 1, 'wsreload' => 2]);
//        } else {
//            $this->error("操作失败", url("dossier.info/index", array("id" => $d_id)));
//        }


        if ($res) {
            $this->success("操作成功", "", ['alert' => 1, 'wsreload' => 2]);
        } else {
            $this->error("操作失败", "");
        }
    }
}