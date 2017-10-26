
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
	require('bootstrap.datetimepicker');

	exports.run = function() {

		$('#version-upTime').datetimepicker({
			locale : 'zh_cn',
			format : 'YYYY-MM-DD HH:mm:ss'
		});

		var $form = $('#version-form');
		var $modal = $form.parents('.modal');

		var validator = new Validator({
			element: $form,
			autoSubmit: false,
			onFormValidated: function(error, results, $form) {
				if (error) {
					return ;
				}
				$('#version-save-btn').button('submiting').addClass('disabled');
				$.post($form.attr('action'), $form.serialize(), function(req){
                                        var data = JSON.parse(req);
					if(data.status == true){
                                            Notify.success(data.message);
                                            $modal.modal('hide');
                                            setTimeout('window.location.reload()' , 1000 );
                                                
					}else{
                                            Notify.danger(data.message)
					}
                                        

				});

			}

		});

		validator.addItem({
			element: '[name="version"]',
			required: true
		});
                
                validator.addItem({
			element: '[name="depict"]',
			required: true
		});
                
                
                


	};
});