define(['jquery', "upload", "form"], function ($, Upload, Form) {

    var Controller = {
        signing: function () {
            var data = signingdata;

            var btndata = data;
            var log = $("#log");
            if (data && data['docid']) {


                var index = 0;
                showmsg("请稍等");
                function showmsg(title) {

                    log.html(title);
                }


                var f = function () {

                    $.getJSON(Config.moduleurl + "/wsdoc.sign/beforeSign/docid/" + data['docid'] + "/auto/1", function (result) {
                        var data = result;
                        if (data.ok == 0) {

                            Layer.alert(data.msg);
                        } else if (data.ok == 1) {
                            showmsg(data.msg);
                            setTimeout(f, 3000);
                        } else if (data.ok == 2) {

                            window.location.href = data.url;

                        } else if (data.ok == 3) {

                            showmsg(data.msg);

                            if (btndata['next_ajax']) {
                                Fast.api.ajax({
                                    url: btndata['next_url'],
                                    type: "GET"
                                }, function (data, ret) {

                                    var s_div =  $("#status");
                                    s_div.removeClass("fa-spinner");
                                    s_div.removeClass("fa-spin");
                                    s_div.addClass("fa-check");
                                    s_div.css({"color":"green"});
                                     showmsg("操作成功");


                                    //
                                    setTimeout(function () {
                                       // window.location.href = ret.url;
                                        ret.data.alert=0;
                                        ret.url="";
                                        WsInit.onAjaxGetData(ret, false);
                                    },1000);
                                 // WsInit.onAjaxGetData(ret, true);

                                });
                            }else{
                                window.location.href = btndata['next_url'];
                            }


                        }


                    });


                };

                f();

            }
        }
    };
    return Controller;
});