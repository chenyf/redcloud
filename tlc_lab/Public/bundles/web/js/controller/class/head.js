define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	exports.run = function () {

		$('#login-modal').on('hidden.bs.modal', function (e) {
			window.location.reload();
		});

		//设置默认班级
		var setDefaultBtn = "#set-default-class";
		$(setDefaultBtn).click(function(){
			var url = $(this).data('url');
			var _this = $(this);
			if (confirm('是否要把当前班级设为默认班?!')) {
				$.ajax({
					url:url,
					success:function(){
						_this.parent('span').text('默认班')
						_this.remove();
						Notify.success('设置成功');
					}
				});
			}
		});

		//退出班级
		var exitBtn = '#exit-btn';
		if ($(exitBtn).length > 0) {
			$(exitBtn).click(function() {
				if (!confirm('真的要退出该班级？您在该班级的信息将删除！')) {
					return false;
				}
			})
		}

		//申请加入班级
		(function (btn) {
			var add_btn_clicked = false;
			$(btn).click(function (event) {
				var _this = $(this);
				var title = _this.text();
				if ( !add_btn_clicked ) {
					$(btn).button('loading').addClass('disabled');
					add_btn_clicked = true;
					$.ajax({
						url : _this.data('url'),
						type : 'post',
						success : function (data) {
							if ( data.status == 'allow' ) {
								Notify.success('加入班级成功');
								setTimeout(function () {
									window.location.href = data.url;
								}, 400);
							} else if ( data.status == 'check' ) {
								Notify.success('申请加入成功，请耐心等待审核...');
								$(btn).text('已申请加入');
							} else if ( data.status == 'nologin' ) {/*by qzw 2015-05-22*/
								if ( $("#login-modal").size() > 0 ) $("#login-modal").modal('show');
								$.get($('#login-modal').data('url'), function (html) {
									$("#login-modal").html(html);
								});
							} else if ( data.status == 'error' ) {
								Notify.danger(data.info);
								$(btn).button(true).text(title).removeClass('disabled');
								add_btn_clicked = false;
							}
						},
						error : function (data) {
							Notify.danger('加入失败 (；′⌒`)');
							$(btn).button(true).text(title).removeClass('disabled');
							add_btn_clicked = false;
						}
					})
				}
				return true;
			});
		})('#add-btn')

	}

});