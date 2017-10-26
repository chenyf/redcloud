/**
 * 用户管理
 * @author : wanglei@redcloud.com
 */
define(function (require, exports, module) {

	var tools = require('./tools');
	var webIm = require('./sdk/webim.js');

  exports.role_icon = {
		'Owner':'<div class="icon-identity icon-teacher pull-left" >师</div>',
		'Admin':'<div class="icon-identity icon-teacher pull-left" >助</div>',
		'Member':'<div class="icon-identity icon-student pull-left" >生</div>',
	};

	function getMemberLists($callback) {
		$.ajax({
			url : window.getMemberUrl,
			success : function (response) {
				var memberLists = {};
				for (var i = 0; i < response.lists.length; i++) {
					var tmp = response.lists[i].split('#');
					memberLists[tmp[0]] = {};
					memberLists[tmp[0]]['nickname'] = tmp[1];
				}
				var options = {
					'GroupId' : window.chatRoomId,
					'Offset' : 0, //必须从 0 开始
					'Limit' : 1000,
					'MemberInfoFilter' : [
						'Account',
						'Role',
						'JoinTime',
						'LastSendMsgTime',
						'ShutUpUntil'
					]
				};
				webIm.getGroupMemberInfo(
					options,
					function (resp) {
						for (var i = 0; i < resp.MemberList.length; i++) {
							var currentMember = resp.MemberList[i];
							var id = currentMember.Member_Account;
							if ( typeof memberLists[id] == "object" ) {
								memberLists[id]['account'] = id;
								memberLists[id]['role'] = currentMember.Role;
								memberLists[id]['LastSendMsgTime'] = currentMember.LastSendMsgTime;
								memberLists[id]['ShutUpUntil'] = currentMember.ShutUpUntil;
							}
						}
						$callback(memberLists);
					},
					function (err) {
						//tools.systemNotify(err.ErrorInfo);
						tools.systemNotify("加载聊天室成员失败！");
					}
				);
			}
		})
	}


	var banHtml = '<span class="pull-right text-normal dropdown-toggle ban-btn" >禁言</span>';
	banHtml += '<ul class="dropdown-menu pull-right text-left plm ban-panel">'+
		'<li class="forbid" data-time="10"><a href="javascript:void(0)"><i class="glyphicon glyphicon-chevron-right mrs"></i>禁言10分钟</a></li>'+
		'<li class="forbid" data-time="1440"><a href="javascript:void(0)" ><i class="glyphicon glyphicon-chevron-right mrs"></i>禁言1天</a></li>'+
		'<li class="forbid" data-time="10080"><a href="javascript:void(0)" ><i class="glyphicon glyphicon-chevron-right mrs"></i>禁言1周</a></li>'+
		'<li class="forbid" data-time="10080000"><a href="javascript:void(0)" ><i class="glyphicon glyphicon-chevron-right mrs"></i>永久禁言</a></li></ul>';
	var removeBanHtml =  "<span class='pull-right text-normal remove-ban-btn'  > 解禁</span>" +
		'<div class="icon-shutup pull-right"><i class="glyphicon glyphicon-ban-circle"></i></div>';

	//心跳检测+刷新用户面板
	function RefreshMemberPanel() {
		getMemberLists(function (memberLists) {
			var memberNum = 0;
			var html="<ul>";
			for (var i in  memberLists) {
				var currentMember = memberLists[i];
				html += "<li class='member-item'>";
				html += '<div class="member-info-content">'+exports.role_icon[currentMember.role]+'<a class="member-name" href="javascript:void(0)" data-account='+i+'>'+currentMember.nickname+'</a>';
				if(currentMember.role != window.userRole &&  $.inArray(window.userRole.toLocaleLowerCase() ,['admin','owner']) != -1){
					var shutup_until_time = new Date(currentMember.ShutUpUntil).getTime();
					var now_time = new Date().getTime();
					var shutup_time = shutup_until_time*1000 - now_time;
					if (shutup_time > 0){
						html += removeBanHtml
					}else{
						html += banHtml;
					}
				}
				html += "</div></li>";
				++memberNum;
			}
			html += "<ul>";
			$(".member_list").html(html);
			$("#memberNum").text(memberNum);
		});
		setTimeout(RefreshMemberPanel,1000 * 60);
	}

	RefreshMemberPanel();

	/**
	 * (显示/隐藏)成员列表面板
	 */
	$('#toggleMemberList').click(function(){
		$('.member_list').slideToggle("fast",function(e){
			if($(".member_list").is(":hidden")){
				$('.chat-member-show-icon').addClass('glyphicon-chevron-down').removeClass('glyphicon-chevron-up')
			}else{
				$('.chat-member-show-icon').addClass('glyphicon-chevron-up').removeClass('glyphicon-chevron-down')
			}
		});
	});

	/**
	 * (显示/隐藏)禁言面板
	 */
	(function(){
		$(document).on('mouseleave',".member-item",function(){
			$(this).find('.ban-panel').hide();
		});
		$(document).on('click',".ban-btn",function(){
			$(this).next('.ban-panel').toggle();
		});
		$(document).on('mouseleave',".ban-panel",function(){
			$(this).hide();
		});
	})();


	/**
	 * 禁止发言
	 */
	$(document).on('click','.forbid',function(){
		var time = $(this).data('time');
		var member = $(this).parents('.member-item').find('.member-name').text();
		var account = $(this).parents('.member-item').find('.member-name').data('account');
		var val = $(this).text();
		var message = '确认将'+member+' "'+val+'" 吗? 禁言后该成员将不能在聊天室发言！';
		var _this = $(this);
		if(confirm(message)){
			var options = {
				'GroupId': window.chatRoomId,
				'Members_Account': [account.toString()],
				'ShutUpTime': parseInt(time)*60
			};
			webIm.forbidSendMsg(
				options,
				function (resp) {
					tools.systemNotify('设置成员禁言时间成功');
					_this.parents('.member-info-content').append(removeBanHtml);
					_this.parents('.member-info-content').find('.ban-btn,.ban-panel').remove();
				},
				function (err) {
					tools.systemNotify(err.ErrorInfo);
				}
			);
		}
	})

	/**
	 * 解禁发言
	 */
	$(document).on('click','.remove-ban-btn',function(){
		var member = $(this).parents('.member-item').find('.member-name').text();
		var account = $(this).parents('.member-item').find('.member-name').data('account');
		var message = '确认将'+member+'解禁吗? 解禁后该成员可以继续在聊天室发言！';
		var _this = $(this);
		if(confirm(message)){
			var options = {
				'GroupId': window.chatRoomId,
				'Members_Account': [account.toString()],
				'ShutUpTime': 1
			};
			webIm.forbidSendMsg(
				options,
				function (resp) {
					tools.systemNotify('解除成员禁言成功');
					_this.parents('.member-info-content').append(banHtml);
					_this.parents('.member-info-content').find('.icon-shutup').remove();
					_this.remove();
				},
				function (err) {
					tools.systemNotify(err.ErrorInfo);
				}
			);
		}
	})
});