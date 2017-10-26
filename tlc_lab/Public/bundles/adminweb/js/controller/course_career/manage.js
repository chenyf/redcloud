define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    require("./index").run();
    // require("./base").run();
    exports.run = function() {

        $(".sc-course-cart").on("click",".remove_x",function(){
            if(!confirm("确定从当前阶段中移除该课程吗？")){
               return false;
            }

            $.ajax({
                method:'POST',
                url:$(this).data("url"),
                data:{
                    id:$(this).parents(".sc-course-cart").data("id")
                },
                success:function(result){
                    if(result.status){
                        window.location.reload();
                    }else{
                        Notify.danger(result.message,5);
                    }

                },
                error:function(){
                    Notify.danger("发生错误！",5);
                }
            });
        });

        $("body").on("click","#course-search-btn",function(){
            var course_cate = $("#course_cate").val();
            var course_name = $("input[name='course_name']").val().trim();
            var course_number = $("input[name='course_number']").val().trim();
            if(parseInt(course_cate) <= 0 && course_name == "" && course_number == ""){
                Notify.danger("请选择课程类别或输入课程名称或课程编号进行检索");
                return false;
            }

            var url = $(this).data("url");
            $.ajax({
                method:'POST',
                url:url,
                data:{
                    cate:course_cate,
                    name:course_name,
                    number:course_number
                },
                success:function(result){
                    if(result.status){
                        if(result.courses.length <= 0){
                            return false;
                        }

                        var course_tr_tpl_html = $("#course_tr_tpl").clone();
                        var course_result_html = course_tr_tpl_html.prop("outerHTML");
                        var course_tr_item_tpl_html = course_tr_tpl_html.removeAttr("class").removeAttr("id").prop("outerHTML");
                        for(var i in result.courses){
                            var course = result.courses[i];
                            var course_tr_item_html = course_tr_item_tpl_html.replace(/%no%/g,course.id).replace(/%cn%/g,course.name).replace(/%cl%/g,course.lesson_count).replace(/%cp%/g,course.price);
                            course_result_html += course_tr_item_html;
                        }

                        $("#course-search-result-table").find("tbody").html(course_result_html);
                    }else{
                        Notify.danger(result.message,5);
                    }

                },
                error:function(){
                    Notify.danger("发生错误！",5);
                }
            });
        });

        $("body").on("click","#submit-add-course-btn",function(){
            var courseIdArr = [];
            $("input[name='course_id']:checked").each(function(){
                if(!$(this).hasClass('course_tr_tpl')){
                    courseIdArr.push($(this).val());
                }
            });

            if(courseIdArr.length <= 0 ){
                Notify.danger("请选择课程！");
                return false;
            }

            $submit_btn = $(this);
            $submit_btn.button('submiting').addClass('disabled');

            $.ajax({
                method:'POST',
                url:$submit_btn.data("url"),
                data:{
                    stage_id:$submit_btn.data('id'),
                    ids:courseIdArr
                },
                success:function(result){
                    if(result.status){
                        Notify.success(result.message,2);
                        window.location.reload();
                    }else{
                        Notify.danger(result.message,5);
                    }

                    $submit_btn.button('reset').removeClass('disabled');
                },
                error:function(){
                    Notify.danger("发生错误！",5);
                    $submit_btn.button('reset').removeClass('disabled');
                }
            });

        });

    };

});