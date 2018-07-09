<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/2
 * Time: ÉÏÎç11:27
 */

namespace wslibs\wszc;


use think\Db;
use wslibs\wsform\GroupItem;
use wslibs\wsform\GroupMoreItem;
use wslibs\wsform\InputType;
use wslibs\wsform\Item;
use wslibs\wsform\WsForm;

class DocAttr
{
    public static function getAttrForm($doc_mod_id, $args = array())
    {


        function mkform(WsForm &$form, $pid, $list, &$args)
        {
            foreach ($list as $k => $v) {
                if ($v['gid'] == $pid) {
                    $item = null;
                    if ($v['sub_type'] == 0) {
                        if ($v['is_more']) {
                            $item = new GroupMoreItem($form, $v['name'], $v['flag']);
                            if (!$args[$v['flag'] . "_num"]) {
                                $args[$v['flag'] . "_num"] = 1;
                            }
                            mkform($item, $v['id'], $list, $args);
                        } else {
                            $item = new GroupItem($form, $v['name'], $v['flag']);
                            mkform($item, $v['id'], $list, $args);
                        }
                    } else if ($v['sub_type'] == 1) {

                        $item = new Item();
                        $item->inputType(InputType::text)->varName($v['flag']);
                        $item->varTitle($v['name']);
                        // $item->required();
                    }

                    if ($item) {

                        $form->addItem($item);
                    }


                }
            }
        }

        $form = new WsForm();
        $list = Db::name("dma")->where("doc_model_id", $doc_mod_id)->where("type", "1")->select();
        mkform($form, 0, $list, $args);

        $form->addDisplayData($args);
        return $form;

    }


    public static function editFormAttr($docid, $data)
    {

        self::delFormAttr($docid);


        $info = Db::name("dr")->find($docid);

        $doc_mod_id = $info['doc_model_id'];
        $attrlist = Db::name("dma")->where("doc_model_id", $doc_mod_id)->where("type", "1")->selectOfIndex("flag");


        $stack = array(array($data, null, 0));
        while ($item = array_shift($stack)) {
            $key = $item[1];
            $value = $item[0];
            $pid = $item[2];
            if ($key === null) {
                foreach ($value as $_k => $_v) {
                    array_push($stack, array($_v, $_k, $pid));
                }
            } else {
                $attrinfo = $attrlist[$key];
                $insertdata = array("doc_id" => $docid, "attr_id" => $attrinfo['id'], "status" => 1, "createtime" => time(), "attr_var" => $attrinfo['flag']);
                $insertdata['ext_id'] = $pid;
                $insertdata['pid'] = $pid;
                if ($attrinfo['sub_type'] == 0) {


                    if ($attrinfo['is_more']) {
                        $insertdata['value'] = "list";
                        $insertid = Db::name("drav")->insertGetId($insertdata);

                        foreach ($value as $_k => $_v) {
                            array_push($stack, array($_v, $_k, $insertid));
                        }


                    } else {
                        $insertdata['value'] = "array";
                        $insertid = Db::name("drav")->insertGetId($insertdata);
                        foreach ($value as $_k => $_v) {
                            array_push($stack, array($_v, $_k, $insertid));
                        }
                    }

                } else {

                    if (!is_array($value)) {
                        $value = array($value);
                    } else {
                        unset($value[count($value) - 1]);
                    }
                    foreach ($value as $_value) {
                        $insertdata['value'] = $_value;
                        $insertid = Db::name("drav")->insertGetId($insertdata);
                    }

                }

            }
        }


    }


    public static function addDocFile($docid, $title, $path, $ext_id = 0)
    {
        $info = Db::name("dr")->find($docid);
        $doc_mod_id = $info['doc_model_id'];

        $imgattr = Db::name("dma")->where("doc_model_id", $doc_mod_id)->where("type", 3)->find();
        $aid = $imgattr['id'];
        $aname = $imgattr['flag'];

        $insertdata = array("doc_id" => $docid, "attr_id" => $aid, "status" => 1, "createtime" => time(), "attr_var" => $aname);
        $insertdata['ext_id'] = $ext_id;
        $insertdata['pid'] = 0;
        $insertdata['value'] = $title;
        $insertdata['path'] = $path;
        return Db::name("drav")->insertGetId($insertdata);

    }


    public static function getDocFiles($docid)
    {
        $info = Db::name("dr")->find($docid);
        $doc_mod_id = $info['doc_model_id'];

        $imgattr = Db::name("dma")->where("doc_model_id", $doc_mod_id)->where("type", 3)->find();
        $aid = $imgattr['id'];

        $list = Db::name("drav")->alias("d")->join("attachment a","d.ext_id=a.id")->where("doc_id", $docid)->where("attr_id", $aid)->field("a.*,d.*")->select();
        return $list;
    }


    public static function delFormAttr($docid, $doc_mod_id = 0)
    {
        if (!$doc_mod_id) {
            $info = Db::name("dr")->find($docid);

            $doc_mod_id = $info['doc_model_id'];
        }


        return Db::name("drav")->where("doc_id", $docid)->where("attr_id", "in", function ($query) use ($doc_mod_id) {
            $query->name('dma')->where('type', 1)->where("doc_model_id", $doc_mod_id)->field('id');
        })->delete();
    }

    public static function getFormAttr($docid)
    {
        $list = Db::name("drav")->alias("ra")->join("dma ma", "ra.attr_id=ma.id")->field("ra.*,ma.type,ma.sub_type,ma.is_more")->where("doc_id", $docid)->selectOfIndex("ra.id");//->where("ma.type", 1)

        $out = array();
        $args = array();
        $stack = array(array(0, &$out));
        while ($item = array_shift($stack)) {


            $pid = $item[0];
            $pdata = &$item[1];

            foreach ($list as $value) {


                if ($value['pid'] == $pid) {

                    if ($value['sub_type'] == 0) {

                        $pdata[$value['attr_var']] = array();

                        array_push($stack, array($value['id'], &$pdata[$value['attr_var']]));


                    } else {


                        if ($list[$pid] && ($list[$pid]['sub_type'] == 0) && ($list[$pid]['is_more'])) {

                            $pdata[$value['attr_var']][] = $value['value'];
                            $args[$list[$pid]['attr_var'] . "_num"] = count($pdata[$value['attr_var']]);
                        } else {
                            $pdata[$value['attr_var']] = $value['value'];
                        }
                    }
                }
            }

        }


        $out['__ARGS__'] = $args;


        return $out;
    }

}