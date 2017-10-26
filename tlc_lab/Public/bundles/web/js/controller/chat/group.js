/**
 * 聊天室
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	var webIm = require('./sdk/webim.js');
	var tools = require('./tools');


	//读取聊天室基本资料
	exports.getGroupInfo = function (group_id, cbOK, cbErr) {
		var   options = {
			'GroupIdList' : [
				group_id
			],
			'GroupBaseInfoFilter' : [
				'Type',
				'Name',
				'Introduction',
				'Notification',
				'FaceUrl',
				'CreateTime',
				'Owner_Account',
				'LastInfoTime',
				'LastMsgTime',
				'NextMsgSeq',
				'MemberNum',
				'MaxMemberNum',
				'ApplyJoinOption'
			],
			'MemberInfoFilter' : [
				'Account',
				'Role',
				'JoinTime',
				'LastSendMsgTime',
				'ShutUpUntil'
			]
		};
		webIm.getGroupInfo(
			options,
			function (resp) {
				if ( cbOK ) {
					cbOK(resp);
				}
			},
			function (err) {
				tools.systemNotify("获取聊天室信息失败");
			}
		);
	};

});