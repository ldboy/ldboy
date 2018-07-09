<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/11
 * Time: 上午8:58
 */

namespace app\admin\controller\wsdoc;


use app\common\controller\Backend;
use think\Db;
use wslibs\cunchuio\CunChuIO;
use wslibs\wscontract\WsContract;
use wslibs\wszc\DocContract;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;

class Sign extends Backend
{
    public function beforeSign()
    {

        $uid = User::getLoginUid();
        if (!$uid) {
            return json(array("ok" => 0, "msg" => "请登录后操作"));
        }
        $docid = $this->request->param("docid/d");

        if (!$docid)
            return json(array("ok" => 0, "msg" => "参数错误"));
        $docinfo = Db::name("dr")->find($docid);


        if (!$docinfo)
            return json(array("ok" => 0, "msg" => "文档不存在"));
        if (!$docinfo['to_sign']) {
            return json(array("ok" => 0, "msg" => "不需要签字"));
        }
//        if (!$docinfo['has_sign']) {
//            return json(array("ok" => 0, "msg" => "已经签字"));
//        }

        if (!$docinfo['c_no']) {
            return json(array("ok" => 0, "msg" => "合同不能签订"));
        }

        if (!WsContract::existContract($docinfo['c_no'])) {


            if (DocContract::initContract($docinfo)) {
                return json(array("ok" => 1, "msg" => "文档正在生成中，请稍等"));
            } else {
                return json(array("ok" => 0, "msg" => "合同生成失败"));
            }
        }
 

        if (WsContract::isCreateContract($docinfo['c_no'])) {
            return json(array("ok" => 1, "msg" => "文件整理,签署中！请稍等..."));
        }


        $auto = $this->request->param("auto/d");

        $signinfo = $this->isCanUserSign($docid, $docinfo['c_no'], $uid);

        if (!$signinfo) {
            return json(array("ok" => 0, "msg" => "您没有权限操作"));
        }
        if ($signinfo['ok'] == 1) {


            if (!$auto)
                return json(array("ok" => 0, "msg" => "您已经签字"));
            else {

                return json(array("ok" => 3, "msg" => "即将成功,请稍等..."));

            }

        }


        if (WsContract::isCanSign($docinfo['c_no'])) {

            $s_id_code = 0;//($info['type']-2==0 ? ContractTools::getCompanyIdCode(1) : ContractTools::getUserIdCode(1,$info['id']) );
            DocContract::updateBackUrl($docinfo, $signinfo['c_idid']);

            if (!$auto)
                return json(array("ok" => 2, "url" => url('contract/gotoSign', ['c_no' => $docinfo['c_no'], 'uid' => $signinfo['c_idid'], 'id_code' => $s_id_code])));
            else {


                DocContract::autoSign($docinfo, $signinfo['idid'], $s_id_code);


                if ($this->request->param("test")) {
                    var_dump($signinfo);
                }

                return json(array("ok" => 3, "msg" => "签字成功,即将进入下一步"));

            }
        }
        return json(array("ok" => 0, "msg" => "位置错误"));
    }

    private function isCanUserSign($docid, $c_no, $uid)
    {
        return Db::name("drs")->where("doc_id", $docid)->where("idid", $uid)->find();

    }


    public function signing()
    {
        $docid = trim($this->request->param("docid"));

        if (!$docid)
            $this->error("参数错误");

        $next_url = urldecode(trim($this->request->param("next_url")));



        if(input('lee')==123){
            echo $next_url;
            exit;
        }



        $next_ajax = $this->request->param("next_ajax/d");
        $this->assign("docid", $docid);
        $this->assign("jsondata", json_encode(['docid' => $docid, "next_url" => $next_url, "next_ajax" => $next_ajax]));

        return $this->fetch();

    }

    public function signimg()
    {

        if ($this->request->isPost()) {
            $content = $this->request->param("img");
//            file_put_contents('img.txt',$content);
//            echo $content;
//            exit;
            $toidid = LoginUser::getIdid();
            $fileimg = "usersignimg/" . $toidid . time() . ".png";

            $ok = CunChuIO::uploadImageContent($fileimg, base64_decode(urldecode($content)), "png");

            if ($ok) {
               
                if (WsContract::signatureChange(IMG_SITE_ROOT . $fileimg, $toidid)) {
                    Db::name("idcards")->where("id", $toidid)->update(['sign_img' => $fileimg]);

                    $this->success("成功", "", ['alert' => 1, 'wsreload' => 2]);
                }

            }
            $this->error("设置失败");
        } else {


            $this->use_action_js();
            return $this->fetch();
        }

    }


}