/**
 * 聊天室高度自适应
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	exports.run = function(){
		var s=$(window).height();
		$('#chatRoom').css({height:s});
		$(window).resize(function() {
			var s=$(window).height();
			$('#chatRoom').css({height:s});
		});
	}

});