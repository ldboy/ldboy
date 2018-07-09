<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/31
 * Time: 下午6:34
 */

namespace wslibs\wszc\publicnumber;


use dossier\DossierDoc;
use think\Db;
use wslibs\wszc\Dossier;
use wslibs\wszc\notice\Notice;

class NewsModel
{

    private $dossier_id = 0;

    public $_err_code = 0;

    public function __construct($dossier_id)
    {
        $this->dossier_id = $dossier_id;
    }

    //组庭通知
    public  function zhongCaizutingTongZhi($idid,$openid,$url,$content='您好,您收到一份仲裁案件的组庭通知')
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_ZUTING_TONGZHI;

        $data = [];
        $dossier_info = Dossier::getSimpleDossier($this->dossier_id);
        $dossier_info['addtime'] = self::getTime30($this->dossier_id);
        list($apply,$bei_apply) = $this->getDangShiRen();

        $zhongcaiyuan = Db::name('arbitrator')->where('dossier_id',$this->dossier_id)->where('status',1)->value('name');

        $data['first'] = self::getList($content."\n",'#173177');
        $data['keyword1'] = self::getList($apply,'#173177');
        $data['keyword2'] = self::getList($bei_apply,'#173177');
        $data['keyword3'] = self::getList(DossierDoc::getZcNoByNo($dossier_info['zno'],$dossier_info['addtime']),'#173177');
        $data['keyword4'] = self::getList($zhongcaiyuan?$zhongcaiyuan."\n":'郭淑娇'."\n",'#173177');
        $data['remark'] = self::getList('如有疑问，请咨询0311-86687359。','#173177');

        return self::sendMsg($idid,$openid,$url,$template_id,$data,BaseVar::$MSG_TYPE_ZHONGCAI_ZUTING);
    }

    //仲裁通知
    public function zhongCaiTongZhi($idid,$openid,$url,$content='您好，您收到一份仲裁案件的仲裁通知。')
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_TONGZHI;

        $dossier_info = Dossier::getSimpleDossier($this->dossier_id);
        $dossier_info['addtime'] = self::getTime30($this->dossier_id);
        $data = [];
        list($apply,$bei_apply) = $this->getDangShiRen();

        $data['first'] = self::getList($content."\n",'#173177');
        $data['keyword1'] = self::getList($apply,'#173177');
        $data['keyword2'] = self::getList($bei_apply,'#173177');
        $data['keyword3'] = self::getList(DossierDoc::getZcNoByNo($dossier_info['zno'],$dossier_info['addtime'])."\n",'#173177');
        $data['remark'] = self::getList('如有疑问，请咨询0311-86687359。','#173177');

        return self::sendMsg($idid,$openid,$url,$template_id,$data,BaseVar::$MSG_TYPE_ZHONGCAI);
    }

    //绑定状态通知
    public function zhongCaiBindTongZhi($openid,$url)
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_BINDUSER_TONGZHI;

        $data = [];
        $info = $this->getUserInfoByOpenid($openid);
        if($info['bind_status']!=2){
            $data['first'] = self::getList("您好，您的智慧仲裁账号绑定成功 \n",'#173177');
        }else{
            $data['first'] = self::getList("您好，您的智慧仲裁账号还未绑定 \n",'#ff0000');
        }

        $data['keyword1'] = self::getList($info['real_name'],'#173177');
        $data['keyword2'] = self::getList($info['bind'],'#173177');
        $data['keyword3'] = self::getList($info['openid_time'],'#173177');
        $data['remark'] = self::getList('感谢您使用智慧仲裁微信服务！如有疑问，请咨询0311-86687359。','#173177');


        if($info['bind_status']!=2){
            $url = BaseVar::$info_url.'?openid='.$openid;
        }

        self::sendBindMsg($openid,$url,$template_id,$data);
    }

    //任务失败提醒
    public function TaskFailureReminding($idid,$openid,$task_name,$type_name,$username,$reason,$time,$remark)
    {
        if(!$remark){
            $remark = '出错原因：服务器IP的发信频率超过邮箱限制。邮箱对来自相同IP的外部发信服务器有一定的频率限制';
        }

        $template_id = BaseVar::$TEMPLATE_ID_TASK_FAIL_TONGZHI;

        $data = [];

        $data['first'] = self::getList('您收到一份任务失败提醒。'."\n",'#ff0000');
        $data['keyword1'] = self::getList($task_name,'#ff0000');
        $data['keyword2'] = self::getList($type_name,'#ff0000');
        $data['keyword3'] = self::getList($username,'#ff0000');
        $data['keyword4'] = self::getList($reason,'#ff0000');
        $data['keyword5'] = self::getList($time."\n",'#ff0000');
        $data['remark'] = self::getList($remark,'#173177');

        return self::sendMsg($idid,$openid,'',$template_id,$data,BaseVar::$MSG_TYPE_TASK_FAIL);

    }

    //裁决书认领通知   已废弃
    public function zhongcaiCaiJueRenLingTongZhi($idid,$openid,$url,$name,$content='您好，您收到一份仲裁案件的裁决通知。')
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_CAIJUE_RENLING_TONGZHI;

        $dossier_info = Dossier::getSimpleDossier($this->dossier_id);
        $dossier_info['addtime'] = self::getTime30($this->dossier_id);
        $data = [];

        $data['first'] = self::getList('您好，您收到一份仲裁案件的裁决通知。'."\n",'#173177');
        $data['keyword1'] = self::getList(DossierDoc::getZcNoByNo($dossier_info['zno'],$dossier_info['addtime']),'#ff0000');
        $data['keyword2'] = self::getList($name."\n",'#ff0000');
        $data['remark'] = self::getList($content.'注：请在工作日内领取。如有疑问，请咨询0311-86687359。','#173177');

        return self::sendMsg($idid,$openid,$url,$template_id,$data,1);

    }



    //裁决书通知
    public function zhongcaiCaiJueShuTongZhi($idid,$openid,$url,$content='您好，您收到一份仲裁案件的裁决书通知。')
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_CAIJUE_TONGZHI;

        $dossier_info = Dossier::getSimpleDossier($this->dossier_id);
        $dossier_info['addtime'] = self::getTime30($this->dossier_id);
        $data = [];

        list($apply,$bei_apply) = $this->getDangShiRen();

        $data['first'] = self::getList($content."\n",'#173177');
        $data['keyword1'] = self::getList(DossierDoc::getZcNoByNo($dossier_info['zno'],$dossier_info['addtime']),'#173177');
        $data['keyword2'] = self::getList($apply,'#173177');
        $data['keyword3'] = self::getList($bei_apply."\n",'#173177');

        $data['remark'] = self::getList('注：请在工作日内领取。如有疑问，请咨询0311-86687359。','#173177');

        return self::sendMsg($idid,$openid,$url,$template_id,$data,1);
    }

    //待办事项通知
    public function zhongCaiDaiBanTongZhi($idid,$openid,$url,$content1,$content2)
    {
        $template_id = BaseVar::$TEMPLATE_ID_ZHONGCAI_DAIBAN_TONGZHI;

        $data = [];

        $data['first'] = self::getList("您好，您有新的待办。\n",'#173177');
        $data['keyword1'] = self::getList($content1,'#173177');
        $data['keyword2'] = self::getList($content2."\n",'#173177');
        $data['remark'] = self::getList('请及时处理。','#173177');

        return self::sendMsg($idid,$openid,$url,$template_id,$data,1);

    }

    public function common_model($openid,$url,$template_id,$data,$idid=0,$xicehngxu_appid='',$pagepath='')
    {

        if(empty($url) && $template_id!=BaseVar::$TEMPLATE_ID_TASK_FAIL_TONGZHI){
            $url = 'http://zcw.wszx.cc/admin/wechat/myinfo/daohang?did='.$this->dossier_id.'&idid='.$idid;
        }
        $data=[
            'touser'=>$openid,//'oMW-u0SiYggyLWs-Gs_F25kJ7nCk'
            'template_id'=>$template_id,
            'url'=>$url,
            'miniprogram'=>array(
                'appid'=>$xicehngxu_appid,
                'pagepath'=>$pagepath,
            ),
            'topcolor'=>"#FF0000",
            'data'=>$data,
        ];

        return $data;
    }

    public function sendBindMsg($openid,$url,$template_id,$data)
    {
        $res = self::curl_post_send_information(json_encode($this->common_model($openid,$url,$template_id,$data)));

        $data = json_decode($res,true);

        if(json_last_error()==JSON_ERROR_NONE){

            if($data['errcode']!=0){
                Notice::sendSms('15201634344','openid:'.$openid.'绑定失败');
            }



        }

    }

    public function sendMsg($idid,$openid,$url,$template_id,$data,$type)
    {
        $res = self::curl_post_send_information(json_encode($this->common_model($openid,$url,$template_id,$data,$idid)));

        if(!$res){
            $this->_err_code = -1;
            return false;
        }

        $data = json_decode($res,true);

        if(json_last_error()==JSON_ERROR_NONE){

            if($data['errcode']!=0){
                $this->_err_code = $data['errcode'];
                return false;
            }
            self::addRecord($idid,$type,BaseVar::GANG_FA_SONG,$openid,$data['msgid']);

            $this->_err_code = 1;
            return true;
        }

        self::addRecord($idid,$type,BaseVar::FA_SONG_FAIL,$openid,'');

        $this->_err_code = -22;
        return false;
    }


    public function addRecord($idid,$type,$status,$openid,$msgid)
    {
        $arr['dossier_id'] = $this->dossier_id;
        $arr['openid'] = $openid;
        $arr['idid'] = $idid;
        $arr['addtime'] = time();
        $arr['callback_time'] = 0;
        $arr['status'] = $status;
        $arr['type'] = $type;
        $arr['msgid'] = $msgid;

        return Db::name('msg_record')->insert($arr);
    }

    public function updateRecord($msgid,$status,$CreateTime,$data=array())
    {

        $arr['callback_time'] = $CreateTime?$CreateTime:time();
        $arr['status'] = $status;

        return Db::name('msg_record')->where('msgid',$msgid)->update($arr);
    }

    public function getDangShiRen($dossier_id=null)
    {
        if($dossier_id==null){
            $dossier_id = $this->dossier_id;
        }

        $users = \wslibs\wszc\Dossier::getDangShiRen($dossier_id, 0);
        foreach($users as $key=>$value){
            if($value['role']==3 || $value['role']==4 || $value['role']==19){
                unset($users[$key]);
            }
        }
        $dangshiren = array_column($users, 'name');

        $apply_peo = $dangshiren[0];

        unset($dangshiren[0]);

        $bei_apply_peo = implode('、', $dangshiren);

        return [$apply_peo,$bei_apply_peo];
    }


    public static function curl_post_send_information($vars,$second=120,$aHeader=array())
    {
        $ch = curl_init();
        //超时时间
        curl_setopt($ch,CURLOPT_TIMEOUT,$second);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
        //这里设置代理，如果有的话
        curl_setopt($ch,CURLOPT_URL,'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.Config::getDbAccessToken());
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
        if( count($aHeader) >= 1 ){
            curl_setopt($ch, CURLOPT_HTTPHEADER, $aHeader);
        }
        curl_setopt($ch,CURLOPT_POST, 1);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$vars);
        $data = curl_exec($ch);
        if($data){
            curl_close($ch);
            return $data;
        }
        else {
            $error = curl_errno($ch);
            curl_close($ch);
            return $error;
        }
    }
    public static function getUserInfoByOpenid($openid)
    {

        $info = Db::name('idcards')->where("openid='$openid'")->find();

        if($info){
            $data['bind'] = '已绑定';
            $data['bind_status'] = 1;
            $data['openid_time'] = date('Y年m月d日',$info['openid_time'])."\n";
            $data['real_name'] = $info['real_name'];
        }else{
            $data['bind'] = '未绑定';
            $data['bind_status'] = 2;
            $data['openid_time'] = '---'."\n";
            $data['real_name'] = '---';
        }
        return $data;
    }

    public static function getList($value,$color)
    {
        return ['value'=>$value,'color'=>$color];
    }


    public static function getTime30($did)
    {
        return Db::name('dossier_time')->where("id",$did)->value('time30');
    }
}