/**
 * Created by mrren on 2018/5/2.
 */
define(['jquery', 'bootstrap', 'backend', 'table', 'form','adminlte'], function ($, undefined, Backend, Table, Form,AdminLTE) {

    if (!top.$("body").hasClass("sidebar-collapse")) {
        top.$(".sidebar-toggle").trigger("click");
    }
   //

    var Controller = {
        initdaohang:function () {
            $("#ws-tab li").each( function (index) {

                $(this).on("click",function(index){
                    return function () {

                        var index1 = index;
                        $("#ws-tab li").each(function () {
                            $(this).removeClass("active");
                        });
                        $(this).addClass("active");
                        if(index==0) {
                            $("#ws-body >div").each(function () {
                                $(this).show();
                            });

                        }else   if(index== ($("#ws-tab li").length-1 ))
                        {
                            index1 =index-1;
                            $("#ws-body >div").each(function (i) {
                                if(i>=index1)
                                {
                                    $(this).show();
                                }else
                                $(this).hide();
                            });


                        }else{
                            index1 =index-1;
                            $("#ws-body >div").each(function () {
                                $(this).hide();
                            });
                            $($("#ws-body >div").get(index1)).show();
                        }

                    };

                }(index));
            });
        },
        index: function () {
            this.initdaohang();
            $(".btn-goto").each( function (index) {
                $(this).on("click",function () {
                   // alert($(this).data("gotoid"));
                    $($("#ws-tab li").get($(this).data("gotoid"))).trigger("click");
                    return false;
                });
            });


        },
        view :function () {
            this.initdaohang();
        }
    };

    return Controller;
});