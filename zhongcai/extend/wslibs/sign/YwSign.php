<?php

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2017/3/17
 * Time: 上午10:32
 */
namespace wslibs\sign;

use wslibs\cunchuio\CunChuIO;

class YwSign
{

    public static $_savePath = 'uploads/sign/gongzheng/ybg/';
    public static $_savePath_yun = 'sign/gongzheng/';//ybg/

    public static function getOrgDir($ywid, $short = false)
    {
        if ($short) {
            return "$ywid/files/";
        }
        return (IS_YUN ? self::$_savePath_yun : self::$_savePath) . $ywid . "/files/";
    }

    public static function getHtmlToImgPre($ywid)
    {
        return self::getHtmlPreDir() . $ywid . "/files/";
    }

    public static function getHtmlPreDir()
    {
        return (!IS_LOC) ? "" : 'uploads/sign/gongzheng/ybg/';//ybg/
    }

    public static function getHttpPre()
    {
        return (IS_YUN ? IMG_SITE_ROOT . self::$_savePath_yun : WEB_SITE_ROOT . self::$_savePath);
    }


    public static function commonSaveImageBase64($ywid, $img64)
    {
        $filename = self::getFilename();
        $imguri = self::saveImgFile($ywid, $filename, base64_decode($img64), true);
        return $imguri;
    }

    public static function saveFujian($ywid)
    {
        import('ORG.Net.UploadFile');
        import('ORG.Net.imgMark');
        $upload = new UploadFile(); //实例化上传类

        $upload->maxsize = 3145728;

        $upload->autoSub = "true";
        $upload->subType = "date";
        $upload->allowExts = array('jpeg', "png", "jpg");
        if (IS_YUN) {
            $upload->savePath = self::getOrgDir($ywid) . "fujian/";
        } else
            $upload->savePath = NAS_WSZX_DIR . self::getOrgDir($ywid) . "fujian/";
        if (!is_dir($upload->savePath)) {
            @mkdir($upload->savePath, 0777, true);

        }


        if (!$upload->upload()) {
            var_dump($upload->getErrorMsg());

            return false;
        } else {
            $info = $upload->getUploadFileInfo();

            $paths = array();
            foreach ($info as $value) {
                $paths[] = self::getOrgDir($ywid, true) . "fujian/" . $value['savename'];
            }
            // $this->add($info);
        }

        return implode(";", $paths);
    }

    public static function saveMp4($ywid)
    {
        import('ORG.Net.UploadFile');
        import('ORG.Net.imgMark');
        $upload = new UploadFile(); //实例化上传类

        $upload->maxsize = 3145728;

        $upload->autoSub = "true";
        $upload->subType = "date";
        $upload->allowExts = array('mp4', "MP4");
        if (IS_YUN) {
            $upload->savePath = self::getOrgDir($ywid) . "mp4/";
        } else
            $upload->savePath = NAS_WSZX_DIR . self::getOrgDir($ywid) . "mp4/";
        if (!is_dir($upload->savePath)) {
            @mkdir($upload->savePath, 0777, true);

        }

        if (!$upload->upload()) {

            return false;
        } else {
            $info = $upload->getUploadFileInfo();
            $paths = array();
            foreach ($info as $value) {
                $paths[] = self::getOrgDir($ywid, true) . "mp4/" . $value['savename'];
            }
            if (count($paths) == 0) {
                return false;
            }
            // $this->add($info);
        }
        return implode(";", $paths);
    }

    public static function saveFile($orgid, $filename, $content, $shortPath = false)
    {


        if (IS_YUN) {

            $fullPath = self::$_savePath_yun  ;

            $returnPath =   $filename;
            $blob_name = $fullPath . $filename;


            if (CunChuIO::uploadContent($blob_name, $content)) {
                return $returnPath;
            }

            return false;
        } else {
            $date = date('Ymd');
            $fullPath = NAS_WSZX_DIR . self::getOrgDir($orgid) . "sign/" . $date;
            !is_dir($fullPath) ? @mkdir($fullPath, 0777, true) : '';
            $returnPath = WEB_SITE_ROOT . self::getOrgDir($orgid) . "sign/" . $date . '/' . $filename;
            if ($shortPath) {
                $returnPath = self::getOrgDir($orgid, true) . "sign/" . $date . '/' . $filename;
            }
            if (file_put_contents($fullPath . '/' . $filename, $content)) {
                return $returnPath;
//            if (!(false === getimagesize($fullPath . '/' . $filename))) {
//                return $returnPath;
//            } else {
//                unlink($fullPath . '/' . $filename);
//                return false;
//            }
            } else {
                return false;
            }
        }
    }

    public static function saveImgFile($orgid, $filename, $content, $shortPath = false)
    {


        if (IS_YUN) {
            $date = date('Ymd');
            $fullPath = self::$_savePath_yun . $date;

            $returnPath = $date . '/' . $filename;
            $blob_name = $fullPath . '/' . $filename;

            importExternal("Azure/CunChuIO");
            if (CunChuIO::uploadContent($blob_name, $content)) {
                return $returnPath;
            }

            return false;
        } else {
            $date = date('Ymd');
            $fullPath = NAS_WSZX_DIR . self::getOrgDir($orgid) . "sign/" . $date;
            !is_dir($fullPath) ? @mkdir($fullPath, 0777, true) : '';
            $returnPath = WEB_SITE_ROOT . self::getOrgDir($orgid) . "sign/" . $date . '/' . $filename;
            if ($shortPath) {
                $returnPath = self::getOrgDir($orgid, true) . "sign/" . $date . '/' . $filename;
            }
            if (file_put_contents($fullPath . '/' . $filename, $content)) {
                return $returnPath;
//            if (!(false === getimagesize($fullPath . '/' . $filename))) {
//                return $returnPath;
//            } else {
//                unlink($fullPath . '/' . $filename);
//                return false;
//            }
            } else {
                return false;
            }
        }
    }

    public static function getFilename()
    {
        return uniqid() . '.png';
    }

    public static function saveZip($ywid)
    {
        //return $_FILES;
        import('ORG.Net.UploadFile');
        import('ORG.Net.imgMark');
        $upload = new UploadFile(); //实例化上传类

        $upload->maxsize = 52428800;

        $upload->autoSub = "true";
        $upload->subType = "date";
        $upload->allowExts = array('zip', "rar");
        if (IS_YUN) {
            $upload->savePath = self::getOrgDir($ywid) . "fujian/";
        } else
            $upload->savePath = NAS_WSZX_DIR . self::getOrgDir($ywid) . "fujian/";
        if (!is_dir($upload->savePath)) {
            @mkdir($upload->savePath, 0777, true);

        }


        if (!$upload->upload()) {

            var_dump($upload->getErrorMsg());

            return false;
        } else {
            $info = $upload->getUploadFileInfo();
//            dump($info);exit;
            $paths = array();
            foreach ($info as $value) {
                $paths[] = self::getOrgDir($ywid, true) . "fujian/" . $value['savename'];
            }
            // $this->add($info);
        }

        return implode(";", $paths);
    }
}