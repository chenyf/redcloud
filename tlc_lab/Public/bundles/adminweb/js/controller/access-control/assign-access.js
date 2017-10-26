define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	require('jquery.form');
	exports.run = function () {

		var module = $('input.module');
		var controller = $('input.controller');
		var action = $('input.action');

		/**
		 * 模块点击
		 */
		module.click(function () {
			//所有子类checkbox状态跟着变化
			$(this).parents('div.panel-heading')
				.siblings('div.panel-body')
				.find('input[type=checkbox]')
				.prop('checked', $(this).prop('checked'))

		});

		/**
		 * 控制器点击
		 */
		controller.click(function (event) {
			//所有子类checkbox状态跟着变化
			$(this).parents('div.row')
				.siblings('div.row')
				.find('input.action')
				.prop('checked', $(this).prop('checked'));
			//当被选中时,所属module也被选中
			if($(this).prop('checked') ) {
				$(this).parents('div.panel')
					.find('input.module')
					.prop('checked', true)
			}
		});

		/**
		 * 动作点击
		 */
		action.click(function (event) {
			//当被选中时,所属controller与module也被选中
			if ( $(this).prop('checked') ) {
				$(this).parents('div.well')
					.find('input.controller')
					.prop('checked', true);
				$(this).parents('div.panel')
					.find('input.module')
					.prop('checked', true)
			}
		});

		var ajaxForm = $('form');

		ajaxForm.ajaxForm({
			dataType : 'json',
			success : function (data) {
				if ( data.status == 1 ) {
					Notify.success(data.info);
					if ( data.url.length > 0 ) {
						setTimeout(function () {
							window.location.href = data.url;
						}, 1000);
					}
					return true;
				}
				Notify.danger(data.info);
			}
		});
	};

});