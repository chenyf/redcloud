define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var delLoak = 1;
        $('.deleteSyllabus-btn').on('click', function() {
            if (!confirm("确定要删除此课程吗")) 
                    return false;
            var url = $(this).data('url');
            if(delLoak != 1) return false;
            delLoak = 2 ;
            var lessonid = $(this).data('lessonid');
            $.ajax({
                url: url,
                type: 'post',
                data: {lessonId : lessonid},
                dataType: 'json',
                success: function(data) {
                    delLoak = 1;
                    if (data.status == false) {
                        Notify.danger(data.message);
                        return false;
                    }
                    Notify.success(data.message);
                    $('.syllaubs-update-close').trigger('click');
                    var a = window.location.href;
                    var tag='showSy';
                    if(a.indexOf(tag)!=-1){
                       window.location.href = a ; 
                    }else{
                       window.location.href = a+'/showSy/1';  
                    }
                    
                    }

            })
            return false;
        });
        
        $('.edit-course-details').click(function(){
            var courseid = $(this).data('courseid');
            $('.modal-title').text('修改课程');
            $('.course-details'+courseid).toggle();
            $('.timetable-details-box').toggle();
            return false;
        });


    }

});