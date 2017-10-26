define(function (require, exports, module) {
	var Validator = require('bootstrap.validator');
	require('common/validator-rules').inject(Validator);
	require('ckeditor');
	var Notify = require('common/bootstrap-notify');

	exports.run = function () {

		CKEDITOR.replace('desc', {
			toolbar: 'Simple',
			filebrowserImageUploadUrl: $('#class-about-field').data('imageUploadUrl')
		});

		var validator = new Validator({
			element : '#class-edit-form',
			failSilently : true,
			onFormValidated : function (error) {
				if ( error ) {
					return false;
				}
				$('#class-save-btn').button('submiting').addClass('disabled');
			}
		});

		validator.addItem({
			element : '[name="title"]',
			required : true,
			rule : 'minlength{min:2} maxlength{max:100} visible_character',
			errormessageUrl : '长度为2-100位'
		});

		validator.on('formValidate', function (elemetn, event) {

		});

	}

});