define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    require('jquery.form');

    exports.run = function() {
        var validator = new Validator({
            element: '#settings-avatar-form'
        });

        validator.addItem({
            element: '[name="form[avatar]"]',
            required: true,
            rule: 'maxsize_image',
            requiredErrorMessage: '请选择要上传的头像文件。'
        });

        var bar = $('#bar em');
        var percent = $('#bar i');
        var progress = $("#progress");
        
        $('#form_avatar').on('change', function(){
           if($(this).val() =='')
                    return false;
           //$('#settings-avatar-form').submit();
           var options = {   
                dataType:  'json',
		        beforeSend: function() {
                    progress.removeClass("hide");
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.text(percentVal);
    		    },
                uploadProgress: function(event, position, total, percentComplete) {
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.text(percentVal);
                },
		        success: function(data) {
                        $('.c-loading-con p').html("图片加载完成");
                        var url='/User/Settings/avatarCropAction/file/'+data.file;
                        window.location.href= url;
                        url ="";
                },
		error:function(xhr){
			$('.c-loading-con p').html("上传失败");
		}
             };
         // 将options传给ajaxSubmit
           $('#settings-avatar-form').ajaxSubmit(options);
        });

/*old code qzw 2015-09-19*/
//        $('.use-partner-avatar').on('click', function(){
//            var goto = $(this).data('goto');
//            $.post($(this).data('url'), function(){
//                window.location.href = goto;
//            });
//        });

    };

});