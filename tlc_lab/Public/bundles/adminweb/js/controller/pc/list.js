define(function (require, exports) {

	var Notify = require('common/bootstrap-notify');

	exports.run = function() {
		$('[data-toggle="popover"]').popover()
		$('tbody').on('click', '.delete-btn', function() {
			if (!confirm('确认要删除此版本吗？')) return false;
			var $btn = $(this);
			$.post($btn.data('url'), function(response) {
				if (response.status == 'ok') {
					Notify.success('删除成功!');
					setTimeout(function(){
						window.location.reload();
					}, 500);
				} else {
					alert('服务器错误!');
				}
			}, 'json');

		});
	};
});

