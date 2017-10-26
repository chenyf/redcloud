define(function (require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var Validator = require('bootstrap.validator');

    var up = $('#modal');
    exports.run = function () {
        $('button[class="close"]').attr('data-dismiss','').addClass('syllaubs-update-close')
        $('.syllaubs-update-close',up).click(function(){
            $('#modal').modal('hide').html('')
            return false;
        })
        $('.ad-toggle',up).click(function(){
            $(this).parent().find('.ad-collapseExample',up).slideToggle(200);
        })
        $('#addSyllabus',up).click(function(){
            //alert(1)
            $('.syllabus-add-conn',up).toggle();
            return false;
        });
        //var add_more_clas_html = $('.add-relevance-sort').parent().html()
//        up.delegate('.syllabus-del-conn','click',function(){
//            remove_syllabus_conn(this);
//        })
        $('.syllabus-del-conn',up).click(function(){
            remove_syllabus_conn(this);
        })
        $('.add-more-class',up).click(function(){
            var _thisForm = $(this).closest('.form-syllabus-update',up) ;
            if(_thisForm.find('.syllabus_list').html().trim() == ''){
                _thisForm.find('.syllabus_list').html(add_more_clas_html).find('.syllabus-del-conn').addClass('show')
            }else{
                _thisForm.find('.add-relevance-sort').last().after(add_more_clas_html);
            }
            _thisForm.find('.syllabus-del-conn').last().click(function() {
                remove_syllabus_conn(this);
            })
            select_syllabus(_thisForm , 1);
        });
        $('.choose-color-list li',up).each(function(){
            $(this).click(function(){
                if(!$(this).hasClass('active')){
                    remove_color_active()
                    $(this).addClass('active')
                    $('input[name="courseColor"]',up).val($('span',this).data('id'))
                }
            })
        })
        $('input[name="startCourseTm"]',up).datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 2,
            pickerPosition : 'top-left' ,
            todayBtn: true
        });
        $('input[name="endCourseTm"]',up).datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 2,
            pickerPosition : 'top-left' ,
            todayBtn: true
        });

        select_syllabus(0)

        $('.syllabus-add-cencel',up).click(function(){
            $('.syllabus-add-conn',up).hide();
            return false;
        })
        var updateLoak = 1;
        $('.syllabus-update-submit',up).click(function(){
            tmp_update_submit_even = $(this)
            var check_res = check_data();
            if(check_res!=true){
                return check_res;
            }
            var url = $(this).data('url');
            
            if(updateLoak != 1) return false;
            updateLoak = 2 ;
            var data= $(this).closest('.form-syllabus-update',up).serialize();
            tmp_update_submit_even.html('编辑中').addClass('disabled');
            $.ajax({
                url:url,
                type: 'post',
                data:data,
                dataType:'json',
                success:function(res){
                    updateLoak = 1;
                    tmp_update_submit_even.html('确定').removeClass('disabled')
                    if(res.status == false){
                        Notify.danger(res.message);
                        return false;
                    }
                    var a = window.location.href;
                    var tag='showSy';
                    $('.syllaubs-update-close').trigger('click');
                    if(a.indexOf(tag)!=-1){
                       window.location.href = a ; 
                    }else{
                       window.location.href = a+'/showSy/1';  
                    }
                }

            })
            return false;
        })

        $('input[name="remind"]',up).click(function(){
            if($(this).prop('checked'))
                $(this).parents('.form-control').find('span').html('课前提醒已开启')
            else
                $(this).parents('.form-control').find('span').html('课前提醒已关闭')
        })
        bind_event();
    }

    function startTmLessEndTm() {
        var startTM = parseInt($('[name="startCourseTm"]',up).val().trim().replace(/-/g,''))
        var endTM = parseInt($('input[name="endCourseTm"]',up).val().trim().replace(/-/g,''));
        if (startTM >= endTM) {
            Notify.danger("开课时间必须比结课时间早");
            return false;
        }
        return true;
    }
    function remove_color_active(){
        $('.choose-color-list li',up).each(function(){
            if($(this).hasClass('active')){
                $(this).removeClass('active')
                return
            }
        })
    }
    function remove_syllabus_conn(e){
        $(e).closest('.add-relevance-sort').slideUp(300,function(){
            $(this).remove();
        });
    }
    function select_syllabus(up , type) {
        for (var i = 0, len = $('.add-relevance-sort',up).length; i < len; i++){
            if(type == 1){
                e = $('.add-relevance-sort',up).last().get(0);
                i = len;
            }else{
                e = $('.add-relevance-sort',up).get(i);
            }
            /** add select */
            $('.select-list-trigger', e).click(function () {
                $(this).parent().parent().children('ul').toggleClass('hidden')
            })
            /** 周期 */
            $('.tt-selected-list', e).eq(0).children('li').click(function () {
                $(this).parent().parent().find('.select-msg').html($('a', this).html()).attr('data-id',$('a',this).data('id'))
                $(this).parents('ul').toggleClass('hidden')
            })
            /** 开始时间*/
            $('.tt-selected-list', e).eq(1).find('a').click(function () {
                $(this).parents('.form-select').find('.select-msg').html($(this).html())
                $(this).parents('ul').toggleClass('hidden')
                if($('.tt-selected-list', e).eq(1).find('a').parents('.form-select').find('.select-msg').html() != '课程开始时间' && $('.tt-selected-list', e).eq(2).children('li').parent().parent().find('.select-msg').html() != '课程时长'){
                    rm_error($(this).parents('.col-md-11'));
                }
            })
            /** 课程时长 */
            $('.tt-selected-list', e).eq(2).children('li').click(function () {
                $(this).parent().parent().find('.select-msg').html($('a', this).html())
                $(this).parents('ul').toggleClass('hidden')
                if($('.tt-selected-list', e).eq(1).find('a').parents('.form-select').find('.select-msg').html() != '课程开始时间' && $(this).parent().parent().find('.select-msg').html() != '课程时长'){
                    rm_error($(this).parents('.col-md-11'));
                }
            })
        }
    }

    scroll_type = true;
    function add_error(event, info) {
        if(scroll_type === false){
            scroll_type = true;
            up.animate({scrollTop:(event.parents('.form-group').offset().top - ($(window).height()*0.1))},1000,function(){
                //scroll_type = false;
            })
        }
        event.parents('.form-group').removeClass('has-error').addClass('has-error');
        event.parent().find('.help-block').html(info).removeClass('show').addClass('show')
        //$('.syllabus-add-submit',up).removeClass('disabled').addClass('disabled');
    }
    function rm_error(event){
        event.parents('.form-group').removeClass('has-error');
        event.parent().find('.help-block').html('').removeClass('show')
        //$('.syllabus-add-submit',up).removeClass('disabled');
    }
    function bind_event(){
        courseName_type = true;
        //检查课程名
        $('input[name="courseName"]',up).focusout(function(){
            var courseName = $('input[name="courseName"]',up).val().trim();
            var courseName_help = $(this)
            if (courseName == "") {
                add_error(courseName_help,'请填写课程名称')
                courseName_type = false;
            } else if (courseName.length > 60){
                add_error(courseName_help,'课程名称仅可输入60个字符')
                courseName_type = false;
            }else{
                rm_error(courseName_help)
                courseName_type = true;
            }
        })

        lessonPlace_type = true;
        //$('body').delegate('input[name="lessonPlace[]"]','focusout',function(){
        //    var courseName = $(this).val().trim();
        //    var courseName_help = $(this)
        //    if (courseName == "") {
        //        add_error(courseName_help,'请填写上课地点')
        //        lessonPlace_type = false;
        //    } else if (courseName.length > 60){
        //        add_error(courseName_help,'上课地点仅可输入60个字符')
        //        lessonPlace_type = false;
        //    }else {
        //        rm_error(courseName_help)
        //        lessonPlace_type = true;
        //    }
        //})
    }

    function check_data() {
        scroll_type = false;
        if(courseName_type ==false) {
            //alert('false1')
            $('input[name="courseName"]',up).focusout().focus();
            return false
        }
        //上课时间和上课地点
        no_select_time = false
        no_lesson_place = false
        $('input[name="lessonPlace[]"]',up).each(function(num){
            //时间
            var tmp_circle = $('.add-relevance-sort',up).eq(num).find('.select-msg').eq(0).attr('data-id')
            $('input[name="lessonCircle[]"]',up).eq(num).val(tmp_circle ? tmp_circle : 8);
            var tmp_time = $('.add-relevance-sort',up).eq(num).find('.select-msg').eq(1).html()
            $('input[name="lessonTime[]"]',up).eq(num).val(tmp_time);
            var tmp_length = $('.add-relevance-sort',up).eq(num).find('.select-msg').eq(2).html()
            $('input[name="timeLength[]"]',up).eq(num).val(tmp_length);

            if(tmp_time == '课程开始时间' || tmp_length == '课程时长'){
                add_error($('.add-relevance-sort',up).eq(num).find('.select-msg').parents('.msg-block').find('.help-block'),'请至少添加一个上课时间')
                no_select_time = true
            }
            $('input[name="lessonPlace[]"]',up).eq(num).focusout()
            if(lessonPlace_type == false) {
                no_lesson_place = true
            }
            $('input[name="remind"]',up).val($('input[name="remind"]',up).prop('checked') === true ? 1: 0)
        })
        if(no_select_time == true){
            return false;
        }
        if(no_lesson_place == true){
            return false;
        }

        if(startTmLessEndTm() == false)
            return false;

        return true;
    }

});
