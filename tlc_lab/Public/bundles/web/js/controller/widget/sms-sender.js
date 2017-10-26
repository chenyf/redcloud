define(function(require, exports, module) {

    var Widget = require('widget');
    var Notify = require('common/bootstrap-notify');

    var SmsSender = Widget.extend({
        attrs: {
            validator: 0,
            url:'',
            smsType:'',
            getPostData: function(data){
                return data;
            },
            preSmsSend: function(){
                return true;
            }
        },
        events: {
            "click" : "smsSend"
        },
        setup: function() {

        },
        postData: function(url, data) {
                var type = this.get('type') ? this.get('type') :  'mobile';
                var time ;
                var btn ;
                var str ;
                if('email' == type){
                    time = '#js-time-left-email'; 
                    btn = '#js-fetch-btn-text-email';
                    str = '获取邮件验证码';
                } 
                if('mobile' == type){
                    time = '#js-time-left';
                    btn = '#js-fetch-btn-text';
                    str = '获取短信验证码';
                }
                if('phone' == type){
                    time = '#js-time-left';
                    btn = '#js-fetch-btn-text';
                    str = '获取动态密码';
                }
	        var refreshTimeLeft = function(){
	        	var leftTime = $(time).html();
	        	$(time).html(leftTime-1);
	        	if (leftTime-1 > 0) {
                            setTimeout(refreshTimeLeft, 1000);
	        	} else {
                            $(time).html('');
                            $(btn).html(str);
	        	}
	        };

        	$.post(url,data,function(response){
        		if (("undefined" != typeof response['ACK'])&&(response['ACK']=='ok')) {
                                var second = parseInt(data.second);
                                if(second>0){
                                    $(time).html(second);
                                    $(btn).html('秒后重新获取');
                                }
                                if($('.yzm-layer').size()){
                                    $('.yzm-layer').hide(); 
                                }
                                Notify.success(response['success']);
	        		refreshTimeLeft();
	        	}else{
	        		if ("undefined" != typeof response['error']){
	        			Notify.danger(response['error']);
	        		}else{
	        			Notify.danger('发送失败，请联系管理员');
	        		}
	        	}
        	},'json');
        	return this;
        },
        smsSend: function(isPreSmsSend){
                var isPreSmsSend = typeof isPreSmsSend!='undefined' ? isPreSmsSend : 1;
                var type = this.get('type') ? this.get('type') :  'mobile';
                var time ;
                if('email' == type) time = '#js-time-left-email';
                if('mobile' == type) time = '#js-time-left';
                if('phone' == type) time = '#js-time-left';
    		var leftTime = $(time).html();
    		if (leftTime.length > 0){
                    return false;
    		}

                var url = this.get("url");
                var data = {};
	        data.to = $('[name="'+type+'"]').val();
	        data.sms_type = this.get("smsType");
                data.second = this.get("second") ? this.get("second") : 0;
                data.code = $("[name='captcha_num']").val();
                data.code = data.code ? data.code : '';
                
                data = $.extend(data, this.get("getPostData")(data));

                if(!isPreSmsSend || this.get("preSmsSend")()) {
                    this.postData(url, data);
                }
    		
                return this;
        }

    });

    module.exports = SmsSender;
});