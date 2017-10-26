define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var upload_course_status = false;

        window.onbeforeunload = function(){
            if(upload_course_status && event.clientX>document.body.clientWidth && event.clientY<0||event.altKey)
            {
                window.event.returnValue="确定要退出本页吗？";
            }
        };

        $("body").on("dblclick",".file-type-catagory",function(){
            var path = $(this).data("path");
            var filename = $(this).data("name");
            
            $.ajax({
                method:'POST',
                url:$("#file_list_block").data("url"),
                data:{
                    path:path,
                    backward:$(this).hasClass("backward")
                },
                success:function(result){
                    result = JSON.parse(result);
                    if(result.success){
                        if(result.toplevel){
                            var current_path = result.current_path;
                            var backward_path = result.backward_path;
                            $("#current_path").html(result.current_path);

                            $("#file_list_block").find("ul").html("");

                            if(current_path != "/"){
                                $("#file_list_block").find("ul").html("<li class='item'><span class='glyphicon glyphicon-folder-open'></span><a href='javascript:;' class='file-item file-type-catagory backward' data-name='...' data-path='" +  backward_path + "'>...</a></li>");
                            }

                            if(result.fileList.length > 0){
                                for(var i=0;i<result.fileList.length;i++){
                                    var file = result.fileList[i];
                                    var html = "";
                                    if(file.type == "file"){
                                        html = "<li class='item'><span class='glyphicon glyphicon-file'><a href='javascript:;' class='file-item file-type-file' data-name='" + file.nametruth + "' data-path='" +  file.abs + "'>" + file.namebrief + "</a></li>";
                                    }else if(file.type == "catagory"){
                                        html = "<li class='item'><span class='glyphicon glyphicon-folder-open'><a href='javascript:;' class='file-item file-type-catagory' data-name='" + file.nametruth + "' data-path='" +  file.abs + "'>" + file.namebrief + "</a></li>";
                                    }else{

                                    }

                                    $("#file_list_block").find("ul").append(html);
                                }
                            }
                        }else{
                            Notify.danger("没有上一级了!");
                        }
                    }else{
                        Notify.danger(result.msg);
                    }
                },
                error:function(){
                    Notify.danger("发生错误！");
                }
            })
        });

        $("body").on("click",".file-type-file",function(){
            var parent = $(this).parents("li.item");
            if(parent.hasClass("active")){
                parent.removeClass("active");
                $("#file_choice").html("");
            }else{
                $("#file_list_block").find("li.item").removeClass("active");
                parent.addClass("active");
                $("#file_choice").html($(this).data("name"));
            }

        });

        $("body").on("click","#sure-select-file",function(){
            var obj = $("#file_list_block").find("li.item.active");
            if(obj.length <= 0){
                Notify.danger("请选择课程资源文件！");
                return false;
            }

            var path = obj.find(".file-item").data("path");
            $("input[name='course_resource_path']").val(path);
            $("#file_select_path").html("已选文件：" + path);
            $("#sure_to_submit").show();
            $("#sure_to_submit").data("path",path);

            $(".modal").modal('hide');
        });

        $("#sure_to_submit").on("click",function(){
            var url = $(this).data("url");
            var path = $(this).data("path");
            var record_url = $(this).data("recordurl");
            if(path == undefined || path == ""){
                Notify.danger("请先选择文件!");
                return false;
            }

            Notify.info("课程正在上传中，请耐心等待片刻，不要关闭窗口!",120);
            upload_course_status = true;
            $.ajax({
                method:'POST',
                url:url,
                data:{
                    course_resource_path:path
                },
                success:function(result){
                    result = JSON.parse(result);
                    if(result.success){
                        Notify.success("上传新课程成功！");
                        setTimeout(function(){
                            window.location.href = record_url;
                        },3000);
                    }else{
                        Notify.danger(result.msg);
                    }
                    upload_course_status = false;
                },
                error:function(){
                    upload_course_status = false;
                    Notify.danger("发生错误！");
                }
            })
        });

    };

});