define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
	
        $("#focus_wx_btn").click(function(){
            $html = $("#focus_wx_div").html();
            $("#modal").html($html);
            $("#modal").modal('show');
        });
        
        $("#rewards_btn").click(function(){
            $htmlObj = $("#data_param");
            $.get($htmlObj.data('canrewardurl'),{uid:$htmlObj.data('uid')},function(dataObj){
                if (dataObj.code == 1000) {
                    $("#modal").html(dataObj.tpl);
                    $("#modal").modal('show');
                    
                    $("#accept-prize-btn").on('click',function(){
                        var mobile = $("#mobile").val();
                        var $btn = $("#accept-prize-btn");

                        if (mobile == '') {
                            $("#mobile").focus();
                            return false;
                        }

                        var mobileReg = /^1[3|4|5|7|8]\d{9}$/;
                        if (!mobileReg.test(mobile)) {
                            $("#mobile").select();
                            Notify.danger('请输入正确的手机号码',2);
                            return false;
                        }
                        $btn.button('submiting').addClass('disabled');
                        $.post($btn.data("suburl"),{uid:$htmlObj.data('uid'),mobile:mobile},function(dataObj){
                            if (dataObj.code == 1000) {
                                $("#modal").modal('hide');
                                Notify.success(dataObj.msg,2);
                            } else {
                                $btn.button('reset').removeClass('disabled');
                                Notify.danger(dataObj.msg,2);
                            }

                        },'json');
                    });
                    
                } else {
                    Notify.danger(dataObj.msg,2);
                }
            },'json');
        });
        
        $("#sign_btn").click(function(){
            Notify.danger('签到任务请在移动端完成',2);
        });
        
    };
  
});