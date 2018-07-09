<?php
/**
 * Created by PhpStorm.
 * User: mrren
 * Date: 2018/4/27
 * Time: 下午1:35
 */

namespace wslibs\wszc;


class Constant
{


    //人员类型
    const D_Type_ZiRanRen = 1;
    const D_Type_JiGou = 2;

    //环节
    const FILE_GROUP_shenqing = 1;//申请的所有资料
    const FILE_GROUP_shouli = 2;//已受理
    const FILE_GROUP_bsqrshouli = 13;//被申请人受理

    const FILE_GROUP_dabian = 3;//答辩
    const FILE_GROUP_zuting = 4;//组庭
    const FILE_GROUP_shengming = 5;//仲裁员声明
    const FILE_GROUP_huibi = 6;//回避
    const FILE_GROUP_zhizheng = 7;//质证
    const FILE_GROUP_wancheng = 10;//完成
    const FILE_GROUP_sqrzhengjulook = 11;//申请人证据
    const FILE_GROUP_bsqrzhengjulook = 12;//被申请人证据
    const FILE_GROUP_pilu = 14;//披露
    const FILE_GROUP_caijue = 15;//出裁决
    const FILE_GROUP_zuting_again = 16;//重新组庭
    const FILE_GROUP_lianshenpi = 17;//立案审批
    const FILE_GROUP_zhizhengzhuanfa = 18;//质证转发
    const FILE_GROUP_dabian_zhuanfa = 19;//质证转发

    const FILE_GROUP_zhidingzhongcaiyuan = 20;//指定仲裁员
    const FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan = 21;//指定仲裁员


    const FILE_GROUP_pilu_zhuanfa = 22;
    const FILE_GROUP_shenming_zhuanfa = 23;

    const FILE_GROUP_caijue_fasongzhizhuren = 24;


    const FILE_GROUP_tijiaozhengju_sqr = 25;//申请人提交证据
    const FILE_GROUP_tijiaozhengju_bsqr = 40;//被申请人提交证据

    const FILE_GROUP_zhengju_zhuanfa = 26;

    const FILE_GROUP_caijue_fasongsuoyouren = 27;

    const FILE_GROUP_caijue_shenpi = 28;
    const FILE_GROUP_fasongwenjian = 29;//裁决审批
    const FILE_GROUP_lianshenpi_zhuren = 30;
    const FILE_GROUP_huibi_huifu = 31; // 对当事人申请仲裁员回避的回复


    const FILE_GROUP_cxzhidingzhongcaiyuan = 32;//指定仲裁员

    const FILE_GROUP_chehuishenqing = 33;//撤回申请
    const FILE_GROUP_chehuishenqing_zhubanzf = 34;//撤回申请主办转发
    const FILE_GROUP_shouli_fagei_jigou = 35;//已受理

    const FILE_GROUP_chehuishenqing_zhuren_zf = 36;//撤回申请主任转发

    const FILE_GROUP_chehuishenqing_zhuban_zf_zth = 37;//撤回申请主办转发 组庭后
    const FILE_GROUP_chehuishenqing_zhongcaiyuanchuli = 38;//撤回申请 仲裁员处理 组庭后
    const FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa = 39;//撤回申请 仲裁员处理 组庭后
    const FILE_GROUP_chehuishenqing_zhuren_zf_zth = 41;//撤回申请 仲裁员处理 组庭后
    const FILE_GROUP_gxq_dangshirenqingqiu = 42; //管辖权异议 当是人刚发起 申请书
    const FILE_GROUP_gxq_zhubanzhuanfa = 43; //管辖权异议 主办转发
    const FILE_GROUP_gxq_fasongsuoyou = 44; //管辖权异议 发送所有人

    const Dvalue_var_name_file = "_files_";
    const Dvalue_var_name_pdf = "_pdf_";
    const Dvalue_var_name_sign = "_sign_";

//
    const DOC_model_shengqingshu = 1;//申请书
    const DOC_model_zhengju1 = 2;//被申请人证件
    const DOC_model_zhengju2 = 30;//线下合同附件
    const DOC_model_zhengju3 = 31;//视频资料
    const DOC_model_huankuanxieyi = 17;//线上还款协议
    const DOC_model_qitazhengju = 3;//其它证据
    const DOC_model_dabianshu = 5;//仲裁答辩书
    const DOC_model_zhengmingwenjian = 6;//法人身份证明书
    const DOC_model_weituoshu = 7;//授权委托书
    const DOC_model_tongzhishu = 13;//仲裁通知书
    const DOC_model_zcguize = 14;//仲裁规则
    const DOC_model_mingce = 18;//仲裁员名册
    const DOC_model_zutingtzs = 20;//组庭通知书
    const DOC_model_dabianzhengju = 21;//答辩证据
    const DOC_model_shengmingshu = 23;//仲裁员声明书
    const DOC_model_pilushu = 24;//
    const DOC_model_huibishenqing = 25;//回避申请（当事人）
    const DOC_model_shoufeibiaozhun = 26;//仲裁收费标准
    const DOC_model_caijueshu = 28;//裁决书
    const DOC_model_zhizhengyijian = 29;//质证意见
    const DOC_model_againzuting = 33;//重新组庭通知书
    const DOC_model_shenfenzhengjian = 34;//身份证明文件
    const DOC_model_lianshenpibiao = 35;//立案审批表

    const DOC_model_zhidingzhongcaiyuan = 36;//立案审批表
    const DOC_model_caijueshenpi = 37;//裁决审批
    const DOC_model_cxzhidingzhongcaiyuan = 38;//重新指定仲裁员申请书
    const DOC_model_huibi_huifu = 39;//申请回避的回复
    const DOC_model_sqrzhengjulist = 40;//申请人证据清单
    const DOC_model_bsqrzhengjulist = 41;//被申请人证据清单
    const DOC_model_sqrzhengjuelse = 42;//债务到期通知
    const DOC_model_zhuanzhangpingju = 43;//转账凭据 申请仲裁时候用
    const DOC_model_chehuishenqingshu = 44;//撤回申请书
    const DOC_model_chehuijuedingshu_ztq = 45;//撤回决定书组庭前
    const DOC_model_chehuijuedingshu_zth = 46;//撤回决定书组庭后
    const DOC_model_shenqinghuibi_zhengju = 48;//申请回避的证据
    const DOC_model_gxqyy_sqs = 49;//管辖权异议申请书
    const DOC_model_gxqyy_jueding = 50;//管辖权异议决定书


    //juse
    const D_Role_ShenQingRen = 1;
    const D_Role_Bei_ShenQingRen = 2;
    const D_Role_ShenQingRen_Dl = 3;
    const D_Role_Bei_ShenQingRen_Dl = 4;
    const D_Role_ZhongCaiWei_GuanLiYuan = 10;// 主办
    const D_Role_ZhongCaiYuan = 15;
    const D_Role_ZhongCaiWei_JiGou = 16;
    const D_Role_ZhongCaiWei_LiAnShenPi = 17;
    const D_Role_ZhongCaiWei_CaiJueShenPi = 17;
    const D_Role_ShenQingRen_FR = 19;
    const D_Role_Beo_ShenQingRen_FR = 20;

    // 权限角色组
    const QX_ROLE_SHENQINGREN = 1;
    const QX_ROLE_BEISHENQINGREN = 2;
    const QX_ROLE_ZHONGCAIYUAN = 4;
    const QX_ROLE_ZHONGCAIWEI_ZHUREN = 8;
    const QX_ROLE_ZHONGCAIWEI_MISHU = 16;
    const QX_ROLE_ADMIN = 32;
    const QX_ROLE_SHENQINGREN_DL = 64;
    const QX_ROLE_BEISHENQINGREN_DL = 128;
    const QX_ROLE_ZHONGCAIWEI_ZHUREN_CAIJUE = 8;
    const QX_ROLE_SHENQINGREN_fr = 256;
    const QX_ROLE_BEI_SHENQINGREN_fr = 512;

    const Cache_pre_dinfoValue = 'getTemplateData_';
    const Cache_tag_pre_dossier = 'dossier_';

    public static $Rolr2Qx = [
        self::D_Role_ShenQingRen => self::QX_ROLE_SHENQINGREN,
        self::D_Role_Bei_ShenQingRen => self::QX_ROLE_BEISHENQINGREN,
        self::D_Role_ShenQingRen_Dl => self::QX_ROLE_SHENQINGREN_DL,
        self::D_Role_Bei_ShenQingRen_Dl => self::QX_ROLE_BEISHENQINGREN_DL,
        self::D_Role_ZhongCaiWei_GuanLiYuan => self::QX_ROLE_ZHONGCAIWEI_MISHU,
        self::D_Role_ZhongCaiYuan => self::QX_ROLE_ZHONGCAIYUAN,
        self::D_Role_ZhongCaiWei_LiAnShenPi => self::QX_ROLE_ZHONGCAIWEI_ZHUREN,
        self::D_Role_ShenQingRen_FR => self::QX_ROLE_SHENQINGREN_fr,
        self::D_Role_Beo_ShenQingRen_FR => self::QX_ROLE_BEI_SHENQINGREN_fr,
    ];
    public static $Qx2Role = [
        self::QX_ROLE_SHENQINGREN => self::D_Role_ShenQingRen,
        self::QX_ROLE_BEISHENQINGREN => self::D_Role_Bei_ShenQingRen,
        self::QX_ROLE_SHENQINGREN_DL => self::D_Role_ShenQingRen_Dl,
        self::QX_ROLE_BEISHENQINGREN_DL => self::D_Role_Bei_ShenQingRen_Dl,
        self::QX_ROLE_ZHONGCAIWEI_MISHU => self::D_Role_ZhongCaiWei_GuanLiYuan,
        self::QX_ROLE_ZHONGCAIYUAN => self::D_Role_ZhongCaiYuan,
        self::QX_ROLE_ZHONGCAIWEI_ZHUREN => self::D_Role_ZhongCaiWei_LiAnShenPi,
        self::QX_ROLE_SHENQINGREN_fr=>self::D_Role_ShenQingRen_FR ,
        self::QX_ROLE_BEI_SHENQINGREN_fr => self::D_Role_Beo_ShenQingRen_FR,
    ];

    const DOSSIER_STATUS_GANGCHUANGJIAN = 1;
    const DOSSIER_STATUS_YIWANSHANZILIAO = 2;
    const DOSSIER_STATUS_SHOULIZHONG = 2;
    const DOSSIER_STATUS_YISHOULI = 3;
    const DOSSIER_STATUS_END = 10; //结束 不能任何操作
    const DOSSIER_STATUS_ZUTING = 5; //组庭
    const DOSSIER_STATUS_DEFENCE = 4; //答辩
    const DOSSIER_STATUS_PILUHUIBI = 6; //披露回避
    const DOSSIER_STATUS_COURT_AGAIN = 7; //重新组庭
    const DOSSIER_STATUS_SETSHOULIFILE = 31; //已发送受理文件


    const Admin_Role_yinhang = 13;
    const Admin_Role_zhongcaiyuan = 12;
    const Admin_Role_putongyonghu = 11;
    const Admin_Role_zhongcaiwei = 10;
    const Admin_Role_admin = 1;


    const Color_danger_class = "danger";
    const Color_success_class = "success";
    const Color_warning_class = "warning";
    const Color_info_class = "info";
    const Color_primary_class = "primary";


    const ZhongCaiWei_Role_ZhuBan = 1;
    const ZhongCaiWei_Role_LianShenPi = 2;
    const ZhongCaiWei_Role_CaiJueShenPi = 3;


    const Time_dabian = 86400*5;


    public static function getRoleQx($roleid)
    {
        return (int)self::$Rolr2Qx[$roleid];
    }

    public static function getColorClass()
    {
        return array(self::Color_danger_class, self::Color_success_class, self::Color_warning_class, self::Color_info_class, self::Color_primary_class);
    }


    public static function getAdminRoles()
    {
        return array(self::Admin_Role_yinhang, self::Admin_Role_putongyonghu, self::Admin_Role_admin, self::Admin_Role_zhongcaiwei, self::Admin_Role_zhongcaiyuan);
    }


    public static function getDangshirenRoles()
    {
        return array(self::D_Role_ShenQingRen, self::D_Role_Bei_ShenQingRen, self::D_Role_ShenQingRen_Dl, self::D_Role_Bei_ShenQingRen_Dl);
    }


    public static function getRoleName($roleid)
    {
        $array = [
            self::D_Role_ShenQingRen => "申请人",
            self::D_Role_ShenQingRen_Dl => "申请人代理",
            self::D_Role_Bei_ShenQingRen => "被申请人",
            self::D_Role_Bei_ShenQingRen_Dl => "被申请人代理",
            self::D_Role_ZhongCaiYuan => "仲裁员",
            self::D_Role_ZhongCaiWei_GuanLiYuan => "仲裁委主办",
            self::D_Role_ZhongCaiWei_LiAnShenPi => "仲裁委领导",
            self::D_Role_ZhongCaiWei_CaiJueShenPi => "仲裁委领导",
            self::D_Role_ShenQingRen_FR => "申请人代理",
            self::D_Role_Beo_ShenQingRen_FR=> "被申请人代理",

        ];

        return $array[$roleid];
    }

    public static function getQxRoleName($qx){
        return self::getRoleName(self::$Qx2Role[$qx]);
    }

    public static function getGroupInfo($gid)
    {
        $arr = array(
            self::FILE_GROUP_shenqing => array("title" => "申请资料", "style" => "success"),
            self::FILE_GROUP_shouli => array("title" => "发送给申请人的立案材料", "style" => "info"),
            self::FILE_GROUP_bsqrshouli => array("title" => "发送给被申请人的立案材料", "style" => "default"),

            self::FILE_GROUP_dabian => array("title" => "答辩文件", "style" => "waring"),
            self::FILE_GROUP_zuting => array("title" => "组庭文件", "style" => "success"),
            self::FILE_GROUP_zuting_again => array("title" => "重新组庭文件", "style" => "success"),
            self::FILE_GROUP_shengming => array("title" => "声明文件", "style" => "success"),
            self::FILE_GROUP_huibi => array("title" => "申请回避文件", "style" => "success"),
            self::FILE_GROUP_zhizheng => array("title" => "质证文件", "style" => "success"),
            self::FILE_GROUP_wancheng => array("title" => "裁决文件", "style" => "success"),
            self::FILE_GROUP_sqrzhengjulook => array("title" => "申请人证据", "style" => "success"),
            self::FILE_GROUP_bsqrzhengjulook => array("title" => "被申请证据", "style" => "success"),
            self::FILE_GROUP_lianshenpi => array("title" => "立案审批文件", "style" => "success"),
            self::FILE_GROUP_zhizhengzhuanfa => array("title" => "质证意见转发", "style" => "success"),
            self::FILE_GROUP_dabian_zhuanfa => array("title" => "答辩意见转发", "style" => "success"),
            self::FILE_GROUP_zhidingzhongcaiyuan => array("title" => "指定仲裁员通知文件", "style" => "success"),
            self::FILE_GROUP_cxzhidingzhongcaiyuan => array("title" => "重新指定仲裁员通知文件", "style" => "success"),
            self::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan => array("title" => "指定仲裁员通知文件", "style" => "success"),
            self::FILE_GROUP_pilu => array("title" => "仲裁员披露文件", "style" => "success"),

            self::FILE_GROUP_shenming_zhuanfa => array("title" => "仲裁员声明文件转发", "style" => "success"),
            self::FILE_GROUP_pilu_zhuanfa => array("title" => "仲裁员披露文件转发", "style" => "success"),
            self::FILE_GROUP_caijue => array("title" => "裁决书", "style" => "success"),
            self::FILE_GROUP_caijue_fasongzhizhuren => array("title" => "裁决意见提交", "style" => "success"),

            self::FILE_GROUP_tijiaozhengju_sqr => array("title" => "提交证据", "style" => "success"),
            self::FILE_GROUP_tijiaozhengju_bsqr => array("title" => "提交证据", "style" => "success"),
            
            self::FILE_GROUP_zhengju_zhuanfa=> array("title" => "证据转发", "style" => "success"),//新证据转发
            self::FILE_GROUP_caijue_fasongsuoyouren=> array("title" => "裁决书", "style" => "success"),
            self::FILE_GROUP_caijue_shenpi=> array("title" => "审批文件", "style" => "success"),
            self::FILE_GROUP_fasongwenjian=> array("title" => "文件列表", "style" => "success"),
            //主任给主办发立案审批表
            self::FILE_GROUP_lianshenpi_zhuren=> array("title" => "立案审批表", "style" => "success"),
            // 申请回避的回复
            self::FILE_GROUP_huibi_huifu=> array("title" => "回避转发资料", "style" => "success"),
            self::FILE_GROUP_chehuishenqing=> array("title" => "撤回仲裁请求申请资料", "style" => "success"),

            self::FILE_GROUP_shouli_fagei_jigou=> array("title" => "受理文件", "style" => "success"),
            self::FILE_GROUP_chehuishenqing_zhubanzf=> array("title" => "报请审批", "style" => "success"),
            self::FILE_GROUP_chehuishenqing_zhuren_zf=> array("title" => "撤回申请主任转发", "style" => "success"),
            self::FILE_GROUP_chehuishenqing_zhuban_zf_zth=> array("title" => "撤回申请主办转发", "style" => "success"),//(组庭后)
            self::FILE_GROUP_chehuishenqing_zhongcaiyuanchuli=> array("title" => "撤回申请仲裁员处理", "style" => "success"),//(组庭后)
            self::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa=> array("title" => "撤回申请仲裁员转发", "style" => "success"),
            self::FILE_GROUP_chehuishenqing_zhuren_zf_zth=> array("title" => "撤回申请主任转发", "style" => "success"),//(组庭后)
            self::FILE_GROUP_gxq_dangshirenqingqiu=> array("title" => "管辖权异议", "style" => "success"),
            self::FILE_GROUP_gxq_zhubanzhuanfa=> array("title" => "管辖权异议报批", "style" => "success"),
            self::FILE_GROUP_gxq_fasongsuoyou=> array("title" => "管辖权异议决定", "style" => "success"),

        );
        return $arr[$gid];
    }

    public static function getGroupFileMode($groupid)
    {
        $arr = array(
            self::FILE_GROUP_shenqing => array(
                self::DOC_model_shengqingshu,
                self::DOC_model_shenfenzhengjian,
                self::DOC_model_zhengju2,
                self::DOC_model_zhengju3,
                self::DOC_model_huankuanxieyi,
                self::DOC_model_qitazhengju,
                self::DOC_model_sqrzhengjulist,
                self::DOC_model_zhuanzhangpingju,
                self::DOC_model_sqrzhengjuelse,

            ),
            self::FILE_GROUP_shouli => array(

                self::DOC_model_tongzhishu,
                self::DOC_model_zcguize,
                self::DOC_model_mingce,
               // self::DOC_model_shoufeibiaozhun,
                //self::DOC_model_sqrzhengjulist,
            ),
            self::FILE_GROUP_shouli_fagei_jigou => array(

                self::DOC_model_tongzhishu,
                self::DOC_model_zcguize,
                self::DOC_model_mingce,
              //  self::DOC_model_shoufeibiaozhun,
                //self::DOC_model_sqrzhengjulist,
            ),


            self::FILE_GROUP_bsqrshouli => array(

                self::DOC_model_shengqingshu,
                //self::DOC_model_zhengju1,
                self::DOC_model_zhengju2,
                self::DOC_model_zhengju3,
                self::DOC_model_huankuanxieyi,
                self::DOC_model_qitazhengju,
                self::DOC_model_tongzhishu,
                self::DOC_model_zcguize,
                self::DOC_model_mingce,
                /*self::DOC_model_shoufeibiaozhun,*/
                self::DOC_model_sqrzhengjulist,
                self::DOC_model_sqrzhengjuelse,
                self::DOC_model_zhuanzhangpingju,
            ),

            self::FILE_GROUP_dabian => array(
                self::DOC_model_dabianshu,
               // self::DOC_model_zhengju1,
//                self::DOC_model_dabianzhengju,
//                self::DOC_model_qitazhengju,
//                self::DOC_model_bsqrzhengjulist,
            ),
            self::FILE_GROUP_zuting => array(
                self::DOC_model_zutingtzs,
            ),
            self::FILE_GROUP_shengming => array(
                self::DOC_model_shengmingshu,
            ),
            self::FILE_GROUP_huibi => array(
                self::DOC_model_huibishenqing,
                self::DOC_model_qitazhengju,
                self::DOC_model_shenqinghuibi_zhengju
            ),
            self::FILE_GROUP_zhizheng => array(
                self::DOC_model_zhizhengyijian,
            ),
            self::FILE_GROUP_wancheng => array(
                self::DOC_model_caijueshu,
            ),
            self::FILE_GROUP_sqrzhengjulook => array(
                self::DOC_model_shengqingshu,
               // self::DOC_model_zhengju1,
                self::DOC_model_zhengju2,
                self::DOC_model_zhengju3,
                self::DOC_model_huankuanxieyi,
                self::DOC_model_qitazhengju,
                self::DOC_model_sqrzhengjuelse,
            ),
            self::FILE_GROUP_bsqrzhengjulook => array(
                self::DOC_model_qitazhengju,
                self::DOC_model_dabianzhengju,
            ),
            self::FILE_GROUP_pilu => array(
                self::DOC_model_pilushu,
            ),
            self::FILE_GROUP_zuting_again => array(
                self::DOC_model_againzuting,
            ),
            self::FILE_GROUP_lianshenpi => array(
                self::DOC_model_lianshenpibiao,
            ),
            self::FILE_GROUP_zhizhengzhuanfa => array(
                self::DOC_model_zhizhengyijian,
            ),
            self::FILE_GROUP_dabian_zhuanfa => array(
                self::DOC_model_dabianshu,
//                self::DOC_model_zhengju1,
//                self::DOC_model_dabianzhengju,
//                self::DOC_model_qitazhengju
            ),
            self::FILE_GROUP_zhidingzhongcaiyuan => array(
                self::DOC_model_zhidingzhongcaiyuan
            ),
            self::FILE_GROUP_cxzhidingzhongcaiyuan=> array(
                self::DOC_model_cxzhidingzhongcaiyuan
            ),
            self::FILE_GROUP_zhidingzhongcaiyuan_zhubanchakan => array(
                self::DOC_model_zhidingzhongcaiyuan
            ),
            self::FILE_GROUP_pilu_zhuanfa => array(
                self::DOC_model_pilushu
            ),
            self::FILE_GROUP_shenming_zhuanfa => array(
                self::DOC_model_shengmingshu
            ),
            self::FILE_GROUP_caijue => array(
                self::DOC_model_caijueshu
            ),
            self::FILE_GROUP_caijue_fasongzhizhuren => array(
                self::DOC_model_caijueshu,
//                self::DOC_model_caijueshenpi
            )
            /*, self::FILE_GROUP_tijiaozhengju => array(
                self::DOC_model_qitazhengju,
                self::DOC_model_bsqrzhengjulist,
                self::DOC_model_sqrzhengjulist,
            )*/

            ,self::FILE_GROUP_tijiaozhengju_sqr => array(
                self::DOC_model_qitazhengju,
                self::DOC_model_sqrzhengjulist,
            )

            ,self::FILE_GROUP_tijiaozhengju_bsqr => array(
                self::DOC_model_qitazhengju,
                self::DOC_model_bsqrzhengjulist,
            )

            , self::FILE_GROUP_zhengju_zhuanfa => array(
                self::DOC_model_qitazhengju,
            ),
            self::FILE_GROUP_caijue_fasongsuoyouren => array(
                self::DOC_model_caijueshu,

            ),
            self::FILE_GROUP_caijue_shenpi => array(
                self::DOC_model_caijueshenpi,

            ), 
            self::FILE_GROUP_lianshenpi_zhuren => array(
                self::DOC_model_lianshenpibiao,
            ),
            self::FILE_GROUP_huibi_huifu => array(
//                self::DOC_model_huibishenqing,
                self::DOC_model_huibi_huifu,
            ),
            self::FILE_GROUP_chehuishenqing => array(
                self::DOC_model_chehuishenqingshu,
            ),
            //撤回申请 主办转发
            self::FILE_GROUP_chehuishenqing_zhubanzf => array(
                self::DOC_model_chehuishenqingshu,
                self::DOC_model_chehuijuedingshu_ztq
            ),
            //撤回申请 主任转发
            self::FILE_GROUP_chehuishenqing_zhuren_zf => array(
                self::DOC_model_chehuijuedingshu_ztq
            ),
            //撤回申请 主办转发 组庭后
            self::FILE_GROUP_chehuishenqing_zhuban_zf_zth=>array(
                self::DOC_model_chehuishenqingshu
            ),
            self::FILE_GROUP_chehuishenqing_zhongcaiyuanchuli=>array(
                self::DOC_model_chehuishenqingshu,
            ),
            self::FILE_GROUP_chehuishenqing_zhongcaiyuan_zhuanfa=>array(
//                self::DOC_model_chehuishenqingshu,
                self::DOC_model_chehuijuedingshu_zth,
            ),
            self::FILE_GROUP_chehuishenqing_zhuren_zf_zth=>array(
                self::DOC_model_chehuijuedingshu_zth,
            ),
            self::FILE_GROUP_gxq_dangshirenqingqiu=>array(
                self::DOC_model_gxqyy_sqs,
            ),
            self::FILE_GROUP_gxq_zhubanzhuanfa=>array(
                self::DOC_model_gxqyy_sqs,
                self::DOC_model_gxqyy_jueding,
            ),
            self::FILE_GROUP_gxq_fasongsuoyou=>array(
                self::DOC_model_gxqyy_jueding,
            ),
        );
        return $arr[$groupid];
    }



    public static function getInitFile($mod_id)
    {

        if ($mod_id == self::DOC_model_shoufeibiaozhun)//收费标准
        {
            return "sign/shoufeibiaozhun.pdf";
        }elseif ($mod_id == self::DOC_model_zcguize){   //仲裁规则
            return "sign/zhongcaiguize.pdf";
        }elseif($mod_id == self::DOC_model_mingce){
            return "sign/zhongcaiyuan.pdf";
        }
        return null;
    }

    
    // 这里设置盖章
    public static function getZhongCaiWeiSignDocIds(){
        return array(
            self::DOC_model_tongzhishu=> Constant::D_Role_ZhongCaiWei_LiAnShenPi,
            self::DOC_model_zutingtzs=> Constant::D_Role_ZhongCaiWei_LiAnShenPi,

            self::DOC_model_againzuting=> Constant::D_Role_ZhongCaiWei_LiAnShenPi,
            self::DOC_model_huibi_huifu=> Constant::D_Role_ZhongCaiWei_LiAnShenPi,
            self::DOC_model_chehuijuedingshu_ztq => Constant::D_Role_ZhongCaiWei_LiAnShenPi,
            self::DOC_model_gxqyy_jueding => Constant::D_Role_ZhongCaiWei_LiAnShenPi,
//            self::DOC_model_zhidingzhongcaiyuan => Constant::D_Role_ZhongCaiWei_LiAnShenPi,
//            self::DOC_model_cxzhidingzhongcaiyuan => Constant::D_Role_ZhongCaiWei_LiAnShenPi,
//            self::DOC_model_lianshenpibiao => Constant::D_Role_ZhongCaiWei_LiAnShenPi,

        );
    }
// 这里设置哪些文件是仲裁委内部文件，内部文件不往外发送
    public static function getZhongCaiWeiNeiBuDocMod(){
        return array(
            self::DOC_model_lianshenpibiao,
            self::DOC_model_zhidingzhongcaiyuan,
            self::DOC_model_cxzhidingzhongcaiyuan,
            self::DOC_model_caijueshenpi
        );
    }

    public static function getNeedZhengjuGroup(){
        return [
            self::FILE_GROUP_dabian,
            self::FILE_GROUP_gxq_dangshirenqingqiu
        ];
    }

    public static function mkDmpUrl($did,$gids,$exid)
    {
        if (is_array($gids))
        {
            $gids = implode(",",$gids);
        }
        return url('dossier.cp/dmp', 'id='.$did.'&gid='.$gids.'&exid='.$exid."&auto=1");
    }

    public static function getDangShiRenDroleArr(){
        return [
            Constant::D_Role_ShenQingRen,
            Constant::D_Role_ShenQingRen_FR,
            Constant::D_Role_ShenQingRen_Dl,
            Constant::D_Role_Bei_ShenQingRen,
            Constant::D_Role_Bei_ShenQingRen_Dl,
        ];
    }
    public static function getDangShiRenQroleArr(){
        return [
            Constant::QX_ROLE_SHENQINGREN,
            Constant::QX_ROLE_SHENQINGREN_DL,
            Constant::QX_ROLE_SHENQINGREN_fr,
            Constant::QX_ROLE_BEISHENQINGREN,
            Constant::QX_ROLE_BEISHENQINGREN_DL,
            Constant::QX_ROLE_BEI_SHENQINGREN_fr,
        ];
    }

    public static function zcyNotSee(){
        return [
            self::FILE_GROUP_huibi,
            self::FILE_GROUP_huibi_huifu,
            self::FILE_GROUP_gxq_fasongsuoyou
        ];
    }

}