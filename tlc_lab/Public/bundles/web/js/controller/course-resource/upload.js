define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    var ResourceChooser = require('../widget/media-chooser/resource-chooser');
    var Util = require('util');

    exports.run = function(){
        if(!Util.flashChecker()){
            Notify.danger("您的浏览器未安装Flash，请更换高版本支持FLash的浏览器进行上传操作",0);
        }

        var $form = $("#resource-upload-form");
        var $modal = $('#resource-upload-form').parents('.modal');
        var resourceBtn = $('#create-resource-btn');
        // var resource_upload_chooser = $("#resource-upload-chooser");
        // var fileInfo = "";
        // var uploadToken = "";
        // var choose_file_name = undefined;
        var choosedMedia = $form.find('[name="media"]').val();
        choosedMedia = choosedMedia ? $.parseJSON(choosedMedia) : {};

        var resourceChooser = new ResourceChooser({
            element: '#resource-chooser',
            choosed: choosedMedia,
            uploaderSettings: {
                button_text: "<span class=\"btnText\" style=\"font-family: '微软雅黑';\">选择文件</span>",
                button_text_style : ".btnText { color: #1C0202; font-size:16px;font-family: '微软雅黑';}",
                button_text_left_padding : 4,
                button_text_top_padding : 7,
            },
        });

        resourceChooser.on('change', function(item) {
             var value = item ? JSON.stringify(item) : '';
             // $("#resource-chooser").find(".file-chooser-main").find(".file-chooser-uploader .help-block").hide();
             $form.find('[name="media"]').val(item['url']);
        });

        $('.modal').unbind("hide.bs.modal");
        $(".modal").on("hide.bs.modal", function(){
            resourceChooser.destroy();
        });

        Validator.addRule('mediaValueEmpty', function(options) {
            var value = options.element.val();
            if (value == '""' || value == '') {
                return false;
            }

            return true;
        }, '请选择或上传资料文件');

        var checkMediaEmpty = function(media){
            if(!media == '""' || media == ''){
                return false;
            }
            return true;
        }

        var validator = new Validator({
            element: '#resource-upload-form',
            autoSubmit: false,
            failSilently: true,
            onFormValidated: function(error, results, $form) {
                var mediaValue = $form.find('[name="media"]').val();

                if(!checkMediaEmpty(mediaValue)){
                    // if($("#resource-chooser").find(".file-chooser-main").find(".file-chooser-uploader .help-block").length > 0){
                    //     $("#resource-chooser").find(".file-chooser-main").find(".file-chooser-uploader .help-block").show();
                    // }else{
                    //     $("#resource-chooser").find(".file-chooser-main").find(".file-chooser-uploader")
                    //         .append("<div class='help-block'><span class='text-danger'>请上传课程资料再进行操作！</span></div>");
                    // }
                    Notify.danger('请上传课程资料再进行操作！');
                    return;
                }

                resourceBtn.button('submiting').addClass('disabled');
                if(resourceBtn.data('lock') == 1) return false;
                resourceBtn.data('lock',1);
                $.post($form.attr('action'), $form.serialize(), function(result) {
                    result = $.parseJSON(result);
                    if(result.status == 0){
                        resourceBtn.data('lock',0);
                        resourceBtn.removeClass('disabled');
                        Notify.danger(result.info);
                    }else{
                        $modal.modal('hide');
                        Notify.success(result.info);
                        window.location.reload();
                    }
                }).error(function(){
                    resourceBtn.data('lock',0);
                    resourceBtn.removeClass('disabled');
                    Notify.danger('操作失败');
                });
            }
        });

        //标题
        validator.addItem({
            element: '#resource-title-field',
            required: true ,
            rule: "maxlength{max:40}",
            display : '标题',
            errormessageRequired : '请输入资料的标题'
        });
        //上传内容
        validator.addItem({
            element: '[name="media"]',
            required: true,
            rule: 'mediaValueEmpty',
            errormessageRequired:"请上传课程资料"
        });


        //提交
        // var validator = new Validator({
        //     element: '#resource-upload-form',
        //     autoSubmit: false,
        //     failSilently: true,
        //     onFormValidated: function(error, results, $form) {
        //         if (error) {
        //             return false;
        //         }
        //         var url = $("input[name=url]").val();
        //         if(!url){
        //             Notify.danger('请上传资料');
        //             return false;
        //         }
        //         resourceBtn.addClass('disabled');
        //         if(resourceBtn.data('lock') == 1) return false;
        //         resourceBtn.data('lock',1);
        //         $.post($form.attr('action'), $form.serialize(), function(result) {
        //             if(result.status == 0){
        //                 resourceBtn.data('lock',0);
        //                 resourceBtn.removeClass('disabled');
        //                 Notify.danger(result.info);
        //             }else{
        //                 $modal.modal('hide');
        //                 Notify.success(result.info);
        //                 window.location.reload();
        //             }
        //         }).error(function(){
        //             resourceBtn.data('lock',0);
        //             resourceBtn.removeClass('disabled');
        //             Notify.danger('操作失败');
        //         });
        //     }
        // });

        // //标题
        // validator.addItem({
        //     element: '[name="title"]',
        //     required: true,
        //     rule: 'remote'
        // });
        //
        //
        // resource_upload_chooser.uploadify({
        //     queueSizeLimit : 1,
        //     auto: false,
        //     method: "Post",
        //     width: 135,
        //     height: 45,
        //     buttonText : '选择文件',
        //     fileSizeLimit : '1024MB',//上传文件大小限制
        //     fileTypeDesc : '视频文件',
        //     fileTypeExts : '*.ppt;*.pptx;*.zip;*.rar;*.tar.gz;*.rm;*.flv;*.avi;*.mp4;*.mp3;*.wma;*.txt;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.jpg;*.jpeg;*.gif;*.png',//文件类型过滤
        //     buttonClass: 'my_upload_button',
        //     swf : '/Public/assets/libs/jquery-plugin/uploadify/uploadify.swf',
        //     uploader: "",
        //     formData: {
        //         token: uploadToken,
        //     },
        //     fileObjName: "file",
        //     onSelect : function(file){
        //         choose_file_name = file.name;
        //         askUploadToken();
        //         // $('#start-resource-upload').show();
        //     },
        //     onUploadStart : function(file) {
        //         resource_upload_chooser.uploadify('disable', true);
        //         fileInfo = $("#file_upload-queue").find(".fileName").eq(0).text();
        //     },
        //     onUploadSuccess: function(file, data, response){
        //         // 根据返回结果指定界面操作
        //         data = $.parseJSON(data);
        //         if(response){
        //             $("input[name=url]").val(data.url);
        //             Notify.success("文件上传成功！",5);
        //             // $('#start-resource-upload').hide();
        //             $("#resource-upload-chooser").html("<label>文件上传成功：</label><p>" + choose_file_name + "</p>");
        //             $("#resource-upload-chooser").css({'width':'auto','height':'auto'});
        //             $(".uploadify-queue-item").fadeOut();
        //         }else{
        //             Notify.danger("文件上传失败!",5);
        //         }
        //     },
        //     onUploadComplete : function(file) {
        //         resource_upload_chooser.uploadify('disable', false);
        //     },
        //     onUploadError:function(file, errorCode, errorMsg){
        //         console.log("errorCode:"+errorCode);
        //         console.log("errorMsg:"+errorMsg);
        //         Notify.danger("上传失败：" + errorMsg,5);
        //     }
        // });
        //
        //
        // //点击开始上传，上传文件
        // function askUploadToken(){
        //     if(choose_file_name == undefined){
        //         Notify.danger("请选择文件后点击上传！",5);
        //         return;
        //     }
        //
        //     var data = {};
        //     data.name = choose_file_name;
        //     $.ajax({
        //         url: $('input[name="data_params_url"]').val(),
        //         async: false,
        //         dataType: 'json',
        //         data: data,
        //         cache: false,
        //         success: function(response, status, jqXHR) {
        //             resource_upload_chooser.uploadify('settings','uploader',response.url);
        //             resource_upload_chooser.uploadify('upload');
        //         },
        //         error: function(jqXHR, status, error) {
        //             Notify.danger('请求上传授权码失败！',5);
        //         }
        //     });
        // }
        //
        // $('#start-resource-upload').on('click',function(){
        //
        // });


    };

});