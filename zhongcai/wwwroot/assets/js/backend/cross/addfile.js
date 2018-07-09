define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {

            //这里需要手动为Form绑定上元素事件
            Form.api.bindevent($("div#cxselectform1"));
        },
 
    };
    return Controller;
});