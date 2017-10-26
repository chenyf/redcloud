    define(function(require, exports, module) {
        var Notify = require('common/bootstrap-notify');
        var Validator = require('bootstrap.validator');
        require('common/validator-rules').inject(Validator);
        var Share = require('../../util/share.js');
        var WidgetAppraise= require('../appraise/appraise.js');
        require('ckeditor');
        require('moment');
        
        //by Yjl  2015-06-01
        require('function');
        $(function(){
            var oAudioNum = $("div[class='n-yy-txt']").length;
            if(oAudioNum>0){
                //加载播放器js  
                playAudio.loadJs();
                //语音按钮点击 播放语音  by Yjl  2015-06-01
                $("div[class='voice-box-rt'] , div[class='voice-box-lt']").on("click",function(){
                    var audioUrl = $(this).attr("audioUrl");
                    var audioId = $(this).attr("audioId");
                    playAudio.doPlay(audioUrl,audioId);
                })
            }
        })
        function checkUrl(url) {
            var hrefArray = new Array();
            hrefArray = url.split('#');
            hrefArray = hrefArray[0].split('?');
            return hrefArray[1];
        }
        exports.run = function() {

            var add_btn_clicked = false;
            
            var threadAppraise = new WidgetAppraise({
                            'elemSelector': 'span.threadApprise',
                            success:function(response,ele) {
                                var data = JSON.parse(response);
                                if(!data.success) {
                                      Notify.danger(data.message);
                                      return false;
                                 }
                                var goodInfo = data.goodInfo;
                                if(data.type == 'add'){
                                  var zanNum = goodInfo['good'];
                                  var zan = zanNum+'人赞';
                                    ele.html(zan);
                                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                                }else{
                                   var exitNum = goodInfo['good'];
                                   var exit = exitNum+'人赞';
                                    ele.html(exit);
                                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                                } 
                           },
                       }).render();
                threadAppraise.init();

            $('#add-btn').click(function(event) {
	            var _this = $(this);
	            var title = _this.text();
                if (!add_btn_clicked) {
                    $('#add-btn').button('loading').addClass('disabled');
                    add_btn_clicked = true;
	                $.ajax({
		                url:_this.data('url'),
		                type:'post',
		                success:function(data){
			                if(data.status == 'allow'){
				                Notify.success('加入班级成功');
				                setTimeout(function(){
					                window.location.href = data.url;
				                },400);
			                }else if(data.status == 'check'){
				                Notify.success('申请加入成功，请耐心等待审核...');
				                $('#add-btn').text('已申请加入');
			                }else if(data.status == 'nologin'){/*by qzw 2015-05-22*/
                                            if($("#login-modal").size()>0) $("#login-modal").modal('show');
                                            $.get($('#login-modal').data('url'), function(html){
                                                $("#login-modal").html(html);
                                            });   
                                        }else if(data.status == 'error'){
				                Notify.danger(data.info);
				                $('#add-btn').button(true).text(title).removeClass('disabled');
				                add_btn_clicked = false;
			                }
		                },
		                error:function(data){
			                Notify.danger('加入失败 (；′⌒`)');
			                $('#add-btn').button(true).text(title).removeClass('disabled');
			                add_btn_clicked = false;
		                }
	                })
                }
                return true;
            });

            $("#thread-list").on('click', '.uncollect-btn, .collect-btn', function() {
                var $this = $(this);

                $.post($this.data('url'), function() {
                    $this.hide();
                    if ($this.hasClass('collect-btn')) {
                        $this.parent().find('.uncollect-btn').show();
                    } else {
                        $this.parent().find('.collect-btn').show();
                    }
                });
            });

            $('.attach').tooltip();

            if ($('#thread_content').length > 0) {
                // group: group
                var editor_thread = CKEDITOR.replace('thread_content', {
                    toolbar: 'Group',
                    filebrowserImageUploadUrl: $('#thread_content').data('imageUploadUrl')
                });

                var validator_thread = new Validator({
                    element: '#user-thread-form',
                    failSilently: true,
                    onFormValidated: function(error) {
                        if (error) {
                            return false;
                        }
                        $('#groupthread-save-btn').button('submiting').addClass('disabled');
                    }
                });

                validator_thread.addItem({
                    element: '[name="thread[title]"]',
                    required: true,
                    rule: 'minlength{min:2} maxlength{max:200}',
                    errormessageUrl: '长度为2-200位'


                });
                validator_thread.addItem({
                    element: '[name="thread[content]"]',
                    required: true,
                    rule: 'minlength{min:2}'

                });

                validator_thread.on('formValidate', function(elemetn, event) {
                    editor_thread.updateElement();
                });
            }

            if ($('#post-thread-form').length > 0) {

                var editor = CKEDITOR.replace('post_content', {
                    toolbar: 'Group',
                    filebrowserImageUploadUrl: $('#post_content').data('imageUploadUrl')
                });

                var validator_post_content = new Validator({
                    element: '#post-thread-form',
                    failSilently: true,
                    autoSubmit: false,
                    onFormValidated: function(error) {
                        if (error) {
                            return false;
                        }
                        $('#post-thread-btn').button('submiting').addClass('disabled');

                        $.ajax({
                            url: $("#post-thread-form").attr('post-url'),
                            data: $("#post-thread-form").serialize(),
                            cache: false,
                            async: false,
                            type: "POST",
                            success: function(url) {
                                if (url) {
	                                if(url.status == 'error'){
		                                Notify.danger(url.message);
		                                $('#post-thread-btn').button('submiting').removeClass('disabled').text('发表');
		                                return;
	                                }
                                    if (url == "/login") {
                                        window.location.href = url;
                                        return;
                                    }
                                    href = window.location.href;
                                    var olderHref = checkUrl(href);
                                    if (checkUrl(url) == olderHref) {
                                        window.location.reload();
                                    } else {
                                        window.location.href = url;
                                    }
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    }
                });
                validator_post_content.addItem({
                    element: '[name="content"]',
                    required: true,
                    rule: 'minlength{min:2} visible_character'
                });

                validator_post_content.on('formValidate', function(elemetn, event) {
                    editor.updateElement();
                });
            }

            if ($('.group-post-list').length > 0) {
                Share.create({
                    selector: '.share',
                    icons: 'itemsAll',
                    display: 'dropdown'
                });
                $('.group-post-list').on('click', '.li-reply', function() {
                    var postId = $(this).attr('postId');
                    var fromUserId = $(this).data('fromUserId');
                    $('#fromUserIdDiv').html('<input type="hidden" id="fromUserId" value="' + fromUserId + '">');
                    $('#li-' + postId).show();
                    $('#reply-content-' + postId).focus();
                    $('#reply-content-' + postId).val("回复 " + $(this).attr("postName") + ":");

                });

                $('.group-post-list').on('click', '.reply', function() {
                    var postId = $(this).attr('postId');
                    if ($(this).data('fromUserIdNosub') != "") {

                        var fromUserIdNosubVal = $(this).data('fromUserIdNosub');
                        $('#fromUserIdNoSubDiv').html('<input type="hidden" id="fromUserIdNosub" value="' + fromUserIdNosubVal + '">')
                        $('#fromUserIdDiv').html("");

                    };
                    $(this).hide();
                    $('#unreply-' + postId).show();
                    $('.reply-' + postId).css('display', "");
                });

                $('.group-post-list').on('click', '.unreply', function() {
                    var postId = $(this).attr('postId');

                    $(this).hide();
                    $('#reply-' + postId).show();
                    $('.reply-' + postId).css('display', "none");

                });

                $('.group-post-list').on('click', '.replyToo', function() {
                    var postId = $(this).attr('postId');
                    if ($(this).attr('data-status') == 'hidden') {
                        $(this).attr('data-status', "");
                        $('#li-' + postId).show();
                        $('#reply-content-' + postId).focus();
                        $('#reply-content-' + postId).val("");

                    } else {
                        $('#li-' + postId).hide();
                        $(this).attr('data-status', "hidden");
                    }


                });

                $('.group-post-list').on('click', '.lookOver', function() {

                    var postId = $(this).attr('postId');
                    $('.li-reply-' + postId).css('display', "");
                    $('.lookOver-' + postId).hide();
                    $('.paginator-' + postId).css('display', "");

                });

                $('.group-post-list').on('click', '.postReply-page', function() {

                    var postId = $(this).attr('postId');
                    $.post($(this).data('url'), "", function(html) {
                        $('.reply-post-list-' + postId).replaceWith(html);

                    })

                });

                $('.group-post-list').on('click', '.reply-btn', function() {

                    var postId = $(this).attr('postId');
                    var fromUserIdVal = "";
                    var replyContent = $('#reply-content-' + postId + '').val();
                    if ($('#fromUserId').length > 0) {
                        fromUserIdVal = $('#fromUserId').val();
                    } else {
                        if ($('#fromUserIdNosub').length > 0) {
                            fromUserIdVal = $('#fromUserIdNosub').val();
                        } else {
                            fromUserIdVal = "";
                        }
                    }

                    var validator_threadPost = new Validator({
                        element: '.thread-post-reply-form',
                        failSilently: true,
                        autoSubmit: false,
                        onFormValidated: function(error) {
                            if (error) {
                                return false;
                            }
                            $(this).button('submiting').addClass('disabled');
                            $.ajax({
                                url: $(".thread-post-reply-form").attr('post-url'),
                                data: "content=" + replyContent + '&' + 'postId=' + postId + '&' + 'fromUserId=' + fromUserIdVal,
                                cache: false,
                                async: false,
                                type: "POST",
                                dataType: 'text',
                                success: function(url) {
                                    if (url == "/login") {
                                        window.location.href = url;
                                        return;
                                    }
                                    window.location.reload();
                                }
                            });
                        }
                    });
                    validator_threadPost.addItem({
                        element: '#reply-content-' + postId + '',
                        required: true,
                        rule: 'visible_character'
                    });

                });

            }

            if ($('#hasAttach').length > 0) {

                $('.ke-icon-accessory').addClass('ke-icon-accessory-red');

            }

            if ($('#post-action').length > 0) {

                $('#post-action').on('click', '#closeThread', function() {

                    var $trigger = $(this);
                    if (!confirm($trigger.attr('title') + '？')) {
                        return false;
                    }

                    $.post($trigger.data('url'), function(data) {

                        window.location.href = data;

                    });
                })

                $('#post-action').on('click', '#elite,#stick,#cancelReward', function() {

                    var $trigger = $(this);

                    $.post($trigger.data('url'), function(data) {
                        window.location.href = data;
                    });
                })

            }
            if ($('#exit-btn').length > 0) {
                $('#exit-btn').click(function() {
                    if (!confirm('真的要退出该班级？您在该班级的信息将删除！')) {
                        return false;
                    }
                })

            }
            if ($('.actions').length > 0) {

                $('.group-post-list').on('click', '.post-delete-btn,.post-adopt-btn', function() {

                    var $trigger = $(this);
                    if (!confirm($trigger.attr('title') + '？')) {
                        return false;
                    }
                    $.post($trigger.data('url'), function() {
                        window.location.reload();
                    });
                })

            }

            if ($('#group').length > 0) {
                var editor = CKEDITOR.replace('group', {
                    toolbar: 'Simple',
                    filebrowserImageUploadUrl: $('#group').data('imageUploadUrl')
                });

                var validator = new Validator({
                    element: '#user-group-form',
                    failSilently: true,
                    onFormValidated: function(error) {
                        if (error) {
                            return false;
                        }
                        $('#group-save-btn').button('submiting').addClass('disabled');
                    }
                });

                validator.addItem({
                    element: '[name="group[grouptitle]"]',
                    required: true,
                    rule: 'minlength{min:2} maxlength{max:100}',
                    errormessageUrl: '长度为2-100位'
                });
                
                validator.addItem({
                    element: '[name="group[categoryId]"]',
                    required: true,
                    errormessageRequired: '请选择班级分类'
                });

	            validator.addItem({
		            element: '[name="group[year]"]',
		            required: true,
		            errormessageRequired: '请选择入学年份'
	            });

                validator.on('formValidate', function(elemetn, event) {
                    editor.updateElement();
                });

            }

	        var now = moment();
	        var nowYear = now.format('YYYY');
	        nowYear = parseInt(nowYear);
	        var years = [];
	        years.push((nowYear + 1));
	        for (var i = 0; i <= 60; i++) {
		        years.push(nowYear - i);
	        }
	        var dataYear = $('select#year').data('year');
	        var selected = '';
	        for (var j = 0; j < years.length; j++) {
		        selected = '';
		        if ( years[j] == dataYear ) {
			        selected = 'selected';
		        }
		        $('select#year').append('<option '+selected+' value="' + years[j] + '">' + years[j] + '年</option>');
	        }



        };

    });