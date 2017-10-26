define(function (require, exports, module) {

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

	exports.run = function () {

		var $form = $('#role-form');
		var $modal = $form.parents('.modal');

		var validator = new Validator({
			element : $form,
			autoSubmit : false,
			onFormValidated : function (error, results, $form) {
				if ( error ) {
					return;
				}
				$('#role-save-btn').button('submiting').addClass('disabled');
				$.post($form.attr('action'), $form.serialize(), function (data) {
					if ( data.status <= 0 ) {
						$('#role-save-btn').text('保存').button(true).removeClass('disabled');
						Notify.danger(data.info);
					} else {
						$modal.modal('hide');
						Notify.success('分配角色成功！');
						window.location.reload();
					}
				});

			}

		});

	};

});