<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 17:25
 */

namespace wslibs\wszc;

use think\Db;
// $Dsg = new Dsg();
// $Dsg->create('发送受理文件')->collectSendFile($dossier_id,$gids)->submit();
class Dsg {
    private  $data=[];
    private  $docList = [];
    public  function create($title){
        $this->data['title'] = $title;
//        $this->data['dossier_id'] = $dossier_id;
//        $this->data['gids'] = $gids;
        return $this;
    }

    private function addDsg(){
      $data = [];
      $data['title'] = $this->data['title'];
      $data['s_uid'] = LoginUser::getIdid();
      $data['s_role'] = LoginUser::getRole();
      $data['s_time'] = time();
      $data['s_file_num'] = $this->data['s_file_num'];
      $data['s_u_num'] = $this->data['s_u_num'];
      $id =  Db::name('dr_sg')->insertGetId($data);
      return $this->addDs($id);
    }
    private  function addDs($sg_id){
        foreach($this->docList as $k=>$v){
            $this->docList[$k]['sg_id'] = $sg_id;
        }
        $res = Db::name('dr_s')->insert($this->docList);
        if($res){
           return  $sg_id;
        }
        return false;
    }
    public function collectSendFile($dossier_id,$gids){
        // 文件组id是确定的  那么 哪个组给谁发就是确定的
        // 写一个组对应表
        foreach($gids as $k=>$v){
            self::getDocidsByGid($dossier_id,$v);
        }
        return $this;
    }

    public function submit(){
       return $this->addDsg();
    }

    private function getDocidsByGid($dossier_id,$gid){
      // 重新封装一个 获得发送文件的函数
        $list = Ddocs::getFilesByGroup($dossier_id,$gid);
        $users = $this->getReceiveUsersByGid($dossier_id,$gid);
        $this->data['s_u_num'] += count($users);
        $this->data['s_file_num'] += count($list);
        $out = [];
        foreach($list as $k=>$v){
            foreach($users as $key=>$val){
                $tmp = [];
                $tmp['doc_id'] = $v['id'];
                $tmp['c_uid'] = $v['uid'];
                $tmp['c_role'] = $v['role'];
                $tmp['r_uid'] = $val['idid'];
                $tmp['r_role'] = $val['role'];
                $tmp['c_time'] = time();
                $tmp['sg_id'] = 0;
                $this->docList[] = $tmp;
            }
        }
    }
    // 找到所有的接收人
    private function getReceiveUsersByGid($dossier_id,$gid){
        return [];
    }

}