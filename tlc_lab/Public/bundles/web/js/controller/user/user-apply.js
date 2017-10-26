define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $modal = $('#user-apply-form').parents('.modal');
        
        var validator = new Validator({
            element: '#user-apply-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                if($("#applyCateid").val() == "0"){
                    Notify.danger('请选择所属院/系');
                    return false;
                }
               
                $('#save-btn').button('submiting').addClass('disabled');

                $.post($form.attr('action'), $form.serialize(), function(res) {
                    if(res.status == 1){
                        $modal.modal('hide');
                        Notify.success(res.info);
                    }else{
                        $('#save-btn').button('submit').removeClass('disabled').text("提交申请");
                        Notify.danger(res.info);
                        return false;
                    }
                },'json').error(function(){
                    $('#save-btn').button('submit').removeClass('disabled').text("提交申请");
                    Notify.danger('申请失败');
                });

            }
        });
        
        validator.addItem({
            element: '[name="applyName"]',
            required: true,
            rule: 'chinese_alphanumeric minlength{min:2} maxlength{max:20}'
        });
        
        validator.addItem({
            element: '[name="applyMobile"]',
            required: true,
            rule: 'mobile fixedLength{len:11}'
        });
        
        validator.addItem({
            element: '[name="applyEmail"]',
            required: true,
            rule: 'email'
        });
        
        
         $("#remove-btn").click(function () {
            
            var applyId = $(this).data("id");
            var url = $(this).data("url");
            var self = $(this);
            if(self.data("ban") == 1) return false;
            self.data("ban",1);
            
            $.get(url, {applyId:applyId}, function (response) {
                self.data("ban",0);
                if ( response.status == 1 ) {
                    $modal.modal('hide');
                    Notify.success(response.info);
                } else {
                    Notify.danger(response.info);
                    return false;
                }
            },'json').error(function () {
                self.data("ban",0);
                Notify.danger('取消申请失败');
            }); 
            
        });
        
    };

});