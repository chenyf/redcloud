define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
	require('common/validator-rules').inject(Validator);

	if ($('#sms-form').length>0){	

		$('[name="sms_enabled"]').click(function(){
			var status = $('[name="sms_enabled"]:checked').val();
			if (status == 0){
				$('.js-usage').hide();
			}else{
				$('.js-usage').show();
			}
		});
		
	}
        
        /**
         * 切换网站
         * @author fubaosheng 2015-07-13
         */
        $("#siteSelect").on('change',function(){
            var code = $(this).val();
            if(!code){
                Notify.danger('请选择一个学校！');
                return false;
            }
            location.search = "?siteSelect="+code;
        });
});