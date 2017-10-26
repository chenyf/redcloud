
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
	require('bootstrap.datetimepicker');

	exports.run = function() {

		$('#datetimepicker').datetimepicker({
			locale : 'zh_cn',
			format : 'YYYY-MM-DD HH:mm:ss'
		});

		var $form = $('#school-form');
		var $modal = $form.parents('.modal');

		var validator = new Validator({
			element: $form,
			autoSubmit: false,
			onFormValidated: function(error, results, $form) {
				if (error) {
					return ;
				}
				$('#school-save-btn').button('submiting').addClass('disabled');
				$.post($form.attr('action'), $form.serialize(), function(data){
					if(data.status >= 1){
						$modal.modal('hide');
						Notify.success(data.info);
						window.location.reload();
					}else{
						Notify.danger(data.info)
					}

				});

			}

		});

		validator.addItem({
			element: '[name="name"]',
			required: true
		});


	};
});