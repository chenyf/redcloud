/**
 * 聊天室初始化
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	var webIm = require('./sdk/webim.js');
	var tools = require('./tools');
	var message = require('./message');
	var users = require('./user-manage');
	var group = require('./group');

	window.currentSession = null;//当前聊天会话
	window.currentSessionType = "GROUP";//当前会话类型
	window.MaxMsgLen = 8898; //消息最大长度（字节）
	window.chatMsgList = ".chat-msg-list"; //聊天列表
	window.sendMsgText = "#sendMsgText";//发送消息文本框
	window.sendBtn = "#sendBtn";//发送消息按钮

	//当前用户身份
	window.loginInfo = {
		sdkAppID : window.sdkAppID,
		appIDAt3rd : window.sdkAppID,
		identifier : window.identifier,
		accountType : window.accountType,
		userSig : window.userSig
	};

	//初始化
	function appInit($callback) {
		//如果用户已登录
		if(loginInfo.identifier){
			var listeners = {
				onConnNotify : null,
				onMsgNotify : message.onMsgNotify,
				onGroupInfoChangeNotify:message.onGroupInfoChangeNotify,
				groupSystemNotifys:message.onGroupSystemNotifys,
			};
			webIm.init(loginInfo, listeners, null);
			$.log('初始化成功...');
			window.currentSession = new webIm.Session(currentSessionType,window.chatRoomId, window.chatRoomId, "", Math.round(new Date().getTime() / 1000));
			$callback();
		}
	}

	exports.run = function () {
		appInit(function(){
			//获取聊天室信息
			group.getGroupInfo(window.chatRoomId, function (resp) {
				$.log('获取聊天室信息成功...');
				var opts = {
					'GroupId' : window.chatRoomId,
					'ReqMsgSeq' :resp.GroupInfo[0].NextMsgSeq - 1,
					'ReqMsgNumber' : 100
				};
				if ( opts.ReqMsgSeq == null || opts.ReqMsgSeq == undefined ) {
					tools.systemNotify('群消息序列号非法');
					return;
				}
				//加载聊天室漫游消息
				webIm.syncGroupMsgs(opts, message.syncMsgCallbackOK);
				$.log('加载聊天室漫游消息成功...');
				$('.loading').hide()
			});
			bindEvent();
			tools.systemNotify('欢迎加入聊天室！');
		});
	};


	//绑定事件
	function bindEvent(){
		//消息文本框回车事件
		$(sendMsgText).keydown(function (event) {
			if ( event.keyCode == 13 ) {
				message.onSendMsg();
			}
		});
		//发送按钮点击事件
		$(sendBtn).click(function () {
			message.onSendMsg();
		});

	}

});