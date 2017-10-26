
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
	require('bootstrap.datetimepicker');

	exports.run = function() {

            $('.btn-group .subOne').on('click', function() {
                
                if (!confirm("确定此操作么")) {
                    return false;
                }
                
                $.post($(this).data('url'), function(data){
                    if(data.status >= 1){
                        Notify.success(data.info);
                        window.location.reload();
                    }else{
                        Notify.danger(data.info);
                    }
                });
            });

	};
});