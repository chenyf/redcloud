define(function(require, exports, module) {
    var DynamicCollection = require('../widget/dynamic-collection4');
    require('jquery.sortable');
    require('ckeditor');
    require("jquery.jcrop-css");
    require("jquery.jcrop");
    require('jquery.form');
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {
		require('./header').run();
                
                var bar = $('#bar em');
                var percent = $('#bar i');
                var progress = $("#progress");
                
                $("#course-picture-field").on("change",function(){
                    if($(this).val() =='') return false;
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
                     success: function(response) {
                             $('.c-loading-con p').html("图片加载完成");
                                var data = response;

                                if(!response.status){
                                    Notify.danger(response.msg);
                                    return false;
                                }

                                $("#course-picture-crops").attr('src',data.pictureUrl);
                                $("#course-picture-crops").attr('width',data.swidth);
                                $("#course-picture-crops").attr('height',data.sheight);
                                $("#course-picture-crops").attr('data-natural-width',data.nwidth);
                                $("#course-picture-crops").attr('data-natural-height',data.nheight);
                                $("#pictureFilePath").val(data.picturePic);
                                if(typeof window.jcrop_api != 'undefined'){
                                    $('.jcrop-holder').remove();
                                    window.jcrop_api.setImage("data.pictureUrl");
                                }
                                getcropLoad();
                                $('#panel-body-atuto').addClass('hide');
                                $("#panel-body-crop").removeClass("hide");
                     },
                     error:function(xhr){
                             Notify.danger('图片上传失败');
                     }
                  };
              // 将options传给ajaxSubmit
                $("#course-picture-form").ajaxSubmit(options);
                });
         
            $(document).on('click','#course-change-pic .pic-list',function(){
                $(this).find('.c-icon-checkd').removeClass('hide');
                $(this).siblings().find('.c-icon-checkd').addClass('hide');
                $(this).addClass('active');
                $(this).siblings().removeClass('active');
            }) ;
            
            $('#picModel').on('click',function(){
                $('#tabRight').removeClass('hide');
            });
            $('#picAuto').on('click',function(){
                $('#tabRight').addClass('hide');
            });
            $('#create-course-btn').on('click',function(){
                var choiceImg = $('#course-change-pic .active img');
                var imgVal = choiceImg.data("value");
                var imgArr = [ 1 , 2 , 3 ]; 
                $('.modal-header .close').trigger("click");
                if($.inArray(imgVal ,imgArr) != -1 ){
                    $('#course-pic .pic-list').find('.c-icon-checkd').addClass('hide');
                    $('#course-pic .pic-list').eq(imgVal).find('.c-icon-checkd').removeClass('hide');
                    $('#course-pic .pic-list').eq(imgVal).addClass('active');
                    $('#course-pic .pic-list').eq(imgVal).siblings().removeClass('active');
                    $("#selectPicture").val(imgVal);
                    return false;
                };
                $('#course-pic .pic-list').eq(0).find('img').attr('alt',imgVal);
                $('#course-pic .pic-list').eq(0).find('img').attr('src',choiceImg.attr("src"));
                $('#course-pic .pic-list').find('.c-icon-checkd').addClass('hide');
                $('#course-pic .pic-list').eq(0).find('.c-icon-checkd').removeClass('hide');
                $('#course-pic .pic-list').eq(0).addClass('active');
                $('#course-pic .pic-list').eq(0).siblings().removeClass('active');
                $("#selectPicture").val(imgVal);
                
            });
		
               
            function getcropLoad(){
              var $formcrop = $("#course-picture-crops-form"),
                  $picturecrop = $("#course-picture-crops");
          
              var scaledWidth = $picturecrop.attr('width'),
                  scaledHeight = $picturecrop.attr('height'),
                  naturalWidth = $picturecrop.data('naturalWidth'),
                  naturalHeight = $picturecrop.data('naturalHeight'),
                  cropedWidth = 480,
                  cropedHeight = 270,
                  ratio = cropedWidth / cropedHeight,
                  selectWidth = 360 * (naturalWidth/scaledWidth),
                  selectHeight = 202.5 * (naturalHeight/scaledHeight);
             window.jcrop_api =   $.Jcrop('#course-picture-crops',{
                    boundary : -2,
                    keySupport : false,
                    trueSize: [naturalWidth, naturalHeight],
                    setSelect: [0, 0, selectWidth, selectHeight],
                    aspectRatio: ratio,
                    boxWidth:480,
                    boxHeight:270,
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


            $('.go-back').click(function(){
                 $("#course-picture-field").val("");
                 $("#progress").addClass("hide");
                 $('#panel-body-atuto').removeClass('hide');
                 $("#panel-body-crop").addClass("hide");
              });
              
           $('#course-picture-crops-form').submit(function() {
            $.post($('#course-picture-crops-form').attr('action'), $('#course-picture-crops-form').serialize(), function(response) {
                
                if (response.status == 'ok') {
                    $('.modal-header .close').trigger("click");
                    if (response.edit == 1){
                       $("#headerPicture").attr('src',response.data.largePicture); 
                       window.location.reload();
                    }else{
                       $('#course-pic .pic-list').eq(0).find('img').attr('alt',response.data.title);
                       $('#course-pic .pic-list').eq(0).find('img').attr('src',response.data.largePicture);
                       $('#course-pic .pic-list').find('.c-icon-checkd').addClass('hide');
                       $('#course-pic .pic-list').eq(0).find('.c-icon-checkd').removeClass('hide');
                       $('#course-pic .pic-list').eq(0).addClass('active');
                       $('#course-pic .pic-list').eq(0).siblings().removeClass('active');
                       $("#selectPicture").val("");
                    }
                }else{
                    Notify.danger('图片更新失败');
                }
            }, 'json');
            return false;
        });
           
    };

});