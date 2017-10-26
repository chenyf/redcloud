define(function (require, exports, module) {
	var Validator = require('bootstrap.validator');
	require('common/validator-rules').inject(Validator);
	var Notify = require('common/bootstrap-notify');
	var WidgetAppraise = require('../../appraise/appraise.js');
	require('smooth-scroll');
	require('ckeditor');
	require('function');

	require('./manageThread.js').run('.thread-footer', '.thread-footer');

	function checkUrl(url) {
		var hrefArray;
		hrefArray = url.split('#');
		hrefArray = hrefArray[0].split('?');
		return hrefArray[1];
	}

	exports.run = function () {
		var threadAppraise = new WidgetAppraise({
			'elemSelector' : '.threadApprise',
			success : function (response, ele) {
				var data = JSON.parse(response);
				if ( !data.success ) {
					Notify.danger(data.message);
					return false;
				}
				var goodInfo = data.goodInfo;
				if ( data.type == 'add' ) {
					var zanNum = goodInfo['good'];
					var zan = zanNum + ' 赞';
					ele.find('span').html(zan);
					ele.find('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
				} else {
					var exitNum = goodInfo['good'];
					var exit = exitNum + ' 赞';
					ele.find('span').html(exit);
					ele.find('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
				}
			},
		}).render();
		threadAppraise.init();
		//语音播放
		$(function () {
			var oAudioNum = $("div[class='n-yy-txt']").length;
			if ( oAudioNum > 0 ) {
				playAudio.loadJs();
				$("div[class='voice-box-rt'] , div[class='voice-box-lt']").on("click", function () {
					var audioUrl = $(this).attr("audioUrl");
					var audioId = $(this).attr("audioId");
					playAudio.doPlay(audioUrl, audioId);
				})
			}
		});

		//收藏话题
		$(".thread-footer").on('click', '.uncollect-btn, .collect-btn', function () {
			var $this = $(this);
			$.post($this.data('url'), function () {
				Notify.success('操作成功');
				$this.hide();
				if ( $this.hasClass('collect-btn') ) {
					$this.parent().find('.uncollect-btn').show();
				} else {
					$this.parent().find('.collect-btn').show();
				}
			});
		});

		//删除回复
		$('.group-post-list').on('click', '.post-delete-btn', function () {
			var $trigger = $(this);
			if ( !confirm($trigger.attr('title') + '？') ) {
				return false;
			}
			$.post($trigger.data('url'), function () {
				Notify.success('删除成功');
				setTimeout(function () {
					window.location.reload();
				}, 500);
			});
		});

		//回复话题
		var postThreadForm = '#post-thread-form';
		if ( $(postThreadForm).length > 0 ) {
			var editor = CKEDITOR.replace('post_content', {
				toolbar : 'Group',
				filebrowserImageUploadUrl : $('#post_content').data('imageUploadUrl')
			});

			//滚动到回复框
			$('#scrollPost').click(function () {
				$.smoothScroll({scrollTarget : '#createPost'});
				editor.focus();
			});

			var validator_post_content = new Validator({
				element : '#post-thread-form',
				failSilently : true,
				autoSubmit : false,
				onFormValidated : function (error) {
					if ( error ) {
						return false;
					}
					$('#post-thread-btn').button('submiting').addClass('disabled');

					$.ajax({
						url : $(postThreadForm).attr('post-url'),
						data : $(postThreadForm).serialize(),
						cache : false,
						async : false,
						type : "POST",
						success : function (url) {
							if ( url ) {
								if ( url.status == 'error' ) {
									Notify.danger(url.message);
									$('#post-thread-btn').button('submiting').removeClass('disabled').text('发表');
									return;
								}
								if ( url == "/login" ) {
									window.location.href = url;
									return;
								}
								var olderHref = checkUrl(window.location.href);
								if ( checkUrl(url) == olderHref ) {
									window.location.reload();
								} else {
									window.location.href = url;
								}
							} else {
								window.location.reload();
							}
						},
						error : function () {
							$('#post-thread-btn').button('submiting').removeClass('disabled').text('发表');
						}
					});
				}
			});
			validator_post_content.addItem({
				element : '[name="content"]',
				required : true,
				rule : 'minlength{min:2} visible_character'
			});

			validator_post_content.on('formValidate', function (elemetn, event) {
				editor.updateElement();
			});
		}

		var groupPostList = '.group-post-list';

		if ( $(groupPostList).length > 0 ) {

			//评论回复
			$(groupPostList).on('click', '.li-reply', function () {
				var postId = $(this).attr('postId');
				var fromUserId = $(this).data('fromUserId');
				$('#fromUserIdDiv').html('<input type="hidden" id="fromUserId" value="' + fromUserId + '">');
				$('#li-' + postId).show();
				$('#reply-content-' + postId).focus();
				$('#reply-content-' + postId).val("回复 " + $(this).attr("postName") + ":");

			});
			$(groupPostList).on('click', '.reply', function () {
				var postId = $(this).attr('postId');
				if ( $(this).data('fromUserIdNosub') != "" ) {
					var fromUserIdNosubVal = $(this).data('fromUserIdNosub');
					$('#fromUserIdNoSubDiv').html('<input type="hidden" id="fromUserIdNosub" value="' + fromUserIdNosubVal + '">')
					$('#fromUserIdDiv').html("");

				}
				;
				$(this).hide();
				$('#unreply-' + postId).show();
				$('.reply-' + postId).css('display', "");
			});

			//收起
			$(groupPostList).on('click', '.unreply', function () {
				var postId = $(this).attr('postId');
				$(this).hide();
				$('#reply-' + postId).show();
				$('.reply-' + postId).css('display', "none");

			});

			//我也要说
			$(groupPostList).on('click', '.replyToo', function () {
				var postId = $(this).attr('postId');
				if ( $(this).attr('data-status') == 'hidden' ) {
					$(this).attr('data-status', "");
					$('#li-' + postId).show();
					$('#reply-content-' + postId).focus();
					$('#reply-content-' + postId).val("");

				} else {
					$('#li-' + postId).hide();
					$(this).attr('data-status', "hidden");
				}
			});

			//查看更多
			$(groupPostList).on('click', '.lookOver', function () {
				var postId = $(this).attr('postId');
				$('.li-reply-' + postId).css('display', "");
				$('.lookOver-' + postId).hide();
				$('.paginator-' + postId).css('display', "");

			});

			//分页查看回复
			$(groupPostList).on('click', '.postReply-page', function () {
				var postId = $(this).attr('postId');
				$.post($(this).data('url'), "", function (html) {
					$('.reply-post-list-' + postId).replaceWith(html);

				})

			});

			(function(){
				var suBtn, fromUserIdVal = "", replyContent,postId;
				//提交回复
				$(groupPostList).on('click', '.reply-btn', function () {
					suBtn = this;
					postId = $(this).attr('postId');
					replyContent = $('#reply-content-' + postId + '').val();
					if ( $('#fromUserId').length > 0 ) {
						fromUserIdVal = $('#fromUserId').val();
					} else {
						if ( $('#fromUserIdNosub').length > 0 ) {
							fromUserIdVal = $('#fromUserIdNosub').val();
						} else {
							fromUserIdVal = "";
						}
					}
				});

				var validator_threadPost = new Validator({
					element : '.thread-post-reply-form',
					failSilently : true,
					autoSubmit : false,
					onFormValidated : function (error) {
						console.log(suBtn);
						if ( error ) {
							return false;
						}
						$(suBtn).button('submiting').addClass('disabled');
						$.ajax({
							url : $(".thread-post-reply-form").attr('post-url'),
							data : {content : replyContent, postId : postId, fromUserId : fromUserIdVal},
							cache : false,
							async : false,
							type : "POST",
							dataType : 'text',
							success : function (url) {
								if ( url == "/login" ) {
									window.location.href = url;
									return;
								}
								window.location.reload();
							},
							error : function () {
								$('.reply-btn').unbind('click');
								$(suBtn).button('submiting').removeClass('disabled').text('发表');
							}
						});
					}
				});
				validator_threadPost.addItem({
					element : '#reply-content-' + postId + '',
					required : true,
					rule : 'visible_character'
				});
			})()


		}

	}

});