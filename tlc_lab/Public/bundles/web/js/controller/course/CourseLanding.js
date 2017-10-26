define(function(require, exports, module) {

    require("jquery.bootstrap-datetimepicker");
   
    require("$");
    var Notify = require('common/bootstrap-notify');
        require('masonry');
        require('jquery.img');
         require('jquery.unslider');
    exports.run = function() {
        var price = $("#price").text();
        var preferentialPrice = $("#preferentialPrice").text();


        $("#economize").text(price - preferentialPrice);

        var banner = document.getElementsByName("banner[]");
        var lb = {};
        for (var i = 0; i < banner.length; i++) {
            lb[i] = $('#' + banner[i].value).unslider({
                dots: true,
                fluid: true
            }),
            lb['data' + banner[i].value] = lb[i].data('unslider');
            $('.unslider-arrow' + banner[i].value).click(function() {
                var t = $(this).data('id');
                var fn = $(this).data('move');
                lb['data' + t][fn]();
            });
        }





        $('#carousel').carouFredSel({
            prev: '#prev',
            next: '#next',
            pagination: "#pager",
            scroll: 1000,
            items:4
        });

    }

})












