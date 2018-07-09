<?php
namespace app\admin\controller\dossier;

use app\common\controller\Backend;
use wslibs\wszc\btn\Btn;
use wslibs\wszc\caijue\Dcaijue;
use wslibs\wszc\defence\DefenceExpand;
use think\Db;
use wslibs\wszc\Constant;
use wslibs\wszc\Ddocs;
use wslibs\wszc\Dossier;
use wslibs\wszc\DossierLog;
use wslibs\wszc\Ds;
use wslibs\wszc\dz\Dz;
use wslibs\wszc\HuiBi;
use wslibs\wszc\LoginUser;
use wslibs\wszc\question\QuestionExpand;
use wslibs\wszc\User;
use wslibs\wszc\divedit\DivEdit;

class ceshi extends Backend{
    public function index()
    {
        $docid=17;
        $enable='';
        $data=DivEdit::getList($docid, $enable);
        var_dump($data);

    }
}