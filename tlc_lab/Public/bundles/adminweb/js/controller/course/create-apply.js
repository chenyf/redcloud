define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');

	exports.run = function () {

		var $form = $('#apply-form');
		var $modal = $form.parents('.modal');

		$('#save-btn').click(function () {
			$.post($form.attr('action'), $form.serialize(), function (response) {
				$modal.modal('hide');
				Notify.success('申请成功!');
				window.location.reload();
			}, 'json').error(function () {
				Notify.danger('申请失败!');
			});
		});


	}

});