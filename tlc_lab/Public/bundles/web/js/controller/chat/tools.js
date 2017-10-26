/**
 * 常用工具
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	var systemMessage = {
		10017 : "你已被禁言，暂不能发送消息！"
	};

	/**
	 * 获取字符串的hash值
	 * @param str
	 * @param caseSensitive 是否忽略大小写，默认不忽略
	 * @returns {number}
	 */
	exports.getHashCode = function (str, caseSensitive) {
		if ( !caseSensitive ) {
			str = str.toLowerCase();
		}
		var hash = 1315423911, i, ch;
		for (i = str.length - 1; i >= 0; i--) {
			ch = str.charCodeAt(i);
			hash ^= ((hash << 5) + ch + (hash >> 2));
		}
		return (hash & 0x7FFFFFFF);
	};

	//聊天室系统提示
	exports.systemNotify = function systemNotify($message) {
		if(typeof  systemMessage[$message] == "string"){
			$message = systemMessage[$message];
		}
		if ( $message.length > 0 ) {
			$(chatMsgList).append(
				'<li class="chat-msg-item">' +
				'<span class="system-alert"> 系统提示：' + $message + '</span>' +
				'</li>'
			);
			exports.scrollToBottom();
		}
	};

	//滚动条到底部
	exports.scrollToBottom = function () {
		var height = $('.chat-main')[0].offsetHeight;
		//for firefox
		$('html').scrollTop(height);
		//for chrome ie
		$('body').scrollTop(height);
	};

	//格式化时间戳
	exports.formatTimeStamp = function (time, format) {
		if ( time == 0 ) {
			return time;
		}
		format = format || 'MM-dd hh:mm:ss';
		var now_time = parseInt(new Date().getTime() / 1000); //获取当前时间的秒数
		var ct = now_time - time;
		if ( ct < 86400 ) {
			format = 'hh:mm:ss';
		}
		var date = new Date(time * 1000);
		var formatTime = format;
		var o = {
			"M+" : date.getMonth() + 1, //月份
			"d+" : date.getDate(), //日
			"h+" : date.getHours(), //小时
			"m+" : date.getMinutes(), //分
			"s+" : date.getSeconds() //秒
		};
		for (var k in o) {
			if ( new RegExp("(" + k + ")").test(formatTime) )
				formatTime = formatTime.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
		}
		return formatTime;
	};

	$.extend({
		log : function (message) {
			console.log("%c" + message, 'color:#006603');
		}
	})
});