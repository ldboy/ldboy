define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dma/index',
                    add_url: 'dma/add',
                    edit_url: 'dma/edit',
                    del_url: 'dma/del',
                    multi_url: 'dma/multi',
                    table: 'dma',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'doc_model_id', title: __('Doc_model_id')},
                        {field: 'type', title: __('Type'), visible:false, searchList: {"4":__('Type 4')}},
                        {field: 'type_text', title: __('Type'), operate:false},
                        {field: 'sub_type', title: __('Sub_type'), visible:false, searchList: {"4":__('Sub_type 4')}},
                        {field: 'sub_type_text', title: __('Sub_type'), operate:false},
                        {field: 'name', title: __('Name')},
                        {field: 'flag', title: __('Flag'), formatter: Table.api.formatter.flag},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'sync_id', title: __('Sync_id')},
                        {field: 'dm.model_name', title: __('Dm.model_name')},
                        {field: 'dm.addtime', title: __('Dm.addtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'dm.type', title: __('Dm.type')},
                        {field: 'dm.create_type', title: __('Dm.create_type')},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});