define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var SmsSender = require('../widget/sms-sender');
    require('common/validator-rules').inject(Validator);
    require("jquery.bootstrap-datetimepicker");
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
       
        var validator = new Validator({
            element: '#quickly-login-form',
            onFormValidated: function(err, results, form) {
                 if (err != false) $('#alertxx').hide();
            }
        });
        
        validator.addItem({
           element: '[name="phone"]',
           required: true,
           rule: 'phone remote'
        });
        
        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: "fixedLength{len:6} mobile_code_remote{mobileSel:'#phone'}"        
        });
        
        $('body').click(function(){
            $('.js-sms-send').parents(".form-group").removeClass("form-position");
            $('.js-sms-send').parents(".form-group").find('.yzm-layer').hide();
            $("[name='captcha_err']").html('');
        })
        $('.js-sms-send,.yzm-layer').click(function(){
            return false;
        });
        $("#getcode_num").click(function(){
            $(this).attr("src",$("#getcode_num").data("url")+ "?" + Math.random());
        });
        
        var smsCode = function(){
            $("[name='btn-code']").click(function(){
                var captcha = $("[name='captcha_num']").val();
                if(!captcha){
                    $("[name='captcha_err']").html('请输入验证码');
                    return false;
                }
                var reg = /^[a-zA-Z0-9]+$/i;
                if(!reg.exec(captcha)){
                    $("[name='captcha_err']").html('验证码必须是英文字母和数字组成');
                    return false;
                }
                var url = $("[name='captcha_num']").data('url');
                $.get(url, {value:captcha}, function(response) {
                    if(response.success == false){
                        $("[name='captcha_err']").html(response.message);
                        return false;
                    }else{
                        $("[name='captcha_err']").html('');
                        var isPreSmsSend = 0;
                        smsSender.smsSend(isPreSmsSend);
                    }
                }, 'json');
            })
            return false;
        }
        
        var smsSender;
        var ele = '.js-sms-send';
        smsSender = new SmsSender({
            element : ele,
            url : $(ele).data('url'),
            second : 60,
            type : 'phone',
            preSmsSend : function(){
                var couldSender = true;
                var phone = true;
                var code = true;
                validator.query('[name="phone"]').execute(function(error, results, element) {
                    if (error) {
                        phone = false;
                        return;
                    }
                    phone = true;
                    return;
                });
                if(phone){
                    $(ele).parents(".form-group").find('#captcha_num').val("");
                    $("#getcode_num").trigger('click');
                    $("[name='btn-code']").unbind('click');
                    $(ele).parents(".form-group").addClass("form-position");
                    $(ele).parents(".form-group").find('.yzm-layer').show();
                    code = false;
                    code = smsCode();
                }
                couldSender = phone && code;
                return couldSender;
            }
        });
        
    };

});