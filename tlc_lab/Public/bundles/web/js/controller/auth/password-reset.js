define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var SmsSender = require('../widget/sms-sender');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require("jquery.autocomplete");
    require("jquery.autocomplete-css");
    $.AutoComplete('#form_email');
    
    exports.run = function() {
        
        // 点击验证码和刷新圈
        $(".fa-refresh").click(function(){
            $("#getcode_num").trigger("click");
        });
        $("#getcode_num").click(function(){
            $(this).attr("src",$("#getcode_num").data("url")+ "?" + Math.random());
        });
        
        window.resetPwdType = $(".course-tabs-sort li.active").data('type');
        var smsSender,validator;
        
        var sendCode = function(){
            var ele,second;
            var type = window.resetPwdType;
            if('email' == type){
                ele = '.js-email-send';  
                second = 120;
            } 
            if('mobile' == type){
                ele = '.js-sms-send';  
                second = 60;
            }

            smsSender = new SmsSender({
                element : ele,
                url : $(ele).data('url'),
                smsType : 'sms_forget_password',
                second : second,
                type : type,
                preSmsSend : function(){
                    var couldSender = true;
                    validator.query('[name="'+type+'"]').execute(function(error, results, element) {
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
        
        var emailValidator = function(){
            var emailForm = '#password-reset-form';
            validator = new Validator({
                element: emailForm,
                onFormValidated: function(err, results, form) {
                    if (err == false) {
                        $(emailForm).find("[type=submit]").button('loading');
                    }else{
                        $('#error-alert').hide();
                    }
                }
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
                element: '[name="email_code"]',
                required: true,
                rule: "integer fixedLength{len:5} email_code_remote{emailSel:'#form_email'}"        
            });

            validator.addItem({
                element: '[name="email_password"]',
                required: true,
                rule: 'password_alphanumeric',
                display: '密码'
            });

            sendCode();
        };
        
        var mobileValidator = function(){
            var mobileForm = '#password-reset-by-mobile-form';
            validator = new Validator({
                element: mobileForm,
                onFormValidated: function(err, results, form) {
                    if (err == false) {
                        $(mobileForm).find("[type=submit]").button('loading');
                    }else{
                        $('#error-alert').hide();
                    }
                }
            });
                
            validator.addItem({
                element: '[name="mobile"]',
                required: true,
                rule: 'phone remote',
                errormessagePhone : "请输入正确的手机号码"
            });
            
            validator.addItem({
                element: '[name="captcha_num"]',
                required: true,
                rule: 'remote'
            });
           
            validator.addItem({
                element: '[name="sms_code"]',
                required: true,
                rule: "integer fixedLength{len:5} mobile_code_remote{mobileSel:'#mobile'}",
                display: '短信验证码'
            });
            
            validator.addItem({
                element: '[name="password"]',
                required: true,
                rule: 'password_alphanumeric',
                display: '密码' 
            });

            sendCode();
        }
        
        if(window.resetPwdType == "email")
            emailValidator();
        if(window.resetPwdType == "mobile")
            mobileValidator();
        
    };

});