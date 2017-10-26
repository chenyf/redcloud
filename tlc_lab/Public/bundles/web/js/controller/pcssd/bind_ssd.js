define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        $("#sure_to_submit").on("click",function(){
            $submit_btn = $(this);
            $submit_btn.button('submiting').addClass('disabled');

            var sn_num = $("input[name='ssd_sn_numer']").val().trim();
            var is_binded = $("input[name='ssd_sn_numer']").data("binded");
            if(sn_num == "" && is_binded == 1){
                if(!confirm("不填意味着解除绑定SSD码，您确定要解除SSD码的绑定吗？")){
                   return false;
                }
            }

            $.ajax({
                method:'POST',
                url:$submit_btn.data('url'),
                data:{
                    "sn_num":sn_num
                },
                success:function(result){
                    result = JSON.parse(result);
                    if(result.success){
                        window.location.reload();
                    }else{
                        Notify.danger(result.msg,5);
                    }
                    $submit_btn.button('reset').removeClass('disabled');
                },
                error:function(){
                    Notify.danger("发生错误！",5);
                    $submit_btn.button('reset').removeClass('disabled');
                }
            });
        });

    };

});