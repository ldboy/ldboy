<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: 下午2:00
 */

namespace wslibs\wszc;


use EasyWeChat\Support\Arr;
use EasyWeChat\Support\Log;
use think\Db;


class Ddocs
{

    private static $group_files = array();

    public static function getFilesByGroup($id, $gid, $ext_id = 0, $initdocids = null)
    {

        if ($initdocids) {
            if (!is_array($initdocids)) {
                $initdocids = explode(",", $initdocids);
            }
            self::initGroupFileFromDocids($gid, $ext_id, $initdocids);
            return self::getFilesByGroup($id, $gid, $ext_id);
        }

        $key = $id . "_" . $gid . "_" . $ext_id;

        if (isset(self::$group_files[$key])) return self::$group_files[$key];
        $modeids = Constant::getGroupFileMode($gid);


        $reget = false;



        if ($has = self::getFilesByGroupExist($id, $gid, $ext_id)) {


            if (!self::isFileGroupInit($modeids, $has)) {


                self::initFilesByGroup($id, $gid, $ext_id);
                $reget = true;

            }


        } else {


            self::initFilesByGroup($id, $gid, $ext_id);
            $reget = true;
        }


        self::$group_files[$key] = $reget ? self::getFilesByGroupExist($id, $gid, $ext_id) : $has;

        return self::$group_files[$key];

    }

    private static function isFileGroupInit($modeids, $has)
    {
        $okarray = array();
        foreach ($modeids as $mid) {
            if ($mid != Constant::DOC_model_qitazhengju)
                $okarray[$mid] = 0;
        }
        foreach ($has as $value) {
            $okarray[$value['doc_mod_id']] = 1;
        }
        foreach ($okarray as $ok) {
            if (!$ok) return false;
        }

        return true;

    }


    public static function initGroupFileFromSgid($gid, $ext_id, $sgid)
    {

        $docids = Ds::getDocIdsFromSgid($sgid, 0);

        return self::initGroupFileFromDocids($gid, $ext_id, $docids);
    }

    public static function initGroupFileFromDocids($gid, $ext_id, $docids)
    {
        $doclist = Db::name("dr")->whereIn("id", $docids)->select();
        foreach ($doclist as $value) {

            if (!Db::name("drg")->where(array("gid" => $gid, "dossier_id" => $value['dossier_id'], "doc_id" => $value['id'], "doc_mod_id" => $value['doc_model_id'], "exid" => $ext_id, "r_no" => $value['r_no']))->find()) {


                $drgid = Db::name("drg")->insertGetId(array("gid" => $gid, "addtime" => time(), "dossier_id" => $value['dossier_id'], "doc_id" => $value['id'], "doc_mod_id" => $value['doc_model_id'], "exid" => $ext_id, "r_no" => $value['r_no']));

                if (!$drgid) return false;
            }
        }
        return true;

    }

    public static function getDmList()
    {
        return Db::name('dm')->selectOfIndex('id');
    }

    public static function getFilesByDocIds($docids_or_docs)
    {

        if (is_array($c = current($docids_or_docs))) {
            $list = $docids_or_docs;
            if (isset($c['_file_num'])) return $list;
        } else {
            $list = (Array)Db::name("dr")->whereIn("id", $docids_or_docs)->order("z_no asc")->select();
        }
        $dmList = self::getDmList();
        foreach ($list as $k => $v) {
            $list[$k]['dm_sort'] = $dmList[$v['doc_model_id']]['sort'];
        }

        $list = sortArrByManyField($list, 'dm_sort', SORT_DESC, 'z_no', SORT_ASC);

        // $usernames = User::getUserNameByIdids(array_column($list, "uid"));

        $id = current($list)['dossier_id'];


        // $roles = Db::name("dossier_roles")->where("dossier_id", $id)->selectOfIndex("idid");


        $signerlist_tmp = Db::name("drs")->whereIn("doc_id", array_column($list, "id"))->select();
        $signerlist = array();
        foreach ($signerlist_tmp as $value) {
            $signerlist[$value['doc_id']][$value['idid']] = $value['ok'];
        }

        $cuid = LoginUser::getIdid();

        $iszhongcaiyuan = LoginUser::isZhongCaiYuan();

        $index = 1;
        foreach ($list as $k => $v) {


            if ($iszhongcaiyuan && in_array($v['doc_model_id'], Constant::getZhongCaiWeiNeiBuDocMod())) {
                unset($list[$k]);
                continue;
            }

            $description = "<span class='label label-danger'>无</span>";
            $doc_num = 0;
            if ($v['to_sign'] || $v['create_type'] == 1) {
                $description = "含<span style='color:green'>1</span>个文件";
                $doc_num = 1;
            } else if ($v['file_num']) {
                $description = "含<span style='color:green'>" . $v['file_num'] . "</span>个文件";
                $doc_num = $v['file_num'];
            }
            //  var_dump($list[$k]['name']);
//            $list[$k]['name'] = $index . "、" . $list[$k]['name'];
//            $index++;
            $list[$k]['desc'] = $description;
            $list[$k]['_file_num'] = $doc_num;

            // $list[$k]['_uid'] = $cuid;
            if ($v['mode_type'] == 1) {
                $list[$k]['show_look'] = 1;
            }
            //  var_dump($v['to_sign']);
            if ($v['to_sign'] == 1) {
                if ($v['has_sign'] == 1) {
                    if (in_array($cuid, array_keys($signerlist[$v['id']])))
                        $list[$k]['show_sign'] = 2;
                } else {


                    // var_dump(User::getRoleInDossier($id,$cuid));
                    if (!$signerlist[$v['id']]) {

                        if (in_array($cuid, Ddocs::getSignerByDocInfo($v))) {
                            $list[$k]['show_sign'] = 1;
                        } else {
                            $list[$k]['show_sign'] = 0;
                        }


                    } else if (in_array($cuid, array_keys($signerlist[$v['id']]))) {

                        if ($signerlist[$v['id']][$cuid])
                            $list[$k]['show_sign'] = 2;
                        else {


                            $list[$k]['show_sign'] = 1;


                        }
                    } else {

                        $list[$k]['show_sign'] = 0;
                    }


                }

            }
            if ($v['create_type'] == 2) {
                if ($cuid == $v['uid'])
                    $list[$k]['show_fj'] = 1;
                else {
                    $list[$k]['show_fj'] = 2;
                }
            }
            $list[$k]['tip'] = "";


            if (($v['doc_model_id'] == Constant::DOC_model_qitazhengju) || ($v['file_type'] == 1)) {

                $list[$k]['name'] = $index . "、 <span class='label label-warning margin'>证据{$v['z_no']}</span>  ---  ".$list[$k]['name'];
                $index++;


                $list[$k]['zhengming_shixiang']="证明事项:" . $v['des'] ;

                $v['min_file_num'] || ($v['min_file_num'] = 1);

                if ($doc_num < $v['min_file_num']) {
                    $list[$k]['tip'] = "至少上传" . ($v['min_file_num'] - $doc_num) . "个文件";
                } else if (!$v['des']) {
                    $list[$k]['tip'] = "请完善证据证明事项";
                }

            } else if ($list[$k]['show_sign'] == 1) {
                $list[$k]['tip'] = "请签字（盖章）";
            }

            $list[$k]['u_name'] = "[" . Constant::getQxRoleName($v['c_qrole']) . "]" . $list[$k]['u_name'];
        }
        return $list;
    }

    public static function getFilesByGroupExist($id, $gid, $ext_id)
    {


        if ($gid == Constant::FILE_GROUP_fasongwenjian) {
            return self::getFilesByDocIds(Ds::getDocIdsFromSgid($ext_id, LoginUser::getIdid()));
        }


        $role = User::getRoleInDossier($id);
        if ($_GET['_rlr']) var_dump($role);
       // $cuid = (int)User::getLoginUid();

        $list = Db::name("drg")->alias("dg")->join("__DR__ dr", "dg.doc_id=dr.id")->where("dg.gid", $gid)->field("dr.*,dg.gid")->where("dg.dossier_id", $id)->where("dr.exid", $ext_id)->order("dr.z_no asc,dr.id desc")->selectOfIndex("dr.id");//->where("dr.qx & $role >0 or dr.uid=$cuid")


        //echo $list;
//
//       var_dump( Db::name("drg")->alias("dg")->join("__DR__ dr", "dg.doc_id=dr.id")->where("dg.gid", $gid)->field("dr.*,dg.gid")->where("dg.dossier_id", $id)->where("dr.exid", $ext_id)->where("dr.qx & $role >0 or dr.uid=$cuid")->fetchSql());
//


        return self::getFilesByDocIds($list);


    }


    private static function initFilesByGroup($id, $gid, $ext_id = 0)
    {
        $modeids = Constant::getGroupFileMode($gid);
        $modeinfo = self::getDocModeInfo($modeids);


        $role = User::getRoleInDossier($id);

        $cuid = User::getLoginUid();
        foreach ($modeids as $value) {

            $has = Db::name("dr")->where("doc_model_id", $value)->where("dossier_id", $id)->where("exid", $ext_id)->selectOfIndex("id");
            $modeinfo_one = $modeinfo[$value];

            if(input('www')==627){
                dump($modeinfo);
                dump($value);
                dump($id);
                dump($ext_id);

            }
            if ($ext_id != 0)
                if ($modeinfo_one['id'] == Constant::DOC_model_qitazhengju) {
                    continue;
                }


            if (!$has) {
                if ($modeinfo_one['id'] == Constant::DOC_model_qitazhengju) {
                    continue;
                }
//                var_dump($modeinfo_one['model_name']);
//                var_dump($modeinfo_one['id']);
//                var_dump($role);
//                var_dump(LoginUser::getRole());
//
//                var_dump($modeinfo_one['c_qx']);
//                var_dump($modeinfo_one['c_qx'] & $role);

                if (1 || ($modeinfo_one['c_qx'] & $role) > 0) {

                    if (!$cuid) {
                        echo "create file must in loging1";
                        exit;
                    }
                    $innsertdata = array("file_type" => $modeinfo_one["file_type"], "uid" => $cuid, "name" => $modeinfo_one["model_name"], "exid" => $ext_id, "doc_model_id" => $value, "dossier_id" => $id, "addtime" => time(), "create_type" => $modeinfo_one['create_type'], "mode_type" => $modeinfo_one['type']);


                    self::mInsertData($innsertdata, $modeinfo_one, $id, $role);

                    if ($docid = Db::name("dr")->insertGetId($innsertdata)) {


                        if ($modeinfo_one['to_sign']) {
                            Db::name("dr")->where("id", $docid)->update(["c_no" => "zc" . $id . "d" . $docid . 'd' . date("YmdHis") . rand(1, 10000)]);
                        }


                        if ($modeinfo_one['create_type'] == 1 && !$modeinfo_one['to_sign']) {
                            if ($pdffile = Constant::getInitFile($value)) {
                                Dvalue::addPdfToDoc($docid, $pdffile);
                            }
                        }

                    } else {
                        return false;
                    }


                } else {

                    continue;
                }


            } else {

                $docid = array_column((Array)$has, "id");
            }
//            var_dump(($modeinfo_one['c_qx'] & $role) );
            // var_dump($role);

            if (!is_array($docid)) {
                $docids = array($docid);
            } else {
                $docids = $docid;
            }
            foreach ($docids as $docid) {

                $r_no = $has[$docid]['r_no'] ? $has[$docid]['r_no'] : 0;

                if (!Db::name("drg")->where(array("gid" => $gid, "dossier_id" => $id, "doc_id" => $docid, "doc_mod_id" => $value, "exid" => $ext_id, "r_no" => $r_no))->find()) {


                    $drgid = Db::name("drg")->insertGetId(array("gid" => $gid, "addtime" => time(), "dossier_id" => $id, "doc_id" => $docid, "doc_mod_id" => $value, "exid" => $ext_id, "r_no" => $r_no));

                    if (!$drgid) return false;
                }
            }

        }
        return true;
    }


    private static function mInsertData(&$innsertdata, $modeinfo_one, $id, $c_qrole)
    {
        if ($modeinfo_one['to_sign']) {
            $innsertdata['to_sign'] = 1;
            $innsertdata['has_sign'] = 0;
            $innsertdata['c_class'] = $modeinfo_one['c_class'];
            $innsertdata['min_file_num'] = 1;

        } else {
            $innsertdata['to_sign'] = 0;
            $innsertdata['has_sign'] = 0;
            $innsertdata['min_file_num'] = $modeinfo_one['min_file_num'];
        }

        if ($modeinfo_one['des']) {
            $innsertdata['des'] = $modeinfo_one['des'];
        }
        if ($modeinfo_one['file_type'] == 1) {
            if (in_array($c_qrole, $qroles = array(Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_DL))) {
                $innsertdata['z_no'] = Db::name("dr")->where("dossier_id", $id)->whereIn("c_qrole", $qroles)->where("file_type", 1)->count() + 1;
            } else if (in_array($c_qrole, $qroles = array(Constant::QX_ROLE_BEISHENQINGREN, Constant::QX_ROLE_BEISHENQINGREN_DL))) {

                $innsertdata['z_no'] = Db::name("dr")->where("dossier_id", $id)->whereIn("c_qrole", $qroles)->where("uid",LoginUser::getIdid())->where("file_type", 1)->count() + 1;
            }
        }
        if ($modeinfo_one['file_type'] == 3) {

            $innsertdata['z_no'] = Db::name("dr")->where("dossier_id", $id)->where("file_type", 3)->count() + 1;

        }
        if (in_array($c_qrole, [Constant::QX_ROLE_SHENQINGREN, Constant::QX_ROLE_SHENQINGREN_DL, Constant::QX_ROLE_SHENQINGREN_fr])) {
            $innsertdata['c_qrole'] = Constant::QX_ROLE_SHENQINGREN;
            $innsertdata['u_name'] = LoginUser::getRoleThName();
        } else {
            $innsertdata['c_qrole'] = $c_qrole;
            $innsertdata['u_name'] = LoginUser::getUserName();
        }


    }

    public static function getOrInitFile($dossier_id, $doc_mod_id, $ext_id = 0, $reload = false)
    {
        static $out = array();

        if ($tmp = $out[$dossier_id . '_' . $doc_mod_id . "_" . $ext_id]) return $tmp;

        $has = Db::name("dr")->where("doc_model_id", $doc_mod_id)->where("dossier_id", $dossier_id)->where("exid", $ext_id)->find();
        $role = User::getRoleInDossier($dossier_id);
        if (!$has) {

            Log::error($doc_mod_id);
            Log::error($dossier_id);
            Log::error($ext_id);

            $modeinfo_one = self::getDocModeInfo($doc_mod_id);

            $cuid = User::getLoginUid();
            if (!$cuid) {
                echo "create file must in loging2";
                exit;
            }
            $innsertdata = array("file_type" => $modeinfo_one["file_type"], "uid" => $cuid, "name" => $modeinfo_one["model_name"], "exid" => $ext_id, "doc_model_id" => $doc_mod_id, "dossier_id" => $dossier_id, "addtime" => time(), "create_type" => $modeinfo_one['create_type'], "mode_type" => $modeinfo_one['type']);

            self::mInsertData($innsertdata, $modeinfo_one, $dossier_id, $role);


            if ($docid = Db::name("dr")->insertGetId($innsertdata)) {

                if ($modeinfo_one['to_sign']) {
                    Db::name("dr")->where("id", $docid)->update(["c_no" => "zc" . $dossier_id . "d" . $docid . 'd' . date("YmdHis") . rand(1, 10000)]);
                }
                if ($modeinfo_one['file_type'] == 1) {
                    Dvalue::addValue($docid, "zhengju_shuoming", "为了说明什么");
                }
                return self::getOrInitFile($dossier_id, $doc_mod_id, $ext_id);
            }


        }
        return $out[$dossier_id . '_' . $doc_mod_id . "_" . $ext_id] = $has;


    }

    public static function addZhengjuFile($dossier_id, $gid, $title, $descrption, $ext_id = 0, $doc_mod_id = 0)
    {
        $modeinfo_one = self::getDocModeInfo($doc_mod_id ? $doc_mod_id : Constant::DOC_model_qitazhengju);
        $role = User::getRoleInDossier($dossier_id);

        if (($modeinfo_one['c_qx'] & $role) > 0) {
            $cuid = User::getLoginUid();
            if (!$cuid) {
                echo "create file must in loging3";
                exit;
            }
            $role = User::getRoleInDossier($dossier_id);
            while (1) {
                $innsertdata = array("des" => $descrption, "file_type" => $modeinfo_one["file_type"], "r_no" => $doc_mod_id ? $doc_mod_id : mt_rand(500, 1000), "uid" => $cuid, "name" => $title == null ? $modeinfo_one["model_name"] : $title, "exid" => $ext_id, "doc_model_id" => Constant::DOC_model_qitazhengju, "dossier_id" => $dossier_id, "addtime" => time(), "create_type" => $modeinfo_one['create_type'], "mode_type" => $modeinfo_one['type']);
                self::mInsertData($innsertdata, $modeinfo_one, $dossier_id, $role);
                if ($docid = Db::name("dr")->insertGetId($innsertdata)) {

                    //   Dvalue::saveUniqueValue($docid, "zhengju_shuoming", $descrption);

                    $drgid = Db::name("drg")->insertGetId(array("gid" => $gid, "addtime" => time(), "dossier_id" => $dossier_id, "doc_id" => $docid, "doc_mod_id" => Constant::DOC_model_qitazhengju, "exid" => $ext_id, "r_no" => $innsertdata['r_no']));

                    if (!$drgid) return false;
                    return $docid;
                }
            }
        }
        return false;

    }


    public static function getDocModeInfo($ids, $mkcontent = false)
    {
        $_ids = $ids;
        if (!is_array($ids)) {
            $ids = explode(",", $ids);

        }


        $list = Db::name("dm")->whereIn("id", $ids)->selectOfIndex("id");
        if ($mkcontent) {
            $tmpfileroot = APP_PATH . "admin" . DS . "view" . DS . "wsdoc" . DS . "doc" . DS;


            $list = array_map(function ($value) use ($tmpfileroot) {
                if ($value['view_file']) {
                    $value['view_content'] = file_get_contents($tmpfileroot . $value['view_file']);
                }
                return $value;

            }, $list);
        }
        if (count($ids) == 1 && !is_array($_ids)) {
            return $list[$_ids];
        }
        return $list;

    }


    public static function reSign($idOrInfo)
    {
        if (is_array($idOrInfo)) {
            $info = $idOrInfo;
        } else {
            $info = self::getDocInfo($idOrInfo);
        }
        $docinfo = array();
        $docinfo['c_no'] = $info['c_no'] . "r";
        $docinfo['has_sign'] = 0;
        Db::name("dr")->where("id", $info['id'])->update($docinfo);
        Db::name("drs")->where("doc_id", $info['id'])->delete();
    }

    public static function getGroupFilesHtml($controlerr, $d_id, $gid, $extid, $title, $class = "")
    {
        $controlerr->useLayout(false);
        return $controlerr->fetch("dossier/info/files", array("sqfiles" => Ddocs::getFilesByGroupExist($d_id, $gid, $extid), "class" => $class, "title" => $title));
    }

    public static function getFilesHtml($controlerr, $docids_or_docs, $title, $class = "", $tip = "", $style = "", $showlaiyuan = 1,$is_phone=false,$phone=0)
    {
        $controlerr->useLayout(false);
        $list = Ddocs::getFilesByDocIds($docids_or_docs);
        if($is_phone){
            return $list;
        }
        if ($list){
            $controlerr->assign('is_phone',$phone);
            return $controlerr->fetch("dossier/info/files" . $style, array("sqfiles" => $list, "class" => $class, "title" => $title, "tip" => $tip, "showlaiyuan" => $showlaiyuan));
        } else{
            return '';
        }
    }

    public static function getFilesLayout($controlerr, $html, $showlaiyuan = 1)
    {
        $controlerr->useLayout(false);
        return $controlerr->fetch("dossier/info/files_layout", array("html" => $html, "showlaiyuan" => $showlaiyuan));

    }

    public static function getDocInfo($docid)
    {
        return Db::name("dr")->find($docid);
    }

    public static function getSignerByDocInfo($info)
    {
        $retun = [];
        if (in_array($info['doc_model_id'], array_keys(Constant::getZhongCaiWeiSignDocIds()))) {
            $zhuren = Drole::getUsersByRole($info['dossier_id'], Constant::D_Role_ZhongCaiWei_LiAnShenPi);
            if ($zhuren) {
                $retun[] = $zhuren[0]['idid'];
            }
            return $retun;
        }
        return [$info['uid']];
    }
}


