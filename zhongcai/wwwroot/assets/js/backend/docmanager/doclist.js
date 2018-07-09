define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'Docmanager/doclist',
                    add_url: 'Docmanager/add',
                    edit_url: 'Docmanager/add',
                    del_url: 'Docmanager/deldoc',
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
                        {field: 'id', title: 'ID' },
                        {field: 'model_name', title: '模板名称' },
                        {field: 'addtime', title: '添加时间' },
                        {field: 'type', title: '模板类型' },
                        {field: 'create_type', title: '创建类型' },
                        {field: 'to_sign', title: '是否需要签字' },
                        {field: 'c_class', title: 'c_class' },
                        {field: 'file_type', title: '证据' },
                        {field: 'des', title: '证据描述' },
                        {field: 'type1_num', title: '表单属性个数' },
                        {field: 'type2_num', title: '签字属性个数' },
                        {field: 'type3_num', title: '上传属性个数' },
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