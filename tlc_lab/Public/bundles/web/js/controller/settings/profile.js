    define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    require('ckeditor');
    var Notify = require('common/bootstrap-notify');

    require('ajaxfileupload');

    exports.run = function() {
        
        var validator = new Validator({
            element: '#user-profile-form',
            failSilently: true,
            onFormValidated: function(error){
                if (error) {
                    return false;
                }
                $('#profile-save-btn').button('submiting').addClass('disabled');
            }
        });

        validator.addItem({
            element: '[name="avatar"]',
            required: false,
            rule: 'maxsize_image'
        });

        //上传图片
        var uploadFile = '#form_avatar';
        var uploadUrl = $(uploadFile).data('url');
        $(uploadFile).ajaxfileupload({
            'action' : uploadUrl,
            'onComplete' : function (response) {
                cropAvatar(response);
            },
            'onStart' : function () {

            },
            'onCancel' : function () {
                console.log('no file selected');
            },
            'onError' : function(){
                Notify.danger("上传失败",3);
            }
        });

        var crop_modal_btn = $('#crop_modal_btn');

        function cropAvatar(response){
            if(response.error){
                Notify.danger(response.message,3);
                return false;
            }
            Notify.success("图片加载完成",2);
            var url='/User/Settings/avatarCropModalAction/file/'+response.file;
            crop_modal_btn.data('url',url);
            crop_modal_btn.trigger('click');
        }

        // $('#form_avatar').on('change', function(){
        //     if($(this).val() =='')
        //         return false;
        //     //$('#settings-avatar-form').submit();
        //     var options = {
        //         dataType:  'json',
        //         beforeSend: function() {
        //             progress.removeClass("hide");
        //             var percentVal = '0%';
        //             bar.width(percentVal);
        //             percent.text(percentVal);
        //         },
        //         uploadProgress: function(event, position, total, percentComplete) {
        //             var percentVal = percentComplete + '%';
        //             bar.width(percentVal);
        //             percent.text(percentVal);
        //         },
        //         success: function(data) {
        //             if(data.error){
        //                 Notify.danger(data.message,3);
        //                 return false;
        //             }
        //             $('.c-loading-con p').html("图片加载完成");
        //             var url='/User/Settings/avatarCropAction/file/'+data.file;
        //             window.location.href= url;
        //             url ="";
        //         },
        //         error:function(xhr){
        //             $('.c-loading-con p').html("上传失败");
        //         }
        //     };
        //     // 将options传给ajaxSubmit
        //     $('#settings-avatar-form').ajaxSubmit(options);
        // });

    };

});