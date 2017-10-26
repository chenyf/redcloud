define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('jquery.form');
    require('jquery.ajaxupload').run();

    require('ckeditor');

    exports.run = function() {

        var media_field = $("input[name='homework_file_media_field']");

        var remove_file_url = $("input[name='remove_file_url']").val();

        var uploadedTotal = $("#fileUploadedStatistic .total-counts");

        var uploading = false;

        var editor = CKEDITOR.replace("homework-remark", {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $("#homework-remark").data('imageUploadUrl')
        });

        //上传文件
        $('#upload_homework_file').on('change', function() {
            if ($(this).val() == '')
                return false;

            if($(".uploaded_file_item").length >= 3){
                Notify.danger('最多只能上传3个文件', 3);
                return false;
            }

            if(uploading){
                Notify.danger('文件正在上传中，请稍候', 3);
                return false;
            }

            $upload_btn = $(this).parents(".homework-file-upload-btn");

            uploading = true;
            $('#create_file_homework').attr('disabled','disabled');
            $('#upload_homework_file').attr('disabled','disabled');
            $upload_btn.find('span').text("上传中...");

            var upload_url = $upload_btn.data('url');
            console.log(upload_url);
            $('#file_form').ajaxUpload({
                url : upload_url,
                error : function () {
                    Notify.danger('文件上传失败!', 2);
                },
                success : function (dataObj) {
                    dataObj = $.parseJSON(dataObj);
                    if (dataObj.status) {
                        Notify.success(dataObj.message, 2);
                        writeMediaField(dataObj.filePath,true);
                        $("#the_file_list").prepend("<div class='uploaded_file_item' data-id='0' data-path='" + dataObj.filePath + "'>" + dataObj.fileName + '<a class="mlm del_file" href="javascript:void(0);">删除</a></div>');
                        incrUploadedCount();
                    } else {
                        Notify.danger(dataObj.message, 2);
                    }

                },
                complete:function(){
                    uploading = false;
                    $('#create_file_homework').removeAttr('disabled');

                    $('#upload_homework_file').removeAttr('disabled');

                    $upload_btn.find('span').text("上传文件");
                    $('#upload_homework_file').val('');
                }
            });
        });

        //删除已上传文件
        $("#the_file_list").on('click', 'a.del_file', function() {

            if (!confirm("确定删除此文件么？"))
            {
                return false;
            }

            var parent = $(this).parents('.uploaded_file_item');

            $.post(remove_file_url,{
                id:parent.data('id'),
                path:parent.data('path')
            },function(result){
                result = $.parseJSON(result);
                if(result.status){
                    writeMediaField(parent.data('path'),false);
                    parent.remove();
                    Notify.success("删除成功！", 2);
                    descUploadedCount();
                }else{
                    Notify.danger("删除失败！", 2);
                }

            }).error(function(){
                Notify.danger("发生错误！", 2);
            });

        });

        //确认创建作业
        $('#create_file_homework').on('click',function(){

            var finishUrl = $(this).data('url');
            var score = $('#homework-score').val();

            var val = media_field.val();
            if(val == undefined || val == ""){
                Notify.danger('请上传文件!', 3);
                return false;
            }

            var obj = $.parseJSON(val);
            if(!obj.hasOwnProperty('paths') || obj.paths == undefined || obj.paths.length == 0 || !$.isArray(obj.paths)){
                Notify.danger('请上传文件!', 3);
                return false;
            }

            if(score.trim() == ""){
                Notify.danger('请填写作业分值!', 3);
                return false;
            }

            if(isNaN(score) || score < 0 || score > 100){
                Notify.danger('作业分值必须为0-100的整数', 3);
                return false;
            }

            var $btn = $(this);
            $btn.button('loading').addClass('disabled');
            
            $.post($(this).data('post'),
                {
                    media:val,
                    // remark:$("textarea[name='remark']").val(),
                    remark:editor.getData(),
                    score:parseInt(score),
                },
                function(result){
                    result = $.parseJSON(result);
                    if(result.status){
                        Notify.success('文件作业创建成功！', 2);
                        // window.location.href = result.url;
                        $('#buildModal').load(finishUrl, function() {
                            $('#buildModal').on('hide.bs.modal', function() {
                                $btn.removeClass('disabled');
                                $btn.button('reset');

                                var count = parseInt(uploadedTotal.text());
                                $('#buildModal').find(".c-choose-result").prepend("<div style='padding: 10px;'>已经上传" +count+ "个作业文件</div>");
                            });
                            $('#buildModal').modal({backdrop: true, keyboard: false, show: 'show'});
                        });
                    }else{
                        $btn.removeClass('disabled');
                        $btn.button('reset');
                        Notify.danger(result.message, 2);
                    }
                }).error(function(){
                    Notify.danger('文件作业创建失败！', 2);
                });
        });

        //将文件路径写入media_field
        var writeMediaField = function(path,add){
            console.log(path);
            add = (add == undefined) ? true : add;
            if(path == "" || path == undefined){
                alert("路径不能为空！");
            }

            var val = media_field.val();
            var obj = {};

            if(val != undefined && val != ""){
                obj = $.parseJSON(val);
            }

            if(!obj.hasOwnProperty('paths') || obj.paths == undefined){
                obj = {paths:[]};
            }

            if(add && obj.paths.length >= 3){
                alert("最多上传3个文件！");
            }

            if(add){    //添加路径
                obj.paths.push(path);
            }else{  //删除路径
                obj.paths = remove(obj.paths,path);
            }

            media_field.val(JSON.stringify(obj));
        }

        //上传数量递增
        var incrUploadedCount = function(){
            var count = parseInt(uploadedTotal.text());
            var new_count = count + 1;
            uploadedTotal.text(new_count+"");
        }

        //上传数量递减
        var descUploadedCount = function(){
            var count = parseInt(uploadedTotal.text());
            var new_count = count > 0 ? count - 1 : 0;
            uploadedTotal.text(new_count+"");
        }

        //数组删除元素
        var remove = function(arr,item){
            var index = arr.indexOf(item);
            if (index > -1) {
                arr.splice(index, 1);
            }
            return arr;
        }

    };

});