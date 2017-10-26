define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');

	exports.run = function () {

		var $form = $('#apply-form');
		var $modal = $form.parents('.modal');

		$('#apply-save-btn').click(function () {
			$.post($form.attr('action'), $form.serialize(), function (response) {
				$modal.modal('hide');
                                if(response.status == 1)
                                    Notify.success('审核操作成功!');
                                else
                                    Notify.danger(response.info);
				window.location.reload();
			}, 'json').error(function () {
				Notify.danger('审核操作失败!');
			});
		});

		$('select[name=status]').change(function(){
			console.log($(this).val());
			if($(this).val() == 1){
				$('div.failType').removeClass('hidden');
			}else{
				$('div.failType').addClass('hidden');
			}
		})

	}

});