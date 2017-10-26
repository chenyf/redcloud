define(function(require, exports, module) {

    require("jquery.uploadify");
    require("jquery.uploadify.css");

//执行文件上传
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require('bootstrap.datetimepicker');

   function flashChecker() {
    var hasFlash = 0; //是否安装了flash
    var flashVersion = 0; //flash版本
    if (document.all) {
      try{
         var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');     
      }catch(e){
          alert(e+"\n"+"解决办法：\n  下载一个flash插件");
      }
        if (swf) {
          hasFlash = 1;
          VSwf = swf.GetVariable("$version");
          flashVersion = parseInt(VSwf.split(" ")[1].split(",")[0]);
        }
      } else {
        if (navigator.plugins && navigator.plugins.length > 0) {
          var swf = navigator.plugins["Shockwave Flash"];
          if (swf) {
            hasFlash = 1;
            var words = swf.description.split(" ");
            for (var i = 0; i < words.length; ++i) {
              if (isNaN(parseInt(words[i]))) continue;
              flashVersion = parseInt(words[i]);
            }
          }
        }
      }
      return { f: hasFlash, v: flashVersion };
    }
    var fls = flashChecker();
    if (!fls.f) $("#flash").css('display','block'),$("#notice").css('display','none');


    exports.run = function() { 
        var $form = $('#class_file');
        var url = $form.data("url");
        
        $('#file_upload').uploadify({
            fileTypeExts: '*.rar; *.zip;',
            fileSizeLimit: '50MB',
            queueSizeLimit: 1,
            height: 30,
            swf: '/Public/assets/libs/jquery-plugin/uploadify/uploadify.swf',
            uploader: url,
            width: 120,
            buttonText: '请选择文件',
            overrideEvents : [ 'onDialogClose', 'onUploadError', 'onSelectError' ],
            onUploadSuccess: function(msg, data) {
                $('#Rarurl').val(data);
                $('#filename').val(msg.name);
                $('#filesize').val(msg.size);
//                    $('#filesize').attr('value',msg.size);
                setTimeout(function() {
                    $('#display').css('display', 'block')
                }, 3000);
            },
            onSelectError: function(file, errorCode, errorMsg) {
//           文件上传失败的错误信息，大小，类型，上传数量
                var msgText = "上传失败\n";
                switch (errorCode) {
                    case SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED:
                        //this.queueData.errorMsg = "每次最多上传 " + this.settings.queueSizeLimit + "个文件";
                        msgText += "每次最多上传 " + this.settings.queueSizeLimit + "个文件";
                        break;
                    case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
                        msgText += "文件大小超过限制( " + this.settings.fileSizeLimit + " )";
                        break;
                    case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
                        msgText += "文件大小为0";
                        break;
                    case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
                        msgText += "文件格式不正确，仅限 " + this.settings.fileTypeExts;
                        break;
                    default:
                        msgText += "错误代码：" + errorCode + "\n" + errorMsg;
                }
                alert(msgText);
            }

        });
   
        $('#save-confirm').click(function() {
            $('#file_upload').uploadify('upload', '*');
        })

        $("#reset").click(function() {
            $('#display').css('display', 'none')
        })

        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                }
                $('#save-confirm').button('submiting').addClass('disabled');
                if ($('#save-confirm').data("lock") == 1)
                    return false;
                $('#save-confirm').data("lock", 1);
                $.post($form.attr('action'), $form.serialize(), function(data) {

                    if (data.status >= 1) {
                        Notify.success(data.info);
                        location.href = data.url;
                    } else {
                        Notify.danger(data.info);
                    }
                    setTimeout(function() {
                        $('#save-confirm').data("lock", 0);
                    }, 3000)
                });
            }
        })
        validator.addItem({
            element: '[name="title"]',
            required: true,
            rule: 'minlength{min:2} maxlength{max:20}'
        });
        validator.addItem({
            element: '[name="uploadUrl"]',
            required: true,
            errormessageRequired: '请选择一个文件'
        });
    }

});