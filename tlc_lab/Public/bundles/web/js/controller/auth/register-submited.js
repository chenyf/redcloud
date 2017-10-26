define(function(require, exports, module) {

    exports.run = function() {
        $.post($('#resend-email').data('url'));
        
        var refreshTime = function(){
            var time = parseInt($(".second-down").html());
            $(".second-down").html(time-1);
            if ( time-1 > 0) {
                window.timeout = setTimeout(refreshTime, 1000);
            } else {
                clearTimeout(window.timeout);
                $("#resend-email").removeAttr("disabled");
            }
        };
        refreshTime();

        $('#resend-email').on('click', function(){
            var time = parseInt($(".second-down").html());
            if(time>0){
                $("#resend-email").attr("disabled","disabled");
                return false;
            }
            $("#email-sending").show();
            $("#email-send-success").hide();
            $.post($(this).data('url'), function(json) {

            }, 'json').complete(function(){
                $(".second-down").html(120);
                $("#resend-email").attr("disabled","disabled");
                $("#email-sending").hide();
                $("#email-send-success").show();
                refreshTime();
            });
        });

    }

});