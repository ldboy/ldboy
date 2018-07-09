<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/3
 * Time: 上午8:08
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;
use think\Db;
use wslibs\wscontract\WsContract;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\defence\DefenceExpand;
use wslibs\wszc\DInfoValue;
use wslibs\wszc\divedit\DivEdit;
use wslibs\wszc\DocAttr;
use wslibs\wszc\Ds;
use wslibs\wszc\Dvalue;
use wslibs\wszc\LoginUser;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\qx\Qx;
use wslibs\wszc\User;

class Show extends Backend
{

    protected $noNeedLogin = ['index'];
    private $look_uid = 0;

    public function index()
    {


        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error();

        $info = Db::name("dr")->find($docid);


        if ($this->check($info, $out)) {
            //  echo Db::name('dr')->getLastSql();
            $doc_mod_id = $info['doc_model_id'];
            $modeinfo = Ddocs::getDocModeInfo($doc_mod_id, true);
            if (!$modeinfo) $this->error();
            $fun = "show" . $modeinfo['type'] . "_" . $modeinfo['create_type'];

            if(input('show')==234){
                dump($fun);
                echo 'hello world';
            }

            return $this->{$fun}($info, $modeinfo);
        } else {

            if(input('show')==234){
                dump($out);
            }

            return $out;
        }


    }

    private function check($info, &$out)
    {

        if ($this->request->param("m_code") == md5($info['id'] . "renlirong")) {

            $out = $this->showFromMail($info);

            if($out===null){
                $this->look_uid = $info['uid'];
                return true;
            }

            return false;
        }
        if (!$this->auth->isLogin()) {

            if ($this->request->param("pdf_user") == "aksjdflkajdflkajsdflkaddjflksafjlk") {
                // 如果$info['uid'];是银行代理 那么应该用银行的角度看文档
                $drole = Db::name('dossier_roles')
                    ->where('dossier_id',$info['dossier_id'])
                    ->where('idid',$info['uid'])
                    ->find();


                if($drole['role']==3){
                    if($info['doc_model_id']==Constant::DOC_model_gxqyy_jueding){
                        $this->look_uid = Db::name('dossier_roles')
                            ->where('dossier_id',$info['dossier_id'])
                            ->where('role',1)
                            ->value('idid');
                    }else{
                        $this->look_uid = $info['uid'];
                    }



                }else{
                    $this->look_uid = $info['uid'];
                }



//                $this->look_uid = $info['uid'];


                return true;
            } else {
                $this->error("没有权限");
            }
        } else {
            $this->look_uid = User::getLoginUid();
            return true;
        }

        if (!Qx::hasQxInDoc($this->look_uid, $info)) {
            $this->error("沒有權限");
        }
        return false;
    }

    private function showFromMail($info)
    {

        if ($info['to_sign']) {

            return $this->showPdf($info);

        } else if ($info['create_type'] == 1) {
            if ($pdf = Dvalue::getDocPdf($info['id'])){

                $this->redirect(IMG_SITE_ROOT . $pdf);
            }else{
                return null;
            }
        } else {

            $this->assign("list", Dvalue::getDocFiles($info['id'], false));
            return $this->fetch("frommail");
        }

    }

    private function show1_1($info, $modeinfo)
    {

        if (!input("show_html")) {
            if ($info['to_sign']) {
                if ($info['has_sign']) {
                    return $this->showPdf($info);
                }

                if (Db::name('drs')->where("doc_id", $info['id'])->where('ok', 1)->find()) {

                    return $this->showPdf($info);
                }



            } else if ($info['create_type'] == 1) {
                if ($pdf = Dvalue::getDocPdf($info['id']))
                    $this->redirect(IMG_SITE_ROOT . $pdf);
            }
        }
   

        $this->useLayout(false);

        $list = DInfoValue::getTemplateData($info['id'], $this->look_uid);


        $huibireason = Db::name('dr')
            ->alias('r')
            ->join('zc_huibi b','r.exid = b.id','left')
            ->where("r.id",$info['id'])
            ->field("b.value")
            ->find();

        $list['uniquevalue']['he_huibi_r'] = $huibireason['value'];


        if(input('lex')==123123){
            dump($list);
        }

        
        $content = $this->display($modeinfo['view_content'], array("d" => $list));

        $this->assign("body", $content);
        $this->assign("divedit", json_encode(array_map(function ($data) {
            $ps = explode("\n", $data['val1']);
            if (count($ps) == 1) {
                return $data;
            }
            $phtml = '';
            $phtml .= " <p style='display: none' data-yuan='1'>{$data['val1']}</p>";
            foreach ($ps as $p) {
                $phtml .= " <p class=\"kongtwo\">$p</p>";
            }
            $data['val1'] = $phtml;
            return $data;
        }, (Array)DivEdit::getList($info['id'], true))));

        $css = Dvalue::getUniqueValueOfDoc($info['id'], "doccss");

        if (!$css) {

            $css = ['letterSpacing' => "0px", "lineHeight" => "1.8"];


        }
        $this->assign("css", $css);
        return $this->fetch("show11");

    }

    private function showPdf($info)
    {


        if ($this->request->param("view_pdf")) {


            if (!$info['has_sign']) {
                header("Content-type:application/pdf");

                if($_GET['zhz']==123){
                    dump($info);
                    dump(WsContract::getContractPdfUrl($info['c_no']));
                    exit;
                }
                echo file_get_contents(WsContract::getContractPdfUrl($info['c_no']));

                exit;
                // $this->redirect(WsContract::getContractPdfUrl($info['c_no']));
            } else

                $this->redirect(IMG_SITE_ROOT . Dvalue::getDocPdf($info['id']));
            exit;
        } else {


            $list = Dvalue::getDocFiles($info['id'], false);

            if(input('lee')==32){
                dump($list);exit;
            }


            $info['imgs'] = $list;
            $this->assign("docs", array($info));
            $this->use_show_Js();
            return $this->fetch("show_imgs_big");
        }


    }


    private function show1_2($info, $modeinfo)
    {


        $this->useLayout(false);
        $content = $this->display($modeinfo['view_content'], DocAttr::getFormAttr($info['id']));
        $this->assign("content", $content);
        return $this->fetch("show_imgs");
    }

    private function show2_1($info, $modeinfo)
    {
        $list = Dvalue::getDocFiles($info['id']);
        // $info['imgs']=$list;
        $info['imgs'] = $list;
        $this->assign("docs", array($info));
        $this->use_show_Js();
        return $this->fetch("show_imgs");
    }

    private function show2_2($info, $modeinfo)
    {
        return $this->show2_1($info, $modeinfo);
    }


    public function showimgs($ids)
    {
        $docids = explode(",", $ids);
        $list = array_map(function ($value) {
            $value['imgs'] = Dvalue::getDocFiles($value['id']);
            return $value;
        }, (Array)Db::name("dr")->where("id", "in", $docids)->select());

        $this->assign("docs", $list);
        $this->use_show_Js();
        return $this->fetch("show_imgs");
    }


    public function showgroupimgs()
    {
        $did = $this->request->param("id/d");
        $gid = (int)$this->request->param("gid/d");
        if (!$did || !$gid) {
            $this->error("参数错误");
        }

        $exid = (int)$this->request->param("exid/d");
        // 判断是否质证过了

        $questionInfo = QuestionExpand::getMy($did);

        if ($questionInfo && $questionInfo['status'] > 0) {
            if ($questionInfo['status'] == 1) {
                $gid = Constant::FILE_GROUP_zhizheng;
                $this->error('你已经提交过质证，但未提交质证意见，即将跳转', url('dossier.cp/doclist', ['id' => $did, 'gid' => $gid, 'exid' => $questionInfo['id']]));
            } else {
                $this->error('你已经质证过了，在质证申请中可查看详情', url('dossier.info/index', ['id' => $did]));
            }
        }


        // var_dump( Ddocs::getFilesByGroupExist($did, $gid, $exid));
        $list = array_map(function ($value) {
            $value['real_name'] = User::getUserInfoByIdid($value['uid'])['real_name'];
            $value['imgs'] = Dvalue::getDocFiles($value['id'], false);
            return $value;
        }, Ddocs::getFilesByGroup($did, $gid, $exid));

        $zjArr = array_column($list, "id");

        //$role = User::getRoleInDossier($did,LoginUser::getIdid());
        $role = Db::name("dossier_users")->where("dossier_id = '$did' and idid = " . LoginUser::getIdid())->value("role");
        //dump($role);

        $re = QuestionExpand::ManagerQuestion($did, LoginUser::getIdid(), $role, $zjArr);

        if (input('sy') == 1) {
            dump($list);
        }

        $this->assign("docs", $list);
        $this->assign('dossier_id', $did);
        $this->use_show_Js();
        return $this->fetch("show_imgs");
    }


    public function viewquestion()
    {
        $zid = $this->request->param("qid/d");

        if (!$zid) {
            $this->error("参数错误");
        }

        $info = Db::name("dossier_question")->where("id = '$zid'")->find();
        $arr = explode(",", $info['zids']);

        $list = array_map(function ($value) {
            $value['real_name'] = User::getUserInfoByIdid($value['uid'])['real_name'];
            $value['imgs'] = Dvalue::getDocFiles($value['id'], false);
            return $value;
        }, Ddocs::getFilesByDocIds($arr));


        $arr_name = array_column((array)Db::name('dr')->whereIn('id', $arr)->select(), 'name', 'id');

        $_list = Db::name('dossier_question_list')->where("q_id", $zid)->select();

        foreach ($_list as $k => $v) {
            if ($v['legal'] == QuestionExpand::DEFULE_STR && $v['relation'] == QuestionExpand::DEFULE_STR && $v['reality'] == QuestionExpand::DEFULE_STR && $v['other'] == QuestionExpand::DEFULE_STR) {
                unset($_list[$k]);
            }
        }

        $list_ = array_column((array)$_list, 'title', 'evidence_id');

        $str = '';

        if ($_GET['zhz'] == 1) {
            dump($list);

            dump($list_);
            dump($arr_name);
            dump($list);
            //上面的name是div  不能直接取值
            //1、视频资料<span class='label label-warning margin'>证据1</span><div class='margin'>证明事项:努努努</div>
        }

        foreach ($arr_name as $key => $value) {
            if ($list_[$key]) {
                $str .= '对' . $value . '【已质证】<br><br>';
            } else {
                $str .= '对' . $value . '【未发表质证意见】<br><br>';
            }

        }

        $str .= '确定要提交质证意见吗？此操作不可逆！';

        $this->assign("docs", $list);
        $this->assign("str", $str);
        $doc_id = Ddocs::getOrInitFile($info['dossier_id'], Constant::DOC_model_zhizhengyijian, $zid)['id'];
        $proposal = Dvalue::getUniqueValueOfDoc($doc_id, 'question_proposal', $zid);
        $this->assign('proposal', $proposal);
        if (isset($_GET['0607'])) {
            dump($proposal);
        }
        $this->use_show_Js();
        return $this->fetch("show_imgs");
    }
}