/**
 * Created by mrren on 2018/5/2.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        doclist: function () {
            $(".btn-click").each(function () {
                $(this).on("click",function () {
                    Fast.api.open($(this).attr("href"));
                    return false;
                })
            })
        },
        info:function () {
            alert(1);
        }
    };

    return Controller;
});