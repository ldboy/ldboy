<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/15
 * Time: 13:51
 */
namespace wslibs\wszc\Statement;
use think\Db;
use wslibs\wszc\LoginUser;

class Statement {
   public static function getMyStatement($dossier_id){
       return Db::name('statement')
           ->where('dossier_id',$dossier_id)
           ->where('idid',LoginUser::getIdid())
           ->find();
   }


    public static function addStatement($dossier_id){
        $info = self::getMyStatement($dossier_id);
        if($info){
            return false;
        }
        $data = [];
        $data['idid'] = LoginUser::getIdid();
        $data['dossier_id'] = $dossier_id;
        $data['addtime'] = time();
       return Db::name('statement')->insert($data);
    }

    public static function getStatementList($dossier_id){
         $List = Db::name("statement")
            ->alias("st")
            ->join("idcards id","st.idid = id.id")
            ->join("dossier_users du","id.id = du.idid")
            ->where("st.dossier_id = '$dossier_id'")
            ->where("du.dossier_id = '$dossier_id'")
            ->field("id.real_name,id.id_card,st.addtime,du.phone")
            /*->group("st.id")*/
            ->select();


         foreach ($List as $k => $v){
             $List[$k]['addtime'] = date("Y-m-d H:i:s",$v['addtime']);
             $List[$k]['status'] = "已声明";
         }

         return $List;
    }
}