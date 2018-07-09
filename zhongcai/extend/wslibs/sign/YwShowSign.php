<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2017/4/5
 * Time: 下午1:59
 */
importExternal("YunBanZheng/Sign/YwSign");
class YwShowSign
{
    public static function getOneByGroup($docid, $groupid, $style = null)
    {

        $docinfo = self::getDocinfo($docid);

        $list = YwJoinUser::getUserByGroup($docinfo['ywid'], $groupid);
        $uids = array_column($list, "uid");
        $qianzi = M("yw_docs_do")->where("docid=" . $docid . " and uid in (" . implode(",", $uids) . ") ")->order('id desc')->find();

        $out = "";
        if ($qianzi) {
            $out = YwSign::getHttpPre() . $qianzi['val'];
            self::callback($style, $out);
        }

        if($qianzi['addtime']){
            $qianzi['Y'] = date("Y",$qianzi['addtime']);
            $qianzi['M'] = date("m",$qianzi['addtime']);
            $qianzi['D'] = date("d",$qianzi['addtime']);
            $qianzi['img'] = $out;
        }
        return $qianzi;

    }

    public static function getAll($docid, $style = null)
    {

        $qianzi = M("yw_docs_do")->where("docid=" . $docid . "  ")->select();
        if(I('t')==1){
            var_dump($qianzi);
        }
        $out = array();
        $outinfo = array();
        foreach ($qianzi as $value) {
            $img = YwSign::getHttpPre() . $value['val'];
            $out[] = $img;
            if($value['addtime']){
                $value['Y'] = date("Y",$value['addtime']);
                $value['M'] = date("m",$value['addtime']);
                $value['D'] = date("d",$value['addtime']);
                $value['img'] = $img;
            }
            $outinfo[] = $value;
        }
        self::callback($style, $out);
        if(I('t')==1){
            var_dump($outinfo);
        }
        return $outinfo;
    }
    public static function getMoreByGroup($docid, $groupid, $style = null)
    {
        $docinfo = self::getDocinfo($docid);
        $list = YwJoinUser::getUserByGroup($docinfo['ywid'], $groupid);
        $uids = array_column($list, "uid");



        $qianzi = M("yw_docs_do")->where("docid=" . $docid . " and uid in (" . implode(",", $uids) . ") ")->select();

        $sq_list_info = M('yw_sq_list')->where("id in(".implode(',',array_column($qianzi,'extid')).")")->select();

        $out = array();
        $outinfo = array();

        $sq_list_ids = array();

        foreach($list as $ke=>$val){
            foreach($sq_list_info as $k=>$v){
                if($v['u_type']==2 && getCompanyIdCode($val['u_group'])==$v['id_code']){
                    $sq_list_ids[] = $v['id'];
                }



                if($v['u_type']==1 && getUserIdCode($val['u_group'] == $v['id_code'])){
                    $sq_list_ids[] = $v['id'];
                }


            }
        }

        $qianzi_ext = M("yw_docs_do")->where("extid in (" . implode(",", $sq_list_ids) . ") ")->select();
        if(I('lee')==1){

            dump($sq_list_ids);
            dump($qianzi);
            dump($qianzi_ext);
            dump($sq_list_info);
            dump($outinfo);
            exit;
        }

        $out = array();
        $outinfo = array();
        foreach ($qianzi_ext as $value) {

            $img = YwSign::getHttpPre() . $value['val'];
            $out[] = $img;
            if($value['addtime']){
                $value['Y'] = date("Y",$value['addtime']);
                $value['M'] = date("m",$value['addtime']);
                $value['D'] = date("d",$value['addtime']);
                $value['img'] = $img;
            }
            $outinfo[] = $value;
        }
        self::callback($style, $out);
        return $outinfo;
    }

    public static function getOneByUid($docid, $uid, $style = null)
    {


        $qianzi = M("yw_docs_do")->where("docid=" . $docid . " and uid  =  $uid  ")->find();

        $out = "";
        if ($qianzi) {
            $out = YwSign::getHttpPre() . $qianzi['val'];
            self::callback($style, $out);
        }

        if($qianzi['addtime']){
            $qianzi['Y'] = date("Y",$qianzi['addtime']);
            $qianzi['M'] = date("m",$qianzi['addtime']);
            $qianzi['D'] = date("d",$qianzi['addtime']);
            $qianzi['img'] = $out;
        }
        return $qianzi;

    }

    protected static function callback($style, $data)
    {

        if ($data && ($style !== null)) {
            if (!is_array($data)) {
                $data = array($data);
            }
            foreach ($data as $value) {
                echo "<img src='" . $value . "' style='$style' > ";
            }

        }
    }

    protected static function getDocinfo($docid)
    {
        return M("yw_docs")->where("id=" . $docid)->find();
    }

}