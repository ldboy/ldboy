<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/5/26
 * Time: 上午9:46
 */
namespace wslibs\wszc\dtime;
use EasyWeChat\Staff\Session;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Dossier;
use wslibs\wszc\LoginUser;
use wslibs\wszc\User;

class Dtime
{
    const D_TIME_shouli_time = 5;
    const D_TIME_zuting_time = 5;
    const D_TIME_dabian_time = 5;
    const D_TIME_zhizheng_time = 5;
    const D_TIME_juzheng_time = 5;
    const D_TIME_send_dabian_time = 5;
    const D_TIME_send_tongzhishu_to_dangshiren = 120;
    const D_TIME_zhongcaiyuan_pilou = 5;

    private $idid = 0;

    public function __construct()
    {
        $this->idid = LoginUser::getIdid();
    }

    public static function getTimeGroupForDid($did)
    {
        $role = LoginUser::getRole();

        $dossier_info = Dossier::getSimpleDossier($did);


        return self::getTimeGroupForRole($role,$dossier_info);
    }

    public static function getTimeGroupForRole($role,$dossier_info)
    {
        $arr = [];
        switch($role){
            case Constant::Admin_Role_putongyonghu:
                $arr = array(
                    'dabian'=>self::is_defence_show($dossier_info),
                    'zhizheng'=>self::is_zhizheng_show($dossier_info),
                    'juzheng'=>self::is_juzheng_show($dossier_info),
                );
                break;
            case Constant::Admin_Role_zhongcaiwei:
                $arr = array(

                    'shouli'=>self::is_shouli_show($dossier_info),
                );
                break;
            case Constant::Admin_Role_yinhang:
                $arr =   array(
                    'dabian'=>self::is_zhizheng_show($dossier_info),
                );
                break;
        }

        return self::deal_data($arr);

    }

    private static function deal_data($arr)
    {

        $list = [];
        foreach($arr as $key=>$value){

                if(is_array($value)){
                    foreach($value['txt'] as $ke=>$val) {

                        $list[] = $val;
                    }
                }
        }

        return $list;
    }

    private static function is_defence_show($dossier_info)
    {


        $question = Db::name('dossier_defence')->where('dossier_id',$dossier_info['id'])->where("idid",LoginUser::getIdid())->find();
        
        if($question['status'] >= 2){
            return [];
        }

        if($question){
            time()>($question['addtime']+self::D_TIME_dabian_time*86400) ?  $is_pass = 1 : $is_pass = 0;
            $is_pass ? $msg = '此案件已经答辩结束'  :  $msg = '距离答辩结束还有'.ceil(($question['addtime']+self::D_TIME_dabian_time*86400-time())/86400).'天';
            return array('is_pass'=>$is_pass,'txt'=>array(/*'答辩开始时间:'.date('Y年m月d日',$question['addtime']),*/'答辩截至时间:'.date('Y年m月d日',$question['addtime']+self::D_TIME_dabian_time*86400)."<br>(<span style='color:red'>".$msg.'</span>)'));
        }else{
            $is_pass = 2;
            $msg = '此案件还未答辩';
            return array('is_pass'=>$is_pass,'txt'=>array('答辩开始后截止到'.(self::D_TIME_dabian_time)."日后结束<br>(<span style='color:red'>".$msg."</span>)"));
        }
    }

    private static function is_shouli_show($dossier_info)
    {

        if($dossier_info['status']<3 && $dossier_info['status']>0 ){
            time()>($dossier_info['addtime']+self::D_TIME_shouli_time*86400) ?  $is_pass = 1 : $is_pass = 0;
            $is_pass ? $msg = '此案件已超过'.self::D_TIME_shouli_time.'天受理日期'  :  $msg = '截至受理结束还有'.ceil(($dossier_info['addtime']+self::D_TIME_shouli_time*86400-time())/86400).'天';
            return array('is_pass'=>$is_pass,'txt'=>array('案件申请时间:'.date('Y年m月d日',$dossier_info['addtime']),'案件受理截至时间:'.date('Y年m月d日',$dossier_info['addtime']+self::D_TIME_shouli_time*86400)."<br>(<span style='color:red'>".$msg.'</span>)'));
        }elseif($dossier_info['status']>=3){
            $is_pass = 2;
            $msg = '此案件已受理';
            return array('is_pass'=>$is_pass,'txt'=>array('申请开始后截止到'.(self::D_TIME_shouli_time)."日后结束<br>(<span style='color:red'>".$msg."</span>)"));
        }
    }

    private static function is_zhizheng_show($dossier_info)
    {
        $question['addtime'] = max(array_column((array)Db::name('dossier_question')->where('dossier_id',$dossier_info['id'])->select(),'addtime'));

        if($question['addtime']){
            time()>($question['addtime']+self::D_TIME_zhizheng_time*86400) ?  $is_pass = 1 : $is_pass = 0;

            $is_pass ? $msg = '此案件已经质证结束'  :  $msg = '距离质证结束还有'.ceil(($question['addtime']+self::D_TIME_zhizheng_time*86400-time())/86400).'天';

            return array('is_pass'=>$is_pass,'txt'=>array(/*'质证开始时间:'.date('Y年m月d日',$question['addtime']),*/'质证截至时间:'.date('Y年m月d日',$question['addtime']+self::D_TIME_zhizheng_time*86400)."<br>(<span style='color:red'>".$msg.'</span>)'));
        }else{
            $is_pass = 2;
            $msg = '此案件还未质证';
            return array('is_pass'=>$is_pass,'txt'=>array('质证开始后截止到'.(self::D_TIME_dabian_time)."日后结束<br>(<span style='color:red'>".$msg."</span>)"));
        }
    }

    private static function is_juzheng_show($dossier_info)
    {
        $dz_info['addtime'] = max(array_column((array)Db::name('dz')->where('dossier_id',$dossier_info['id'])->select(),'addtime'));

            if($dz_info['addtime']){
                time()>($dz_info['addtime']+self::D_TIME_juzheng_time*86400) ?  $is_pass = 1 : $is_pass = 0;

                $is_pass ? $msg = '此案件已经举证结束'  :  $msg = '距离举证结束还有'.ceil(($dz_info['addtime']+self::D_TIME_juzheng_time*86400-time())/86400).'天';

                return array('is_pass'=>$is_pass,'txt'=>array(/*'质证开始时间:'.date('Y年m月d日',$question['addtime']),*/'举证截至时间:'.date('Y年m月d日',$dz_info['addtime']+self::D_TIME_juzheng_time*86400)."<br>(<span style='color:red'>".$msg.'</span>)'));
            }else{
                $is_pass = 2;
                $msg = '此案件还未举证';
                return array('is_pass'=>$is_pass,'txt'=>array('举证开始后截止到'.(self::D_TIME_dabian_time)."日后结束<br>(<span style='color:red'>".$msg."</span>)"));
            }

    }

}