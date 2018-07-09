<?php
namespace app\admin\controller\wsdoc;

use app\common\controller\Backend;
use EasyWeChat\Support\Log;
use think\Db;
use wslibs\wszc\DocAttr;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/2
 * Time: ÉÏÎç11:10
 */
class Attr extends Backend
{
    public function index()
    {
        $docid = $this->request->param("docid/d");
        if (!$docid) $this->error();



        $info = Db::name("dr")->find($docid);

        $doc_mod_id = $info['doc_model_id'];


        if ($this->request->isPost()) {

            $post = $this->request->param("row/a");


            DocAttr::editFormAttr($docid, $post);
            $this->success();

        } else {


            $attrlist = DocAttr::getFormAttr($docid);

            $form = DocAttr::getAttrForm($doc_mod_id, $attrlist['__ARGS__']);



            $this->assign("row", $attrlist);

            return $form->display($this);
        }


    }
}