define(function (require, exports, module) {
	var Validator = require('bootstrap.validator');
	require('jquery.form');
	require('common/validator-rules').inject(Validator);
	var Notify = require('common/bootstrap-notify');

	exports.run = function () {
		var suBtn = "#save-btn";
		var form = "#announcement-form";
		var $modal = $(form).parents('.modal');
		var validator = new Validator({
			element : form,
			autoSubmit: false,
			onFormValidated : function (error) {
				if ( error ) {
					return false;
				}
				$(suBtn).button('submiting').addClass('disabled');
				$.post($(form).attr('action'), $(form).serialize(), function(data){
					$modal.modal('hide');
					Notify.success('保存成功');
					window.location.reload();
				});
			}
		});

		validator.addItem({
			element : '[name="data"]',
			rule : 'minlength{min:0} maxlength{max:150}',
			errormessage: '最多输入150个字符'
		});

		/**
		 * 输入事件监听
		 */
		$('textarea[name=data]').keydown(function(e){
			console.log($(this).val().length);
			var length = $(this).val().split("\n").length;
			if(length >= 5){
				var et=e||window.event;
				var keycode=et.charCode||et.keyCode;
				if(keycode==13) {
					if ( window.event )
						window.event.returnValue = false;
					else
						e.preventDefault();//for firefox
				}
			}
		});
	}

});