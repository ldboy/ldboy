<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/1/21
 * Time: 17:58
 */
// 以utf-8进行通信

namespace wslibs\wszc\mobilesend;
class SmsCenterSdk{
    private $username = 'xys'; // 平台在短信中心注册的用户名
    private $token = 'U2cm2VFPEDOGVxFveZbzHmE4O8ZU6d5w'; // 短信中心分配给平台的token

    public function __construct($username='',$token=''){
        // 也可以从外部传入
        if($username){
            $this->username = $username;
        }
        if($token){
            $this->token = $token;
        }
    }

    /**
     * @param $phone       用户手机号
     * @param $businessId  在短信中心开通的业务对应ID
     */
    public function sendCode($phone,$businessId){
      $arr = array(
          'username'=>$this->username,
          'token'=>$this->token,
          'act'=>'send',
          'sign'=>$this->sign($phone,$businessId),
          'phone'=>$phone,
          'business_id'=>$businessId,
      );

        if(input('zhz')==1){
            dump($arr);
        }
        return $this->request($arr);
    }

    public function check($phone,$code,$businessId){
        $arr = array(
            'username'=>$this->username,
            'token'=>$this->token,
            'act'=>'check',
            'sign'=>$this->sign($phone,$businessId),
            'phone'=>$phone,
            'code'=>$code,
            'business_id'=>$businessId,
        );
        return $this->request($arr);
    }

    public function sendText($phone,$businessId,$content){
        $arr = array(
            'username'=>$this->username,
            'token'=>$this->token,
            'act'=>'sendText',
            'sign'=>$this->sign($phone,$businessId),
            'phone'=>$phone,
            'content'=>$content,
            'business_id'=>$businessId,
        );


        return $this->request($arr);
    }

    private function sign($phone,$businessId){
        return md5($this->username.$this->token.$phone.$businessId);
    }

    private function request($arr){
        if(input('zhz')==1){
            dump($arr);
        }
       $ch = curl_init('http://8.ddle.cc/SmsCenter-index');
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $arr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $res = null;
        $res = curl_exec($ch);
        curl_close($ch);
        if(input('zhz')==1){

        }

        return $res;
    }

}