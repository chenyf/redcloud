define(function(require, exports, module) {

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

	exports.run = function() {
		var $form = $('#version-form');
		var $modal = $form.parents('.modal');
		var $table = $('#version-table');

		var validator = new Validator({
			element: $form,
			autoSubmit: false,
			onFormValidated: function(error, results, $form) {
				if (error) {
					return ;
				}
				$('#version-save-btn').button('submiting').addClass('disabled');
				$.post($form.attr('action'), $form.serialize(), function(html){
					$modal.modal('hide');
					Notify.success('保存版本成功！');
					window.location.reload();
				});

			}

		});

		validator.addItem({
			element: '[name="name"]',
			required: true
		});

		validator.addItem({
			element: '[name="url"]',
			required: true

		});

		validator.addItem({
			element: '[name="type"]',
			required: true

		});

	};

});