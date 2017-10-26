define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var $form = $("#message-create-form");
        var $modal = $form.parents('.modal');

        var message_input = $("input[name='message']");
        var submit_btn = $("#submit_btn");

        $('#modal').modal('show');

        submit_btn.on('click',function(){
            if(message_input.val() == ""){
                Notify.danger("内容不能为空啊",3);
                return false;
            }

            submit_btn.attr('disable','disable');
            $.post($form.attr('action'),{'message':message_input.val()},function(result){
                if(result.status){
                    Notify.success(result.message,3);
                    $modal.modal('hide');
                }else{
                    Notify.danger(result.message,3);
                }
                submit_btn.removeAttr('disable');
            },'json').error(function(){
                Notify.danger("发生错误！",3);
            });

        });
    }

});