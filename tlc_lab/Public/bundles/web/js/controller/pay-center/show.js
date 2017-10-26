define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        $('.course-nav-tabs li').hover(function() {
            $('.course-nav-tabs li').removeClass('active');
            $(this).addClass('active');
            $(".payment-box .inter-bank").hide();
            $(".payment-box .inter-bank").eq($(this).index()).show();
        });
        setInterval(function() {
            var url = $('#orderUrl').val();
            $.post(url, function(data) {
                if (data == 'true') {
                    Notify.success('订单支付成功！');
                    setTimeout("window.location.reload()", 1000);
                }
            });
        }, 3000);
        $('#cashPay').click(function(){
            var cash=parseFloat($('#cash').val());
            var amount=parseFloat($("#amount").val());
            if(amount>cash){
                 Notify.danger('余额不足！');  
            }else{
                window.location.href=$(this).attr('url'); 
            }
        })
    };

});