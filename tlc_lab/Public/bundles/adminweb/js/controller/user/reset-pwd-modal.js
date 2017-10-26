define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $modal = $('#reset-pwd-task-form').parents('.modal');
        
        var validator = new Validator({
            element: '#reset-pwd-task-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                
                var type = $("[name=range]:checked").data("type");
                if(type != "recoed" && type != "all"){
                     Notify.danger('非法操作');
                     return false;
                }
                
                $('#reset-pwd-task-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), {type:type,recoedList:$(".userImportList").val()}, function(html) {
                    if(html.success){
                        Notify.success(html['message']);
                        $modal.modal('hide');
                        window.location.reload();
                    }else{
                        $('#reset-pwd-task-btn').button('submit').removeClass('disabled');
                        Notify.danger(html['message']);
                    }
                },'json').error(function(){
                    $('#reset-pwd-task-btn').button('submit').removeClass('disabled');
                    Notify.danger('添加重置密码任务失败');
                });

            }
        });
        
        $(".range").on("click",function(){
            var type = $(this).data("type");
            if(type == "recoed"){
                if($(".userImportList").size()<1){
                    Notify.danger('暂无导入用户记录');
                    $(".range[data-type=all]").trigger('click');
                    return false;
                }else{
                    $(".userImportList").removeClass("hide");
                }
            }else{
                $(".userImportList").addClass("hide").val("");
            }
        })
        

    };


});