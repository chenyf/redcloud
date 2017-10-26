define(function (require, exports) {

	var Notify = require('common/bootstrap-notify');
	require('jquery.sortable');

	exports.run = function () {
		var list = $('#module-item-list').sortable({
			distance : 20,
			onDrop : function (item, container, _super) {
				_super(item, container);
				sortList(list);
			},
			isValidTarget : function (item, container) {
				if ( item.data('action') == 'addItem' ) {
					return false;
				} else {
					return true;
				}
			}
		});

		$('.editStatus').click(function () {
			if ( confirm('确定要禁用吗？ ') ) {
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

	$('.install-config').click(function(){
		$(this).addClass('disabled').text('正在更新配置,请稍等...');
		var _this = $(this);
		$.ajax({
			url:_this.data('url'),
			type:'get',
			success:function(data){
				if(data.status == 1){
					Notify.success(data.info);
					setTimeout(function(){
						window.location.reload();
					},500);
					return true;
				}
				Notify.danger(data.info);
				$(this).removeClass('disabled').text('更新配置');
			}
		})
	});

	var sortList = function (list) {
		var data = list.sortable("serialize").get();
		$.post(list.data('sortUrl'), {sortIds : data}, function (response) {
			if ( response.status == 1 ) {
				Notify.success(response.info);
				setTimeout(function () {
					window.location.reload();
				}, 500);
				return true;
			}
		});
	}
});

