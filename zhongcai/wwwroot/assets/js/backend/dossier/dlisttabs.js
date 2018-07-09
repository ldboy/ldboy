define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dossier.dlisttabs/dlist',
                    table: 'dlist',
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
                        {field: 'zno', title: "案件号"},
                        {field: 'title', title: "案由"},
                        {field: 'sq_time', title: "申请时间"},
                        {field: 'sl_time', title: "立案时间"},

                        {field: 'sq_string', title: "申请人", align: 'left'},
                        //{field: 'phone', title: "申请方手机号", align: 'left'},
                        //{field: 'id_num', title: "申请方身份证号", align: 'left'},
                        {field: 'bsq_string', title: "被申请人",align: 'left'},

                        {field: 'status_string', title: "当前状态", operate: false, formatter: Table.api.formatter.bgcolorClass},

                        {field: 'sub_status_string', title: "二级状态", operate: false, formatter: Table.api.formatter.bgcolorClass,visible:false},
                        {field: 'two_status', title: "温馨提示", operate: false, formatter: Table.api.formatter.bgcolorClass},

                        //{field: 'zcw_name', title: "人员"},
                        {field: 'operate', title: __('Operate'), formatter: function () {

                             return '<a href="'+(arguments[1]['status']==1 ? ($.fn.bootstrapTable.defaults.extend.view_url1+arguments[1]['id']):($.fn.bootstrapTable.defaults.extend.view_url+arguments[1]['id']))+'" class="btn btn-info btn-xs btn-detail btn-addtabs" title="'+arguments[1]['zno_title']+'" ></i>查看</a>'
                        }}
                    ]
                ]
            };
            // 初始化表格
            table.bootstrapTable(tableOptions);

            // 为表格绑定事件
            Table.api.bindevent(table);
            console.log(tableOptions);

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