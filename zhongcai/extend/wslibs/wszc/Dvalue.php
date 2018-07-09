<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/8
 * Time: 下午7:48
 */

namespace wslibs\wszc;


use think\Config;
use think\Db;

class Dvalue
{


    public static function mkFileValue($value, $title = null)
    {
        return array("not_json" => 1, "val" => $value, "title" => $title);
    }

    public static function saveUniqueValueByDocMode($dossier_id, $mod_id, $var_name, $value, $exid = 0)
    {
        return self::saveToDoc(Ddocs::getOrInitFile($dossier_id, $mod_id, $exid)['id'], $var_name, $value, 0, 0, true, true);
    }

    //存储文件、图片

    public static function addFileToDocByDocMode($dossier_id, $mod_id, $path, $uid, $exid = 0)
    {
        return self::saveToDoc(Ddocs::getOrInitFile($dossier_id, $mod_id, $exid)['id'], Constant::Dvalue_var_name_file, $path, $exid, $uid, false, false);
    }

    //设置普通字段
    public static function saveUniqueValue($doc_id, $var_name, $value)
    {
        return self::saveToDoc($doc_id, $var_name, $value, 0, 0, false, true);
    }

    //直接添加到卷宗。类似于公共字段
    public static function saveUniqueValueToDossier($did, $var_name, $value)
    {
        return self::saveToDoc(0, $var_name, $value, 0, 0, true, true, $did);
    }

    //设置普通字段多个
    public static function addValue($doc_id, $var_name, $value)
    {
        return self::saveToDoc($doc_id, $var_name, $value, 0, 0, false, false);
    }

    //存储文件、图片

    public static function addFileToDoc($doc_id, $path, $fid, $uid = 0)
    {
        return self::saveToDoc($doc_id, Constant::Dvalue_var_name_file, $path, $fid, $uid, false, false);
    }

    //存储文件、图片

    public static function addSignToDoc($doc_id, $path, $uid, $weizhi = 0)
    {
        return self::saveToDoc($doc_id, Constant::Dvalue_var_name_sign, $path, $weizhi, $uid, false, true);
    }

    //生成pdf后存储
    public static function addPdfToDoc($doc_id, $path, $uid = 0, $weizhi = 0)
    {
        return self::saveToDoc($doc_id, Constant::Dvalue_var_name_pdf, $path, $weizhi, $uid, false, true);
    }


    //获取文件的所有图片
    public static function getDocFiles($docid, $attachment = true)
    {

        if ($attachment) {
            $list = Db::name("drv")
                ->alias("d")
                ->join("attachment a", "d.ext_id=a.id")
                ->where("doc_id", $docid)
                ->where("var_name", Constant::Dvalue_var_name_file)
                ->field("a.*,d.*")
                ->select();

            $cdnurl = preg_replace("/\/(\w+)\.php$/i", '', request()->root());
            foreach ($list as $k => &$v) {
                $v['fullurl'] = stripos($v['url'], "ttp:") > 0 ? $v['url'] : (($v['storage'] == 'local' ? $cdnurl : \Think\Config::get("upload.cdnurl")) . $v['url']);
            }
        } else {
            $list = Db::name("drv")
                ->where("doc_id", $docid)
                ->where("var_name", Constant::Dvalue_var_name_file)
                ->select();


            $img_types = ['png', 'gif', 'jpg', 'jpeg', 'psd', 'tiff', 'bmp', 'eps', 'tga'];
            $video_types = ['avi', 'wma', 'mpeg', 'mp4', 'mov', 'mkv', 'flv', 'f4v', 'm4v', 'rmvb', 'rm', '3gp', 'dat', 'ts', 'mts', 'vob'];
            foreach ($list as $k => &$v) {
                $v['fullurl'] = stripos($v['val'], "ttp:") > 0 ? $v['val'] : (\Think\Config::get("upload.cdnurl") . $v['val']);
                $v['var_type'] = in_array(strtolower($v['var_type']), $img_types) ? 'png' : (in_array(strtolower($v['var_type']), $video_types) ? 'mp4' : $v['var_type']);
            }
        }


        return $list;
    }

    public static function getDocPdf($doc_id)
    {
        return self::getUniqueValueOfDoc($doc_id, Constant::Dvalue_var_name_pdf);
    }

    public static function getDocSign($doc_id, $uid = 0, $weizhi = 0)
    {
        return self::getUniqueValueOfDoc($doc_id, Constant::Dvalue_var_name_sign, $weizhi, $uid);
    }

    public static function getDocSignsByWeiZhi($doc_id, $weizhi = 0)
    {
        return self::getListValueOfDoc($doc_id, Constant::Dvalue_var_name_sign, $weizhi);
    }

    public static function saveToDoc($doc_id, $var_name, $value, $ext_id = 0, $uid = 0, $unique_in_dossier = false, $unique_in_doc = false, $d_id = 0)
    {
        $dinfo = Db::name("dr")->where("id", $doc_id)->find();
        $d_id = $d_id ? $d_id : $dinfo['dossier_id'];
        if (!$d_id) exit("wrong");

        $var_josn = null;
        if (is_array($value)) {
            if ($value['not_json']) {
                $var_josn = $value['title'];
                $value = $value['val'];
            }
        }


        $data_value = ["val" => !is_array($value) ? $value : "_json_", "val_json" => is_array($value) ? json_encode($value) : $var_josn];

        if (in_array($var_name, array(Constant::Dvalue_var_name_file, Constant::Dvalue_var_name_pdf))) {

            $data_value['var_type'] = pathinfo($value, PATHINFO_EXTENSION);
        } else if (in_array($var_name, array(Constant::Dvalue_var_name_sign))) {

            $data_value['var_type'] = "png";
        } else {
            if (is_array($value)) {
                $data_value['var_type'] = "json";
            } else {
                $data_value['var_type'] = "string";
            }


        }


        $data = array_merge(['doc_id' => $doc_id, "dossier_id" => $d_id, "var_name" => $var_name, "ext_id" => $ext_id, "uid" => $uid, "createtime" => time()], $data_value);


        if ($unique_in_dossier) {
            if ($find = self::getUniqueValueOfDossier($d_id, $var_name, $ext_id)) {
                return Db::name("drv")->where("var_name", $var_name)->where("dossier_id", $d_id)->update($data_value);

            }
        }

        if ($unique_in_doc && $doc_id) {
            if ($find = self::getUniqueValueOfDoc($doc_id, $var_name, $ext_id)) {
                return Db::name("drv")->where("var_name", $var_name)->where("doc_id", $doc_id)->update($data_value);

            }

        }

        $outid = Db::name("drv")->insertGetId($data);

        $upnumdata = array();

        if ($dinfo['to_sign']) {
            $upnumdata['file_num'] = 1;
        } else {


            if (in_array($var_name, array(Constant::Dvalue_var_name_file, Constant::Dvalue_var_name_pdf))) {
                $upnumdata['file_num'] = $dinfo['file_num'] + 1;

            } else if (in_array($var_name, array(Constant::Dvalue_var_name_sign))) {
                $upnumdata['sign_num'] = $dinfo['sign_num'] + 1;

            } else {

                $upnumdata['var_num'] = $dinfo['var_num'] + 1;
            }
        }


        if ($upnumdata)
            Db::name("dr")->where("id", $doc_id)->update($upnumdata);

        return $outid;


    }

    public static function saveDocValueByid($vid, $value)
    {
        $data_value = ["val" => !is_array($value) ? $value : "_json_", "val_json" => is_array($value) ? json_encode($value) : null, "createtime" => time()];
        return Db::name("drv")->where("id", $vid)->update($data_value);


    }


    public static function delDocImgs($doc_id)
    {
        return self::delDocValue($doc_id, Constant::Dvalue_var_name_file);
    }

    public static function delPdf($doc_id)
    {
        return self::delDocValue($doc_id, Constant::Dvalue_var_name_pdf);
    }

    public static function delDocValue($doc_id, $var_name, $ext_id = 0, $uid = 0)
    {
        $query = Db::name("drv")->where("var_name", $var_name);
        if ($var_name) {
            $query->where("doc_id", $doc_id);
        }
        if ($ext_id) {
            $query->where("ext_id", $ext_id);
        }
        if ($uid) {
            $query->where("uid", $uid);
        }


        $num = $query->delete();
        if ($var_name && ($num > 0)) {
            self::decFieldNum($doc_id, $var_name, $num);
        }
        return $num;
    }


    private static function decFieldNum($docid, $var_name, $num)
    {
        $out = "";
        if (in_array($var_name, array(Constant::Dvalue_var_name_file, Constant::Dvalue_var_name_pdf))) {
            $out = "file_num";

        } else if (in_array($var_name, array(Constant::Dvalue_var_name_sign))) {

            $out = "sign_num";

        } else {


            $out = "var_num";
        }
        Db::name('dr')->where('id', $docid)->setDec($out, $num);
    }

    public static function delDocValueByid($vid)
    {
        $docidnfo = Db::name("drv")->where("id", $vid)->find();
        $query = Db::name("drv")->where("id", $vid);
        $num = $query->delete();
        if (($num > 0)) {
            self::decFieldNum($docidnfo['doc_id'], $docidnfo['var_name'], $num);
        }
        return $num;
    }


    public static function getUniqueValueOfDossier($did, $var_name = null, $ext_id = 0)
    {

        $cuid = (int)User::getLoginUid();
        $role = User::getRoleInDossier($did);

        return self::getValue($var_name ? Db::name("drv")->where("dossier_id", $did)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("ext_id", $ext_id)->where("var_name", $var_name)->find()

            : Db::name("drv")->where("dossier_id", $did)->where("var_name", $var_name)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("ext_id", $ext_id)->selectOfIndex("var_name"));


    }

    public static function getUniqueValueOfDoc($doc_id, $var_name = null, $ext_id = 0, $uid = 0)
    {

        $cuid = User::getLoginUid();
        $role = User::getRoleInDossier(self::getDidFromDocid($doc_id));
        return self::getValue($var_name ? Db::name("drv")->where("doc_id", $doc_id)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("ext_id", $ext_id)->where("uid", $uid)->where("var_name", $var_name)->order("id desc")->find()

            : Db::name("drv")->where("doc_id", $doc_id)->where("ext_id", $ext_id)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("uid", $uid)->selectOfIndex("var_name"));
    }


    private static function getDidFromDocid($docid)
    {
        return Db::name("dr")->where("id", $docid)->value("dossier_id");
    }

    public static function getListValueOfDossier($did, $var_name = null, $ext_id = 0)
    {
        $cuid = User::getLoginUid();
        $role = User::getRoleInDossier($did);
        return self::getValues($var_name ? Db::name("drv")->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("dossier_id", $did)->where("ext_id", $ext_id)->where("var_name", $var_name)->select()

            : Db::name("drv")->where("dossier_id", $did)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("var_name", $var_name)->where("ext_id", $ext_id)->select(), $var_name);


    }

    public static function getListValueOfDoc($doc_id, $var_name = null, $ext_id = 0)
    {
        $cuid = User::getLoginUid();
        $role = User::getRoleInDossier(self::getDidFromDocid($doc_id));
        return self::getValues($var_name ? Db::name("drv")->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("doc_id", $doc_id)->where("ext_id", $ext_id)->where("var_name", $var_name)->select()

            : Db::name("drv")->where("doc_id", $doc_id)->where(" uid=0 or qx & $role >0 or uid=$cuid ")->where("var_name", $var_name)->where("ext_id", $ext_id)->select(), $var_name);
    }


    public static function getValue($row)
    {

        if (!$row) return null;
        if (isset($row['val'])) {
            return $row['val'] == "_json_" ? json_decode($row['val_json'], true) : $row['val'];
        } else {
            foreach ($row as $key => $value) {
                $row[$key] = self::getValue($value);
            }

            return $row;
        }

    }

    private static function getValues($list, $var_name = null)
    {
        $out = array();
        foreach ($list as $value) {
            $out[$value['var_name']][] = self::getValue($value);
        }


        return $var_name ? $out[$var_name] : $out;
    }


}