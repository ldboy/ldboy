<?php

namespace app\admin\controller;

use app\common\controller\Backend;

use think\Config;
use wslibs\wszc\datashow\DashboardShow;
use wslibs\wszc\datashow\DashboardLine;

/**
 * 控制台
 *
 * @icon fa fa-dashboard
 * @remark 用于展示当前系统中的统计数据、统计报表及重要实时数据
 */
class Dashboard extends Backend
{

    /**
     * 查看
     */
    public function index()
    {


        $list = DashboardShow::getTemplateData();

        $line = new DashboardLine();
        list($paylist,$createlist) = $line->getSevenCount();

        $inform = new \wslibs\wszc\mes\Inform();

        $inform->addHere(0);

        $inform_list = $inform->getList(0,15);

        if($_GET['zhz']==111){
            dump($paylist);
            dump($createlist);
        }

        $hooks = config('addons.hooks');
        $uploadmode = isset($hooks['upload_config_init']) && $hooks['upload_config_init'] ? implode(',', $hooks['upload_config_init']) : 'local';
        $addonComposerCfg = ROOT_PATH . '/vendor/karsonzhang/fastadmin-addons/composer.json';
        Config::parse($addonComposerCfg, "json", "composer");
        $config = Config::get("composer");
        $addonVersion = isset($config['version']) ? $config['version'] : __('Unknown');
        $this->view->assign([
            'totaluser'        => $list['all_element']['sl_num'],
            'totalviews'       => $list['all_element']['bank_num'],
            'totalorder'       => $list['all_element']['person_num'],
            'totalorderamount' => $list['all_element']['zcy_num'],
            'todayuserlogin'   => 321,
            'todayusersignup'  => 430,
            'todayorder'       => 2324,
            'unsettleorder'    => 132,
            'sevendnu'         => '80%',
            'sevendau'         => '32%',
            'paylist'          => $paylist,   //波浪图数据
            'createlist'       => $createlist,
            'addonversion'       => $addonVersion,  //版本号
            'uploadmode'       => $uploadmode
        ]);
        $this->view->assign([
            'all_num'        => $list['case_data']['all_num'],
            'dsl_num'       => $list['case_data']['dsl_num'],
            'db_num'       => $list['case_data']['db_num'],
            'zcz_num' => $list['case_data']['zcz_num'],
            'list'=>$list,
            'inform_list'=>$inform_list,
        ]);


        return $this->view->fetch();
    }

}
