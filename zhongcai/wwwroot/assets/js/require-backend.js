require.config({
    urlArgs: "v=" + requirejs.s.contexts._.config.config.site.version,
    packages: [{
            name: 'moment',
            location: '../libs/moment',
            main: 'moment'
        }
    ],
    //在打包压缩时将会把include中的模块合并到主文件中
    include: ['css', 'layer', 'toastr', 'fast', 'backend', 'backend-init', 'table', 'form', 'dragsort', 'drag', 'drop', 'addtabs', 'selectpage',"wsinit","slimscroll","adminlte","jquery","addons","backend/dossier/info","backend/dossier/dlisttabs"],
    paths: {
        'lang': "empty:",
        'form': 'require-form',
        'table': 'require-table',
        'upload': 'require-upload',
        'validator': 'require-validator',
        'drag': 'jquery.drag.min',
        'drop': 'jquery.drop.min',
        'echarts': 'echarts.min',
        'echarts-theme': 'echarts-theme',
        'adminlte': 'adminlte',
        'bootstrap-table-commonsearch': 'bootstrap-table-commonsearch',
        'bootstrap-table-template': 'bootstrap-table-template',
        //
        // 以下的包从bower的libs目录加载
        'jquery': '../libs/jquery/dist/jquery.min',
        'bootstrap': '../libs/bootstrap/dist/js/bootstrap.min',
        'bootstrap-datetimepicker': '../libs/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min',
        'bootstrap-daterangepicker': '../libs/bootstrap-daterangepicker/daterangepicker',
        'bootstrap-select': '../libs/bootstrap-select/dist/js/bootstrap-select.min',
        'bootstrap-select-lang': '../libs/bootstrap-select/dist/js/i18n/defaults-zh_CN',
        'bootstrap-table': '../libs/bootstrap-table/dist/bootstrap-table.min',
        'bootstrap-table-export': '../libs/bootstrap-table/dist/extensions/export/bootstrap-table-export.min',
        'bootstrap-table-mobile': '../libs/bootstrap-table/dist/extensions/mobile/bootstrap-table-mobile',
        'bootstrap-table-lang': '../libs/bootstrap-table/dist/locale/bootstrap-table-zh-CN',
        'tableexport': '../libs/tableExport.jquery.plugin/tableExport.min',
        'dragsort': '../libs/fastadmin-dragsort/jquery.dragsort',
        'sortable': '../libs/Sortable/Sortable.min',
        'addtabs': '../libs/fastadmin-addtabs/jquery.addtabs',
        'slimscroll': '../libs/jquery-slimscroll/jquery.slimscroll',
        'validator-core': '../libs/nice-validator/dist/jquery.validator',
        'validator-lang': '../libs/nice-validator/dist/local/zh-CN',
        'plupload': '../libs/plupload/js/plupload.min',
        'toastr': '../libs/toastr/toastr',
        'jstree': '../libs/jstree/dist/jstree.min',
        'layer': '../libs/layer/dist/layer',
        'cookie': '../libs/jquery.cookie/jquery.cookie',
        'cxselect': '../libs/fastadmin-cxselect/js/jquery.cxselect',
        'template': '../libs/art-template/dist/template-native',
        'selectpage': '../libs/fastadmin-selectpage/selectpage',
        'citypicker': '../libs/city-picker/dist/js/city-picker.min',
        'citypicker-data': '../libs/city-picker/dist/js/city-picker.data',
        'jsign': '../libs/jsignature/jSignature',
    },
    // shim依赖配置
    shim: {
        'addons': ['backend'],
        'jsign': ['jquery',"../libs/jsignature/flashcanvas"],
        'bootstrap': ['jquery'],
        'bootstrap-table': {
            deps: [
                'bootstrap',
//                'css!../libs/bootstrap-table/dist/bootstrap-table.min.css'
            ],
            exports: '$.fn.bootstrapTable'
        },
        'bootstrap-table-lang': {
            deps: ['bootstrap-table'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'bootstrap-table-export': {
            deps: ['bootstrap-table', 'tableexport'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'bootstrap-table-mobile': {
            deps: ['bootstrap-table'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'bootstrap-table-advancedsearch': {
            deps: ['bootstrap-table'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'bootstrap-table-commonsearch': {
            deps: ['bootstrap-table'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'bootstrap-table-template': {
            deps: ['bootstrap-table', 'template'],
            exports: '$.fn.bootstrapTable.defaults'
        },
        'tableexport': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        },
        'slimscroll': {
            deps: ['jquery'],
            exports: '$.fn.extend'
        },
        'adminlte': {
            deps: ['bootstrap', 'slimscroll'],
            exports: '$.AdminLTE'
        },
        'bootstrap-datetimepicker': [
            'moment/locale/zh-cn',
//            'css!../libs/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css',
        ],
        'bootstrap-select': ['css!../libs/bootstrap-select/dist/css/bootstrap-select.min.css', ],
        'bootstrap-select-lang': ['bootstrap-select'],
//        'toastr': ['css!../libs/toastr/toastr.min.css'],
        'jstree': ['css!../libs/jstree/dist/themes/default/style.css', ],
        'plupload': {
            deps: ['../libs/plupload/js/moxie.min'],
            exports: "plupload"
        },
//        'layer': ['css!../libs/layer/dist/theme/default/layer.css'],
//        'validator-core': ['css!../libs/nice-validator/dist/jquery.validator.css'],
        'validator-lang': ['validator-core'],
//        'selectpage': ['css!../libs/fastadmin-selectpage/selectpage.css'],
        'citypicker': ['citypicker-data', 'css!../libs/city-picker/dist/css/city-picker.css']
    },
    baseUrl: requirejs.s.contexts._.config.config.site.cdnurl + '/assets/js/', //资源基础路径
    map: {
        '*': {
            'css': '../libs/require-css/css.min'
        }
    },
    waitSeconds: 30,
    charset: 'utf-8' // 文件编码
});


define("lang",{"no file upload or server upload limit exceeded":"未上传文件或超出服务器上传限制","uploaded file format is limited":"上传文件格式受限制","upload successful":"上传成功","user id":"会员ID","username":"用户名","nickname":"昵称","password":"密码","sign up":"注 册","sign in":"登 录","sign out":"注 销","keep login":"保持会话","guest":"游客","welcome":"%s，你好！","view":"查看","add":"添加","edit":"编辑","del":"删除","delete":"删除","import":"导入","detail":"详情","multi":"批量更新","setting":"配置","move":"移动","name":"名称","status":"状态","weigh":"权重","operate":"操作","warning":"温馨提示","default":"默认","article":"文章","page":"单页","ok":"确定","acceptance":"受理","no acceptance":"拒绝受理","apply":"应用","cancel":"取消","clear":"清空","custom range":"自定义","today":"今天","yesterday":"昨天","last 7 days":"最近7天","last 30 days":"最近30天","last month":"上月","this month":"本月","loading":"加载中","more":"更多","yes":"是","no":"否","normal":"正常","hidden":"隐藏","locked":"锁定","submit":"提交","reset":"重置","execute":"执行","close":"关闭","choose":"选择","search":"搜索","refresh":"刷新","install":"安装","uninstall":"卸载","first":"首页","previous":"上一页","next":"下一页","last":"末页","none":"无","home":"主页","online":"在线","login":"登录","logout":"注销","profile":"个人配置","index":"首页","hot":"热门","recommend":"推荐","dashboard":"控制台","upload":"上传","uploading":"上传中","code":"编号","message":"内容","line":"行号","file":"文件","menu":"菜单","type":"类型","title":"标题","content":"内容","append":"追加","select":"选择","memo":"备注","parent":"父级","params":"参数","permission":"权限","check all":"选中全部","expand all":"展开全部","begin time":"开始时间","end time":"结束时间","create time":"创建时间","update time":"更新时间","flag":"标志","drag to sort":"拖动进行排序","redirect now":"立即跳转","common search":"普通搜索","search %s":"搜索 %s","view %s":"查看 %s","%d second%s ago":"%d秒前","%d minute%s ago":"%d分钟前","%d hour%s ago":"%d小时前","%d day%s ago":"%d天前","%d week%s ago":"%d周前","%d month%s ago":"%d月前","%d year%s ago":"%d年前","set to normal":"设为正常","set to hidden":"设为隐藏","go back":"返回首页","jump now":"立即跳转","operation completed":"操作成功!","operation failed":"操作失败!","unknown data format":"未知的数据格式!","network error":"网络错误!","invalid parameters":"未知参数","no results were found":"记录未找到","no rows were deleted":"未删除任何行","no rows were updated":"未更新任何行","parameter %s can not be empty":"参数%s不能为空","are you sure you want to delete the %s selected item?":"确定删除选中的 %s 项?","are you sure you want to delete this item?":"确定删除此项?","are you sure you want to delete or turncate?":"确定删除或清空?","you have no permission":"你没有权限访问","please enter your username":"请输入你的用户名","please enter your password":"请输入你的密码","please login first":"请登录后操作","you can upload up to %d file%s":"你最多还可以上传%d个文件","you can choose up to %d file%s":"你最多还可以选择%d个文件","an unexpected error occurred":"发生了一个意外错误,程序猿正在紧急处理中","this page will be re-directed in %s seconds":"页面将在 %s 秒后自动跳转","general":"常规管理","category":"分类管理","addon":"插件管理","auth":"权限管理","config":"系统配置","attachment":"附件管理","admin":"管理员管理","admin log":"管理员日志","group":"角色组","rule":"规则管理","user":"会员管理","user group":"会员分组","user rule":"会员规则","select attachment":"选择附件","update profile":"更新个人信息","local install":"本地安装","update state":"禁用启用","admin group":"超级管理组","second group":"二级管理组","third group":"三级管理组","second group 2":"二级管理组2","third group 2":"三级管理组2","dashboard tips":"用于展示当前系统中的统计数据、统计报表及重要实时数据","config tips":"可以在此增改系统的变量和分组,也可以自定义分组和变量,如果需要删除请从数据库中删除","category tips":"用于统一管理网站的所有分类,分类可进行无限级分类","attachment tips":"主要用于管理上传到服务器或第三方存储的数据","addon tips":"可在线安装、卸载、禁用、启用插件，同时支持添加本地插件。FastAdmin已上线插件商店 ，你可以发布你的免费或付费插件：<a href=\"https:\/\/www.fastadmin.net\/store.html\" target=\"_blank\">https:\/\/www.fastadmin.net\/store.html<\/a>","admin tips":"一个管理员可以有多个角色组,左侧的菜单根据管理员所拥有的权限进行生成","admin log tips":"管理员可以查看自己所拥有的权限的管理员日志","group tips":"角色组可以有多个,角色有上下级层级关系,如果子角色有角色组和管理员的权限则可以派生属于自己组别的下级角色组或管理员","rule tips":"规则通常对应一个控制器的方法,同时左侧的菜单栏数据也从规则中体现,通常建议通过命令行进行生成规则节点","undefined variable":"未定义变量","undefined index":"未定义数组索引","undefined offset":"未定义数组下标","parse error":"语法解析错误","type error":"类型错误","fatal error":"致命错误","syntax error":"语法错误","dispatch type not support":"不支持的调度类型","method param miss":"方法参数错误","method not exists":"方法不存在","module not exists":"模块不存在","controller not exists":"控制器不存在","class not exists":"类不存在","property not exists":"类的属性不存在","template not exists":"模板文件不存在","illegal controller name":"非法的控制器名称","illegal action name":"非法的操作名称","url suffix deny":"禁止的URL后缀访问","route not found":"当前访问路由未定义","undefined db type":"未定义数据库类型","variable type error":"变量类型错误","psr-4 error":"PSR-4 规范错误","not support total":"简洁模式下不能获取数据总数","not support last":"简洁模式下不能获取最后一页","error session handler":"错误的SESSION处理器类","not allow php tag":"模板不允许使用PHP语法","not support":"不支持","redisd master":"Redisd 主服务器错误","redisd slave":"Redisd 从服务器错误","must run at sae":"必须在SAE运行","memcache init error":"未开通Memcache服务，请在SAE管理平台初始化Memcache服务","kvdb init error":"没有初始化KVDB，请在SAE管理平台初始化KVDB服务","fields not exists":"数据表字段不存在","where express error":"查询表达式错误","no data to update":"没有任何数据需要更新","miss data to insert":"缺少需要写入的数据","miss complex primary data":"缺少复合主键数据","miss update condition":"缺少更新条件","model data not found":"模型数据不存在","table data not found":"表数据不存在","delete without condition":"没有条件不会执行删除操作","miss relation data":"缺少关联表数据","tag attr must":"模板标签属性必须","tag error":"模板标签错误","cache write error":"缓存写入失败","sae mc write error":"SAE mc 写入错误","route name not exists":"路由标识不存在（或参数不够）","invalid request":"非法请求","bind attr has exists":"模型的属性已经存在","relation data not exists":"关联数据不存在","relation not support":"关联不支持","chunk not support order":"Chunk不支持调用order方法","closure not support cache(true)":"使用闭包查询不支持cache(true)，请指定缓存Key","unknown upload error":"未知上传错误！","file write error":"文件写入失败！","upload temp dir not found":"找不到临时文件夹！","no file to uploaded":"没有文件被上传！","only the portion of file is uploaded":"文件只有部分被上传！","upload file size exceeds the maximum value":"上传文件大小超过了最大值！","upload write error":"文件上传保存错误！","has the same filename: {:filename}":"存在同名文件：{:filename}","upload illegal files":"非法上传文件","illegal image files":"非法图片文件","extensions to upload is not allowed":"上传文件后缀不允许","mimetype to upload is not allowed":"上传文件MIME类型不允许！","filesize not match":"上传文件大小不符！","directory {:path} creation failed":"目录 {:path} 创建失败！",":attribute require":":attribute不能为空",":attribute must be numeric":":attribute必须是数字",":attribute must be integer":":attribute必须是整数",":attribute must be float":":attribute必须是浮点数",":attribute must be bool":":attribute必须是布尔值",":attribute not a valid email address":":attribute格式不符",":attribute not a valid mobile":":attribute格式不符",":attribute must be a array":":attribute必须是数组",":attribute must be yes,on or 1":":attribute必须是yes、on或者1",":attribute not a valid datetime":":attribute不是一个有效的日期或时间格式",":attribute not a valid file":":attribute不是有效的上传文件",":attribute not a valid image":":attribute不是有效的图像文件",":attribute must be alpha":":attribute只能是字母",":attribute must be alpha-numeric":":attribute只能是字母和数字",":attribute must be alpha-numeric, dash, underscore":":attribute只能是字母、数字和下划线_及破折号-",":attribute not a valid domain or ip":":attribute不是有效的域名或者IP",":attribute must be chinese":":attribute只能是汉字",":attribute must be chinese or alpha":":attribute只能是汉字、字母",":attribute must be chinese,alpha-numeric":":attribute只能是汉字、字母和数字",":attribute must be chinese,alpha-numeric,underscore, dash":":attribute只能是汉字、字母、数字和下划线_及破折号-",":attribute not a valid url":":attribute不是有效的URL地址",":attribute not a valid ip":":attribute不是有效的IP地址",":attribute must be dateformat of :rule":":attribute必须使用日期格式 :rule",":attribute must be in :rule":":attribute必须在 :rule 范围内",":attribute be notin :rule":":attribute不能在 :rule 范围内",":attribute must between :1 - :2":":attribute只能在 :1 - :2 之间",":attribute not between :1 - :2":":attribute不能在 :1 - :2 之间","size of :attribute must be :rule":":attribute长度不符合要求 :rule","max size of :attribute must be :rule":":attribute长度不能超过 :rule","min size of :attribute must be :rule":":attribute长度不能小于 :rule",":attribute cannot be less than :rule":":attribute日期不能小于 :rule",":attribute cannot exceed :rule":":attribute日期不能超过 :rule",":attribute not within :rule":"不在有效期内 :rule","access ip is not allowed":"不允许的IP访问","access ip denied":"禁止的IP访问",":attribute out of accord with :2":":attribute和确认字段:2不一致",":attribute cannot be same with :2":":attribute和比较字段:2不能相同",":attribute must greater than or equal :rule":":attribute必须大于等于 :rule",":attribute must greater than :rule":":attribute必须大于 :rule",":attribute must less than or equal :rule":":attribute必须小于等于 :rule",":attribute must less than :rule":":attribute必须小于 :rule",":attribute must equal :rule":":attribute必须等于 :rule",":attribute has exists":":attribute已存在",":attribute not conform to the rules":":attribute不符合指定规则","invalid request method":"无效的请求类型","invalid token":"令牌数据无效","not conform to the rules":"规则错误"});

require(['jquery', 'bootstrap'], function ($, undefined) {

   // console.log(requirejs.s);
    //初始配置
    var Config = requirejs.s.contexts._.config.config;
    //将Config渲染到全局
    window.Config = Config;
    // 配置语言包的路径
    var paths = {};
    //paths['lang'] = Config.moduleurl + '/ajax/lang?callback=define';
    // 避免目录冲突
    paths['backend/'] = 'backend/';
    require.config({paths: paths});







    // 初始化
    $(function () {
        require(['fast'], function (Fast) {
            require(['backend', 'backend-init', 'addons'], function (Backend, undefined, Addons) {

                if (Config.jsname) {
                    require([Config.jsname], function (Controller) {
                       Controller[Config.actionname] != undefined && Controller[Config.actionname]();


                       require(['wsinit'],function (Wsinit) {
                           Wsinit.init();
                       })

                    }, function (e) {
                        console.error(e);
                        // 这里可捕获模块加载的错误
                    });
                }
            });
        });
    });
});
