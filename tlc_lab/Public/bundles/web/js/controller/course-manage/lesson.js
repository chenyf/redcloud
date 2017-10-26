define(function(require, exports, module) {

    require('jquery.sortable');
    var Sticky = require('sticky');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        require('./header').run();
        require('../course/deploy').run();
        require('../course/deploy-sortable').run();
        require('../../util/collapse')('hide');

        var sortList = function($list) {
            var data = $list.sortable("serialize").get();
            $.post($list.data('sortUrl'), {ids: data}, function(response) {
                var lessonNum = chapterNum = unitNum = 0;

                for (var key in response.data) {
                    var newPid = response.data[key];
                    $("li#" + key).removeAttr("data-pid").attr("data-pid", newPid);
                }

                $list.find('.item-lesson, .item-chapter').each(function() {
                    var $item = $(this);
                    if ($item.hasClass('item-lesson')) {
                        lessonNum++;
                        $item.find('.number').text(lessonNum);
                    } else if ($item.hasClass('item-chapter-unit')) {
                        unitNum++;
                        $item.find('.number').text(unitNum);
                    } else if ($item.hasClass('item-chapter')) {
                        chapterNum++;
                        unitNum = 0;
                        $item.find('.number').text(chapterNum);
                    }

                    window.deploy($item.attr("id"));
                });

                showAllChapterHour();

            }, 'json');
        };
        
        var caclHour = function(element){
            var liObj = element;
            var pid = liObj.data('id');
            var childLesson = $("li.item-lesson[data-pid='"+pid+"']");
            var lessonNum = childLesson.length;
            if(liObj.hasClass('item-chapter-unit')){    //节
                return {
                    'lessonNum':lessonNum
                };
            }else if(liObj.hasClass('item-chapter-chapter')){   //章
                var childUnit = $("li.item-chapter-unit[data-pid='"+liObj.data('id')+"']");
                var unitNum = childUnit.length;
                childUnit.each(function(){
                    var that = $(this);
                    lessonNum += $("li.clearfix[data-pid='" + that.data('id') + "']").length;
                });

                return {
                    'lessonNum':lessonNum,
                    'unitNum':unitNum
                };
            }
        }

        var showAllChapterHour = function(){
            $("li.c-item-chapter.item-chapter-chapter").each(function(){
                var hours = caclHour($(this));
                if(hours.hasOwnProperty('lessonNum')){
                    $(this).find('span.lesson-hour').text(hours['lessonNum']);
                }
            });
        }

        showAllChapterHour();

        var $list = $("#course-item-list").sortable({
            distance: 20,
            onDrop: function(item, container, _super) {
                _super(item, container);
                sortList($list);
            },
            serialize: function(parent, children, isContainer) {
                return isContainer ? children : parent.attr('id');
            },
            isValidTarget: function(item, container) {
                var dragObj = $(item.context);
                if (dragObj.attr("class") == "dropdown-menu" || dragObj.parents("ul").attr("class") == "dropdown-menu") {
                    return false;
                }
                return true;
            },
            onDragStart: function(item, container) {
                var dragObj = $(item.context);
                if (dragObj.attr("class") == "dropdown-menu" || dragObj.parents("ul").attr("class") == "dropdown-menu") {
                    return false;
                }
            }
        });

        $list.on('click', '.delete-lesson-btn', function(e) {
            if (!confirm('删除课程内容的同时会删除课程内容的资料、测验。\n您真的要删除该课程内容吗？')) {
                return;
            }
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(response) {
                $btn.parents('.item-lesson').remove();
                sortList($list);
                Notify.success('课程内容已删除！');
            }, 'json');
        });

        $list.on('click', '.delete-chapter-btn', function(e) {
            var chapter_name = $(this).data('chapter');
            var part_name = $(this).data('part');

            var liChapter = $(this).parents('li.item-chapter');

            var hours = caclHour(liChapter);
            if(hours.hasOwnProperty('unitNum') && !confirm("该章节下有" + hours['unitNum'] + "节、" + hours['lessonNum'] + "个课程内容，删除后，资料、测验将被全被删除，确定删除吗？")){
                return ;
            }
            if(!hours.hasOwnProperty('unitNum') && !confirm("该章节下有" + hours['lessonNum'] + "个课程内容，删除后，资料、测验将被全被删除，确定删除吗？")){
                return ;
            }
            if (!confirm('您真的要删除该' + chapter_name + '' + part_name + '吗？')) {
                return;
            }
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(response) {
                liChapter.remove();
                $("li.item-lesson[data-pid='"+liChapter.data('id')+"'],li.item-chapter-unit[data-pid='"+liChapter.data('id')+"']").remove();
                sortList($list);
                Notify.success('' + chapter_name + '' + part_name + '已删除！');
            }, 'json');
        });

        $list.on('click', '.replay-lesson-btn', function(e) {
            if (!confirm('您真的要录制回放吗？')) {
                return;
            }
            $.post($(this).data('url'), function(html) {
                if (html.error) {
                    if (html.error.code == 10019)
                        Notify.danger("录制失败，直播时您没有进行录制！");
                    else
                        Notify.danger("录制失败！");
                } else {
                    var id = '#' + $(html).attr('id');
                    $(id).replaceWith(html);
                    Notify.success('课程内容已录制！');
                }
            });
        });

        $list.on('click', '.publish-lesson-btn', function(e) {
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(html) {
                var id = '#' + $(html).attr('id');
                $(id).find('.item-content .unpublish-warning').remove();
                $(id).find('.item-actions .publish-lesson-btn').addClass('hidden');
                $(id).find('.item-actions .unpublish-lesson-btn').removeClass('hidden');
                $(id).find('.btn-link').tooltip();
                Notify.success('课程内容发布成功！');
                setTimeout(function(){
                    window.location.reload();
                },1200);
            });
        });

        $list.on('click', '.unpublish-lesson-btn', function(e) {
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(html) {
                var id = '#' + $(html).attr('id');
                $(id).find('.item-content').append('<span class="unpublish-warning text-warning">(未发布)</span>');
                $(id).find('.item-actions .publish-lesson-btn').removeClass('hidden');
                $(id).find('.item-actions .unpublish-lesson-btn').addClass('hidden');
                $(id).find('.btn-link').tooltip();
                Notify.success('课程内容已取消发布！');
                setTimeout(function(){
                    window.location.reload();
                },1200);
            });
        });

        $list.on('click', '.delete-exercise-btn', function(e) {
            if (!confirm('您真的要删除该课程内容练习吗？')) {
                return;
            }
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(response) {
                Notify.success('练习已删除！');
                window.location.reload();
            }, 'json');
        });

        $list.on('click', '.delete-homework-btn', function(e) {
            if (!confirm('您真的要删除该课程内容作业吗？')) {
                return;
            }
            var $btn = $(e.currentTarget);
            $.post($(this).data('url'), function(response) {
                Notify.success('作业已删除！');
                window.location.reload();
            }, 'json');
        });

        $list.on('click', '.document-new-convert', function(e) {
            var fileid = $(this).data('fileid');
            var url = $(this).data('url');
            if (!fileid) {
                Notify.danger('文件ID为空！');
                return false;
            }

            var self = this;
            if ($(self).data('lock'))
                return false;
            $(self).data('lock', 1);

            $.get(url, {fileid: fileid}, function(response) {
                $(self).data('lock', 0);
                if (response['status']) {
                    if (response['info'] == "waiting") {
                        $(self).parent().attr('class', 'text-warning').html("(正在等待文件格式转换)");
                    }
                    if (response['info'] == "doing") {
                        $(self).parent().attr('class', 'text-info').html("(正在转换文件格式)");
                    }
                    if (response['info'] == "success") {
                        $(self).parent().remove();
                    }
                    if (response['info'] == "error") {
                        Notify.danger("文档转换失败");
                    }
                } else {
                    Notify.danger(response['info']);
                }
            }, 'json')
        });

        Sticky('.lesson-manage-panel .panel-heading', 0, function(status) {
            if (status) {
                var $elem = this.elem;
                $elem.addClass('sticky');
                $elem.width($elem.parent().width() - 10);
            } else {
                this.elem.removeClass('sticky');
                this.elem.width('auto');
            }
        });

        $("#course-item-list .item-actions .btn-link").tooltip();
        $("#course-item-list .fileDeletedLesson").tooltip();

        $('.dropdown-menu').parent().on('shown.bs.dropdown', function() {
            if ($(this).find('.dropdown-menu-more').css('display') == 'block') {
                $(this).parent().find('.dropdown-menu-more').mouseout(function() {
                    $(this).parent().find('.dropdown-menu-more').hide();
                });

                $(this).parent().find('.dropdown-menu-more').mouseover(function() {
                    $(this).parent().find('.dropdown-menu-more').show();
                });

            } else {
                $(this).parent().find('.dropdown-menu-more').show();
            }
        });

        $('.dropdown-menu').parent().on('hide.bs.dropdown', function() {
            $(this).find('.dropdown-menu-more').show();
        });

        $('li .chapter-create-class').on('click', function() {
            window.tempChapterId = $(this).parents('.item-chapter').data('id');
            
        });

        $('.modal').on('hidden.bs.modal', function() {
            window.tempChapterId = undefined;
        });
    };

});