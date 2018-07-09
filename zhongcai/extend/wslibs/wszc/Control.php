<?php
namespace wslibs\wszc;


class Control
{
    private static $buttons = [];

    public static function addBtn($index, $data, $attr_data = [])
    {
       if (!self::$buttons[102]) {

           if ($index==102) self::$buttons = [];
           self::$buttons[$index] = [$data, $attr_data];
       }
    }

    public static function getBtnHtml($isphone=false)
    {
        $btnList = [];
        foreach (self::$buttons as $key => $valtmp) {
            $val = $valtmp[0];
            $tmpBtn = self::button($key);
            if (isset($val['title'])) {
                $tmpBtn['name'] = $val['title'];
            }
            $tmpBtn['para'] = array_merge($tmpBtn['para'], $val);
            $tmpBtn['attr'] = array_merge($tmpBtn['attr'], $valtmp[1]);



            $btnList[$key] = $tmpBtn;
        }
        if($isphone) return $btnList;
        
        return self::button2html($btnList);
    }

    public static function button($index = -1)
    {
        $button = [


            100 => ['name' => '无操作', 'url' => '', 'class' => 'btn   btn-default btn-alert', 'attr' => []],
            101 => ['name' => '答辩到期设定', 'url' => 'time/daoqi', 'para' => ['id' => 0, "title" => "答辩到期设定"], 'class' => "btn btn-success  btn-warning-tip", 'attr' => ['data-ajax' => 1, 'data-title' => '提示', 'data-tip' => '这真的是模拟到期，为了测试方便！']],//,'data-dialog'=>'1'
            102 => ['name' => 'Loading', 'para' => ['id' => 0, "title" => "Loading"]],//,'data-dialog'=>'1'

            0 => ['name' => '无操作', 'url' => '', 'class' => 'btn btn-yahoo label-default'],
            1 => ['name' => '申请', 'url' => 'refuse/reapply', 'para' => ['dossier_id' => 0, 'title' => '申请仲裁'], 'class' => "btn btn-yahoo", 'attr' => ['data-title' => 'aaa']],
            2 => ['name' => '同意', 'url' => 'receive/agree', 'para' => ['id' => 0, "title" => "受理"], 'class' => "btn btn-success btn-warning-tip", 'attr' => ['data-ajax' => 1, 'data-title' => '提示', 'data-tip' => '确定要受理此业务吗？此操作不可逆！确定将发送至领导审批？'/*,'data-dialog'=>'1'*/]],
            3 => ['name' => '发送受理文件', 'url' => 'dossier.cp/doclist', 'para' => ['id' => 0, 'gid' => '2,13'], 'class' => "btn btn-success"],
            4 => ['name' => '拒绝受理', 'url' => 'Refuse/index', 'para' => ['id' => 0], 'class' => "btn btn-yahoo btn-prompt-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要拒绝受理此业务吗？此操作不可逆！']],
            5 => ['name' => '答辩', 'url' => 'dossier.cp/defence', 'para' => ['id' => 0, "title" => "答辩意见"], 'class' => "btn btn-yahoo  btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要答辩吗？'/*,'data-dialog'=>"1"*/]],
            6 => ['name' => '发送答辩文件', 'url' => 'dossier.cp/doclist', 'para' => ['id' => 0, 'gid' => 3], 'class' => "btn btn-yahoo"],
            // 申请人质证
            7 => ['name' => '质证', 'url' => 'wsdoc.show/viewquestion', 'para' => ['qid' => 0], 'class' => "btn btn-yahoo btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要质证吗？']],
            // 被申请人质证
            //  8 => ['name' => '质证', 'url' => 'wsdoc.show/viewquestion','para'=>['qid'=>0,'gid' => 11] ,'class' => "btn btn-yahoo btn-warning-tip" ,'attr'=>['data-title'=>'提示','data-tip'=>'确定要质证吗？'] ],
//            9 => ['name' => '指定仲裁员', 'url' => 'dossier.cp/court','para'=>['id'=>0],'class' => "btn btn-success" ],
            9 => ['name' => '指定仲裁员', 'url' => 'cross.addfile/court', 'para' => ['id' => 0], 'class' => "btn btn-success btn-dialog"],
            10 => ['name' => '申请仲裁员回避', 'url' => 'dossier.cp/Huibi', 'para' => ['id' => 0], 'class' => "btn btn-yahoo btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要申请仲裁员回避吗？']],
            11 => ['name' => '仲裁披露（拒绝）', 'url' => 'dossier.cp/pilu', 'para' => ['id' => 0], 'class' => "btn btn-yahoo btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要披露吗？']],
            12 => ['name' => '撤回仲裁申请', 'url' => 'Cancel/cancel', 'para' => ['id' => 0], 'class' => "btn btn-yahoo btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要撤回此次申请吗？'/*,'data-ajax'=>'1'*/]],
            13 => ['name' => '仲裁声明（同意）', 'url' => 'dossier.cp/shenming', 'para' => ['id' => 0], 'class' => "btn btn-success btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定同意并签署声明书吗？']],
            14 => ['name' => '裁决', 'url' => 'dossier.cp/CaiJue', 'para' => ['id' => 0], 'class' => "btn btn-yahoo btn-dialog"],

//            15 => ['name' => '重新指定仲裁员', 'url' => 'dossier.cp/court','para'=>['id'=>0,'again' => 1,"title"=>"重新指定仲裁员"],'class' => "btn btn-success btn-warning-tip ",'attr'=>['data-dialog'=>1,'title'=>"重新指定仲裁员", 'data-title'=>'提示','data-tip'=>'确定要重新指定仲裁员吗？'] ],
            15 => ['name' => '重新指定仲裁员', 'url' => 'cross.addfile/court', 'para' => ['id' => 0, 'again' => 1, "title" => "重新指定仲裁员"], 'class' => "btn btn-success btn-warning-tip ", 'attr' => ['data-dialog' => 1, 'title' => "重新指定仲裁员", 'data-title' => '提示', 'data-tip' => '确定要重新指定仲裁员吗？']],
            16 => ['name' => '提交证据', 'url' => 'dossier.cp/tijiaozhengju', 'para' => ['id' => 0], 'class' => "btn btn-yahoo"],
            17 => ['name' => '审批通过', 'url' => 'dossier.cp/spshouli', 'para' => ['id' => 0, "title" => "审批通过"], 'class' => "btn btn-success  btn-warning-tip", 'attr' => ['data-ajax' => 1, 'data-title' => '提示', 'data-tip' => '确定同意立案吗？此操作不可逆！']],//,'data-dialog'=>'1'
            18 => ['name' => '发送组庭文件', 'url' => 'dossier.cp/doclist', 'para' => ['id' => 0, 'gid' => '4'], 'class' => "btn btn-success btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要发送组庭文件吗？']],
            19 => ['name' => '主任指定仲裁员', 'url' => 'dossier.cp/court', 'para' => ['id' => 0], 'class' => "btn btn-yahoo"],
            20 => ['name' => '裁决', 'url' => 'dossier.caijue/caijueview', 'para' => ['id' => 0], 'class' => "btn btn-success btn-dialog", 'attr' => ['title' => '裁决书']],
            21 => ['name' => '立案', 'url' => 'dossier.cp/doclist', 'para' => ['id' => 0, 'gid' => '17'], 'class' => "btn btn-yahoo"],


            22 => ['name' => '答辩意见处理', 'url' => '#', 'para' => [], 'class' => "btn btn-warning btn-goto", 'attr' => ['data-gotoid' => '4']],
            23 => ['name' => '质证意见处理', 'url' => '#', 'para' => [], 'class' => "btn btn-info btn-goto", 'attr' => ['data-gotoid' => '6']],
            24 => ['name' => '提交证据处理', 'url' => '#', 'para' => [], 'class' => "btn btn-success btn-goto", 'attr' => ['data-gotoid' => '7']],
            25 => ['name' => '回避/披露/声明处理', 'url' => '#', 'para' => [], 'class' => "btn btn-success btn-goto", 'attr' => ['data-gotoid' => '5']],
            26 => ['name' => '拒绝立案', 'url' => 'dossier.cp/spjujue', 'para' => ['id' => 0, "title" => "拒绝立案"], 'class' => "btn btn-yahoo  btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定拒绝立案审批吗，此操作不可逆！'/*,'data-dialog'=>'1'*/]],
            27 => ['name' => '撤回申请处理', 'url' => '#', 'para' => ['id' => 0, "title" => "撤回申请处理"], 'class' => "btn btn-warning btn-goto", 'attr' => ['data-gotoid' => '3']],
            28 => ['name' => '撤回修改', 'url' => 'Cancel/cancel', 'para' => ['id' => 0], 'class' => "btn btn-success btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要撤回此次申请吗？'/*,'data-ajax'=>'1'*/]],

            29 => ['name' => '审批通过', 'url' => 'dossier.lian/spshouli', 'para' => ['id' => 0, "title" => "审批通过"], 'class' => "btn btn-success  btn-warning-tip", 'attr' => ['data-ajax' => 1, 'data-title' => '提示', 'data-tip' => '确定同意立案吗？此操作不可逆！']],//,'data-dialog'=>'1'
            30 => ['name' => '变更仲裁请求', 'url' => '#', 'para' => ['id' => 0, "title" => "变更仲裁请求"], 'class' => "btn btn-success  btn-alert", 'attr' => ['data-title' => '提示','data-icon'=>'0', 'data-msg' => '如需变更仲裁请求，请在线下提交书面变更仲裁请求申请书，并交纳相应仲裁费至石家庄仲裁委员会，联系方式0311-86687359。']],//,'data-dialog'=>'1'
            31 => ['name' => '反请求', 'url' => '#', 'para' => ['id' => 0, "title" => "反请求"], 'class' => "btn btn-success  btn-alert", 'attr' => ['data-title' => '提示','data-icon'=>'0', 'data-msg' => '如有反请求，请自收到仲裁通知书之日起五日内提交书面反请求仲裁申请书，并交纳相应仲裁费至石家庄仲裁委员会，联系方式0311-86687359。']],//,'data-dialog'=>'1'

            32 => ['name' => '管辖权异议', 'url' => 'Gxqyy/index', 'para' => ['id' => 0, "title" => "管辖权异议"], 'class' => "btn btn-success btn-warning-tip", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要提出管辖权异议吗？']],
            33 => ['name' => '管辖权异议处理', 'url' => '#', 'para' => ['id' => 0, "title" => "管辖权异议处理"], 'class' => "btn btn-info btn-goto", 'attr' => ['data-gotoid' => '8']],
            34 => ['name' => '管辖权异议意见', 'url' => 'admin/dossier.subinfo/gxqyy', 'para' => ['gxid' => 0, "title" => "管辖权异议意见"], 'class' => "btn btn-success", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要发表意见吗？']],
            35 => ['name' => '其他申请', 'url' => 'Dotherapply/index', 'para' => ['id' => 0, "title" => "其他申请"], 'class' => "btn btn-success  btn-dialog", 'attr' => ['data-title' => '提示', 'data-tip' => '确定要提出申请吗？']],
            36 => ['name' => '其他申请处理', 'url' => '#', 'para' => [], 'class' => "btn btn-info btn-goto", 'attr' => ['data-gotoid' => '9']],

        ];
        if ($index >= 0) {
            return $button[$index];
        }
        return $button;
    }

    public static function button2html($btnList)
    {

        $btnStr = '<div> ';
        foreach ($btnList as $k => $v) {

            if ($k == 102) {
                $btnStr .= '<div class="overlay" style="text;text-align:  center;"><i class="fa fa-refresh fa-spin fa-loading-check" style="color:#3498db;" data-check-tag="'.$v['para']['check_tag'].'" data-check-value="'.$v['para']['check_value'].'"></i><div style="margin-top: 30px;font-size:large"> '.$v['name'].'</div></div>';
            } else {
                $str = '';
                if (isset($v['attr'])) {
                    foreach ($v['attr'] as $key => $val) {
                        $str .= $key . " = " . $val . " ";
                    }
                }
                $btnStr .= "<a $str href='" . url($v['url'], $v['para']) . "' class='margin " . $v['class'] . "'>" . $v['name'] . "</a>&nbsp;&nbsp;&nbsp;";


            }

        }
        $btnStr .= "</div>";
        return $btnStr;
    }


}