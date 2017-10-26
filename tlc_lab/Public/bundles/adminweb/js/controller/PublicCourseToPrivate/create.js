
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

	exports.run = function() {

		var validator = new Validator({
			element: '#pub-form',
			autoSubmit: false
		});

		validator.addItem({
			element: '[name=courseId]',
			required: true,
			errormessageRequired: '请选择课程'
		});


		validator.on('formValidated', function(error, msg, $form) {
			if (error) {
				return;
			}
			$('#save-btn').button('submiting').addClass('disabled');
			$.ajax({
				url: $form.attr('action'),
				type:'POST',
				data:$form.serialize(),
				success:function(data){
					$('#save-btn').removeClass('disabled');
					if(data.status > 0){
						$('#modal').modal('hide');
						Notify.success(data.info);
						window.location.reload();
						return true;
					}
					Notify.danger(data.info);
				}
			});
		});


	};
});