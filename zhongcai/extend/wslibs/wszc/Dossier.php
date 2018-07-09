<?php
namespace wslibs\wszc;

use think\Db;
use think\db\Query;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: 上午10:18
 */
class Dossier
{

    public static $arrStatus = [1=>'申请中',2=>'待立案',3=>'已立案',4=>'已完成',0=>'已取消'];

   public static $arrSubStatus = [0=>"已撤回",5=>"已被拒",10=>'表单待提交',11=>'表单已提交',12=>'已提交仲裁委',20=>'待受理',21=>'审批文件待提交',22=>'待审批',23=>'立案中...',30=>'答辩期',31=>'待组庭',32=>'已组庭',35=>"组庭中...",40=>'已完成'];

  //  public static $arrSubStatus = [10=>'申请待提交',11=>'申请已提交',12=>'已提交仲裁委',20=>'待立案',230=>'已立案',21=>'审批文件待提交',22=>'待主任审批',23=>'主任已审批',30=>'待发送受理文件',31=>'待组庭',300=>'待答辩',350=>'待质证',32=>'待声明/披露',400=>'待裁决',403=>'待审核裁决',40=>'已完成'];

    public static function getStatus($status,$sub_status=0)
    {

        if ($status==0)
        {
            return self::getSubStatus($sub_status);
        }

        if(self::$arrStatus[$status]) return self::$arrStatus[$status];

        return '';

    }
    public static function getSubStatus($status)
    {
        if(self::$arrSubStatus[$status]) return self::$arrSubStatus[$status];

        return '';

    }

    public static function get230Status()
    {
        return [21,22,23,30,31];
    }
    public static function getStatusForRole($role)
    {
        $arr = [];

        switch($role){
            case Constant::D_Role_ZhongCaiWei_GuanLiYuan:
                $arr = [20,21,22,30,31,32,400,403,40];
                break;
            case Constant::Admin_Role_putongyonghu:
                $arr = [];
                break;
            case Constant::Admin_Role_zhongcaiyuan:
                $arr = [32,400,40];
                break;
            case Constant::Admin_Role_yinhang:
                $arr = [10,11,12,20,30,31,32,230,350,400,40];
                break;
            case Constant::D_Role_ZhongCaiWei_CaiJueShenPi || Constant::D_Role_ZhongCaiWei_LiAnShenPi:
                $arr = [22,23,30,31,32,403,40];
        }

        return self::getStatuaArr($arr);
    }

    private static function getStatuaArr($arr)
    {

        $out = array();
        foreach ($arr as $value)
        {
            $out[$value] = self::$arrSubStatus[$value];
        }
        return $out;
    }

    public static function getSubStatusByStatus($status)
    {
        if(!$status) return '';

        switch($status){
            case 1:
                $arr = array();
                break;
            case 2:
                $arr = array(20,22);
                break;
            case 3:
                $arr =  $arr = array(30,31,32);
                break;
            case 4:
                $arr = array();
                break;
            default:
                $arr = [];
                break;
        }


        $out = array();
        foreach ($arr as $value)
        {
            $out[$value] = self::$arrSubStatus[$value];
        }
        return $out;

    }

    public static function getStatusColor($status)
    {
        list($color_1,$color_2,$color_3,$color_4,$color_5) = Constant::getColorClass();

        switch($status){
            case 1:
                $color = $color_1;
                break;
            case 2:
                $color = $color_2;
                break;
            case 3:
                $color = $color_3;
                break;
            case 4:
                $color = $color_4;
                break;
            default :
                $color = $color_5;
                break;
        }

        return  $color;
    }
    public static function getAllStatus($role)
    {

        $statusArr = self::$arrStatus;
        switch($role){
            case Constant::Admin_Role_admin:
                break;
            case Constant::Admin_Role_zhongcaiwei:

                unset($statusArr[1]);
                unset($statusArr[4]);
                break;
            case Constant::Admin_Role_zhongcaiyuan:
                unset($statusArr[1]);
                unset($statusArr[2]);
                break;
            case Constant::Admin_Role_putongyonghu:
                unset($statusArr[1]);
                unset($statusArr[2]);
                break;
            case Constant::Admin_Role_yinhang:

                break;
        }
        return $statusArr;
    }
    /**
     * @param $zc_jg_id 仲裁机构id
     * @param $third_jg_id 银行id
     * @param $title 标题
     * @param $third_order_id
     */
    public static function add($zc_jg_id, $third_jg_id, $third_order_id, $type = 1, $title = "新仲裁",$hk_sign_time)
    {


        $data = ["zc_jg_id" => $zc_jg_id, "third_jg_id" => $third_jg_id, "third_order_id" => $third_order_id, "title" => $title, "type" => $type, "addtime" => time(), "status" => 1,"sub_status"=>10,"hk_sign_time"=>$hk_sign_time];

        try {


            $id = Db::name("dossier")->insertGetId($data);
            Db::name("dossier_time")->insertGetId(["id" => $id, "time10" => time()]);
            return $id;
        } catch (Exception $exception) {
            $exception->getMessage();
            return 0;
        }


    }

    public static function setTitle($id, $title)
    {
        return self::updata($id, array("title" => $title));
    }

    public static function updata($id, $data)
    {
        /*if ($data['status']) {
            return self::changeStatus($id, $data['status'], $data);
        }*/
        return Db::name("dossier")->where("id", $id)->update($data);
    }

    public static function changeStatus($id, $status, $data = array())
    {
        if(!is_array($status)){
            $status = self::makeStatus($status);
        }else
        {
            if (!isset($status['subStatus']))
            {
                $status = self::makeStatus($status[0],$status[1]);
            }
        }
        $data['status'] = $status['status'];
        $data['sub_status'] = $status['subStatus'];
        if($data['status']==0){
            $data['is_valid'] = 0;
        }
        $rs = self::updata($id, $data);
        if ($rs) {
            return Db::name("dossier_time")->where(["id" => $id])->update(["time" . $status['subStatus'] => time()]);
        }
        return 0;
    }
    public static function makeStatus($status,$subStatus=0){
         if($subStatus==0){
             $subStatus = $status*10;
         }
         return ['status'=>$status,'subStatus'=>$subStatus];
    }
    public static function isStatus($idOrInfo,$subStatus){
        if(!is_array($idOrInfo)){
            $idOrInfo = self::getSimpleDossier($idOrInfo);
        }
        return $subStatus==$idOrInfo['sub_status'];
    }


    /**
     * @param $id
     * @param DossierUser $user
     * @return int|mixed|string
     */
    public static function addDangShiRen($id, DossierUser $user)
    {
        return $user->pushToDossier($id);
    }



    public static function getDangShiRen($id,$D_Role=1)
    {


        $query = new Query();
        $query->table('zc_dossier_users')
            ->alias('u')
            ->join('zc_dossier_users uu','u.f_uid = uu.id','left')
            ->where('u.dossier_id','eq',$id)
            ->where('u.type','in',[1,2,3])
            ->field("u.*,uu.name as f_name,uu.id_num as f_id_card ,uu.phone as f_phone ")
            ->order('u.role asc,u.r_no asc')/*->fetchSql()*/;


//       $query =  Db::name("dossier_users")
//           ->where("dossier_id", $id) ;
       if($D_Role)
       {
           if(!is_array($D_Role))
           {
               $D_Role = explode(",",$D_Role);
           }
           $query->where("u.role","in",$D_Role);
       }

       return Db::select($query);
    }


    public static function addLog($dossier_id,$uid,$name,$type=1,$remark='修改了此文档')
    {
        $data = [
            'uid'=>$uid,
            'dossier_id'=>$dossier_id,
            'type'=>$type,
            'name'=>$name,
            'addtime'=>time(),
            'remark'=>$remark,
        ];


        return Db::name('dossier_log')->insertGetId($data);

    }


    public static function editLogStatus($id,$status=0)
    {
        $data = [
            'status'=>$status
        ];

        Db::name('dossier_log')->where('id',$id)->update($data);
    }

    public static function getDossierRemind($dossier_id,$type)
    {
        return Db::name('dossier_log')
            ->where('dossier_id',$dossier_id)
            ->where('type',$type)
            ->select();
    }

    public static function getSimpleDossier($id)
    {
        return Db::name('dossier')->where("id = '$id' ")->find();
    }
    public static function getZhongcaiyuan($dossier_id){
        return Db::name("arbitrator")
            ->where('dossier_id',$dossier_id)
            ->where('status',1)
            ->select();
    }

}
