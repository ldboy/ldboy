<?php

/*
 *     功能：发送邮件内容类，调用SendMail 函数，带入参数发送邮件
 *     邮件配置在 webconfig 文件里写入
 */

class ReportService extends Action {

    /**
     * 发送邮件 *
     */
    public function sendMail($address, $title, $message) {
        Vendor('PHPMailer.PHPMailerAutoload');
        $mail = new PHPMailer();
        $mail->Host = "smtp.yeah.net"; //SMTP服务器
        $mail->Port = 25;  //邮件发送端口
        $mail->SMTPAuth = true;  //启用SMTP认证
        $mail->CharSet = "UTF-8"; //字符集
        $mail->Encoding = "base64"; //编码方式
        $mail->Username = "wenshizhengxin@yeah.net";  //你的邮箱
        $mail->Password = "wenshijituan1";  //你的密码
        $mail->Subject = "文始征信"; //邮件标题
        $mail->From = "wenshizhengxin@yeah.net";  //发件人地址（也就是你的邮箱）
        $mail->FromName = "文始征信";  //发件人姓名
        $mail->IsSMTP();
        // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($address);
        // 设置邮件正文
        $mail->Body = $message;
        //设置邮件主题
        $mail->Subject = $title;
        $mail->IsHTML(true);

        return $mail->Send();
    }

    //发送注册邮件验证码
    public function sendEmailActive($email, $code) {
        $text = "亲爱的用户：您好！<p>收到这封这封电子邮件是因为您 (也可能是某人冒充您的名义) 在点点乐申请验证邮箱。假如这不是您本人所申请, 请不用理会这封电子邮件, 但是如果您持续收到这类的信件骚扰, 请您尽快联系客服。</p>";
        $text .= "<p>你的本次验证码是： <span style='background:#555; color:#DF7F18; font-weight:bold;'>" . $code . "</span>， 请在30分钟内操作<p>";
        if (!$this->sendMail($email, "点点乐邮箱验证", $text)) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * 发送找回登录密码邮件
     */

    public function sendPassEmail($email, $code) {

        $info = M("zx_order")->where("order_id = $code")->field("order_id,order_sn,com_name,vison_status,email,order_key")->find();
        $link = M("zx_order_extid")->where("order_id = $code and type = 2")->find();

        $emailLink = $link['url'];

        $vison = $this->Svison($info['vison_status'])['name'];
        $text='<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=yes">
<meta name="format-detection" content="telephone=NO">
<meta name="apple-touch-fullscreen" content="YES"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
<meta name="HandheldFriendly" content="True">
<title>征信报告</title>

<style>
    body{
      padding:0;
      margin:0;
      font-family:"Microsoft YaHei";
    }

     ul{
        padding: 0px !important;
        color:#666;
        list-style: none;
        font-size: 14px;
    }
     a{
        text-decoration: none;
        color: #5e89d7;
    }
     a:hover{
        text-decoration: none;
        color: #4174d1;
    }
     li{
        color: #666;
        font-size: 13px;
        line-height: 18px;
    }
     .linebox{
        width: 100%;height: 1px;background-color: #f0f0f0;margin: 20px 0 0 0;
    }
     .tablebox{
        overflow: hidden;
        border: 1px solid #dfeaf6;
        border-right:none;
    }
     .tablebox>div{
        width: 33.33%;
        float: left;
        text-align: center;
    }
     .table_up{
        border-right: 1px solid #dfeaf6;
        border-bottom: 1px solid #dfeaf6;
        background: #f4faff;
        color: #9a9a9a;
        padding: 10px 0;
    }
     .table_bottom{
        border-right: 1px solid #dfeaf6;
        padding: 10px 0;
    }

     .contactbox{
        overflow: hidden;
    }
     .contact_left{
        float: left;
        font-size: 14px;
    }

     .pingjiabox{
        padding-top: 10px;
        padding-bottom: 10px;
        color: #666;
    }
     .outbox{
      width:700px;
      margin:0 auto;
      border:1px solid #d9d9d9;
      margin-top:50px;
      margin-bottom: 10px;
      padding: 20px 30px 0 30px;
      position: relative;
    }
     .headtext{
      font-size:16px;line-height: 24px;color:#333;padding:30px 0 30px 0px;
    }
     .headtext>span{
        color: #EE8B02;
    }
     .contenttext>span{
        color: #EE8B02;
    }
     .firstbox{
        overflow: hidden;
        border-bottom: 1px solid #eff2f5;
        padding-bottom:18px;
    }
     .logoimg{
        display: block;
        float: left;
    }
     .secondbox{
        overflow: hidden;
    }
     .contentbox{
        overflow: hidden;
    }
     .contenttext{
        line-height: 26px;
        padding-bottom: 20px;
    }
     .fujian{
        margin: 30px 0 30px 0;
        font-size: 16px;
    }
     .phonebox{
        color: #666;
    }
     .qqbox{
        color: #666;
        margin-top: 8px;
    }
     .qqbox>span{
        float: left;
    }
     .qqtalk{
        width: 80px;
    }
     .thirdbox{
        margin-top: 40px;
        margin-bottom: 40px;
    }
     .thirdtitile{
        font-size: 14px;
        margin-bottom: 20px;
        border-top: 1px solid #dedede;
        padding-top: 30px;
    }
     .thirdbox>textarea{
        width: 100%;
        height: 140px;
        padding:10px;
        font-size: 14px;
        border-color:#dedede;
    }
     .inbtnbox{
        overflow: hidden;
        width: 340px;
        margin: 0 auto;
        padding-bottom: 15px;
    }
     .inbtn_left{
        width: 150px;
        height: 42px;
        line-height: 42px;
        border-radius: 3px;
        color: #fff;
        text-align: center;
        background-color: #fbad25;
        margin: 0 auto;
        margin-top: 30px;
        display: block;
        text-decoration: none;
        float: left;
    }
     .inbtn_left:hover{
        background-color:#f1a116;
        color:#fff;
    }
     .inbtn_right{
        width: 150px;
        height: 42px;
        line-height: 42px;
        border-radius: 3px;
        color: #fff;
        text-align: center;
        background-color: #5e89d7;
        margin: 0 auto;
        margin-top: 30px;
        display: block;
        text-decoration: none;
    }
     .inbtn_right:hover{
        background-color:#527ecc;
        color: #fff;
    }
     .footerbox{
        width: 760px;
        margin: 0 auto;
        text-align: right;
        margin-bottom: 50px;
        color:#999;
        font-size: 12px;
    }
     .pingfen{
        overflow: hidden;
    }
     .pingfen>div{
        overflow: hidden;
        margin-bottom: 20px;
        float: left;
        width: 33%;
    }
     .pingfen>div>span{
        float: left;
        color: #e5830c;
        font-size: 14px;
    }
  .QRcode_box{
        width: 280px;margin: 0 auto;overflow: hidden;padding: 20px 0;
    }
      .wechatbox{
        float: left;
        text-align: center;
    }
     .wechatbox>div{
        font-size: 14px;
        padding-top: 8px;
        color: #666;
    }
     .downloadbox>div{
        font-size: 14px;padding-top: 8px;color: #666;
    }
     .downloadbox{
        float: right;text-align: center;
    }
     .QRcodebox{
        width:100px;
    }
     .overbox{
        width: 760px;margin: 0 auto;text-align: right;font-size: 12px;color: #999;padding-bottom: 10px;
    }
</style>
</head>

<body>
 <div class="outbox" style="width:700px;margin:0 auto;border:1px solid #d9d9d9;margin-top:50px;margin-bottom: 10px;padding: 20px 30px 0 30px;position: relative;">
   <div class="firstbox" style="overflow: hidden;border-bottom: 1px solid #eff2f5;padding-bottom:18px;">
    <img src="http://8.ddle.cc/pay/qixinlogo.png" class="logoimg" style="display: block;float: left;">
   </div>
   <div class="headtext" style="font-size:16px;line-height: 24px;color:#333;padding:30px 0 30px 0px;">
    尊敬的用户：
    <span style="color: #EE8B02;"><a href="mailto:490788719@qq.com" target="_blank">'.$email.'<wbr></a></span>
    <br>您好！
   </div>
   <div class="secondbox" style="overflow: hidden;">
    <div class="contentbox" style="overflow: hidden;">
     <div class="contenttext" style="line-height: 26px;padding-bottom: 20px;">
      您购买的 '.$vison.' ，订单内容如下：
     </div>
     <div class="tablebox" style="overflow: hidden;border: 1px solid #dfeaf6;border-right:none;">
      <div style="width: 33.33%;float: left;text-align: center;">
       <div class="table_up" style="border-right: 1px solid #dfeaf6;border-bottom: 1px solid #dfeaf6;background: #f4faff;color: #9a9a9a;padding: 10px 0;">
        企业名称
       </div>
       <div class="table_bottom" style="border-right: 1px solid #dfeaf6;padding: 10px 0;">
        '.$info["com_name"].'
       </div>
      </div>
      <div style="width: 33.33%;float: left;text-align: center;">
       <div class="table_up" style="border-right: 1px solid #dfeaf6;border-bottom: 1px solid #dfeaf6;background: #f4faff;color: #9a9a9a;padding: 10px 0;">
        报告类型
       </div>
       <div class="table_bottom" style="border-right: 1px solid #dfeaf6;padding: 10px 0;">
        '.$vison.'
       </div>
      </div>
      <div style="width: 33.33%;float: left;text-align: center;">
       <div class="table_up" style="border-right: 1px solid #dfeaf6;border-bottom: 1px solid #dfeaf6;background: #f4faff;color: #9a9a9a;padding: 10px 0;">
        订单编号
       </div>
       <div class="table_bottom" style="border-right: 1px solid #dfeaf6;padding: 10px 0;">
        A<span style="border-bottom-width: 1px; border-bottom-style: dashed; border-bottom-color: rgb(204, 204, 204); z-index: 1; position: static;" t="7" onclick="return false;" data="20170114390015529428" isout="1">'.$info["order_sn"].'</span>
       </div>
      </div>
     </div>
     <div class="inbtnbox" style="overflow: hidden;width: 340px;margin: 0 auto;padding-bottom: 15px;">
      <a href="http://report.wszx.cc/report-index.html?bgstr='.$info["order_key"].'" target="_blank" class="inbtn_right" style="width: 150px;height: 42px;line-height: 42px;border-radius: 3px;color: #fff;text-align: center;background-color: #5e89d7;margin: 0 auto;margin-top: 30px;display: block;text-decoration: none;">下载报告</a>
     </div>
    </div>
    <div class="contactbox" style="overflow: hidden;">
     <div class="contact_left" style="float: left;font-size: 14px;">
      <div class="pingjiabox">
       对我们的报告进行
       <a href="" target="_blank">评价</a>
      </div>
      <div class="phonebox">
       联系邮箱：
       <span><a href="mailto:wenshizhengxin@yeah.net" target="_blank">wenshizhengxin@yeah.net</a></span>
      </div>
      <div class="qqbox">
       <span>客服QQ：</span>
       <a href="http://wpa.qq.com/msgrd?v=3&uin=1536017333&site=qq&menu=yes" target="_blank"><img src="http://pic.qixin007.com/email_image/qqtalk.png" class="qqtalk"></a>
      </div>
     </div>
    </div>
   </div>
   <div class="linebox"></div>
   <ul style="padding: 0px !important;color:#666;list-style: none !important;font-size: 14px;">
    说明：
    <li style="list-style: none !important;">1、自购买日起6个月内用户可至“我的订单”中重复发送提取已付费企业信用报告 - 基础版。</li>
    <li style="list-style: none !important;">2、本报告的使用仅限于对目标公司的初步评估。未经启文始征信书面授权，任何机构或个人不得以任何形式复制、转发或公开传播本报告的全部或部分内容，不得将报告内容作为诉讼、仲裁、传媒所引用之证明或依据，不得用于营利或用于未经允许的其它用途。</li>
   </ul>
   <div class="QRcode_box" style="width: 280px;margin: 0 auto;overflow: hidden;padding: 20px 0;">
    <div class="wechatbox" style="float: left;text-align: center;">
     <img src="http://8.ddle.cc/pay/weixinof.jpg" class="QRcodebox" style="width:100px;">
     <div style="font-size: 14px;padding-top: 8px;color: #666;">
      关注微信号
     </div>
    </div>
    <div class="downloadbox" style="float: right;text-align: center;">
     <img src="http://8.ddle.cc/pay/weixinof.jpg" class="QRcodebox" style="width:100px;">
     <div style="font-size: 14px;padding-top: 8px;color: #666;">
      下载文始征信APP
     </div>
    </div>
   </div>
  </div>
  <div class="overbox" style="width: 760px;margin: 0 auto;text-align: right;font-size: 12px;color: #999;padding-bottom: 10px;">
   此为系统邮件，请勿回复
  </div>


</body>';
        
        if (!$this->sendMail($email, $info['com_name']." - ".$vison, $text)) {
            return false;
        } else {
            return true;
        }
    }

    //发送修改支付密码验证码
    public function sendSafepassEmail($uid, $email, $code) {
        $text = "亲爱的用户：您好！<p>收到这封这封电子邮件是因为您 (也可能是某人冒充您的名义) 申请修改交易密码。假如这不是您本人所申请, 请不用理会这封电子邮件, 但是如果您持续收到这类的信件骚扰, 请您尽快联系客服。</p>";
        $text .= "<p>你的本次验证码是： <span style='background:#555; color:#DF7F18; font-weight:bold;'>" . $code . "</span>,请在30分钟内操作<p>";
        if (!$this->sendMail($email, "点点乐修改交易密码", $text)) {
            return false;
        } else {
            return true;
        }
    }

    private function Svison($id){
        $zx_config = array(

            1=>array("name"=>"企业信用报告 - 专业版","price"=>3),
            2=>array("name"=>"企业信用报告 - 基础版","price"=>1),
        );
        if($id){
            return $zx_config[$id];
        }else{
            return $zx_config;
        }

    }
}

?>