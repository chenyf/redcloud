define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('jquery.ajaxupload').run();

    exports.run = function(){

        var media_field = $("input[name='submit_homework_file_media_field']");

        var remove_file_url = $("input[name='remove_file_url']").val();

        var uploadedTotal = $("#fileUploadedStatistic .total-counts");

        var isCommit = $("input[name='is_commit']").val() == 'true';

        var uploading = false;

        //上传文件
        $('#upload_submit_homework_file').on('change', function() {
            if (isCommit || $(this).val() == '')
                return false;

            if($(".uploaded_file_item").length >= 3){
                Notify.danger('最多只能上传3个文件', 3);
                return false;
            }

            if(uploading){
                Notify.danger('文件正在上传中，请稍候', 3);
                return false;
            }

            $upload_btn = $(this).parents(".submit-homework-file-upload-btn");

            uploading = true;
            $('#finishPaper').attr('disabled','disabled');
            $('#upload_submit_homework_file').attr('disabled','disabled');
            $upload_btn.find('span').text("上传中...");

            var upload_url = $upload_btn.data('url');

            $('#submit_homework_file_form').ajaxUpload({
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
                    $('#finishPaper').removeAttr('disabled');

                    $('#upload_submit_homework_file').removeAttr('disabled');

                    $upload_btn.find('span').text("上传文件");
                    $('#upload_submit_homework_file').val('');
                }
            });
        });

        //删除已上传文件
        $("#the_file_list").on('click', 'a.del_file', function() {

            if (isCommit || !confirm("确定删除此文件么？"))
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

        //提交作业
        $('body').on('click', '#finishPaper', function() {
            if(getUploadedFileCount() <= 0){
                Notify.danger("必须上传文件，才能提交作业！",2);
                return false;
            }
            $('#testpaper-finished-dialog').modal('show');
        });

        $('#testpaper-finish-btn').on('click', function() {
            $finishBtn = $('#finishPaper');
            $('#testpaper-finish-btn').button('saving');
            $('#testpaper-finish-btn').attr('disabled', 'disabled');

            var submitUrl = $finishBtn.data('submit');
            var gotoUrl = $finishBtn.data('goto');

            $.post(submitUrl,{media:media_field.val()},function(result){
                result = $.parseJSON(result);
                if(result.status){
                    Notify.success("提交作业成功", 2);
                    window.location.href = gotoUrl;
                }else{
                    Notify.danger(result.message, 2);

                    $('#testpaper-finish-btn').removeClass('disabled');
                    $('#testpaper-finish-btn').button('reset');
                    $('#testpaper-finished-dialog').modal('hide');
                }
            }).error(function(){
                Notify.danger("发生错误！", 2);
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
                Notify.danger("最多上传3个文件！",2);
            }

            if(add){    //添加路径
                obj.paths.push(path);
            }else{  //删除路径
                obj.paths = remove(obj.paths,path);
            }

            media_field.val(JSON.stringify(obj));
        }

        //获取上传文件数量
        var getUploadedFileCount = function(){
            var val = media_field.val();
            var obj = {};

            if(val != undefined && val != ""){
                obj = $.parseJSON(val);
            }

            if(!obj.hasOwnProperty('paths') || obj.paths == undefined){
                return 0;
            }

            return obj.paths.length;
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