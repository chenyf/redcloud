define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    require("jquery.autocomplete");
    require("jquery.autocomplete-css");
    var SmsSender = require('../widget/sms-sender');
    var Notify = require('common/bootstrap-notify');
    $.AutoComplete('#register_email');

    exports.run = function() {
        
        var $form = $('#register-form');
        
        var passwordAlphanumeric = function(option){
            var password = option.element.val();
            var reg = /([\u4E00-\uFA29]|[\uE7C7-\uE7F3])/ig;
            if(password.match(reg)){
                return false;
            }else{
                if(password.indexOf(" ")>=0){
                    return false;
                }
                var len = Number(password.length);
                if(len<5 || len>20){
                    return false;
                }
                return true;
            }
        };
        Validator.addRule('password_alphanumeric', passwordAlphanumeric, '密码由5-20位英文、数字、符号组成，区分大小写,不能有空格');
        
        var validator = new Validator({
            element: $form,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#register-btn').button('submiting').addClass('disabled');
            },
            failSilently: true
        });
        
        // 点击验证码和刷新圈
        $(".fa-refresh").click(function(){
            $("#getcode_num").trigger("click");
        });
        $("#getcode_num").click(function(){
            $(this).attr("src",$("#getcode_num").data("url")+ "?" + Math.random());
        });

        validator.addItem({
            element: '[name="email"]',
            required: true,
            rule: 'email email_remote'
        });
        
        validator.addItem({
            element: '[name="captcha_num"]',
            required: true,
            rule: 'remote'
        });

        validator.addItem({
            element: '[name="nickname"]',
            required: true,
            rule: 'chinese_alphanumeric minlength{min:2} maxlength{max:20}',
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'password_alphanumeric',
            display: '密码'
        });
       
        validator.addItem({
            element: '[name="terms"]',
            required: true,
            errormessageRequired: '请先阅读并同意《高校云用户注册协议》'
        });

        validator.addItem({
            element: '[name="mobile"]',
            required: true,
            rule: 'phone remote',
            errormessagePhone : "请输入正确的手机号码"
        });

        if($('input[name="sms_code"]').size()>0){
            
            validator.addItem({
                element: '[name="sms_code"]',
                required: true,
                rule: "integer fixedLength{len:5} mobile_code_remote{mobileSel:'#register_mobile'}",
                display: '短信验证码'
            });
            
            var smsSender = new SmsSender({
                element : '.js-sms-send',
                url: $('.js-sms-send').data('url'),
                second : 60,
                smsType:'sms_registration',
                preSmsSend: function(){
                    var couldSender = true;
                    validator.query('[name="mobile"]').execute(function(error, results, element) {
                        if (error) {
                            couldSender = false;
                            return;
                        }
                        couldSender = true;
                        return;
                    });
                    return couldSender;
                }      
            });
        }
        
    };

});