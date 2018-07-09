<?php
namespace wslibs\exec;
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
        exec($cmd);
    }
}