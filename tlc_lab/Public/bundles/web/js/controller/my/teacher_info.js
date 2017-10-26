define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        $('td.upload-item-td').each(function(){
            setUploadify($(this).find('div').not('.uploaded-show'));
        });

        $('div.hiddenuploadfield').on('change',function(){
            var chooser = $('#teacherinfo_edit_main_table td.upload-item-td>div');
            askUploadToken(chooser);
        });

        function setUploadify(element){
            element.uploadify({
                queueSizeLimit : 1,
                auto: false,
                method: "Post",
                // width: 135,
                // height: 45,
                // buttonText : '选择文件',
                fileSizeLimit : '1024MB',//上传文件大小限制
                fileTypeDesc : '资料文件',
                fileTypeExts : '*.ppt;*.pptx;*.zip;*.rar;*.tar.gz;*.rm;*.flv;*.avi;*.mp4;*.mp3;*.wma;*.txt;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.jpg;*.jpeg;*.gif;*.png',//文件类型过滤
                buttonClass: 'my_upload_button',
                swf : '/Public/assets/libs/jquery-plugin/uploadify/uploadify.swf',
                uploader: "",
                formData: {
                    token: "",
                },
                fileObjName: "file",
                onSelect : function(file){
                    choose_file_name = file.name;
                    askUploadToken();
                },
                onUploadStart : function(file) {
                    element.uploadify('disable', true);
                    fileInfo = $("#file_upload-queue").find(".fileName").eq(0).text();
                },
                onUploadSuccess: function(file, data, response){
                    // 根据返回结果指定界面操作
                    data = $.parseJSON(data);
                    if(response){
                        $("input[name=url]").val(data.url);
                        Notify.success("文件上传成功！",5);
                        // $('#start-resource-upload').hide();
                        $("#resource-upload-chooser").html("<label>文件上传成功：</label><p>" + choose_file_name + "</p>");
                        $(".uploadify-queue-item").fadeOut();
                    }else{
                        Notify.danger("文件上传失败!",5);
                    }
                },
                onUploadComplete : function(file) {
                    element.uploadify('disable', false);
                },
                onUploadError:function(file, errorCode, errorMsg){
                    Notify.danger("上传失败：" + errorMsg,5);
                }
            });
        }


        //点击开始上传，上传文件
        function askUploadToken(file_upload_chooser){
            if(choose_file_name == undefined){
                Notify.danger("请选择文件后点击上传！",5);
                return;
            }

            var data = {};
            data.name = choose_file_name;
            $.ajax({
                url: $('input[name="data_params_url"]').val(),
                async: false,
                dataType: 'json',
                data: data,
                cache: false,
                success: function(response, status, jqXHR) {
                    file_upload_chooser.uploadify('settings','uploader',response.url);
                    file_upload_chooser.uploadify('upload');
                },
                error: function(jqXHR, status, error) {
                    Notify.danger('请求上传授权码失败！',5);
                }
            });
        }

        //点击删除已上传文件
        $('div#uploaded-show').on('click','li .delete',function(){

        });
    }
});