<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/5/17
 * Time: 17:25
 */

namespace wslibs\wszc;

use think\Db;

class Ds
{
    private $sg = [];
    private $s = [];
    private $count = [
        'file_key' => [],
        "r_key" => []
    ];
    private $create_time = 0;

    public function __construct($dossier_id, $title,$gid=0)
    {
        $this->sg['title'] = $title;
        $this->sg['dossier_id'] = $dossier_id;
        $this->sg['gid'] = $gid;
        $this->create_time = time();
    }

    public function addFile($docid, $r_uid, $r_role)
    {
        $this->count['file_key'][$docid] = 1;
        $this->count['r_key'][$r_uid] = 1;
        $tmp = [];
        $tmp['doc_id'] = $docid;
        $tmp['c_uid'] = '';
        $tmp['c_role'] = '';
        $tmp['r_uid'] = $r_uid;
        $tmp['r_role'] = $r_role;
        $tmp['c_time'] = $this->create_time;
        $tmp['sg_id'] = 0;
        $this->s[] = $tmp;
        return $this;
    }

    public function submit()
    {
        $this->sg['s_uid'] = LoginUser::getIdid();
        $this->sg['s_role'] = LoginUser::getRole();
        $this->sg['s_time'] = $this->create_time;
        $this->sg['s_file_num'] = count($this->count['file_key']);
        $this->sg['s_u_num'] = count($this->count['r_key']);

        $sg_id = Db::name('dr_sg')->insertGetId($this->sg);
        $docIds = array_column($this->s, 'doc_id');
        $docUids = Db::name('dr')
            ->where('id', 'in', $docIds)
            ->field('uid')
            ->selectOfIndex('id');

        foreach ($this->s as $k => $v) {
            $this->s[$k]['sg_id'] = $sg_id;
            $uid = $docUids[$v['doc_id']];
            $this->s[$k]['c_uid'] = $uid;
            $this->s[$k]['c_role'] = $role = User::getRoleInDossier($this->sg['dossier_id'], $uid);


        }
        $res = Db::name('dr_s')->insertAll($this->s);
        if ($res) {
            return $sg_id;
        } else {
            return false;
        }
    }


    public static function sendGroupFileToDocRole($did, $gid, $role, $idid = [], $exit = 0, $title = '')
    {
        $groupList = Ddocs::getFilesByGroup($did, $gid, $exit);
        if (!$title) {
            $title = Constant::getGroupInfo($gid)['title'];
        }
//        if($role==Constant::D_Role_ShenQingRen){
//            $role = [Constant::D_Role_ShenQingRen,Constant::D_Role_ShenQingRen_Dl,Constant::D_Role_ShenQingRen_FR];
//        }
//        if($role==Constant::D_Role_Bei_ShenQingRen){
//            $role = [Constant::D_Role_Bei_ShenQingRen,Constant::D_Role_Bei_ShenQingRen_Dl,];
//        }
        $userList = Drole::getUsersByRole($did, $role);

        $Ds = new Ds($did, $title,$gid);
        foreach ($groupList as $k => $v) {
            foreach ($userList as $key => $val) {
                if ($idid && (!in_array($val['idid'], $idid))) {
                    $Ds->addFile($v['id'], $val['idid'], $role);
                } else if (!$idid) {
                    $Ds->addFile($v['id'], $val['idid'], $role);
                }
                $Ds->addFile($v['id'], $val['idid'], $role);
            }
        }

        return $Ds->submit();
    }

    // 给某个人发送文件
    public static function sendGroupFileToUid($did, $gid, $idid, $extid = 0, $title = '')
    {
        $groupList = Ddocs::getFilesByGroup($did, $gid, $extid);
        if (!$title) {
            $title = Constant::getGroupInfo($gid)['title'];
        }
        $role = User::getRoleInDossier($did, $idid);
        $Ds = new Ds($did, $title);
        foreach ($groupList as $k => $v) {
            $Ds->addFile($v['id'], $idid, $role);
        }
        return $Ds->submit();
    }

    public static function getFilesOfOne($did, $idid,$gid=[])
    {
        if (!is_array($idid)) {
            $idid = array($idid);
        }


        $model = Db::name('dr_sg')
            ->alias('sg')
            ->join('dr_s s', 'sg.id = s.sg_id', 'right')
            ->field('sg.*,s.doc_id,s.id as sid,s.r_uid')
            ->distinct('s.doc_id')
            ->where('sg.dossier_id', $did);

        if(LoginUser::isZhongCaiYuan()){
            if($gid){
                unset($idid[array_search(LoginUser::getIdid(),$idid)]);
                $model->where("s.r_uid = ".LoginUser::getIdid()." or (s.r_uid in(".implode(',',$idid).") and  sg.gid not in(".implode(',',$gid)."))");
            }else{
                $model->whereIn('s.r_uid', $idid);
            }
        }else{
            $model->whereIn('s.r_uid', $idid);
        }

        $model->order("sg.id asc");
        $listtmp = $model->select();

        if(input('wsw')==5069){
            dump($listtmp);


            //dump(Ddocs::getFilesLayout($this,$html));
        }



        $tmpdel = [];
        foreach ($listtmp as $k => $v) {
            $zhi = [$v['s_uid']==$v['r_uid']?1:0,$v];
            if (!$tmpdel[$v['doc_id']])
            {
                $tmpdel[$v['doc_id']] = [0,$v];
            }
            $nowzhid = $tmpdel[$v['doc_id']][0];

            if ($zhi[0]>$nowzhid[0])
            {
                $tmpdel[$v['doc_id']] = $zhi;
                continue;
            }
            if ($zhi[0]<$nowzhid[0]) continue;

            if ($zhi[0]==1)
            {
                if ($zhi[1]['s_time']<$nowzhid[1]['s_time'])
                {
                    $tmpdel[$v['doc_id']] = $zhi;
                    continue;
                }
            }else
            {
                if ($zhi[1]['s_time']>$nowzhid[1]['s_time'])
                {
                    $tmpdel[$v['doc_id']] = $zhi;
                    continue;
                }
            }


        }


        $list = array();
        foreach ($tmpdel as $value)
        {
            $list[] =   $value[1] ;
        }



        $outDoc = [];
        $out = [];
        foreach ($list as $k => $v) {
            if (in_array($v['doc_id'], $outDoc)) {
                continue;
            }
            $outDoc[] = $v['doc_id'];


            if (!isset($out[$v['id']])) {
                $out[$v['id']]['title'] = $v['title'];
                $out[$v['id']]['time'] = date("Y-m-d H:i", $v['s_time']);
            }
            $out[$v['id']]['item'][] = $v;
        }
        return array_reverse($out);
    }

    public static function sendFileByDocids($docs, $role, $title, $exceptIdid = [])
    {
        if (!$docs) {
            return false;
        }
        $did = $docs[0]['dossier_id'];
        $userList = Drole::getUsersByRole($did, $role);
        $Ds = new Ds($did, $title);
        foreach ($docs as $k => $v) {
            foreach ($userList as $key => $val) {
                if (!in_array($val['idid'], $exceptIdid)) {
                    $Ds->addFile($v['id'], $val['idid'], $role);
                }
            }
        }
        return $Ds->submit();
    }

    public static function getDocIdsFromSgid($sgid, $toidid)
    {

        return array_column((Array)Db::name("dr_s")->where("sg_id", $sgid)->select(), "doc_id");//->where("r_uid", $toidid)
    }

}