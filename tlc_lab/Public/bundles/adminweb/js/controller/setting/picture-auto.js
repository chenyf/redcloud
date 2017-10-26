define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("jquery.jcrop-css");
    require("jquery.jcrop");
    require('jquery.form');

    exports.run = function() {
        
        //author fubaosheng 2016-03-04
        
        var bar = $('#bar em');
        var percent = $('#bar i');
        var progress = $("#progress");
        var type = $("#type").val();
        var typeStr,indexEle,indexPicEle;
        if(type == "login"){
            typeStr = "login";
            indexEle = "backgroundImgIndex";
            indexPicEle = "backgroundImg";
        }
        if(type == "registerSuccess"){
            typeStr = "register-success";
            indexEle = "registerSuccessBackgroundPicIndex";
            indexPicEle = "registerSuccessBackgroundPic";
        }
        if(type == "registerPoster"){
            typeStr = "register-poster";
            indexEle = "registerPosterBackgroundPicIndex";
            indexPicEle = "registerPosterBackgroundPic";
        }
        //table 切换 因此确定和取消按钮
         $('#picModel').on('click',function(){
            $('#tabRight').removeClass('hide');
        });
        $('#picAuto').on('click',function(){
            $('#tabRight').addClass('hide');
        });
        
        //点击 选择默认图片
        $(document).on('click','#'+typeStr+'-change-pic .pic-list',function(){
            var self = $(this);
            var pic_list = $("#"+typeStr+"-change-pic .pic-list");
            pic_list.removeClass("active");
            pic_list.find(".c-icon-checkd").addClass("hide");
            self.addClass("active");
            self.find(".c-icon-checkd").removeClass("hide");
        });
        
        //点击 确定
        $('#create-login-btn').on('click',function(){
            var choiceImg = $('#'+typeStr+'-change-pic .active img');
            var imgVal = choiceImg.data("value");
            var imgArr = [ 1 , 2  ];
            var pic_list =  $('#'+typeStr+'-pic .pic-list');
            pic_list.removeClass("active");
            pic_list.find(".c-icon-checkd").addClass("hide");
            if($.inArray(imgVal ,imgArr) != -1 ){
                pic_list.eq(imgVal).addClass("active");
                pic_list.eq(imgVal).find(".c-icon-checkd").removeClass("hide");
            }else{
                pic_list.eq(0).addClass("active");
                pic_list.eq(0).find(".c-icon-checkd").removeClass("hide");
                pic_list.eq(0).find("."+typeStr+"-picture").attr("src",choiceImg.attr("src")).data("value",imgVal);
            }
            $("#"+indexEle).val(imgVal);
            $('.modal-header .close').trigger("click");
        });
        
        // 点击 重新选择图片
        $('.go-back').click(function(){
            progress.addClass("hide");
            $("#login-picture-field").val("");
            $("#panel-body-crop").addClass("hide");
            $("#select-pic").show();
            $('#panel-body-atuto').removeClass('hide');
        });
        
        //上传图片
        $("#login-picture-field").on("change",function(){
            if( $(this).val() == '' )
                return false;
            
            var options = {   
                dataType:  'json',
                beforeSend: function() {
                    progress.removeClass("hide");
                    var percentVal = '0%';
                    bar.width(percentVal);
                    percent.text(percentVal);
                 },
                 uploadProgress: function(event, position, total, percentComplete) {
                    $("#select-pic").hide();
                    var percentVal = percentComplete + '%';
                    bar.width(percentVal);
                    percent.text(percentVal);
                 },
                 success: function(response) {
                    if(response.status == "error"){
                        Notify.danger(response.msg,5);
                        $('.go-back').trigger("click");
                    }else{
                        $('.c-loading-con p').html("图片加载完成");
                        var data = response;
                        $("#login-picture-crops").attr('src',data.pictureUrl);
                        $("#login-picture-crops").attr('width',data.swidth);
                        $("#login-picture-crops").attr('height',data.sheight);
                        $("#login-picture-crops").attr('data-natural-width',data.nwidth);
                        $("#login-picture-crops").attr('data-natural-height',data.nheight);
                        $("#pictureFilePath").val(data.picturePic);
                        if(typeof window.jcrop_api != 'undefined'){
                            $('.jcrop-holder').remove();
                            window.jcrop_api.setImage("data.pictureUrl");
                        }
                        getcropLoad();
                        $('#panel-body-atuto').addClass('hide');
                        $("#panel-body-crop").removeClass("hide");
                    }
                 },
                 error:function(xhr){
                    Notify.danger('图片上传失败');
                 }
            };
            $("#login-picture-form").ajaxSubmit(options);
        });

        function getcropLoad(){
            var $formcrop = $("#login-picture-crops-form"),
                $picturecrop = $("#login-picture-crops"),
                scaledWidth = $picturecrop.attr('width'),
                scaledHeight = $picturecrop.attr('height'),
                naturalWidth = $picturecrop.data('naturalWidth'),
                naturalHeight = $picturecrop.data('naturalHeight');
            var cropedWidth = 797, 
                cropedHeight = 330,
                selectWidth = 797,
                selectHeight = 330;
            if(type == "registerPoster"){
                var cropedWidth = 256, 
                    cropedHeight = 405,
                    selectWidth = 256,
                    selectHeight = 405;
            }
            var ratio = cropedWidth / cropedHeight,
                selectWidth = selectWidth * (naturalWidth/scaledWidth),
                selectHeight = selectHeight * (naturalHeight/scaledHeight);
          
                window.jcrop_api =   $.Jcrop('#login-picture-crops',{
                    boundary : -2,
                    keySupport : false,
                    trueSize: [naturalWidth, naturalHeight],
                    setSelect: [0, 0, selectWidth, selectHeight],
                    aspectRatio: ratio,
                    boxWidth:cropedWidth,
                    boxHeight:cropedHeight,
                    onSelect: function(c) {
                        c.x = parseInt(c.x)<0 ? 0 : c.x;
                        c.y = parseInt(c.y)<0 ? 0 : c.y;
                        if( parseInt(c.x)<0 || parseInt(c.y)<0 ){
                            this.release();
                        }else{
                            $formcrop.find('[name=x]').val(c.x);
                            $formcrop.find('[name=y]').val(c.y);
                            $formcrop.find('[name=width]').val(c.w);
                            $formcrop.find('[name=height]').val(c.h);
                        }
                    }
                });    
            }

        //截取图片
        var loginCropsForm = $('#login-picture-crops-form');
        loginCropsForm.submit(function() {
            var url = loginCropsForm.attr('action');
            var data = loginCropsForm.serialize();
            $.post(url, data, function(response) {
                if(response.status == 'ok') {
                    $('.modal-header .close').trigger("click");
                    var pic_list = $('#'+typeStr+'-pic .pic-list');
                    pic_list.removeClass("active");
                    pic_list.find(".c-icon-checkd").addClass("hide");
                    pic_list.eq(0).addClass("active");
                    pic_list.eq(0).find(".c-icon-checkd").removeClass("hide");
                    pic_list.eq(0).find("."+typeStr+"-picture").attr("src",response.data).data("value",0);
                    $("#"+indexEle).val(0);
                    $("#"+indexPicEle).val(response.data);
                }else{
                    Notify.danger('图片更新失败');
                }
            }, 'json');
            return false;
        });

    };

});