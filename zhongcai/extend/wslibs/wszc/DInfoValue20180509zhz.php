<?php
namespace wslibs\wszc;
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/9
 * Time: ����9:37
 *
 * $d.info ���ڱ�
 *
 *
 * $d.users.role[1]
 * $d.users.id[1]
 * $d.users.me
 *
 *
 *  $d.defence.this  //��� info
 *  $d.defence.all    //list
 *  $d.defence.me    //info
 *
 *
 * $d.question.this
 * $d.question.me //list
 * $d.question.all
 * $d.question.zid[zid]  //֤��id list
 *
 *$d.me //��ǰ�û�
 *$d.uniquevalue //Ψһ����
 *$d.morevalue //�����
 *$d.dvalue.this.uniquevalue //Ψһ����
 *$d.dvalue.this.morevalue //�����
 *$d.dvalue.docid[1].uniquevalue //Ψһ����
 *$d.dvalue.docid[1].morevalue //�����
 */
class DInfoValue
{


    private $info = array();
    private $dossier_id = 0;

    public function __construct($dossier_id)
    {
        $this->dossier_id = $dossier_id;
    }


    public static function getTemplateData($doc_id, $uid)
    {


        $docinfo = \think\Db::name("dr")->find($doc_id);
        $ywid = $docinfo['exid'];
        $dinfo = new DInfoValue($docinfo['dossier_id']);
        $out = ['users' => [], "defence" => [], "question" => []];
        $out['users']['all'] = $dinfo->getUsers();
        $out['users']['role'] = $dinfo->makeListArray($out['users']['all'], "role");
        $out['users']['id'] = $dinfo->makeUniqueArray($out['users']['all'], "idid");
        $out['me'] = $out['users']['me'] = $dinfo->makeUniqueArray($out['users']['all'], "idid", $uid);

        $out['defence']['all'] = $dinfo->getDefences();
        $out['defence']['this'] = $dinfo->makeUniqueArray($out['defence']['all'], "id", $ywid);
        $out['defence']['me'] = $dinfo->makeUniqueArray($out['defence']['all'], "uid", $uid);

        $out['question']['all'] = $dinfo->getQuestion();
        $out['question']['this'] = $dinfo->makeUniqueArray($out['question']['all'], "id", $ywid);
        $out['question']['me'] = $dinfo->makeListArray($out['question']['all'], "uid", "", $uid);
        $out['question']['zid'] = $dinfo->makeListArray($out['question']['all'], "evidence_id", "id");


        $allvalues = $dinfo->getDvalues();
        $dinfo->fenlei($allvalues, $out);

        $out['dvalue']['this'] = [];
        $dinfo->fenlei($dinfo->makeListArray($allvalues, "doc_id", "id", $doc_id));
        $tmp = $dinfo->makeListArray($allvalues, "doc_id");

        foreach ($tmp as $key => $item) {
            $out['dvalue']['docid'][$key] = $dinfo->fenlei($item);
        }
        if ($_GET['_rlr'])
            var_dump($out);
        return $out;


    }


    public function fenlei($allvalues, &$out = array())
    {
        $out['uniquevalue'] = $this->makeUniqueArray($allvalues, "var_name", null, function ($value) {
            return \wslibs\wszc\Dvalue::getValue($value);
        });
        $out['morevalue'] = $this->makeListArray($allvalues, "var_name", "", null, function ($value) {
            return \wslibs\wszc\Dvalue::getValue($value);
        });
        return $out;
    }

    public function getUsers()
    {
        if ($this->info["users"]) {
            return $this->info["users"];
        }
        $users = \wslibs\wszc\Dossier::getDangShiRen($this->dossier_id);

        return $this->info["users"] = array_map(function ($value) {
            $value['html_show'] = '<p>姓名:'.$value['name'].'、性别:'.$value['sex'].'、民族:'.$value['minzu'].'、工作单位:'.$value['company'].'、身份证号:'.$value['id_num'].'、邮政编码:'.$value['code'].'、手机号:'.$value['phone'].'、法人名称:'.$value['f_name'].'、手机号:'.$value['f_phone'].'、法人身份证号:'.$value['f_id_card'].'</p>';

            return $value;
        }, $users);
    }

    public function getDvalues()
    {
        if ($this->info["dvalues"]) {
            return $this->info["dvalues"];
        }
        return $this->info["dvalues"] = \think\Db::name("drv")->where("dossier_id", $this->dossier_id)->select();

    }

    public function getFiles()
    {
        if ($this->info["dvalues"]) {
            return $this->info["dvalues"];
        }
        return $this->info["dvalues"] = \think\Db::name("drv")->where("dossier_id", $this->dossier_id)->select();

    }

    public function getDefences()
    {
        if ($this->info["defence"]) {
            return $this->info["defence"];
        }
        return $this->info["defence"] = \think\Db::name("dossier_defence")->where("dossier_id", $this->dossier_id)->select();
    }


    public function getQuestion()
    {
        if ($this->info["question"]) {
            return $this->info["question"];
        }
        return $this->info["question"] = \think\Db::name("dossier_question")->where("dossier_id", $this->dossier_id)->select();
    }

    public function makeUniqueArray($array, $key, $keyvalue = null, $value_key_or_function = null)
    {
        $out = [];
        foreach ($array as $value) {
            if ($keyvalue) {
                if ($keyvalue == $value[$key]) {
                    $out = self::valueKeyOrFunction($value, $value_key_or_function);
                }
            } else
                $out[$value[$key]] = self::valueKeyOrFunction($value, $value_key_or_function);
        }
        return $out;
    }

    public function makeListArray($array, $key, $listindex = "", $keyvalue = null, $value_key_or_function = null)
    {
        $out = [];
        foreach ($array as $value) {

            if ($keyvalue) {

                if ($keyvalue == $value[$key]) {
                    if ($listindex) {
                        $out[$value[$listindex]] = self::valueKeyOrFunction($value, $value_key_or_function);
                    } else
                        $out[] = self::valueKeyOrFunction($value, $value_key_or_function);

                }

            } else {

                if ($listindex) {
                    $out[$value[$key]][$value[$listindex]] = self::valueKeyOrFunction($value, $value_key_or_function);
                } else
                    $out[$value[$key]][] = self::valueKeyOrFunction($value, $value_key_or_function);
            }

        }
        return $out;
    }

    public static function valueKeyOrFunction($value, $callable)
    {
        if (is_callable($callable)) {
            return $callable($value);
        } else {
            return $value;
        }
    }

}