define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	var chatRoomVisitor = "#chatRoomVisitor";

	exports.run = function () {
		require('./header').run();
                require('../../util/collapse')('hide');
		$('.chatRoomStatus').click(function(){
			var url = $(this).data('url');
			var _this = $(this);
			if(_this.data('select') == 1) return false;
			$(this).val() == 1 ? $(chatRoomVisitor).show() : $(chatRoomVisitor).hide();
			$.ajax({
				url:url,
				type:'POST',
				async:false,
				success:function(response){
					$('.chatRoomStatus').data('select',0);
					_this.data('select',1);
					if(response.chatRoomId.length > 0){
						$('#enter-chatRoom').removeAttr('disabled');
					}else{
						$('#enter-chatRoom').attr('disabled',"disabled");
					}
					Notify.success('操作成功');
				},
				error:function(){
					Notify.danger('操作失败');
				}
			});
		})

		$('input[name=chatRoomVisitor]').click(function(){
			var url = $(this).data('url');
			var chatRoomVisitor = $(this).prop("checked") == true ? 1 :0;
			$.ajax({
				url:url,
				type:'POST',
				data:{chatRoomVisitor:chatRoomVisitor},
				success:function(){
					Notify.success('操作成功');
				}
			})
		})

		$('#removeBan').click(function(){
			var url = $(this).data('url');
			var name = $(this).data('name');
			var _this = $(this);
			if(confirm('确认将'+name+"解禁吗？ 解禁后该成员可以继续在聊天室发言！")){
				$.ajax({
					url:url,
					success:function(){
						Notify.success('操作成功');
						window.location.reload();
					},
					error:function(res){
						Notify.danger('操作失败');
						console.log(res);
					}
				})
			}

		})


	};

});