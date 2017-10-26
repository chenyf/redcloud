define(function(require, exports, module) {

    var WidgetAppraise = require('../appraise/appraise.js');
    var Validator = require('bootstrap.validator'),
            Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require('ckeditor');

    //by Yjl  2015-06-01
    require('function');
    $(function() {
        var oAudioNum = $("div[class='n-yy-txt']").length;
        if (oAudioNum > 0) {
            //加载 播放器js
            playAudio.loadJs();
            //语音按钮点击 播放语音
            $("div[class='voice-box-rt'] , div[class='voice-box-lt']").on("click", function() {
                var audioUrl = $(this).attr("audioUrl");
                var audioId = $(this).attr("audioId");
                playAudio.doPlay(audioUrl, audioId);
            })
        }
    })


    exports.run = function() {
        require('./common').run();
        var courseThreadAppraise = new WidgetAppraise({
            'elemSelector': 'span.course_threadApprise',
            success: function(response, ele) {
                var data = JSON.parse(response);
                if (!data.success) {
                    Notify.danger(data.message);
                    return false;
                }
                var goodInfo = data.goodInfo;
                if (data.type == 'add') {
                    var zanNum = goodInfo['good'];
                    var zan = zanNum;
                    ele.html(zan);
                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                } else {
                    var exitNum = goodInfo['good'];
                    var exit = exitNum;
                    ele.html(exit);
                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                }
            },
        }).render();
        courseThreadAppraise.init();

        var threadPostAppraise = new WidgetAppraise({
            'elemSelector': 'span.thread_postApprise',
            success: function(response, ele) {
                var data = JSON.parse(response);
                if (!data.success) {
                    Notify.danger(data.message);
                    return false;
                }
                var goodInfo = data.goodInfo;
                if (data.type == 'add') {
                    var zanNum = goodInfo['good'];
                    var zan = "(" + zanNum + ")";
                    ele.html(zan);
                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                } else {
                    var exitNum = goodInfo['good'];
                    var exit = "(" + exitNum + ")";
                    ele.html(exit);
                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                }
            },
        }).render();
        threadPostAppraise.init();


        // group: 'course'
        var editor = CKEDITOR.replace('post_content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#post_content').data('imageUploadUrl')
        });

        var validator = new Validator({
            element: '#thread-post-form'
        });

        validator.addItem({
            element: '[name="post[content]"]',
            required: true,
            rule: 'remote'
        });

        Validator.query('#thread-post-form').on('formValidate', function(elemetn, event) {
            editor.updateElement();
        });

        validator.on('formValidated', function(err, msg, $form) {
            if (err == true) {
                return;
            }
            $form.find('[type=submit]').attr('disabled', 'disabled');
            $.post($form.attr('action'), $form.serialize(), function(data) {
                window.location.reload();
                $form.find('[type=submit]').removeAttr('disabled');
            });
        });

//        Validator.query('#thread-post-form').on('formValidated', function(err, msg, ele) {
//            if (err == true) {
//                return;
//            }
////            $('.thread-post-list').find('li.empty').remove();
////            var $form = $("#thread-post-form");
////
////            $form.find('[type=submit]').attr('disabled', 'disabled');
////            $.post($form.attr('action'), $form.serialize(), function(html) {
////                if (html.status) {
////                    Notify.warning(html.info);
////                    return false;
////                }
////                $("#thread-post-num").text(parseInt($("#thread-post-num").text()) + 1);
////                if ($("#isTeacher").val() == 1 && $("#isType").val() == 'question') {
////                    var id = $(html).appendTo('#teaYes').attr('id');
////                    $(html).appendTo('#teaNo');
////                } else {
////                    var id = $(html).appendTo('#teaNo').attr('id');
////                }
////
////                editor.setData('');
////
////                $form.find('[type=submit]').removeAttr('disabled');
////
////                window.location.href = '#' + id;
////            });
//
////            return false;
//        });


        $('[data-role=confirm-btn]').click(function() {
            var $btn = $(this);
            if (!confirm($btn.data('confirmMessage'))) {
                return false;
            }
            $.post($btn.data('url'), function() {
                var url = $btn.data('afterUrl');
                if (url) {
                    window.location.href = url;
                } else {
                    window.location.reload();
                }
            });
        });

        $('.thread-post-list').on('click', '.thread-post-action', function() {

            var userName = $(this).data('user');

            editor.focus();
            editor.insertHtml('@' + userName + '&nbsp;');

        });

        $(".thread-post-list").on('click', '[data-action=post-delete]', function() {
            if (!confirm("您真的要删除该回帖吗？")) {
                return false;
            }
            var $btn = $(this);
            $.post($btn.data('url'), function() {
                window.location.reload();
            });
        });

        /******问答改版*******/
        $('.reply').on('click', function() {
            var id = $(this).data('id');
            if ($(this).html() == '取消') {
                $(this).html($(this).attr('cancel'));
                $(this).attr('cancel', '取消');
            } else {
                $(this).attr('cancel', $(this).html());
                $(this).html('取消');
                $.get($(this).data('url'), function(html) {
                    $('#post-' + id).append(html);
                    var $form = $('[name=answer-form]');
                    var replyValidator = new Validator({
                        element: '[name=answer-form]',
                        autoSubmit: false,
                    });
                    replyValidator.addItem({
                        element: '[name="reply[content]"]',
                        required: true,
                        rule: 'remote'
                    });

                    replyValidator.on('formValidated', function(err, msg, $form) {
                        if (err == true) {
                            return;
                        }
                        $form.find('[type=submit]').attr('disabled', 'disabled');
                        $.post($form.attr('action'), $form.serialize(), function(data) {
                            window.location.reload();
                            $form.find('[type=submit]').removeAttr('disabled');
                        });
                    });
                });

            }
            $('form[name=answer-form]').remove();
        });
        //测试弹层
//        $('#jubao').on('click', function() {
//            $('#buildModal').load('/Course/CourseThread/threadCommentAction', function() {
//                $('#buildModal').modal({backdrop: true, keyboard: false, show: 'show'});
//            });
//        });

        //采纳为最佳答案
        $('.accept-post').on('click', function() {
            $.get($(this).data('url'), function(data) {
                if (data.status == 'success') {
                    Notify.success(data.msg);
                    if (data.role == 'teacher') {
                        $('#buildModal').load(data.commentUrl, function() {
                            $('#buildModal').modal({backdrop: true, keyboard: false, show: 'show'});
                        });
                    } else {
                        document.location.reload();
                    }
                } else {
                    Notify.danger(data.msg);
                }
            }
            );
        });

        var $grabMode = $('#grabMode');
        var status = $('.ask-content').data('status');
        var limitTime = $('.ask-content').data('limit');

        switch (status) {
            case 'grab':
                var grabTimer = setInterval(function grabModetimer() {
                    limitTime--;
                    if (limitTime <= 0) {
                        $.get($grabMode.data('url'), function(response) {
                            if (response.grabStatus == 'grab') {
                                $('.ask-content').attr('data-limit', response.limitTime);
                                limitTime = response.limitTime;
                            } else {
                                alert('已经被其他老师抢到！');
                                clearInterval(grabTimer);
                                $('.post-btn').attr("disabled", true);
                            }
                        });
                    }
                    $grabMode.html('剩余时间：' + strToTime(limitTime));
                }, 1000);
                break;

            case 'over':
                $grabMode.html('您尚有未回答的问题，抢先回答抢到的问题！');
                $('.post-btn').attr("disabled", true);
                break;

            case 'unlimited':
                $grabMode.html('剩余时间：不限时间');
                break;

            case 'grabOther':
                alert('对不起，该提问被其他老师抢答,请换一道问题回答，谢谢！');
                $grabMode.html('对不起，该提问被其他老师抢答！');
                $('.post-btn').attr("disabled", true);
                break;

            case 'posted':
                $grabMode.html('');
                break;

            case 'noPost':
                $grabMode.html('');
                $('.post-btn').attr("disabled", true);
                break;

            default :
                $grabMode.html('');

        }

        function strToTime(time) {
            var h = time / 3600;
            var i = (time % 3600) / 60;
            var s = ((time % 3600) % 60);
            return Math.floor(h) + ':' + Math.floor(i) + ':' + Math.floor(s);
        }


    };

});


