define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'person/index',
                    add_url: '{%add_url%}',
                    edit_url: '{%edit_url%}',
                    del_url: '{%del_url%}',
                    multi_url: 'person/index',

                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.list_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        //{checkbox: true},
                        {field: 'id', title: 'idid' },
                        {field: 'id_card', title: '身份证号' },
                        {field: 'real_name', title: '姓名' },
                        {field: 'num', title: '仲裁案件数' },
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
          
            // 为表格绑定事件
            Table.api.bindevent(table);
        },

        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});