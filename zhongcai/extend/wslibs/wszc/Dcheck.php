<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/5/10
 * Time: 上午8:53
 */

namespace wslibs\wszc;


use think\Db;

class Dcheck
{
    public static function checkSubmitFiles($did, $gids, $extid, $uid)
    {

        if(input('lee')==32){
            dump($gids);
        }
        foreach ($gids as $gid) {

            $doces = Ddocs::getFilesByGroup($did, $gid, $extid);
            if(input('www')=='06021'){
                dump($gid);
                dump($doces);
                exit;
            }
            if (!$doces) return false;
            $zhengju_num = 0;
            foreach ($doces as $value) {
                if (((int)$value['_file_num']) < ((int)$value['min_file_num'])) {
                    if(input('www')=='0602'){
                        dump(2);
                    }
                    return false;
                }

//                var_dump($value['to_sign']);
//                var_dump($value['show_sign']);
                // var_dump($value);
                if ($value['to_sign'] && ($value['show_sign'] == 1)) {
                    if(input('www')=='0602'){
                        dump(55);
                        dump($value);
                        exit;
                    }
                    return false;
                }
                if ($value['file_type'] == 1)
                    $zhengju_num = $zhengju_num + 1;


                if ($value['file_type'] == 1) {
                    if(input('www')=='0602'){
                        dump(4);
                    }
                    if (!$value['des']) return false;
                }

            }

            if (in_array(Constant::DOC_model_qitazhengju, Constant::getGroupFileMode($gid))) {
                if(input('www')=='0602'){
                    dump(5);
                }
                if ($zhengju_num == 0) return false;
            }


        }

        return true;
    }


    public static function checkArbitratorCanDo($idid)
    {


        $info = Db::name('zcy')->where("idid", $idid)->find();

        if (!$info['speciality']) return false;
        if (!$info['address']) return false;
        if (!$info['province']) return false;
        if (!$info['city']) return false;
        if (!$info['district']) return false;
        if (!$info['avatar']) return false;

        return true;
    }
}