define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'user_manager/jglist',
                    add_url: 'user_manager/addjg',
                    edit_url: 'user_manager/addjg',
                    del_url: 'user_manager/deljg',
                    multi_url: 'Tools/list1',

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
                                                {field: 'name', title: '机构名称' },
                        {field: 'address', title: '机构地址' },
                        {field: 'tel', title: '联系方式' },
                        {field: 'status', title: '状态' },
                        {field: 'zc_jg_id', title: '仲裁机构id' },
                        {field: 'a_jgid', title: '银行id' },
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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