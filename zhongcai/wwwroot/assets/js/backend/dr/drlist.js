define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'Dr/drlist/dossier_id/'+"<?php echo $dossier_id; ?>",
                    add_url: '',
                    edit_url: '',
                    del_url: '',
                    multi_url: '',

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
                        {field: 'name', title: '文档名称' },
                        {field: 'addtime', title: '创建时间' , operate:'RANGE', addclass:'datetimerange'},
                        {field: 'create_type', title: '创建方式' },
                        {field: 'exid', title: '扩展id' },
                        {field: 'dossier_id', title: '卷宗id' },
                        {field: 'attr1_num', title: '表单属性个数' },
                        {field: 'attr2_num', title: '签字属性个数' },
                        {field: 'attr3_num', title: '其它属性个数' },
                        {field: 'attr1_success', title: '表单属性提交个数' },
                        {field: 'attr2_success', title: '签字属性提交个数' },
                        {field: 'attr3_success', title: '其它属性提交个数' },
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