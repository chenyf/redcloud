define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        $(".delete_upload_course").on("click",function(){
            if(confirm("确定删除该课程吗？将会同时删除和该课程有关的所有文件和记录！")) {
                var url = $(this).data("url");
                var courseId = $(this).data("id");
                var goto_url = $(this).data("goto");

                $.ajax({
                    method:'POST',
                    url:url,
                    data:{
                        courseId:courseId
                    },
                    success:function(result){
                        result = JSON.parse(result);
                        if(result.success){
                            Notify.success(result.msg);
                            setTimeout(function(){
                                window.location.href = goto_url;
                            },2000);
                        }else{
                            Notify.danger(result.msg);
                        }
                    },
                    error:function(){
                        Notify.danger("发生错误！");
                    }
                });
            }
        });

    };

});