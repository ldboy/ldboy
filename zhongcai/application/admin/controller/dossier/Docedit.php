<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/6/25
 * Time: 11:32
 */
namespace app\admin\controller\dossier;
use app\common\controller\Backend;
use think\Db;

use wslibs\socketsend\SocketSend;
use wslibs\wszc\Ddocs;
use wslibs\wszc\divedit\DivEdit;
use wslibs\wszc\divedit\DocEditor;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;


class Docedit extends Backend{
    // 这里是通用的 文档编辑
    public function docview()
    {
        $d_id = $this->request->param("id/d");
        $dmid =  $this->request->param("dmid/d");
        $extid = (int)$this->request->param('extid/d');

        $doc = DocEditor::getDoc($d_id,$dmid,$extid);
        $doc_id = $doc['id'];

//        Ddocs::reSign($doc_id);

        $data = DivEdit::getList($doc_id);
        foreach ($data as $k => $v) {
            $data[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
        }

        $dh_content = json_decode(Dvalue::getUniqueValueOfDoc($doc_id, 'dh_content'), true);

        $docurl = url("wsdoc.show/index", ['view_pdf' => 1, 'docid' => $doc_id]);

        $this->assign("view_url", $docurl);
        $this->assign("docid", $doc_id);
        $this->assign("logList", $data);

        $this->use_form_Js();

        $this->assign('did', $d_id);
        $this->assign('title',$doc['name']);

        $this->assign("dh_content", $dh_content);

        $css = Dvalue::getUniqueValueOfDoc($doc_id, "doccssvalue");

        if (!$css) {
            $css = ['letterSpacing' => "0", "lineHeight" => "1.8"];
        }

        $this->assign("css", $css);
        $this->assign("xianyouurl", ($f_file=Dvalue::getUniqueValueOfDoc($doc_id, "ruing_yulan_url"))?(IMG_SITE_ROOT ."sign/gongzheng/".$f_file):"" );
        $this->assign("editcount", json_encode(DivEdit::getEditCount($doc_id)));
        $this->assign("edit_css", LoginUser::isZhongCaiWeiZhuBan()?1:0);
        // 判断什么情况显示样式编辑 什么情况可以编辑
        
        return $this->fetch('docview');
    }

    public function show()
    {
        $d_id = $this->request->param("cid/d");
        $data = Db::name("dre")->where("id", $d_id)->find();
        $data['addtime'] = date("Y-m-d H:i:s", $data['addtime']);
        $this->assign("vo", $data);
        return $this->fetch('docshow');

    }

    public function allshow()
    {

        $div_id = $this->request->param("divid/d");
        $doc_id = $this->request->param("doc_id/d");

        $data = DivEdit::getList($doc_id, null, $div_id ? $div_id : -1);
        foreach ($data as $k => $v) {
            $data[$k]['addtime'] = date("Y-m-d H:i:s", $v['addtime']);
        }
        $this->assign("data", $data);
        return $this->fetch('docallshow');
    }

    public function css()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error("参数错误");
        Dvalue::saveUniqueValue($docid, "doccss", ["letterSpacing" => $this->request->param("zijianjue") . "px", "lineHeight" => $this->request->param("hangjianju")]);
        Dvalue::saveUniqueValue($docid, "doccssvalue", ["letterSpacing" => $this->request->param("zijianjue"), "lineHeight" => $this->request->param("hangjianju")]);
        $this->success("保存成功.", "", array("alert" => 0, "wsreload" => 2, "in_this" => 1));
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
}