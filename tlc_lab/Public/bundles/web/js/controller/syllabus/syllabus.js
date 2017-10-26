define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var Validator = require('bootstrap.validator');
    var Datetimepicker = require('jquery.bootstrap-datetimepicker');
    var Validator = require('bootstrap.validator');

    exports.changeSyllabus = function() {
        //切换本周下周课程表 
        $('#changeSyllabus').on('change', function() {
            var nextSy = $(this).children('option:selected').val();
            var syUrl = $(this).data('url') + '/showSy/1/nextSy/' + nextSy;

            $('#timetable-syllabus-change').load(syUrl + ' .timetable', function() {
                window.computeWeek();
            });
        });




        $('.ad-toggle').click(function() {
            $(this).parent().find('.ad-collapseExample').slideToggle(200);
        })
        $('#seeSyllabus').click(function() {
            $('.seeSyllabus').toggle();
            if ($('.seeSyllabus').css('display') == 'block') {
                $('#seeSyllabus').text('隐藏本周课表');
                window.computeWeek();
            } else {
                $('#seeSyllabus').text('查看本周课表');
            }
            return false;
        })
        $('#addSyllabus').click(function() {
            $('.syllabus-add-conn').toggle();
            return false;
        });
        add_more_clas_html = $('.add-relevance-sort').parent().html()
        $('.add-more-class').click(function() {
            $('.add-relevance-sort').last().after(add_more_clas_html);
            $('.syllabus-del-conn').last().click(function() {
                remove_syllabus_conn(this);
            })
            select_syllabus(1)
        });
        $('.choose-color-list li').each(function() {
            $(this).click(function() {
                if (!$(this).hasClass('active')) {
                    remove_color_active()
                    $(this).addClass('active')
                    $('input[name="courseColor"]').val($('span', this).data('id'))
                }
            })
        })
        $('input[name="startCourseTm"]').datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 2,
            todayBtn: true
        });
        $('input[name="endCourseTm"]').datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 2,
            todayBtn: true
        });

        select_syllabus(0)

        $('.syllabus-add-cencel').click(function() {
            $('.syllabus-add-conn').hide();
            return false;
        })
        var addLoak = 1;
        $('.syllabus-add-submit').click(function() {
            tmp_add_submit_even = $(this)
            var check_res = check_data();
            if (check_res != true) {
                return check_res;
            }
            var url = $(this).data('url');
            if (addLoak != 1)
                return false;
            addLoak = 2;
            var data = $('.form-syllabus-add').serialize();
            tmp_add_submit_even.html('添加中').addClass('disabled');
            $.ajax({
                url: url,
                type: 'post',
                data: data,
                dataType: 'json',
                success: function(res) {
                    addLoak = 1;
                    tmp_add_submit_even.html('确定').removeClass('disabled');
                    if (res.status == false) {
                        Notify.danger(res.message)
                        return false;
                    }
                    var a = window.location.href;
                    var tag = 'showSy';
                    $('.syllabus-add-conn').hide();
                    if (a.indexOf(tag) != -1) {
                        window.location.href = a;
                    } else {
                        window.location.href = a + '/showSy/1';
                    }
                }

            })
            return false;
        })
        
        $(window).resize(function() {
            window.computeToday();
            window.computeWeek();
          });
        //计算今日课程的宽高
        window.computeToday = function() {
            var todayWidth = $('.dt-today').width() - 68;
            var dtWidth = todayWidth / 17;

            $('.dt-event').each(function(i) {
                var width = $(this).data('width');
                var left = $(this).data('left');
                var _thisWidth = width * (dtWidth - 1) - 10;
                var _thisLeft = left * dtWidth;
                $(this).css('left', _thisLeft);
                $(this).width(_thisWidth);
            });

            $('.restLapRow').each(function(i) {
                var lapLeft = $(this).data('left');
                var lapLength = $(this).data('length');
                var rowLeft = lapLeft * dtWidth + lapLength * (dtWidth - 1) - 28;
                $(this).css('left', rowLeft);
            });

        }

        //计算一周课程的宽高
        window.computeWeek = function() {
            var weekWidth = $('.tt-times').width() - 120;
            var ttWidth = weekWidth / 7;
            
            $('.tt-day').each(function(i) {
                var _thisWidth = ttWidth - 11;
                $(this).width(_thisWidth);
            });

            $('.tt-event').each(function(i) {
                var left = $(this).data('left');
                var start = $(this).data('start') + 1;
                var duration = $(this).data('duration');
                var _thisTop = start * 49;
                var _thisHeight = duration * 48 - 10;
                var _thisLeft = left * ttWidth + 120;
                
                var _thisWidth = ttWidth - 14;
                $(this).css('left', _thisLeft);
                $(this).css('top', _thisTop);
                $(this).css('line-height', _thisHeight+'px');
                $(this).height(_thisHeight);
                $(this).width(_thisWidth);
            });

            $('.restLap').each(function(i) {
                var lapLeft = $(this).data('left');
                var lapStart = $(this).data('start') + 1;
                var _thisTop = lapStart * 49;
                var _thisLeft = lapLeft * ttWidth  + 88;
                $(this).css('left', _thisLeft);
                $(this).css('top', _thisTop);
            });

        }

        $('input[name="remind"]').click(function() {
            if ($(this).prop('checked'))
                $(this).parents('.form-control').find('span').html('课前提醒已开启')
            else
                $(this).parents('.form-control').find('span').html('课前提醒已关闭')
        })

        window.computeToday();
        window.computeWeek();

        bind_event();

    }
    exports.changeSyllabus();
    function remove_color_active() {
        $('.choose-color-list li').each(function() {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active')
                return
            }
        })
    }
    function remove_syllabus_conn(e) {
        $(e).parents('.add-relevance-sort').slideUp(300, function() {
            $(this).remove()
        });
    }
    function startTmLessEndTm() {
        var startTM = parseInt($('[name="startCourseTm"]').val().trim().replace(/-/g, ''))
        var endTM = parseInt($('input[name="endCourseTm"]').val().trim().replace(/-/g, ''));
        if (startTM >= endTM) {
            Notify.danger("开课时间必须比结课时间早");
            return false;
        }
        return true;
    }
    function select_syllabus(type) {
        for (var i = 0, len = $('.add-relevance-sort').length; i < len; i++) {
            if (type == 1) {
                e = $($('.add-relevance-sort').last().get(0));
                i = len;
            } else {
                e = $('.add-relevance-sort').get(i);
            }
            /** add select */
            $('.select-list-trigger', e).click(function() {
                $(this).parent().parent().children('ul').toggleClass('hidden')
            })
            $(document).click(function(e) {
                var target = $(e.target);
                $('.tt-selected-list').each(function(num) {
                    if (!$('.tt-selected-list').eq(num).hasClass('hidden') && target.closest(".tt-selected-list").length == 0 && target.closest('.select-list-trigger').length == 0) {
                        $('.tt-selected-list').eq(num).addClass('hidden');
                        return false;
                    }
                })
            });
            /** 周期 */
            $('.tt-selected-list', e).eq(0).children('li').click(function() {
                $(this).parent().parent().find('.select-msg').html($('a', this).html()).attr('data-id', $('a', this).data('id'))
                $(this).parents('ul').toggleClass('hidden')
            })
            /** 开始时间*/
            $('.tt-selected-list', e).eq(1).find('a').click(function() {
                $(this).parents('.form-select').find('.select-msg').html($(this).html())
                $(this).parents('ul').toggleClass('hidden')
                if ($('.tt-selected-list', e).eq(1).find('a').parents('.form-select').find('.select-msg').html() != '课程开始时间' && $('.tt-selected-list', e).eq(2).children('li').parent().parent().find('.select-msg').html() != '课程时长') {
                    rm_error($(this).parents('.col-md-11'));
                }
            })
            /** 课程时长 */
            $('.tt-selected-list', e).eq(2).children('li').click(function() {
                $(this).parent().parent().find('.select-msg').html($('a', this).html())
                $(this).parents('ul').toggleClass('hidden')
                if ($('.tt-selected-list', e).eq(1).find('a').parents('.form-select').find('.select-msg').html() != '课程开始时间' && $(this).parent().parent().find('.select-msg').html() != '课程时长') {
                    rm_error($(this).parents('.col-md-11'));
                }
            })
        }
    }

    scroll_type = true;
    function add_error(event, info) {
        if (scroll_type === false) {
            scroll_type = true;
            if (event.parents('#modal').length > 0) {
                $('#modal').animate({scrollTop: (event.parents('.add-relevance-sort').offset().top + $('#modal').offset().top)}, 1000, function() {
                    //scroll_type = false;
                })
            } else {
                $('html,body').animate({scrollTop: (event.parents('.form-group').offset().top - ($(window).height() * 0.1))}, 1000, function() {
                    //scroll_type = false;
                })
            }
        }
        event.parents('.form-group').removeClass('has-error').addClass('has-error');
        event.parent().find('.help-block').html(info).removeClass('show').addClass('show')
        //$('.syllabus-add-submit').removeClass('disabled').addClass('disabled');
    }
    function rm_error(event) {
        event.parents('.form-group').removeClass('has-error');
        event.parent().find('.help-block').html('').removeClass('show')
        //$('.syllabus-add-submit').removeClass('disabled');
    }
    function bind_event() {
        courseName_type = false;
        //检查课程名
        $('input[name="courseName"]').focusout(function() {
            var courseName = $('input[name="courseName"]').val().trim();
            var courseName_help = $(this)
            if (courseName == "") {
                add_error(courseName_help, '请填写课程名称')
                courseName_type = false;
            } else if (courseName.length > 60) {
                add_error(courseName_help, '课程名称仅可输入60个字符')
                courseName_type = false;
            } else {
                rm_error(courseName_help)
                courseName_type = true;
            }
        })

        lessonPlace_type = false;
        $('body').delegate('input[name="lessonPlace[]"]', 'focusout', function() {
            var courseName = $(this).val().trim();
            var courseName_help = $(this)
            if (courseName == "") {
                add_error(courseName_help, '请填写上课地点')
                lessonPlace_type = false;
            } else if (courseName.length > 60) {
                add_error(courseName_help, '上课地点仅可输入60个字符')
                lessonPlace_type = false;
            } else {
                rm_error(courseName_help)
                lessonPlace_type = true;
            }
        })
    }



    function check_data() {
        scroll_type = false;
        if (courseName_type == false) {
            $('input[name="courseName"]').focusout().focus();
            return false
        }
        //上课时间和上课地点
        no_select_time = false
        no_lesson_place = false
        $('input[name="lessonPlace[]"]').each(function(num) {
            //时间
            var tmp_circle = $('.add-relevance-sort').eq(num).find('.select-msg').eq(0).attr('data-id')
            $('input[name="lessonCircle[]"]').eq(num).val(tmp_circle ? tmp_circle : 8);
            var tmp_time = $('.add-relevance-sort').eq(num).find('.select-msg').eq(1).html()
            $('input[name="lessonTime[]"]').eq(num).val(tmp_time);
            var tmp_length = $('.add-relevance-sort').eq(num).find('.select-msg').eq(2).html()
            $('input[name="timeLength[]"]').eq(num).val(tmp_length);

            if (tmp_time == '课程开始时间' || tmp_length == '课程时长') {
                add_error($('.add-relevance-sort').eq(num).find('.select-msg').parents('.msg-block').find('.help-block'), '请至少添加一个上课时间')
                no_select_time = true
            }
            $('input[name="lessonPlace[]').eq(num).focusout()
            if (lessonPlace_type == false) {
                no_lesson_place = true
            }
            $('input[name="remind"]').val($('input[name="remind"]').prop('checked') === true ? 1 : 0)
        })
        if (no_select_time == true) {
            return false;
        }
        if (no_lesson_place == true) {
            return false;
        }

        if (startTmLessEndTm() == false)
            return false;

        return true;
    }


});