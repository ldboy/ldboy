<?php

/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2018/6/9
 * Time: 上午10:50
 */
namespace wslibs\wszc\mes;

use think\Db;
use wslibs\wszc\LoginUser;

class Mes
{
    private $_role;
    private $_idid;
    private $yw_id;

    private $where;

    public function addMust()
    {
        $this->_role = LoginUser::getRole();
        $this->_idid = LoginUser::getIdid();
        $this->where['cz_role'] = $this->_role;
        $this->where['cz_idid'] = $this->_idid;
        return $this;
    }

    public function addtype($type=1)
    {
        $this->where['type'] = $type ;
        return $this;
    }

    public function is_finish($is_finish=false)
    {
        $is_finish ? $this->where['is_finish'] = 1 : $this->where['is_finish'] = 0;
        return $this;
    }

    public function is_delete($is_delete=false)
    {
        $is_delete ? $this->where['is_delete'] = 1 : $this->where['is_delete'] = 0;
        return $this;
    }

    public function is_chakan($is_chakan=false)
    {
        $is_chakan ? $this->where['status'] = 1 : $this->where['status'] = 0;
        return $this;
    }

    public function dealWhere()
    {
        $this->where['type'] ?  $this->where['type'] : $this->where['type']=1;
        $this->where['is_finish'] ?  $this->where['is_finish'] : $this->where['is_finish']=0;
        $this->where['is_delete'] ?  $this->where['is_delete'] : $this->where['is_delete']=0;
        $this->where['status'] ?  $this->where['status'] : $this->where['status']=0;
    }

    public function addIDId($idid)
    {
        $this->where['idid'] = $idid;
        return $this;
    }

    public function msg_finish($msg_id)
    {
        if(!$msg_id)  return false;

        return Db::name('inform')->where("id",$msg_id)->update(['is_finish'=>1]);
    }


    public function msg_chakan($msg_id)
    {
        if(!$msg_id)  return false;

        return Db::name('inform')->where("id",$msg_id)->update(['status'=>1]);
    }

    public function msg_delete($msg_id)
    {
        if(!$msg_id)  return false;

        return Db::name('inform')->where("id",$msg_id)->update(['is_delete'=>1]);
    }



    public function msg_insert($yw_id,$content,$gid,$idid,$ext_id=0,$type=1)
    {
        $this->addMust();
        $this->dealWhere();

        $data = $this->where;

        $data['type'] = $type;
        $data['idid'] = $idid;

        $data['ywid'] = $yw_id;
        $data['content'] = $content;
        $data['addtime'] = time();
        $data['extid'] = $ext_id;
        $data['gid'] = $gid;


        return Db::name('inform')->insert($data);
    }


    public function msg_select()
    {
        $inform = new Inform();

        $inform->addHere(10);

        $inform->addStatus(0);

        $list = $inform->getList();

        return [count($list),$this->dealList($list)];
    }



    public function dealList($list)
    {
        $new_list = [];

        foreach($list as $key=>$value){

            $new_list[] = $this->getList($value['id'],$value['ywid'],$value['content'],$this->getUrlByType($value['id'],$value['type'],$value['ywid']),$value['gid'],$value['zno_title'],$value['extid']);
        }

        return $new_list;
    }

    private function getUrlByType($msgid,$type,$ywid)
    {
        if($type==1){
            return 'http://zcw.wszx.cc/admin/dossier.info/index?id='.$ywid.'&msgid='.$msgid.'&ref=addtabs';
        }elseif($type==2){
            return '';
        }
        return '';
    }

    private function getList($msg_id,$yw_id,$content,$url,$g_id,$zno_title,$ext_id='',$class='btn btn-info btn-xs btn-detail btn-addtabs')
    {
        return array(
            'id'=>$msg_id,
            'yw_id'=>$yw_id,
            'content'=>$content,
            'url'=>$url,
            'class'=>$class,
            'extid'=>$ext_id,
            'gid'=>$g_id,
            'zno_title'=>$zno_title,
        );
    }

}