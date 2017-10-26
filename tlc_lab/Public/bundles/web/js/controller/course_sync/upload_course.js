define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    require('jquery.form');
    require('jquery.ajaxupload').run();

    exports.run = function() {

        var upload_course_status = false;

        window.onbeforeunload = function(){
            if(upload_course_status && event.clientX>document.body.clientWidth && event.clientY<0||event.altKey)
            {
                window.event.returnValue="确定要退出本页吗？";
            }
        };
        
        $("input[name='course_resource_file']").on('change',function(){
            if ($(this).val() == '')
                return false;
            $("#sure_to_submit").show();
        });

        $("#sure_to_submit").on("click",function(){

            var $file_input = $("input[name='course_resource_file']");

            if ($file_input.val() == '')
                return false;

            if(upload_course_status){
                Notify.danger('正在上传课程资源中，请稍候', 3);
                return false;
            }

            $upload_btn = $(this);
            var record_url = $upload_btn.data("recordurl");

            Notify.info("课程正在上传中，请耐心等待片刻，不要关闭窗口!",120);
            upload_course_status = true;
            $file_input.attr('disabled','disabled');
            $upload_btn.attr('disabled','disabled');
            $upload_btn.text("上传中...");

            var upload_url = $upload_btn.data('url');
            console.log(upload_url);
            $('#course_resource_file_homework').ajaxUpload({
                url : upload_url,
                error : function () {
                    Notify.danger('文件上传失败!', 2);
                },
                success : function (dataObj) {
                    dataObj = $.parseJSON(dataObj);
                    if (dataObj.status) {
                        Notify.success("上传新课程成功！");
                        setTimeout(function(){
                            window.location.href = record_url;
                        },3000);
                    } else {
                        Notify.danger(dataObj.msg, 5);
                    }

                    upload_course_status = false;
                    $file_input.removeAttr('disabled');
                    $upload_btn.removeAttr('disabled');
                    $upload_btn.text("上传文件");

                },
                complete:function(){
                    upload_course_status = false;
                    $file_input.removeAttr('disabled');
                    $upload_btn.removeAttr('disabled');
                    $upload_btn.text("上传文件");
                }
            });
        });

    };

});