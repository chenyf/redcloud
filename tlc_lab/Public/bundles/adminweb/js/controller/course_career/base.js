define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $form = $('#career_base_form');

        $('body').on('click','#submit-confirm-btn',function(){

            if($("input[name='name']").val().trim() == ""){
                Notify.danger("名称不能为空",3);
                return false;
            }

            if($("textarea[name='description']").val().trim() == ""){
                Notify.danger("描述不能为空",3);
                return false;
            }

            var url = $(this).data("url");
            $submit_btn = $(this);
            $submit_btn.button('submiting').addClass('disabled');

            $.ajax({
                method:'POST',
                url:url,
                data:{
                    name:$("input[name='name']").val().trim(),
                    description:$("textarea[name='description']").val().trim()
                },
                success:function(result){
                    if(result.status){
                        Notify.success(result.message,2);
                        window.location.reload();
                    }else{
                        Notify.danger(result.message,5);
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