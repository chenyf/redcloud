define(function (require, exports, module) {

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

	exports.run = function () {

		var $form = $('#access-form');
		var $modal = $form.parents('.modal');

		var validator = new Validator({
			element : $form,
			autoSubmit : false,
			onFormValidated : function (error, results, $form) {
				if ( error ) {
					return;
				}
				$('#access-save-btn').button('submiting').addClass('disabled');
				$.post($form.attr('action'), $form.serialize(), function (data) {
					if ( data.status <= 0 ) {
						$('#access-save-btn').text('保存').button(true).removeClass('disabled');
						Notify.danger(data.info);
					} else {
						$modal.modal('hide');
						Notify.success('保存权限成功！');
						if(data.url){
							window.location.href = data.url;
							return true;
						}
						window.location.reload();
					}
				});

			}

		});

		validator.addItem({
			element : '[name="name"]',
			required : true
		});

		validator.addItem({
			element : '[name="title"]',
			required : true
		});
	};

});