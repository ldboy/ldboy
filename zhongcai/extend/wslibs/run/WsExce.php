<?php
namespace wslibs\run;

use think\Session;

/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/9
 * Time: 上午10:48
 */
class WsExce
{
    public static function exce($cmd)
    {
        chdir(ROOT_PATH);

        if (IS_WIN) {

            pclose(popen('start /B ' . $cmd, 'r'));
        } else {
            pclose(popen($cmd . ' > /dev/null &', 'r'));
        }
    }


    public static function dsend($dossier_id, $gid, $ext_id)
    {
        self::exce("php think dsend -d $dossier_id -g $gid -e $ext_id -s " . Session::getSessionId());//start /b
    }

    public static function exceSync($cmd)
    {
        chdir(ROOT_PATH);
 

        passthru($cmd, $out);
        var_dump($out);

    }
}