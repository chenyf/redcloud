define(function(require, exports, module) {
    var DynamicCollection = require('../widget/dynamic-collection4');
    require('jquery.sortable');
    require('ckeditor');
    require("jquery.jcrop-css");
    require("jquery.jcrop");
    require('jquery.form');
    require('jquery.select2-css');
    require('jquery.select2');
    var Notify = require('common/bootstrap-notify');

	exports.run = function() {
                
           
            $('#success-sub .pture').on('click',function(){
                $('.modal-header .close').trigger("click");
                window.location.reload();
            });
            
           
        $("textarea").focus(function(){
            $('#type-error').addClass("hide");
            $('#content-error').addClass("hide");
          });
          
        $("select").focus(function(){
            $('#type-error').addClass("hide");
            $('#content-error').addClass("hide");
          });    
          
          
          
                var bar = $('#bar em');
                var percent = $('#bar i');
                var progress = $("#progress");
                
                $("#problem-picture").on("change",function(){
                    if($(this).val() =='') return false;
                    $('#pic-error').addClass("hide");
                $('#problem-form').attr('action','/System/Feedback/problemPictureAction');
                var options = {   
                     dataType:  'json',
                     beforeSend: function() {
//                             progress.removeClass("hide");
//                             var percentVal = '0%';
//                             bar.width(percentVal);
//                             percent.text(percentVal);
                     },
                     uploadProgress: function(event, position, total, percentComplete) {
//                             var percentVal = percentComplete + '%';
//                             bar.width(percentVal);
//                             percent.text(percentVal);
                     },
                     success: function(response) {
                             $('.c-loading-con p').html("图片加载完成");
                            var data = response;
                            if(data.status == 'success'){
                               $('.problem-photo img').attr('src',data.pictureUrl);
                               $('#problemPic').val(data.pictureUrl); 
                               $('.problem-photo img').css('width',data.width+'px');
                               $('.problem-photo img').css('height',data.height+'px');
                               progress.addClass("hide");
                            }else{
                                $('#pic-error').html('');
                                $('#pic-error').html(data.info);
                                $('#pic-error').removeClass('hide');
                            }
                            
                               
                     },
                     error:function(xhr){
                             Notify.danger('图片上传失败');
                     }
                  };
                  
                  
              // 将options传给ajaxSubmit
                $("#problem-form").ajaxSubmit(options);
                $('#problem-form').attr('action','/System/Feedback/indexAction');
                });
                
                $("#problem-submit").on('click',function(){
                   $("#problem-form").submit(function() {
                       
                    $.post($("#problem-form").attr('action'),$("#problem-form").serialize(), function(data) {
                        
                            if(data.status == 'OK'){
                               $("#problem-form").addClass('hide');
                               $('#success-sub').removeClass("hide");
                               window.pcountdown = setInterval("problemClose()", 1000);
                            }else{
                                $('#'+ data.type+'-error').html('');
                                $('#'+ data.type+'-error').html(data.info);
                                $('#'+ data.type+'-error').removeClass("hide");
                            }
                    },'json');
                     return false;
                    }); 
                })
                
        
        
    };
    
   
    

});