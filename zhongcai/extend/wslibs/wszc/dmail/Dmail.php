<?php
namespace wslibs\wszc\dmail;

use app\common\library\Email;
use dossier\DossierDoc;
use think\Db;
use wslibs\wszc\Ddocs;
use wslibs\wszc\notice\Notice;
use wslibs\wszc\publicnumber\NewsModel;


/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/22
 * Time: 下午2:55
 */
class Dmail extends Email
{

    public static function instance($options = [])
    {
        if (is_null(self::$instance)) {
            self::$instance = new static($options);
        }

        return self::$instance;
    }

    public $data = array();
    public $html = "";

    public function addShenqingren($did)
    {
        $list = Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(1, 3))->select();
        foreach ($list as $value) {
//            $value['email'] = '15201634344@163.com';
            $value['email'] && $this->addUser($value['email'], $value['name']);
        }



        return $this;
    }

    public function addBeiShenqingren($did)
    {
        $list = Db::name("dossier_users")->where("dossier_id", $did)->whereIn("role", array(2, 4))->select();
        foreach ($list as $value) {
//            $value['email'] = '15201634344@163.com';
            $value['email'] && $this->addUser($value['email'], $value['name']);
        }


        return $this;
    }

    public function addDangShiRen($did, $e_idids)
    {
        if (!is_array($e_idids)) {
            $e_idids = explode(",", $e_idids);
        }
        $list = Db::name("dossier_users")->where("dossier_id", $did)->select();
        foreach ($list as $value) {
//            $value['email'] = '15201634344@163.com';
            $value['email'] && (!in_array($value['idid'], $e_idids)) && $this->addUser($value['email'], $value['name']);
        }

        return $this;
    }


    public function setHtml($file, $data)
    {

        ob_start();
        extract($data);
        include EXTEND_PATH . "wslibs" . DS . "wszc" . DS . "dmail" . DS . "file" . DS . $file . ".php";
        $this->html = ob_get_contents();
        ob_end_clean();

        $this->message($this->html, true);
        return $this;
    }

    public function setTitle($title)
    {
        return $this->subject($title);
    }

    public function setHtmlTitle($title)
    {
        $this->data['title'] = $title;
        return $this;
    }

    public function setHtmlJingyu($name)
    {
        $this->data['apply_name'] = $name;
        return $this;
    }
    public function setHtmlJingyu2($name)
    {
        $this->data['bei_apply_name'] = $name;
        return $this;
    }

    public function setHtmlContent($content)
    {


        $this->data['content'] = $content;
        return $this;
    }

    public function addDocFile($docid, $name)
    {
//        $url = WEB_DOMAIN_ROOT . url('admin/wsdoc.show/index', 'view_pdf=1&docid=' . $docid . "&m_code=" . md5($docid . "renlirong"), '');
        $url = 'http://zcw.wszx.cc/admin/wsdoc.show/index?view_pdf=1&docid=' . $docid . "&m_code=" . md5($docid . "renlirong");

        $this->data['files'][] = array("url" => $url, "name" => $name);

        return $this;
    }

    public function sendTemplate($file)
    {

        $this->setHtml($file, $this->data);




        $res = $this->send();
        if(!$res){
            $str = '';
            foreach($this->tousers as $key=>$value){
                $str .= $value['to_name'].':'.$value['to'];
            }
            Notice::sendSms('18332051721','邮箱发送失败原因:'.$this->getError().'人员:'.$str);
            (new NewsModel(0))->TaskFailureReminding(2,'oMW-u0SiYggyLWs-Gs_F25kJ7nCk','邮箱发送失败','邮箱',$str,'看下面',date('Y-m-d H:i:s',time()),$this->getError());
        }
    }
    //ok
    public function sendShouLiWenjianToShenQingRen($did)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>立案材料")->setHtmlJingyu('申请人:'.$apply) ;

        $this->setHtmlContent("你与被申请人".$bei_apply."之间的".$dossier_info['title']."纠纷一案已受理。案号为".$znoStr.",被申请人".$bei_apply."现依法向你送达，".$znoStr."案的《仲裁通知书》（见附件）、《仲裁员名册》（见附件）和《仲裁规则》（见附件），请及时查阅。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, 2);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->addShenqingren($did);

        $this->sendTemplate("t1");
    }

    //ok
    public function sendShouLiWenjianToBeiShenQingRen($did)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr.'案<br>立案材料')->setHtmlJingyu('被申请人:'.$bei_apply);//->setHtmlJingyu('申请人:'.$apply)

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title'].",申请人".$apply."纠纷一案。现依法向你送达该案的《仲裁通知书》（见附件）、《仲裁员名册》（见附件）、《仲裁规则》（见附件）、《仲裁申请书》（见附件）、申请人证据材料（见附件）。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, 13);
        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->addBeiShenqingren($did);

        $this->sendTemplate("t1");
    }




    public function sendZhengjuZhuanfa($did,$gid,$ywid)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr.'案<br>证据材料')->setHtmlJingyu('被申请人'.$bei_apply.':');

        $dz_info = Db::name('dz')->where("id",$ywid)->find();
        $dossier_users = Db::name('dossier_users')->where('idid',$dz_info['idid'])->find();

        if($dossier_users['role']==1 || $dossier_users['role']==3){
            $people = '申请人';
            $this->setHtmlJingyu($people.':'.$apply);

        }else{
            $people = '被申请人';
            $this->setHtmlJingyu('申请人:'.$apply);
            $this->setHtmlJingyu2($people.':'.$bei_apply);
        }
        $faqiren = $people.$dossier_users['name'] ;

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案，".$faqiren."于".date('Y年m月d日',$dz_info['addtime'])."提交的证据材料（见附件）。现依法向你送达。".$this->getLink());

        $list_shu = Ddocs::getFilesByGroup($did, $gid,$ywid);

        $list = $list_shu;
        $this->addDangShiRen($did,$dz_info['idid']);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->sendTemplate("t1");
    }

    public function sendDaBianZhengJu($did,$gid,$ywid)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr.'案<br>答辩意见');

        $dz_info = Db::name('dossier_defence')->where("id",$ywid)->find();

        $dossier_users = Db::name('dossier_users')->where('idid',$dz_info['idid'])->find();

        if($dossier_users['role']==1 || $dossier_users['role']==3){
            $people = '申请人';
            $this->setHtmlJingyu('申请人:'.$apply);
        }else{
            $people = '被申请人';
            $this->setHtmlJingyu('申请人:'.$apply);
            $this->setHtmlJingyu2('被申请人:'.$bei_apply);
        }

        $faqiren = $people.$dossier_users['name'];

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."借款合同纠纷一案,".$faqiren."于".date('Y年m月d日',$dz_info['addtime'])."提交《答辩意见》（见附件）。现依法向你送达。".$this->getLink());

        $list = Ddocs::getFilesByDocIds($dz_info['zids']);

        $list_shu = Ddocs::getFilesByGroup($did, $gid,$ywid);

        $list = array_merge($list,$list_shu);

        $this->addDangShiRen($did,$dz_info['idid']);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->sendTemplate("t1");
    }
    public function sendQuestionZhengJu($did,$gid,$ywid)
    {


        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>质证意见");

        $dz_info = Db::name('dossier_question')->where("id",$ywid)->find();

        $dossier_users = Db::name('dossier_users')->where('idid',$dz_info['idid'])->find();

        if($dossier_users['role']==1 || $dossier_users['role']==3){
            $people = '申请人:';
            $this->setHtmlJingyu($people.$apply);

        }else{
            $people = '被申请人';
            $this->setHtmlJingyu('申请人:'.$apply);
            $this->setHtmlJingyu2($people.':'.$bei_apply);
        }

        $faqiren = $people.$dossier_users['name'] ;

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案,".$faqiren."于".date('Y年m月d日',$dz_info['addtime'])."提交《质证意见》（见附件），现依法向你（们）送达。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, $gid,$ywid);

        $this->addDangShiRen($did,$dz_info['idid']);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->sendTemplate("t1");

    }


    public function sendQuestionZhengJu1($did,$gid,$ywid)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>质证意见");
        $dz_info = Db::name('dossier_question')->where("id",$ywid)->find();

        $dossier_users = Db::name('dossier_users')->where('idid',$dz_info['idid'])->find();

        if($dossier_users['role']==1 || $dossier_users['role']==3){
            $people = '申请人:';
            $this->setHtmlJingyu($people.$apply);

        }else{
            $people = '被申请人';
            $this->setHtmlJingyu('申请人:'.$apply);
            $this->setHtmlJingyu2($people.':'.$bei_apply);
        }

        $faqiren = $people.$dossier_users['name'] ;

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案,".$faqiren."于".date('Y年m月d日',$dz_info['addtime'])."提交《质证意见》（见附件），现依法向你（们）送达。".$this->getLink());

        $list = Ddocs::getFilesByDocIds($dz_info['zids']);

        $this->addDangShiRen($did,$dz_info['idid']);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }


        $this->sendTemplate("t1");
    }



    public function sendZuTingAllPersons($did,$gid=4,$ext_id,$is_chongxin)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        if($is_chongxin){
            $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>重新组庭通知书");
            $zuting = '重新组庭通知书';
        }else{
            $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>组庭通知书");
            $zuting = '组庭通知书';
        }
        $this->setHtmlJingyu('申请人:'.$apply);
        $this->setHtmlJingyu2('被申请人:'.$bei_apply);

        $this->setHtmlContent(" 石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."借款合同纠纷一案，现依法向你（们）送达《".$zuting."》（见附件）。 ".$this->getLink() );

        $list = Ddocs::getFilesByGroup($did, $gid,$ext_id);
        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }
        $this->addShenqingren($did);
        $this->addBeiShenqingren($did);

        $this->sendTemplate("t1");
    }

    public function sendShengmingAllPersons($did,$gid,$ext_id)
    {

        $huibi = Db::name('huibi')->where("id",$ext_id)->where("is_valid=1")->find();

        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        if($huibi['type']==1){
            $leixing = '披露';
        }elseif($huibi['type']==2){
            $leixing = '回避';
        }elseif($huibi['type']==3){
            $leixing = '声明';
        }

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>".$leixing."决定书");

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案，现依法向你（们）送达".$znoStr."决定书（见附件）。".$this->getLink());

        $this->setHtmlJingyu('申请人:'.$apply);

        $this->setHtmlJingyu2('被申请人:'.$bei_apply);



        $list = Ddocs::getFilesByGroup($did, $gid,$ext_id);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }
        $this->addDangShiRen($did,$huibi['idid']);

        $this->sendTemplate("t1");
    }

    public function sendCaiJueAllPeoples($did,$gid,$ext_id)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>裁决书");

        $this->setHtmlJingyu('申请人:'.$apply);

        $this->setHtmlJingyu2('被申请人:'.$bei_apply);

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案，仲裁庭已依法审理完毕，现依法向你（们）送达".$znoStr."裁决书（见附件）。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, $gid,$ext_id);

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->addShenqingren($did);
        $this->addBeiShenqingren($did);

        $this->sendTemplate("t1");
    }


    public function sendGuanXiaQuanAllPeoples($did,$gid,$ext_id)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>管辖权异议决定书");

        $this->setHtmlJingyu('申请人:'.$apply);

        $this->setHtmlJingyu2('被申请人:'.$bei_apply);

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案，仲裁庭已依法审理完毕，现依法向你（们）送达".$znoStr."管辖权异议决定书（见附件）。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, $gid,$ext_id);
        //管辖权   那个表呢
        $gxq_info = Db::name('gxq_yy')->where("id",$ext_id)->find();

        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }

        $this->addDangShiRen($did,$gxq_info['idid']);

        $this->sendTemplate("t1");
    }

    public function sendCheHuiApplyAllPeoples($did,$gid,$ext_id)
    {
        list($dossier_info,$znoStr,$apply,$bei_apply) = $this->func_data($did);

        $dz_info = Db::name('dossier_cancel')->where("id",$ext_id)->find();

        $this->setTitle("来自石家庄仲裁委员会的邮件")->setHtmlTitle($znoStr."案<br>撤回决定书");

        $this->setHtmlJingyu('申请人:'.$apply);

        $this->setHtmlJingyu2('被申请人:'.$bei_apply);

        $this->setHtmlContent("石家庄仲裁委员会（以下简称本会）受理的".$znoStr.$dossier_info['title']."纠纷一案，申请人".$apply."于".date('Y年m月d日',$dz_info['addtime'])."提交《撤回决定书》（见附件）。现依法向你送达。".$this->getLink());

        $list = Ddocs::getFilesByGroup($did, $gid,$ext_id);

        if($_GET['zhz']==1){
            dump($list);
            exit;
        }


        foreach ($list as $value) {
            $this->addDocFile($value['id'], $value['name']);
        }
        $this->addShenqingren($did);
        $this->addBeiShenqingren($did);

        $this->sendTemplate("t1");
    }



    private function getDossierName($did)
    {
        return Db::name('dossier')->where('id',$did)->find();
    }

    public function getLink()
    {
        return "你可登陆<a href='http://zc.wszx.cc' style='color:#3c8dbc ' target='_blank'>申请人</a>、<a href='http://zc.wszx.cc/zclogin-dsrindex' style='color:#3c8dbc ' target='_blank'>被申请人</a> 进行相关操作。（联系电话：0311-86687359）";
    }


    public function getCaseNumberStr($zno,$statusStr='')
    {
        return DossierDoc::getZcNoByNo($zno).$statusStr;
    }

    public function func_data($did)
    {
        $dossier_info = $this->getDossierName($did);

        $znoStr = $this->getCaseNumberStr($dossier_info['zno']);

        list($apply,$bei_apply) = (new NewsModel($did))->getDangShiRen($did);

        return [$dossier_info,$znoStr,$apply,$bei_apply];
    }
}