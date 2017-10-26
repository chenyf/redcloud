define(function(require, exports, module) {
    var binded = true;
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $modal = $('#user-create-form').parents('.modal');
        
        var validator = new Validator({
            element: '#user-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                var oVal = $("#email").val();
                if(!checkEmail(oVal) && !checkMobile(oVal)){
                    $("#email").next().show();
                    return false;
                }
                
                if (error) {
                    return false;
                }
                
                $('#user-create-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(html) {
                    if(html==1){
                        Notify.danger('新用户添加失败');return false;
                    }
                    $modal.modal('hide');
                    Notify.success('新用户添加成功');
                    window.location.reload();
                }).error(function(){
                    Notify.danger('新用户添加失败');
                });

            }
        });
        
//        validator.addItem({
//            element: '[name="email"]',
//            required: true,
//            rule: 'email email_remote'
//        });
        
        $("#email").blur(function(){
            var oVal  = $(this).val();
            if(oVal.indexOf("@")==-1){
                //手机
                var res = checkMobile(oVal);
                if(res){
                    var url = $(this).attr("data-mobile-url");
                    isBind(url, oVal);
                    if(binded){
                        $("#isbind").show();
                    }else{
                        $(this).next().hide();
                    }
                }else{
                    $(this).next().show();
                }
            }else{
                //email
               var res = checkEmail(oVal);
                if(res){
                    var url = $(this).attr("data-url");
                    isBind(url, oVal);
                    if(binded){
                        $("#isbind").show();
                    }else{
                        $(this).next().hide();
                    }
                }else{
                    $(this).next().show();
                }
                
            }
        })
        
        $("#email").focus(function(){
            $(this).next().hide();
            $("#isbind").hide();
        })
        
        validator.addItem({
            element: '[name="nickname"]',
            required: true,
            rule: 'chinese_alphanumeric minlength{min:2} maxlength{max:20}'
        });

        validator.addItem({
            element: '[name="password"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:20}'
        });

        validator.addItem({
            element: '[name="confirmPassword"]',
            required: true,
            rule: 'confirmation{target:#password}'
        });
    };
    
    function  checkMobile(str) {
        var  re = /^1\d{10}$/
        if(re.test(str)){
            return true;
        }
        return false;
    }
    
    
    function checkEmail(str){
        var re = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/
        if(re.test(str)){
            return true;
        }
        return false;
    }
    
    function isBind(url,value){
        $.ajax({
            type: "GET",
            url: url,
            data: {value:value},
            dataType: "json",
            async: false,
            success: function(response){
                if(response.success){
                    binded = false;
                }else{
                    binded = true;
                }   
            }
        });
    }

});