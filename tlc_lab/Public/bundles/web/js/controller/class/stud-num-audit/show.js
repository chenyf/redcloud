define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');


	exports.run = function () {

		$('.sub-btn').click(function(){
			var url = $(this).data('url');
			$(this).button('submiting').addClass('disabled');
			var _this = $(this);
			$.ajax({
				url:url,
				success:function(response){
					if(response.status == 1){
						Notify.success('操作成功');
						setTimeout(function(){
							$('.modal').modal('hide');
							window.location.reload();
						},800)
					}else{
						Notify.error('操作失败')
						_this.removeClass('disabled').text('提交申请');
					}
				},error:function(){
					Notify.danger("操作失败:学号已被使用!");
				}
			})
		})

	}
});