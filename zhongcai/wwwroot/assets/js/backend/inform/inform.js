
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'inform/dlist',
                    //table: 'dlist',
                    view_url:'dossier.info/index?id=',
                    view_url1:'dossier/cp/add?dossier_id='
                }
            });


            var table = $("#table");
            var tableOptions = {
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                escape: false,
                pk: 'id',
                sortName: 'addtime',
                pagination: true,
                commonSearch: true,
                columns: [
                    [

                        {field: 'id', title: "ID",visible:false},

                        {field: 'zno', title: "案号"},
                        {field: 'status_', title: "查看状态"},
                        //{field: 'is_finish', title: "完成状态"},
                        {field: 'addtime', title: "消息时间"},
                        {field: 'content', title: "消息内容"},

                        {field: 'cz_role', title: "操作角色ID", align: 'left'},
                        {field: 'cz_idid', title: "操作人IDID", align: 'left'},
                        {field: 'type_', title: "消息类型", align: 'left'},

                        {field: 'operate', title: __('Operate'), formatter: function () {

                             return '<a href="'+$.fn.bootstrapTable.defaults.extend.view_url+arguments[1]['ywid']+'&msgid='+arguments[1]['id']+'" class="btn btn-info btn-xs btn-detail btn-addtabs" title="'+arguments[1]['zno_title']+'" ></i>查看</a>'
                        }}
                    ]
                ]
            };
            // 初始化表格
            table.bootstrapTable(tableOptions);

            // 为表格绑定事件
            Table.api.bindevent(table);


            var selecttype = -1;

            //绑定TAB事件
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                // var options = table.bootstrapTable(tableOptions);
                var typeStr = $(this).attr("href").replace('#','');

                console.log(12131);
                console.log(typeStr);
                var options = table.bootstrapTable('getOptions');
                options.pageNumber = 1;
                selecttype=typeStr;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});
                    params.type = typeStr;

                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;

            });


            $("#kuaijiesousuo").on("submit",function () {


                var options = table.bootstrapTable('getOptions');

                var fieleds = $(this).serialize();
                options.pageNumber = 1;
                options.queryParams = function (params) {
                    // params.filter = JSON.stringify({type: typeStr});

                    params.keywords = fieleds;
                    params.type = selecttype;
                    return params;
                };
                table.bootstrapTable('refresh', {});
                return false;
            });


            //必须默认触发shown.bs.tab事件
             //$($('ul.nav-tabs li.active a[data-toggle="tab"]').get(0)).trigger("shown.bs.tab");

        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                $(document).on("change", "#c-type", function () {
                    $("#c-pid option[data-type='all']").prop("selected", true);
                    $("#c-pid option").removeClass("hide");
                    $("#c-pid option[data-type!='" + $(this).val() + "'][data-type!='all']").addClass("hide");
                    $("#c-pid").selectpicker("refresh");
                });
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});