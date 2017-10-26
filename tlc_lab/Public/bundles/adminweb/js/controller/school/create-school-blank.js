define(function(require, exports, module){
	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
        var $form = $('#school-form');
	var $modal = $form.parents('.modal');
      
	exports.run = function() {
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

	};
});