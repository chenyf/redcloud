define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        var validator = new Validator({
            element: '#setting-email-form',
            onFormValidated: function(error){
                if (error) {
                    return false;
                }
                $('#email-save-btn').button('submiting').addClass('disabled');
            }
        });

        validator.addItem({
            element: '[name="form[password]"]',
            required: true
        });

        validator.addItem({
            element: '[name="form[email]"]',
            required: true,
            rule: 'email'
        });

        $('#send-verify-email').click(function(){
            var $btn = $(this);
            //edit fubaosheng 2015-08-13 提示
            $.post($btn.data('url'),function(res){
                if(res == "true")
                    Notify.success('请到邮箱中接收验证邮件，并点击邮件中的链接完成验证。');
                else
                    Notify.danger('邮箱验证邮件发送失败，请联系管理员。');
            });
        });
    };

});