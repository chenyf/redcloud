define(function (require, exports) {

	var Notify = require('common/bootstrap-notify');
	require('jquery.sortable');

	exports.run = function () {

		$('.btn-del').click(function () {
			if ( confirm('确定要删除吗？ ') ) {
				var url = $(this).data('url');
				$.get(url, function (response) {
					if ( response.status == 1 ) {
						Notify.success(response.info);
						setTimeout(function () {
							window.location.reload();
						}, 200);
						return true;
					}
				});
			}
		})
	};

});

