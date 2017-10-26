define(function(require, exports, module) {
    require("jquery.jcrop-css");
    require("jquery.jcrop");
    require('jquery.form');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $form = $("#avatar-crop-form"),
            $picture = $("#avatar-crop"),
            $modal = $form.parents('.modal');

        var scaledWidth = $picture.attr('width'),
            scaledHeight = $picture.attr('height'),
            naturalWidth = $picture.data('naturalWidth'),
            naturalHeight = $picture.data('naturalHeight'),
            cropedWidth = 220,
            cropedHeight = 220,
            ratio = cropedWidth / cropedHeight,
            selectWidth = 200 * (naturalWidth/scaledWidth),
            selectHeight = 200 * (naturalHeight/scaledHeight);

        $picture.Jcrop({
            trueSize: [naturalWidth, naturalHeight],
            setSelect: [0, 0, selectWidth, selectHeight],
            aspectRatio: ratio,
            onSelect: function(c) {
                $form.find('[name=x]').val(c.x);
                $form.find('[name=y]').val(c.y);
                $form.find('[name=width]').val(c.w);
                $form.find('[name=height]').val(c.h);
            }
        });

        $('.go-back').click(function(){
            $modal.modal('hide');
            $('input[name="avatar"]').val('');
        });

        //保存图片
        $form.ajaxForm({
            dataType : 'json',
            success : function (data) {
                $('.modal').modal('hide');
                $('#avatar-form-group').find('img').attr('src', data.url);
                if(data.uri0 != undefined && data.uri0 != "") {
                    $('#profile_avatar_field0').val(data.uri0);
                }

                if(data.uri1 != undefined && data.uri1 != "") {
                    $('#profile_avatar_field1').val(data.uri1);
                }

                if(data.uri2 != undefined && data.uri2 != "") {
                    $('#profile_avatar_field2').val(data.uri2);
                }
            },
            error:function(){
                Notify.danger("保存头像失败！",2);
            }
        });
    };

});