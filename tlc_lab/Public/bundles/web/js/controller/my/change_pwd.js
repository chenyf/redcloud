define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function(){


        var validator = new Validator({
            element: '#change-pwd-form',
            failSilently: true,
            triggerType : 'change',
            autoSubmit: false,
            onFormValidated: function(error, results, $form){
                if (error) {
                    return false;
                }
                $('#password-save-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(result) {
                    result = JSON.parse(result);
                    $('#password-save-btn').button('reset').removeClass('disabled');
                    if(!result.status){
                        Notify.danger(result.message,2);
                        return false;
                    }else{
                        Notify.success("修改密码成功！",2,function(){
                            window.location.reload();
                        });
                    }
                });
            }
        });

        validator.addItem({
            element: '[name="pwd[current]"]',
            required: true,
            errormessageRequired : '请输入当前密码'
        });

        validator.addItem({
            element: '[name="pwd[new_pwd]"]',
            required: true,
            errormessageRequired : '请输入新密码'
        });

        validator.addItem({
            element: '[name="pwd[new_pwd1]"]',
            required: true,
            errormessageRequired : '请输入确认密码'
        });

        validator.addItem({
            element: '[name="pwd[new_pwd1]"]',
            rule: 'compare_pwd',
            errormessage : '两次输入密码不一致'
        });

        var compare_pwd = function(option){
            var pwd1_val = option.element.val();
            if(pwd1_val != $('[name="pwd[new_pwd]"]').val()){
                return false;
            }
            return true;
        }

        Validator.addRule('compare_pwd', compare_pwd,"两次输入密码不一致");

    }
});
