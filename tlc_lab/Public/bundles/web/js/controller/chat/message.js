/**
 * 消息收发
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	var webIm = require('./sdk/webim.js');
	var tools = require('./tools');
	var users = require('./user-manage');

	var addEmote = "#add-emote";//表情弹出按钮

	//拉取聊天室漫游消息
	exports.syncMsgCallbackOK = function () {
		if ( webIm.MsgStore.sessCount() > 0 ) {
			var sessMap = webIm.MsgStore.sessMap();
			for (var i in sessMap) {
				var sess = sessMap[i];
				if ( window.chatRoomId == sess.id() ) {//处于当前聊天室
					var msgCount = sess.msgCount();
					for (var mj = 0; mj < msgCount; mj++) {
						var msg = sess.msg(mj);
						addMsg(msg);
					}
				}
			}
		}
	};

	//监听新消息
	exports.onMsgNotify = function (newMsg) {
		for (var i in newMsg) {
			if(typeof newMsg[i].groupInfo != "object"){
				$.log('无效的消息');
				return;
			}
			if ( newMsg[i].groupInfo.GroupId == window.chatRoomId ) {//处于当前聊天室
				addMsg(newMsg[i]);
			}
		}
	};

	//监听聊天室系统消息
	exports.onGroupSystemNotifys = function(newMsg){
		console.log(newMsg);
	};


	//发送消息
	exports.onSendMsg = function () {
		if ( !chatRoomId ) {
			tools.systemNotify('您还没有加入聊天室');
			$(sendMsgText).val('');
			return false;
		}
		//获取消息内容
		var msgtosend = $.trim($(sendMsgText).val());
		var MaxMsgLen = 500;//消息最大长度限制
		var messageAlert = ".message-alert";

		//消息不能为空
		if ( msgtosend.length < 1 ) {
			$(messageAlert).text('发送的消息不能为空!');
			$(sendMsgText).val('');
			return false;
		}

		//消息长度限制
		if (  msgtosend.length > MaxMsgLen ) {
			var errInfo = "消息长度超出限制(最多" + MaxMsgLen + "个字符)";
			$(messageAlert).text(errInfo);
			$(sendMsgText).val('');
			return false;
		}

		$(messageAlert).text('');
		$(sendMsgText).val('');

		//解析文本和表情
		var msg = new webIm.Msg(currentSession, true);
		msg.fromAccount = loginInfo.identifier;
		var expr = /\[[^[\]]{1,3}\]/mg;
		var emotions = msgtosend.match(expr);
		if (!emotions || emotions.length < 1) {
			var text_obj = new webIm.Msg.Elem.Text(msgtosend);
			msg.addText(text_obj);
		} else {
			for (var i = 0; i < emotions.length; i++) {
				var tmsg = msgtosend.substring(0, msgtosend.indexOf(emotions[i]));
				if (tmsg) {
					var text_obj = new webIm.Msg.Elem.Text(tmsg);
					msg.addText(text_obj);
				}
				var emotion = webIm.EmotionPicData[emotions[i]];
				if (emotion) {
					var face_obj = new webIm.Msg.Elem.Face(webIm.EmotionPicDataIndex[emotions[i]], emotions[i]);
					msg.addFace(face_obj);
				} else {
					var text_obj = new webIm.Msg.Elem.Text(emotions[i]);
					msg.addText(text_obj);
				}
				var restMsgIndex = msgtosend.indexOf(emotions[i]) + emotions[i].length;
				msgtosend = msgtosend.substring(restMsgIndex);
			}
			if (msgtosend) {
				var text_obj = new webIm.Msg.Elem.Text(msgtosend);
				msg.addText(text_obj);
			}
		}

		//发送消息
		webIm.sendMsg(msg, function (resp) {
			$(sendMsgText).val('');
		}, function (err) {
			tools.systemNotify(err.ErrorCode);
			$(sendMsgText).val('');
		});
	};


	//聊天页面增加一条消息
	function addMsg(msg) {
		var nickname = msg.groupInfo.From_AccountNick;
		var time = tools.formatTimeStamp(msg.time);
		var msgBody = msg.toHtml();
		msgBody = msgBody.replace(/^<pre>/,"").replace(/<\/pre>$/,"");
		$(chatMsgList).append(
			'<li class="chat-msg-item">' +
			'<div class="chat-msg-con">' +
			'<span class="msg-name-time text-muted" >' + nickname + '<em class="text-muted text-normal mlm">'+time+'</em></span>' +
			'<p class="msg-text">' + msgBody + '</p>' +
			'</div></li>'
		);
		tools.scrollToBottom()
	}

	var emoteBox = '.emote_faces_box';
	var emotionFlag = false;//是否打开过表情选择框
	$(addEmote).click(function(){
		$(emoteBox).toggle("fast",function(){
			if (emotionFlag) {
				return;
			}
			var emotionPicData= webIm.EmotionPicData;
			for (var key in emotionPicData) {
				var emotions = $('<img>').attr({
					"id": key,
					"src": emotionPicData[key],
					"style": "cursor:pointer;"
				}).click(function () {
					selectEmotionImg(this);
				});
				$('<li>').append(emotions).appendTo($('#emotionUL'));
			}
			emotionFlag = true;
		})
	});


	$('#close-emote').click(function(){
		$(emoteBox).hide();
	});

	function selectEmotionImg(ele){
		$(window.sendMsgText).val($(window.sendMsgText).val()+ele.id);
		$(window.sendMsgText).focus();
		$(emoteBox).hide();
	}

});