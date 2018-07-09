<?php
namespace app\admin\controller;

class Ywimgcallback
{

    //http://zhygz.wszx.cc/Ywimgcallback-index.html?docid=48&img=ybg/15/files/docimgs/15_3_7_1509527233.png
    //http://zhygz.wszx.cc/Ywimgcallback-index.html?docid=48&img=ybg/77/files/docimgs/77_1_6_1509623256.png
    public function index()
    {


        $docid = intval($_GET["docid"]);
 
        $img = $_GET["img"];
        $_file_ok_ = $_GET["_file_ok_"];
//        $info = M("yw_docs")->where("id=" . $docid)->find();
        if($docid!=0/* && $info*/){
            if($_file_ok_ ==1){
                echo 1;

//                YwWord::changedocstatus($docid,4);
            }else{

                echo 2;
//                YwWord::changedocstatus($docid,6);
            }
//            $result = YwProgressManager::isCanPushToAdmin($info['ywid']);

            exit;
        }
    }



    public function ing()
    {
        $docid = (int)$_GET["docid"];

        $_file_ok_ = (int)$_GET["_file_ok_"];
        $ywid = (int)$_GET["ywid"];

        echo 'here';

        exit;
        $data = YwValues::getCommonFildByYwid($ywid);

        $info = M("yw_docs")->where("id=" . $docid)->find();
        if($info['status']!=3){
            return ;
        }
        file_put_contents('aa.txt','111');

        if ($_file_ok_) {
            file_put_contents('ab.txt','111');
            YwWord::imgokl($docid);

            YwValues::setCommonValuesOfYwid($ywid, array("img_success" => 1 + (int)$data['img_success']));
        } else {
            YwWord::imgerror($docid);
            YwValues::setCommonValuesOfYwid($ywid, array("img_error" => 1 + (int)$data['img_error']));
        }


        importExternal("YunBanZheng/Yw/YwManager");
        $ywid = (int)$_GET["ywid"];
        YwManager::ywIng($ywid);

    }


}