define(function(require, exports, module) {
    exports.run = function() {
        
        //倒计时
        var refreshTime = function(){
            var time = parseInt($(".second-down").html());
            $(".second-down").html(time-1);
            if ( time-1 > 0) {
                window.timeout = setTimeout(refreshTime, 1000);
            } else {
                clearTimeout(window.timeout);
                window.location.href = $(".second-down").parent().attr("href");
            }
        };
        refreshTime();
        
        $("#modal").on('show.bs.modal', function () {
            clearTimeout(window.timeout);
        }).on('hide.bs.modal', function () {
            refreshTime();
        });
    };
});