define(function(require, exports, module) {
    var Widget = require('widget');
    var Notify = require('common/bootstrap-notify');
    var WidgetAppraise = require('../appraise/appraise.js');
    /*
     * 
     * 调用方法
     * 页面加上
     * <div class="redcloud_comment" data-cmttype="页面类型参数" data-cmtid="页面唯一标识"></div>
     *  
     *    js文件加上
     * var WidgetComment= require('../comment/comment.js');
     
     //seajs.use('comment/comment.js');
     exports.run = function () {
     var CommentA = new WidgetComment({
     cmtSelector:'.redcloud_comment'
     }).render();
     CommentA.init();
     * 
     * 
     * 
     */

    var WidgetA = Widget.extend({
        attrs: {
            a: 1,
            cmtSelector: '', /*#id .class [name=3]*/
            cmtPostFinishCb: null,
        },
        comment: null,
        cmtType: '',
        cmtIdStr: '',
        vcode: '',
        replyLock: 1,
        cmtLock: 1,
        page: 2,
        load: true,
        init: function() {
            var oDiv = $(this.get('cmtSelector'));

            this.comment = oDiv
            this.cmtType = oDiv.data('cmttype');
            this.cmtIdStr = oDiv.data('cmtid');
            this.vcode = oDiv.data('vcode');
            this.page = 2;
            this.load = true;
            var thisCmt = this;
            $.post("/System/Comment/commentAction", {cmtType: this.cmtType, cmtIdStr: this.cmtIdStr, vcode: this.vcode}, function(response) {
                var result = JSON.parse(response);
                if (!result.success) {
                    //Notify.danger(result.message);
                    return false;
                }

                oDiv.html(result.message);
                thisCmt.addComment();
                thisCmt.delComment();
                thisCmt.replyComment();
                thisCmt.delReplyComment();
                thisCmt.exitComment();
                thisCmt.backChange();
                thisCmt.moreComment();
                thisCmt.appraise();

            });
        },
        moreComment: function() {
            var thisComment = this.comment;
            var thisCmt = this;
            thisComment.find('#loading-more').unbind('click');
            thisComment.find('#loading-more').on('click', function() {
                var load = thisComment.find('#loading-more');
                if (thisCmt.load == false)
                    return false;
                load.find('span').text('加载中...');
                $.post("/System/Comment/commentAction", {page: thisCmt.page, cmtType: thisCmt.cmtType, cmtIdStr: thisCmt.cmtIdStr, vcode: thisCmt.vcode}, function(response) {
                    var result = JSON.parse(response);
                    if (!result.success) {
                        //Notify.danger(result.message);
                        return false;
                    }
                    thisComment.find('.discuss-redcloud-1').append(result.message);
                    thisCmt.addComment();
                    thisCmt.delComment();
                    thisCmt.replyComment();
                    thisCmt.delReplyComment();
                    thisCmt.exitComment();
                    thisCmt.backChange();
                    thisCmt.page = thisCmt.page + 1;
                    load.find('span').text('加载更多评论');
                    if (result.loadOver == 2) {
                        load.html('').hide();
                        thisCmt.load = false;
                    }
                    thisCmt.appraise();
                });
            });
        },
        backChange: function() {
            var thisComment = this.comment;
            var thiscmtIdStr = this.cmtIdStr;
            thisComment.find('.textareastyle-' + thiscmtIdStr).unbind('input propertychange');
            thisComment.find('.textareastyle-' + thiscmtIdStr).on('input propertychange', function() {
                var textVal = $(this).val();
                var backC = $(this).closest('.input-change-text').find('.change-back');
                if (textVal.length > 140) {
                    var textar = textVal.substring(0, 140);
                    $(this).val(textar);
                    backC.html('还可以输入<i>0</i>字');
                    return false;
                }
                var len = 140 - textVal.length;
                backC.html('还可以输入<i>' + len + '</i>字');
            });
        },
        addComment: function() {
            var thisaddCmt = this;
            var thisCom = this.comment;
            var thisStr = this.cmtIdStr;
            thisCom.find('#comment_form_' + this.cmtIdStr).submit(function() {
                var sendName = $(this).find('input[name="sendName"]').val();
                var release = $(this).data("release");
                if (sendName == '') {
                    $(this).find('input[name="sendName"]').focus();
                    Notify.danger('匿名不能为空');
                    return false;
                }

                if (release == 1 && sendName.length > 7) {
                    $(this).find('input[name="sendName"]').focus();
                    Notify.danger('匿名字数不超过七个');
                    return false;
                }
                var cmtContent = $(this).find('[name="content"]').val();
                if (cmtContent == '') {
                    $(this).find('[name="content"]').focus();
                    Notify.danger('评论不能为空');
                    return false;
                }

                if (cmtContent.length > 140) {
                    $(this).find('[name="content"]').focus();
                    Notify.danger('评论字数过多');
                    return false;
                }
                var issuebtn = $(this).find('.fabu_' + thisStr);
                issuebtn.text('发布中');
                if (thisaddCmt.cmtLock != 1)
                    return false;
                thisaddCmt.cmtLock = 2;
                $.post($(this).attr('action'), $(this).serialize(), function(response) {
                    thisaddCmt.cmtLock = 1;
                    var data = JSON.parse(response);
                    if (!data.success) {
                        Notify.danger(data.message);
                        issuebtn.text('发布');
                        return false;
                    }
                    $(data.message).prependTo(thisCom.find('.discuss-redcloud-1'));
                    thisCom.find('#textareastyle-' + thisaddCmt.cmtIdStr).val('');
                    if (release == 1) {
                        thisCom.find('.anonymity-exit .anonyName').text(data.anonyName);
                        thisCom.find('input[name="sendName"]').val(data.anonyName);
                        thisCom.find('.anonymity-exit').show();
                        thisCom.find('.anonymity-nick').hide();
                        thisCom.find('.anonymity-nick-cmt').hide();
                    }

                    var comm = '' + data.cmtNum + '条评论，' + data.userNum + '人参与。';
                    thisCom.find('#comment-statistics').text(comm);
                    thisCom.find('.change-back').html('还可以输入<i>140</i>字');
                    thisaddCmt.delComment();
                    thisaddCmt.replyComment();
                    thisaddCmt.delReplyComment();
                    thisaddCmt.exitComment();
                    thisaddCmt.backChange();
                    issuebtn.text('发布');
                    thisaddCmt.appraise();
                });
                return false;
            });
        },
        delComment: function() {
            var thisComment = this.comment;
            var _this = this;
            thisComment.find('.del-' + this.cmtIdStr).unbind('click');
            thisComment.find('.del-' + this.cmtIdStr).on("click", function() {
                var ele = $(this);
                if (!confirm("确定删除此评论吗？"))
                    return false;
                ;
                $.post("/System/Comment/delCommentAction", {cmtId: $(this).data('id'), cmtType: _this.cmtType, cmtIdStr: _this.cmtIdStr, vcode: _this.vcode}, function(response) {
                    if (!response.success) {
                        Notify.danger(response.message);
                        return false;
                    }

                    ele.closest('.all-list').remove();
                    var comNum = '' + response.cmtNum + '条评论，' + response.userNum + '人参与。';
                    thisComment.find('#comment-statistics').text(comNum);
                    Notify.success(response.message);

                }, 'json');
            })
        },
        replyComment: function() {
            var thisReply = this;
            var thisCom = this.comment;
            var thisStr = this.cmtIdStr;
            thisCom.find('.reply-' + this.cmtIdStr).unbind('click');
            thisCom.find('.reply-' + this.cmtIdStr).on("click", function() {
                $(this).closest('.discuss-content-' + thisReply.cmtIdStr + '').next('.reply-box').toggle();
            })

            thisCom.find('.reply-comment-form-' + this.cmtIdStr).submit(function() {
                var rep = $(this);
                var replyName = rep.find('input[name="sendName"]').val();
                var num = rep.data("insert");
                var release = $(this).data("release");
                if (replyName == '') {
                    rep.find('input[name="sendName"]').focus();
                    Notify.danger('匿名不能为空');
                    return false;
                }

                if (release == 1 && replyName.length > 7) {
                    rep.find('input[name="sendName"]').focus();
                    Notify.danger('匿名字数不超过七个');
                    return false;
                }
                var replyContent = $(this).find('[name="content"]').val();
                if (replyContent == '') {
                    rep.find('[name="content"]').focus();
                    Notify.danger('回复内容不能为空');
                    return false;
                }
                if (replyContent.length > 140) {
                    rep.find('[name="content"]').focus();
                    Notify.danger('回复字数过多');
                    return false;
                }


                if (thisReply.replyLock != 1)
                    return false;
                thisReply.replyLock = 2;
                var issuebtn = $(this).find('.issuebtn_' + thisStr);
                issuebtn.text('回复中');

                $.post($(this).attr('action'), $(this).serialize(), function(res) {
                    thisReply.replyLock = 1;
                    var data = JSON.parse(res);
                    if (!data.success) {
                        Notify.danger(data.message);
                        issuebtn.text('回复');
                        return false;
                    }
                    if (release == 1) {
                        thisCom.find('.anonymity-exit .anonyName').text(data.anonyName);
                        thisCom.find('input[name="sendName"]').val(data.anonyName);
                        thisCom.find('.anonymity-exit').show();
                        thisCom.find('.anonymity-nick').hide();
                        thisCom.find('.anonymity-nick-cmt').hide();
                    }
                    if (num == 1)
                        rep.closest('.p-replybox').next('ul').children('.discuss-redcloud').prepend(data.message);

                    if (num == 2)
                        rep.closest('.discuss-redcloud').prepend(data.message);

                    thisCom.find('.textareastyle').val('');
                    thisCom.find('.textareastyle').closest('.p-replybox').hide();

                    var comm = '' + data.cmtNum + '条评论，' + data.userNum + '人参与。';
                    thisCom.find('#comment-statistics').text(comm);
                    thisCom.find('.change-back').html('还可以输入<i>140</i>字');

                    thisReply.replyComment();
                    thisReply.delReplyComment();
                    thisReply.exitComment();
                    thisReply.backChange();
                    thisReply.appraise();
                    issuebtn.text('回复');
                });
                return false;
            });
        },
        delReplyComment: function() {
            var thisComment = this.comment;
            var _this = this;
            thisComment.find('.del-reply-' + this.cmtIdStr).unbind('click');
            thisComment.find('.del-reply-' + this.cmtIdStr).on("click", function() {
                var ele = $(this);
                var del = '.all-reply-list';

                if (!confirm("确定删除此回复吗？"))
                    return false;
                ;
                $.post("/System/Comment/delReplyCommentAction", {rid: $(this).data('id'), vcode: _this.vcode}, function(response) {
                    if (!response.success) {
                        Notify.danger(response.message);
                        return false;
                    }

                    ele.closest(del).remove();
                    var idArr = response.idArr;
                    for (item in idArr) {
                        $('.reply-list-' + idArr[item]).remove();
                    }

                    var comm = '' + response.cmtNum + '条评论，' + response.userNum + '人参与。';
                    thisComment.find('#comment-statistics').text(comm);
                    Notify.success(response.message);

                }, 'json');
            });
        },
        exitComment: function() {
            var thisStr = this.cmtIdStr;
            var thisExit = this.comment;
            thisExit.find('.exitbtn').unbind('click');
            thisExit.find('.exitbtn').on("click", function() {
                $.post("/System/Comment/exitAnonymousAction", {anony: 1}, function(response) {
                    if (!response.success) {
                        Notify.danger(response.message);
                        return false;
                    }
                    thisExit.find('.anonymity-exit').hide();
                    thisExit.find('.anonymity-exit .anonyName').html('');
                    var nickHtml = '<h6>以游客身份回复</h6><span><input type="text" name="sendName" placeholder="昵称"><button type="submit" data-anony ="anony"   class="btn btn-primary issuebtn_' + thisStr + '">回复</button></span>';
                    var cmtHtml = '<h6>以游客身份发布</h6><span><input type="text" name="sendName" placeholder="昵称"><button type="submit" data-anony ="anony"   class="btn btn-primary fabu_' + thisStr + '">发布</button></span>';
                    thisExit.find('.anonymity-nick-cmt .visitors-login').html(cmtHtml);
                    thisExit.find('.anonymity-nick .visitors-login').html(nickHtml);
                    thisExit.find('input[name="sendName"]').val('');
                    thisExit.find('.anonymity-nick-cmt').show();
                    thisExit.find('.anonymity-nick').show();

                }, 'json');
            })
        },
        appraise: function() {
            var thisComment = this.comment;
            thisComment.find('.user-like-text').mouseenter(function() {
                $(this).siblings('.user-like-content').show();
            });
            thisComment.find('.discuss-body').mouseleave(function() {
                $(this).find('.user-like-content').hide();
            });
            thisComment.find('.poster-body').mouseleave(function() {
                $(this).find('.user-like-content').hide();
            });
            thisComment.find('.more-reply').on('click', function() {
                $(this).closest('.discuss-children').find('.all-reply-list').removeClass('hide');
                $(this).remove();
            })
            var Appraise = new WidgetAppraise({
                'elemSelector': 'b.cmtApprise',
                success: function(response, ele) {
                    var data = JSON.parse(response);
                    if (!data.success) {
                        Notify.danger(data.message);
                        return false;
                    }
                    var goodInfo = data.goodInfo;
                    if (data.type == 'add') {
                        var zanNum = goodInfo['good'];
                        var zan = '取消赞(<i>' + zanNum + '</i>)';
                        ele.html(zan);
                        ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                    } else {
                        var exitNum = goodInfo['good'];
                        var exit = '赞(<i>' + exitNum + '</i>)';
                        ele.html(exit);
                        ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                    }
                },
                eachSuccess: function(response, _this) {
                    var data = JSON.parse(response);
                    if (!data.success) {
                        var zan = '赞(<i>0</i>)';
                        _this.html(zan);
                        _this.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                    }
                    var goodInfo = data.appraise;
                    var zanNum = goodInfo['good'];

                    if (typeof zanNum == 'undefined')
                        zanNum = 0;
                    if (goodInfo['isGood'] === 1) {
                        var zan = '取消赞(<i>' + zanNum + '</i>)';
                        _this.html(zan);
                        _this.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                    } else {

                        var zan = '赞(<i>' + zanNum + '</i>)';
                        _this.html(zan);
                        _this.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                    }
                },
            }).render();
            Appraise.init();
        }

    });
    module.exports = WidgetA;






});