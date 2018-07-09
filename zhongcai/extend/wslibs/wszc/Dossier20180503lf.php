<?php
namespace wslibs\wszc;

use think\Db;
use think\Exception;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: 上午10:18
 */
class Dossier
{
    /**
     * @param $zc_jg_id 仲裁机构id
     * @param $third_jg_id 银行id
     * @param $title 标题
     * @param $third_order_id
     */
    public static function add($zc_jg_id, $third_jg_id, $third_order_id, $type = 1, $title = "新仲裁")
    {


        $data = ["zc_jg_id" => $zc_jg_id, "third_jg_id" => $third_jg_id, "third_order_id" => $third_order_id, "title" => $title, "type" => $type, "addtime" => time(), "status" => 1];

        try {


            $id = Db::name("dossier")->insertGetId($data);
            Db::name("dossier_time")->insertGetId(["id" => $id, "time1" => time()]);
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
        if ($data['status']) {
            return self::changeStatus($id, $data['status'], $data);
        }
        return Db::name("dossier")->where("id", $id)->update($data);
    }

    public static function changeStatus($id, $status, $data = array())
    {
        $data['status'] = $status;
        $rs = self::updata($id, $data);
        if ($rs) {
            return Db::name("dossier_time")->where(["id" => $id])->update(["time" . $status => time()]);
        }
        return 0;
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



    public static function getDangShiRen($id,$D_Role=0)
    {

       $query =  Db::name("dossier_users")->where("dossier_id", $id) ;
       if($D_Role)
       {
           if(!is_array($D_Role))
           {
               $D_Role = explode(",",$D_Role);
           }
           $query->where("role","in",$D_Role);
       }
       return $query->select();
    }



}
