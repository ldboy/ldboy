define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var exp = {
        run: function () {
            exp.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
                Form.events.plupload($("form"));
            }
        }
    };
    return exp;
});