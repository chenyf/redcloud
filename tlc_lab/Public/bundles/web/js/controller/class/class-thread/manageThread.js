define(function(require, exports, module) {
	var Notify = require('common/bootstrap-notify');

	exports.run = function(manageThread,mediaItem){
		//加精、置顶、删除
		if ($(manageThread).length > 0) {
			$(mediaItem).on('click', '.closeThread,.elite,.stick', function() {
				var $trigger = $(this);
				if (!confirm($trigger.data('confirmMessage') + '？')) {
					return false;
				}
				$.post($trigger.data('url'), function() {
					Notify.success('操作成功!');
					setTimeout(function(){
						window.location.reload();
					},500)
				});
			})

		}
	}

});