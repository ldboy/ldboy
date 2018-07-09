define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'dma/dmalist',
                    add_url: 'dma/add',
                    edit_url: 'dma/add',
                    del_url: 'dma/del',
                    multi_url: 'dma/index',

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
                        {field: 'id', title: 'id' },
                        {field: 'doc_model_id', title: '文档模型id' },
                        {field: 'type', title: '类型' },
                        {field: 'sub_type', title: '二级类型' },
                        {field: 'op', title: '选项值' },
                        {field: 'name', title: '提示' },
                        {field: 'flag', title: '唯一标识' , formatter: Table.api.formatter.flag},
                        {field: 'createtime', title: '创建时间' },
                        {field: 'gid', title: '上级id' },
                        {field: 'sync_id', title: '同步id' },
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