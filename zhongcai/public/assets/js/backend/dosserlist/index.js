define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        run: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    list_url: 'Dosserlist/index',
                    add_url: 'Dosserlist/add',
                    edit_url: 'Dosserlist/add',
                    del_url: 'Dosserlist/delete',
                    multi_url: 'Dosserlist/index',

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
                        {field: 'title', title: '标题' },
                        {field: 'jg_name', title: '机构名称' },
                        {field: 'address', title: '机构地址' },
                        {field: 'no', title: '卷宗编号' },
                        {field: 'zno', title: '仲裁受理编号' },
                        {field: 'type', title: '业务类型' },
                        {field: 'zc_jg_id', title: '仲裁机构' },
                        {field: 'third_jg_id', title: '第三方机构' },
                        {field: 'status', title: '状态' },
                        {field: 'addtime', title: '添加时间' },
                        {field: 'is_valid', title: '是否失效' },
                        {field: 'money', title: '仲裁金额' },
                        {field: 'dispute_money', title: '争议金额' },
                        {field: 'du_type', title: '自然人/机构/法人' },
                        {field: 'du_name', title: '用户名称' },
                        {field: 'role', title: '角色' },
                        {field: 'id_num', title: '身份证号/信用代码' },
                        {field: 'phone', title: '联系方式' },
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