<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/6/11
 * Time: ����9:30
 */

namespace app\admin\controller;

/**
 * ���ģ���ļ�����  http://zcw.wszx.cc/index.php/admin/cli/clear_temp_ahce
 * �����־�ļ�  http://zcw.wszx.cc/index.php/admin/cli/clear_log_chache
 * ������л���  http://zcw.wszx.cc/index.php/admin/cli/cache
 * �����������ݿ��ֶλ���  http://zcw.wszx.cc/index.php/admin/cli/optimize
 */
use wslibs\run\WsExce;

class Cli
{


    public function e()
    {

         WsExce::dsend(137,13,0);
        return "";
    }
    public function optimize()
    {
        WsExce::exce("php think optimize:schema");
        // WsExce::exce("php think optimize:schema");
        // WsExce::exce("php think optimize:schema");
        exit;
    }





    public function min()
    {
        WsExce::exceSync("node -v");
        WsExce::exceSync("php think min -m backend -r js");
        exit;

    }

    public function cache()
    {

        WsExce::exceSync("php think clear");
        $this->optimize();

    }
    public function php()
    {

        WsExce::exceSync("php -m");
        $this->optimize();

    }
    public function clear_temp_ahce() {
        array_map( 'unlink', glob( TEMP_PATH.DS.'.php' ) );
        echo 1;
        exit;
    }
    public function clear_log_chache() {
        $path = glob( LOG_PATH.'/' );
        foreach ($path as $item) {
            array_map( 'unlink', glob( $item.DS.'.' ) );
            rmdir( $item );
        }
       exit;
    }
}