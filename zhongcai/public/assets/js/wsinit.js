define(['jquery'], function ($) {


    function init() {


        if (Config.wsreload) {
            // var cmd = "";
            // for (var i =0;i<Config.wsreload;i++)
            // {
            //     cmd = cmd+"parent.";
            //     eval(cmd+"Ly")
            // }

            if (parent) {
                parent.Layer.wsreload = Config.wsreload;
            }

        }
        $('.btn-prompt-tip').on("click",

            function (e) {

                e.stopPropagation();

                e.preventDefault();

                var that = this;
                var data = $(this).data();


                Layer.prompt(
                    {
                        title: data.tip ? data.tip : "请填写...",
                        formType: 2
                    },
                    function (text, index) {

                        if (index) {
                            if ($(that).is("a")) {


                                Fast.api.ajax({
                                    url: $(that).attr("href").indexOf("?") > 0 ? ( $(that).attr("href") + "&msg=" + text) : ($(that).attr("href") + "?msg=" + text),
                                    type: "POST"
                                }, function (data, result) {

                                    WsInit.onAjaxGetData(result, true);

                                });


                            }


                        }
                    }
                )
                ;


            });

        $('.btn-warning-tip').on("click",

            function (e) {

                e.stopPropagation();

                e.preventDefault();

                var that = this;
                var data = $(this).data();


                Layer.confirm(
                    data.tip ? data.tip : "确定要提交吗?",
                    {icon: 3, title: data.title ? data.title : "提示", shadeClose: true},
                    function (index) {
                        Layer.close(index);


                        if (index) {
                            var btn = $(that);

                            if (btn.attr("type") && btn.attr("type") == "submit") {
                                var form = $(that).closest('form');
                                if (form.length > 0) {
                                    form.submit();
                                }
                            } else {

                                if ($(that).is("a")) {

                                    if (data.ajax) {

                                        Fast.api.ajax({
                                            url: $(that).attr("href"),
                                            type: "GET"
                                        }, function (data, result) {


                                            WsInit.onAjaxGetData(result, true);

                                        });

                                    }
                                    else if (data.dialog) {

                                        Backend.api.open($(that).attr("href"), Fast.api.query("title", $(that).attr("href")));

                                    } else

                                        window.location.href = $(that).attr("href");
                                }

                            }


                        }
                    }
                );


            });


        $('.bt-gotosign').on("click",

            function (e) {

                e.stopPropagation();

                e.preventDefault();


                var data = $(this).data();


                if (data && data['docid']) {


                    var index = 0;
                    showmsg("请稍等");
                    function showmsg(title) {
                        Layer.closeAll();
                        index = Layer.msg(title, {
                            time: 2000000, //2秒关闭（如果不配置，默认是3秒）

                            shade: 0.4
                        });
                    }


                    var f = function () {

                        $.getJSON(Config.moduleurl + "/wsdoc.sign/beforeSign/docid/" + data['docid'], function (result) {
                            var data = result;
                            if (data.ok == 0) {
                                Layer.closeAll();
                                Layer.alert(data.msg);
                            } else if (data.ok == 1) {
                                showmsg(data.msg);
                                setTimeout(f, 3000);
                            } else if (data.ok == 2) {
                                layer.close(index);
                                window.location.href = data.url;
                            }


                        });


                    };

                    f();
                }


            });


    }


    function onAjaxGetData(ret, reloadinthis) {


        function dothis(ret, url) {
            if (url) {

                window.location.href = url;

            } else {
                //提示及关闭当前窗口

                var relaod = function () {
                    var index = parent.Layer.getFrameIndex(window.name);
                    if (parent.$(".btn-refresh").length > 0) {
                        parent.$(".btn-refresh").trigger("click");
                        parent.Layer.close(index);

                    } else {

                        if (reloadinthis) {
                            Fast.api.reload(ret.data && ret.data.wsreload ? ret.data.wsreload : 1);
                        } else
                            parent.Fast.api.closeAndReload(index, ret.data && ret.data.wsreload ? ret.data.wsreload : 1);
                    }
                };


                relaod();


            }
        }


        if (ret.data && ret.data.alert) {
            Layer.alert(ret.msg, function () {
                dothis(ret, ret.url);
            });
        } else if (ret.data && ret.data.confirm) {

            layer.confirm(ret.data.tip, {
                btn: [ret.data.btn1, ret.data.btn2] //按钮
            }, function () {
                dothis(ret, ret.url);
            }, function () {
                dothis(ret, ret.url2);
            });

        } else {
            if (ret.msg) Layer.msg(ret.msg);
            dothis(ret, ret.url);
        }


    }

    var out = {init: init, onAjaxGetData: onAjaxGetData};

    window.WsInit = out;
    return out;
});