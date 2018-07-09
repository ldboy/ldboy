define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'user_manager/zcadminlist',
                    add_url: 'user_manager/addzcadmin',
                    edit_url: 'user_manager/addzcadmin',
                    del_url: 'user_manager/delzcadmin',
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
                                                {field: 'name', title: '姓名' },
                        {field: 'th_id', title: '仲裁id' },
                        {field: 'role', title: '角色' },
                        {field: 'status', title: '状态' },
                        {field: 'idid', title: '身份证id' },
                        {field: 'addtime', title: '添加时间' },
                        {field: 'phone', title: '手机号' },
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