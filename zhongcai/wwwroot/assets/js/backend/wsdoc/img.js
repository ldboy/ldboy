define(['jquery', "upload","form"], function ($, Upload,Form) {

    var Controller = {
        index: function () {

            Form.api.bindevent($("form[role=form]"));
            //使用Plupload上传
            Upload.api.plupload($(".plupload"), function (data, ret) {
                // alert(1);
            }, function (data, ret) {

            }, function (data, ret) {
                // console.log(data);
                //console.log(ret);
                var imgurls = [];
                var fids = [];
                data.forEach(function (e) {

                    console.log(e.ret);
                    if (e.ret.code == 1) {
                        imgurls.push(e.ret.data.url);
                        fids.push(e.ret.data.fid);
                    }
                });
                Fast.api.ajax({
                    url: Config.moduleurl + "/wsdoc.img/adds?&docid=" + Fast.api.query("docid", window.location.href),
                    type: "POST",
                    data: {imgs: imgurls,fids:fids}
                }, function (data, result) {




                    WsInit.onAjaxGetData(result, true);

                });
            });
        },
        add: function () {
            Form.api.bindevent($("form[role=form]"));
        },
        edit: function () {
            Form.api.bindevent($("form[role=form]"));
        }
    };
    return Controller;
});