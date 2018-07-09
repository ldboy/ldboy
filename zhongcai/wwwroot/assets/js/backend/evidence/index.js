define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'Evidence/index',
                    add_url: '',
                    edit_url: 'Evidence/upload',
                    del_url: '',
                    multi_url: 'Evidence/index',

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
                        {field: 'doc_id', title: '文档id' },
                        {field: 'attr_id', title: '属性id' },
                        {field: 'value', title: '描述' },
                        {field: 'status', title: '状态' },
                        {field: 'ext_id', title: '操作人' },
                        {field: 'path', title: '文件' },
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