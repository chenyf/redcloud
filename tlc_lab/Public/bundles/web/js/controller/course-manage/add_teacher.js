define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        console.log('load add_teacher.js');

        //多教师模式
        var ids_input = $("input[name='teacher_selected']");

        //添加教师
        $('.teacher-add-btn').on('click',function(){
            var num = $('#add-teacher-input').val().trim();
            var url = $(this).data('url');
            if(num != ""){
                if($(".teacher-add-ok").length >= 9){
                    Notify.danger('已达到最大教师人数，不能继续添加',2);
                    return false;
                }
                $.post(url,{num:num},function(result){
                    result = JSON.parse(result);
                    if(result.status){
                        var name = result.data.name;
                        var uid = result.data.uid;
                        var teacherSpan = $("<span></span>");
                        teacherSpan.addClass('teacher-add-ok').data('id',uid);
                        teacherSpan.append("<i data-id='" + uid + "' class='del-selected pull-right'>×</i>");
                        teacherSpan.append("<name>" + name + "-" + num + "</name>");
                        $("#course-teacher-selected-block").append(teacherSpan);
                        $('#add-teacher-input').val('');
                        writeTeacherIds();
                    }else{
                        Notify.danger(result.message,2);
                    }
                }).error(function(){
                    Notify.danger('发生错误!',2);
                });
            }
        });

        var cancel_state = {};

        $("#confirm-cancel-invitation").click(function () {

            $.get(cancel_state.url, {
                invitation_id: cancel_state.invitation_id
            }, function (result) {
                result = JSON.parse(result);
                if (result.status){
                    Notify.success('取消邀请成功！',2);
                    cancel_state.obj.remove();
                }else{
                    Notify.danger(result.message,2);
                }
            });
            $('#cancel-invitation-modal').modal('hide');
        });
        
        //删除已经邀请的教师
        $("#course-teacher-selected-block").on('click','.teacher-have-added .del-selected',function(){

            var obj = $(this).parents('.teacher-have-added');
            var url = "/Course/CourseManage/cancelInvitedTeacher";
            var invitation_id = obj.data('iid');
            cancel_state.obj = obj;
            cancel_state.url = url;
            cancel_state.invitation_id = invitation_id;

            $('#cancel-invitation-modal').modal('show');
        });
        
        //删除教师
        $("#course-teacher-selected-block").on('click','.teacher-add-ok .del-selected',function(){

            var obj = $(this).parents('.teacher-add-ok');
            var lessonNum = obj.data('lessonnum');
            var url = obj.data('url');
            var courseId = obj.data('cid');
            var teacherId = obj.data('tid');

            if(lessonNum != undefined && url != undefined){ //已经加入过课程的教师进行删除
                $.post(url,{cid:courseId,tid:teacherId},function(result){
                    result = JSON.parse(result);
                    if(result.status){
                        Notify.success('删除教师成功！',2);
                        obj.remove();
                        writeTeacherIds();
                    }else{
                        Notify.danger(result.message,2);
                    }
                }).error(function(){
                    Notify.danger('发生错误！',2);
                });
            }else{
                obj.remove();
                writeTeacherIds();
            }

        });

        var writeTeacherIds = function(){
            var s = "";
            var count = 0;
            $(".teacher-add-ok").each(function(){
                var sid = $(this).data('id');
                s += sid;

                count++;
                if(count < $(".teacher-add-ok").length){
                    s += ";";
                }
            });
            ids_input.val(s);

            var select_count = $(".teacher-add-ok").length;
        };

    }

});