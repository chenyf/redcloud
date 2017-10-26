define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("jquery.autocomplete");
    require("jquery.autocomplete-css");
    $.AutoComplete('#login_username');
    
    exports.run = function() {
        var usernameEle = $("#login_username");
        var usernameBox = usernameEle.parents(".form-group");
        var usernameErrorEle = usernameBox.find(".help-block");
        var usernameSign = usernameErrorEle.find("i");
        var usernameErrorDanger = usernameErrorEle.find(".text-danger");

        var passwordEle = $("#login_password");
        var passwordBox = passwordEle.parents(".form-group");
        var passwordErrorEle = passwordBox.find(".help-block");
        var passwordSign = passwordErrorEle.find("i");
        var passwordErrorDanger = passwordErrorEle.find(".text-danger");
        
        var captchaEle = $("#captcha_num");
        var captchaBox = captchaEle.parents(".form-group");
        var captchaErrorEle = captchaBox.find(".help-block");
        var captchaSign = captchaErrorEle.find("i");
        var captchaErrorDanger = captchaErrorEle.find(".text-danger");

        //账号和密码都为空
        var inputEmpty = function(){
            usernameBox.addClass("has-error");
            usernameErrorEle.removeClass("hide");
            usernameSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign").addClass("glyphicon-minus-sign");
            usernameErrorDanger.html("请输入账号");
            passwordBox.addClass("has-error");
            passwordErrorEle.removeClass("hide");
            passwordSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign").addClass("glyphicon-minus-sign");
            passwordErrorDanger.html("请输入密码");
        }
        
        //账号为空、密码不为空
        var accountEmpty = function(errorCode,error){
            if(errorCode == "accountEmpty" )
                var phicons = "glyphicon-minus-sign";
            else
                var phicons = "glyphicon-remove-sign";
            usernameBox.addClass("has-error");
            usernameErrorEle.removeClass("hide");
            usernameSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign").addClass(phicons);
            usernameErrorDanger.html(error);
            passwordBox.removeClass("has-error");
            passwordErrorEle.addClass("hide");
            passwordSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            passwordErrorDanger.html("");
        }
        
        //密码为空、账号不为空
        var passwordEmpty = function(errorCode,error){
            if(errorCode == "passwordEmpty" )
                var phicons = "glyphicon-minus-sign";
            else
                var phicons = "glyphicon-remove-sign";
            usernameBox.removeClass("has-error");
            usernameErrorEle.addClass("hide");
            usernameSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            usernameErrorDanger.html("");
            passwordBox.addClass("has-error");
            passwordErrorEle.removeClass("hide");
            passwordSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign").addClass(phicons);
            passwordErrorDanger.html(error);
        }
        
        //账号和密码都不为空
        var noEmpty = function(){
            usernameBox.removeClass("has-error");
            usernameErrorEle.addClass("hide");
            usernameSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            usernameErrorDanger.html("");
            passwordBox.removeClass("has-error");
            passwordErrorEle.addClass("hide");
            passwordSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            passwordErrorDanger.html("");
        }
        
        //验证码不为空
        var captchaEmpty = function(errorCode,error){
             if(errorCode == "captchaEmpty")
                var phicons = "glyphicon-minus-sign";
            else
                var phicons = "glyphicon-remove-sign";
            captchaBox.addClass("has-error");
            captchaErrorEle.removeClass("hide");
            captchaSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign").addClass(phicons);
            captchaErrorDanger.html(error);
        }
        
        //登录弹出层
        var $modal = $("#login-form").parents('.modal');
        function updateUserHeader(){
            $.ajax({
                url : $('#headerUser').data('url'),
                success : function(data){
                    $('.navbar-right').html(data);
                }
            })
        }
        
        //回车验证
        $("#login-form").on("keydown","#login_username,#login_password,#captcha_num",function(event){
            if(event.keyCode == 13)
                $("#login-btn").trigger("click");
        });

        //点击登录
        $("#login-form").on("click","#login-btn",function(){
            //获取值
            var account = usernameEle.val();
            var password = passwordEle.val();
            var remember = $("#login_remember").is(":checked");
            remember = remember ? 1 : 0 ;
            var trigger = $("#login-form").data("trigger");
            var url =  $("#login-form").data("url");
            var goto = $("#login-form").data("goto");
            var codeErr = 0;
            
            //账号和密码都为空
            if( (!account) && (!password)  ){
                inputEmpty();
                return false;
            }

            //账号为空、密码不为空
            if( (!account) && (password)  ){
                accountEmpty('accountEmpty','请输入账号');
                return false;
            }

            //密码为空、账号不为空
            if( (account) && (!password)  ){
                passwordEmpty('passwordEmpty','请输入密码');
                return false;
            }

            //账号和密码都不为空
            if( account && password  ){
                noEmpty();
                var data = {account:account,password:password,remember:remember};
                
                //验证码
                if(!captchaBox.is(":hidden")){
                    var captchaNum = captchaEle.val();
                    if(!captchaNum){
                        captchaEmpty("captchaEmpty","请输入验证码");
                        return false;
                    }
                    $.ajax({
                        url : captchaEle.data("url"),
                        data : {value:captchaNum},
                        async : false,
                        dataType : "json",
                        success : function(result){
                            if(result.success == false){
                                codeErr = 1;
                                captchaEmpty("codeError","验证码错误");
                                return false;
                            }
                        }
                    });
                    data.captchaNum = captchaNum;
                }
                if(codeErr === 1) return false;
                if($("#login-btn").data('lock') == 1) return false;
                $("#login-btn").data('lock',1);
                $("#login-btn").attr('disabled',true).html("登录中...");
                $.ajax({
                    url : url,
                    type : 'post',
                    data: data,
                    dataType : 'json',
                    success : function(result){
                        $("#login-btn").data('lock',0);
                        if(result.status){
                            if($modal.length){
                                $modal.modal('hide');
                                Notify.success(result.error);
                                updateUserHeader();
                            }else{
                                if(trigger != undefined && trigger != ''){
                                    eval(trigger);
                                }else{
                                    window.location.href = goto;
                                }
                            }
                        }else{
                            $("#login-btn").attr('disabled',false).html("登录");
                            if(result.errorCode == "methodError" || result.errorCode == "accountLock" || result.errorCode == "noAccount"){
                                Notify.danger(result.error);
                            }
                            
                            if(result.errorCode == "empty"){
                                inputEmpty();
                            }
                            if(result.errorCode == "accountEmpty" || result.errorCode == "accountLock"){
                                accountEmpty(result.errorCode,result.error);
                            }
                            if(result.errorCode == "passwordEmpty" || result.errorCode == "inputError"){
                                passwordEmpty(result.errorCode,result.error);
                                if(result.errorNum){
                                    $("#getcode_num").trigger("click");
                                    captchaBox.removeClass("hide");
                                }
                            }
                            if(result.errorCode == "codeError"){
                                captchaEmpty(result.errorCode,result.error);
                            }
                            return false;
                        }
                    }
                });

            }

        });

        //账号和密码、验证码获取焦点
        $("#login-form").on("focus","#login_username",function(){
            usernameBox.removeClass("has-error");
            usernameErrorEle.addClass("hide");
            usernameSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            usernameErrorDanger.html("");
        });

        $("#login-form").on("focus","login_password",function(){
            passwordBox.removeClass("has-error");
            passwordErrorEle.addClass("hide");
            passwordSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            passwordErrorDanger.html("");
        });

        $("#login-form").on("focus","#captcha_num",function(){
            captchaBox.removeClass("has-error");
            captchaErrorEle.addClass("hide");
            captchaSign.removeClass("glyphicon-minus-sign glyphicon-remove-sign");
            captchaErrorDanger.html("");
        });
        
        //验证码

        $("#login-form").on("click",".fa-refresh",function(){
            $("#getcode_num").trigger("click");
        });
        $("#login-form").on("click","#getcode_num",function(){
            $(this).attr("src",$("#getcode_num").data("url")+ "?" + Math.random());
        });
    }
});
