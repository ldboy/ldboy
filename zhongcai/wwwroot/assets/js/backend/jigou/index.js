define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'Jigou/jglist',
                    add_url: 'Jigou/add',
                    edit_url: 'Jigou/add',
                    del_url: 'Jigou/delete',
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
                        {checkbox: true},
                        {field: 'id', title: 'ID' },
                        {field: 'name', title: '机构名称' },
                        {field: 'address', title: '机构地址' },
                        {field: 'tel', title: '联系方式' },
                        {field: 'status', title: '状态' },
                        {field: 'intro', title: '简介' },
                        {field: 'total', title: '总业务' },
                        {field: 'finish_num', title: '成功业务' },
                        {field: 'failed_num', title: '失败业务' },
                        {field: 'addtime', title: '添加时间' },
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