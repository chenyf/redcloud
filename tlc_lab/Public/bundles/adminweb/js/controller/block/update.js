define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    var Uploader = require('upload');
    require('common/validator-rules').inject(Validator);
    require('colorpicker/colorpicker');
    require('jquery.form');

    exports.run = function() {
        
        var $form = $('#block-form');
        var $modal = $form.parents('.modal');
        var $table = $('#block-table');

        $form.find('.upload-img').each(function(index, el) {

            var uploader = new Uploader({
                trigger: $(el),
                name: 'file',
                action: $(el).data('url'),
                data: {'_csrf_token': $('meta[name=csrf-token]').attr('content') },
                accept: 'image/*',
                error: function(file) {
                    Notify.danger('上传图片失败，请重试！')
                },
                success: function(response) {
                    response = $.parseJSON(response);
                    $(el).siblings('a').attr('href', response.url);
                    $(el).siblings('a').show();
                    $(el).siblings('input').val(response.url);
                    $(el).siblings('button').show();
                    Notify.success('上传图片成功！');
                }
            });

        });

        $form.find('.upload-img-del').each(function(index, el) {
            $(el).on('click', function(event) {
                $(this).siblings('span').html('');
                $(this).siblings('input').val('');
                $(this).hide();
                $(this).siblings('a').hide();
                Notify.success('删除图片成功！');
            });
        });

        $form.submit(function() {
            var isThemes = $('#isTheme').val();
            var valueSmall = '';
            var values = '';
            if(isThemes=='yes'){
                 $.each($('#blockInputSmall .controls'), function(i, n){
                   valueSmall += $('#blockInputSmall .controls').eq(i).find('.content').val()+'\n';
                 });
                 
                 $.each($('#blockInput .controls'), function(i, n){
                     values += $('#blockInput .controls').eq(i).find('.content').val()+'\n';
                 });
                 values = values.substr(0,values.length-1);
                 valueSmall = valueSmall.substr(0,valueSmall.length-1);
                 $("#blockContentSmall").val(valueSmall);
                 $("#blockContent").val(values);        
               }
          
            $('#block-update-btn').button('submiting').addClass('disabled');
            $.post($form.attr('action'), $form.serialize(), function(response) {
                if (response.status == 'ok') {
                    var $html = $(response.html);
                    if ($table.find('#' + $html.attr('id')).length > 0) {
                        $('#' + $html.attr('id')).replaceWith($html);
                        Notify.success('更新成功！');
                    } else {
                        $table.find('tbody').prepend(response.html);
                        Notify.success('提交成功!');
                    }
                    $modal.modal('hide');
                }
            }, 'json');
            return false;
        });
        
       $('.close').on('click', function(event) {
           $("#colorpanel").hide();
        });     
        //by yjl
//        $("#block-form").find("input[type=radio]").on('click', function() {
//            var type = $("#block-form").find("input[type=radio]:checked").val();
//            if(type=="small"){
//                $("#blockContent").css("display","none");
//                $("#blockContentSmall").css("display","block");
//            }else if(type=='big'){
//                $("#blockContent").css("display","block");
//                $("#blockContentSmall").css("display","none");
//            }
//        })
        
        $("#ImgTab >li").on('click', function() {
            $(this).addClass("active").siblings().removeClass("active");
            var type = $(this).attr("type");
            if(type=="small"){
//                $("#blockContent").css("display","none");
//                $("#blockContentSmall").css("display","block");
                $("#blockInput").css("display","none");
                $("#blockInputSmall").css("display","block");
            }else if(type=='big'){
//                $("#blockContent").css("display","block");
//                $("#blockContentSmall").css("display","none");
                $("#blockInput").css("display","block");
                $("#blockInputSmall").css("display","none");
            }
            $("#colorpanel").hide();
        })

         
         
          $("#block-image-upload-form").submit(function(){
            var $uploadForm = $(this);
            var isTheme = $('#isTheme').val();
//            var type = $("#block-form").find("input[type=radio]:checked").val();
            var type = $("#ImgTab").find("li[class=active]").attr("type");
            var file = $uploadForm.find('[name=file]').val();
            if (!file) {
                Notify.danger('请先选择要上传的图片');
                return false;
            }

            $uploadForm.ajaxSubmit({
                clearForm: true,
                dataType:'json',
                success: function(response){
                     if(isTheme=='no'){
                        var htmlTheme = '<a href="#" yanse=""><img src="' + response.url + '"></a>';
                         $("#blockContent").val($("#blockContent").val() + '\n' + htmlTheme);
                     }else{
                       if(typeof($('#blockInput .controls').eq(0).find('.content').val()) == "undefined"){
                         var  blength =0;
                       }else{
                        var blength =  $('#blockInput .controls').last().find('.content').attr('id');
                        blength = blength.replace('imageColor',"");
                        blength++;  
                       }
                      if(typeof($('#blockInputSmall .controls').eq(0).find('.content').val()) == "undefined"){
                           var slength = 0; 
                       }else{
                           var slength =  $('#blockInputSmall .controls').last().find('.content').attr('id');
                           slength = slength.replace('imageColorSmall',"");
                           slength++;
                       }
                   
                    
                        
                    var resurl = "<a href='#' yanse=''><img src='"+response.url+"'></a>";
                    var resurlSmall = "<a href='#' yanse=''  data-img=''><img src='"+response.url+"'></a>";
                    
                    var bigHtml = '<div class="controls" style="margin:5px 0;"><input class="form-control content" id="imageColor'+blength+'" type="text" name="content" value="'+resurl+'"  style="width:350px;display:inline-block;"><div style="width:110px;display:inline-block;margin:0 5px;"><select id="colorImg'+blength+'" class="form-control colorImg" name="beijing"><option class="beijing"  value="beiColor">背景颜色</option></select></div><button class="btn btn-default delete-btn blockDel" id="blockDel'+blength+'" >删除</button><div style="display:inline-block;margin:0 5px;" id="choiceColor'+blength+'" class="choiceColor hide" ><input style="display:inline-block;width:90px;" type="text" id="themeFontColor'+blength+'"  class="form-control blockTheme"><img src="/Public/assets/libs/colorpicker/colorpicker.png" id="imgFontColor'+blength+'" class="imgFontColor" style="cursor:pointer;margin:0 0 0 10px;"/></div></div>';
                    var smallHtml = '<div class="controls" style="margin:5px 0;"><input class="form-control content" id="imageColorSmall'+slength+'" type="text" name="content" value="'+ resurlSmall+'"  style="width:350px;display:inline-block;"><div style="width:110px;display:inline-block;margin:0 5px;"><select id="colorImgSmall'+slength+'" class="form-control colorImgSmall" name="beijing"><option class="beiImgSmall"  value="beiImg">背景图片</option><option class="beijingSmall" value="beiColor">背景颜色</option></select></div><button class="btn btn-default delete-btn blockDel" id="blockDelSmall'+slength+'" >删除</button><div style="display:inline-block;" class="imgFileSmall" id="imgFileSmall'+slength+'" ><form id="block-image-upload-formSmall'+slength+'" class="block-image-upload-form" action="/Home/File/uploadAction/group/default"  method="post" enctype="multipart/form-data"><input class="btn btn-default" type="file" name="file" value="上传" style="display:inline-block;margin:0 5px;width:200px;"><button id="subTypeSmall'+slength+'" type="big" class="btn btn-default">上传图片</button></form></div><div style="display:inline-block;margin:0 5px;" id="choiceColorSmall'+slength+'" class="choiceColorSmall hide" ><input style="display:inline-block;width:90px;" type="text" id="themeFontColorSmall'+slength+'"  class="form-control blockThemeSmall"><img src="/Public/assets/libs/colorpicker/colorpicker.png" id="imgFontColorSmall'+slength+'" class="imgFontColorSmall" style="cursor:pointer;margin:0 0 0 10px;"/></div></div>';
                     if(type=='small'){
                         $("#blockInputSmall").append(smallHtml);
                     }else{
                         $("#blockInput").append(bigHtml);
                     }
                     
                    }
                    getBlockLoad();  
//                    $("#blockContent").val($("#blockContent").val() + '\n' + html);
                    Notify.success('插入图片成功！');
                },
                error: function(response) {
                    Notify.danger('上传图片失败，请重试！');
                }
            });

            return false;
        });

//        $('.btn-recover-content').on('click', function() {
//            var html = $(this).parents('tr').find('.data-role-content').text();
//            $("#blockContent").val(html);
//        });
        
        $('.btn-recover-content').on('click', function() {
            var type =$(this).attr("type");
            if(type=='big'){
                var html = $(this).parents('tr').find('.big-data-role-content').text();
                $("#blockContent").val(html);
            }else if(type=='small'){
                var html = $(this).parents('tr').find('.small-data-role-content').text();
                $("#blockContentSmall").val(html);
            }
        });
        
      
        
         

        $('.btn-recover-template').on('click', function() {
            var html = $(this).parents('tr').find('.data-role-content').text();
            var templates = $.parseJSON(html);

            $form.find("input").each(function(index, el) {

                if (templates != null ) {
                $.each(templates,function(n,value) {
                    if ($(el).attr('name') == n ) {
                        $(el).val(value);
                        $(el).siblings('a').attr('href', value);
                        if($(el).siblings('a').attr('href')) {
                            $(el).siblings('a').show();
                            $(el).siblings('.upload-img-del').show();
                        } else {
                            $(el).siblings('a').hide();
                            $(el).siblings('.upload-img-del').hide();
                        }
                    };
                });
              };
            });
        });
       
       
        getBlockLoad();   
          
    };
    
function getBlockLoad() {
    if($('#blockInput .controls').eq(0).find('.content').val() ==''){
          $('#blockInput .controls').eq(0).find('.content').parent().remove();  
     }
    if($('#blockInputSmall .controls').eq(0).find('.content').val() ==''){
          $('#blockInputSmall .controls').eq(0).find('.content').parent().remove();  
     }
    // $('#beiColor'+i).attr({selected:"selected"});
    $.each($('#blockInputSmall .controls'), function(i, n){
        var str = $('#blockInputSmall .controls').eq(i).find('.content').val();
         str.replace(/yanse=[\"|'](.*?)[\"|']/gi, function() { 
         if(arguments[1].length == 7){
             $('#blockInputSmall .controls').eq(i).find('.beijingSmall').attr({selected:"selected"});
             $('#blockInputSmall .controls').eq(i).find('.blockThemeSmall').val(arguments[1]);
             $('#blockInputSmall .controls').eq(i).find('.blockThemeSmall').css("color",arguments[1]);  
             $('#blockInputSmall .controls').eq(i).find('.imgFileSmall').addClass("hide");
             $('#blockInputSmall .controls').eq(i).find('.choiceColorSmall').removeClass("hide");
         }
     }); 
     
   });
    $.each($('#blockInput .controls'), function(i, n){
        var str = $('#blockInput .controls').eq(i).find('.content').val();
         str.replace(/yanse=[\"|'](.*?)[\"|']/gi, function() { 
         if(arguments[1].length == 7){
            $('#blockInput .controls').eq(i).find('.beijing').attr({selected:"selected"});
            $('#blockInput .controls').eq(i).find('.blockTheme').val(arguments[1]);
            $('#blockInput .controls').eq(i).find('.blockTheme').css("color",arguments[1]); 
            $('#blockInput .controls').eq(i).find('.imgFile').addClass("hide");
            $('#blockInput .controls').eq(i).find('.choiceColor').removeClass("hide");
         }
     }); 
     
   });
   
   
    $.each($('#blockInputSmall .controls'), function(i, n){
            $('#blockInputSmall .controls').eq(i).find('.imgFontColorSmall').colorpicker({
            fillcolor:true,
            event:'mouseover',
            target:$('#blockInputSmall .controls').eq(i).find('.blockThemeSmall'),
            success:function(o,color){ 
                 var htm = $('#blockInputSmall .controls').eq(i).find('.content').val();
                 htm = htm.replace(/yanse=[\"|'](.*?)[\"|']/gi,"yanse='"+color+"'");
                 $('#blockInputSmall .controls').eq(i).find('.content').val(htm);
                }
        });
        $('#blockInputSmall .controls').eq(i).find('.blockThemeSmall').on('change', function(event) {
                 var vhtm = $('#blockInputSmall .controls').eq(i).find('.content').val();
                 vhtm = vhtm.replace(/yanse=[\"|'](.*?)[\"|']/gi,"yanse='"+$(this).val()+"'");
                 $('#blockInputSmall .controls').eq(i).find('.content').val(vhtm);
        });
   });
   
    $.each($('#blockInput .controls'), function(i, n){
            $('#blockInput .controls').eq(i).find('.imgFontColor').colorpicker({
            fillcolor:true,
            event:'mouseover',
            target:$('#blockInput .controls').eq(i).find('.blockTheme'),
            success:function(o,color){ 
                 var htm = $('#blockInput .controls').eq(i).find('.content').val();
                 htm = htm.replace(/yanse=[\"|'](.*?)[\"|']/gi,"yanse='"+color+"'");
                 $('#blockInput .controls').eq(i).find('.content').val(htm);
                }
        });
        $('#blockInput .controls').eq(i).find('.blockTheme').on('change', function(event) {
                 var vvhtm = $('#blockInput .controls').eq(i).find('.content').val();
                 vvhtm = vvhtm.replace(/yanse=[\"|'](.*?)[\"|']/gi,"yanse='"+$(this).val()+"'");
                 $('#blockInput .controls').eq(i).find('.content').val(vvhtm);
        });
   });

    $.each($('#blockInputSmall .controls'), function(i, n){
            $('#blockInputSmall .controls').eq(i).find('.colorImgSmall').on('click', function() {
            var vals =$(this).val();
            if(vals=='beiImg'){
                 $('#blockInputSmall .controls').eq(i).find('.choiceColorSmall').addClass("hide");
                 $('#blockInputSmall .controls').eq(i).find('.imgFileSmall').removeClass("hide");
            }else if(vals=='beiColor'){
               $('#blockInputSmall .controls').eq(i).find('.imgFileSmall').addClass("hide");
               $('#blockInputSmall .controls').eq(i).find('.choiceColorSmall').removeClass("hide");
            }
           
             });
         });
         
          $.each($('#blockInput .controls'), function(i, n){
            $('#blockInput .controls').eq(i).find('.colorImg').on('click', function() {
            var vals =$(this).val();
            if(vals=='beiImg'){
                 $('#blockInput .controls').eq(i).find('.choiceColor').addClass("hide");
                 $('#blockInput .controls').eq(i).find('.imgFile').removeClass("hide");
            }else if(vals=='beiColor'){
               $('#blockInput .controls').eq(i).find('.imgFile').addClass("hide");
               $('#blockInput .controls').eq(i).find('.choiceColor').removeClass("hide");
            }
           
        });
         });
       
        $("#colorpanel").css({zIndex: 1999});

        $.each($('#blockInputSmall .controls'), function(i, n){
            $('#blockInputSmall .controls').eq(i).find('.block-image-upload-form').submit(function(){
            var $uploadFormSmall = $(this);
//            var type = $("#block-form").find("input[type=radio]:checked").val();
            var type = $("#ImgTab").find("li[class=active]").attr("type");
            var file = $uploadFormSmall.find('[name=file]').val();
            if (!file) {
                Notify.danger('请先选择要上传的图片');
                return false;
            }

            $uploadFormSmall.ajaxSubmit({
                clearForm: true,
                dataType:'json',
                success: function(response){
                 var htmlDataSmall =  $('#blockInputSmall .controls').eq(i).find('.content').val();
                 htmlDataSmall = htmlDataSmall.replace(/data-img=[\"|'](.*?)[\"|']/gi,"data-img='"+response.url+"'");
                 $('#blockInputSmall .controls').eq(i).find('.content').val(htmlDataSmall); 
//                    $("#blockContent").val($("#blockContent").val() + '\n' + html);
                    Notify.success('插入图片成功！');
                },
                error: function(response) {
                    Notify.danger('上传图片失败，请重试！');
                }
            });

            return false;
             });
         });
         
          $.each($('#blockInputSmall .controls'), function(i, n){
             $('#blockInputSmall .controls').eq(i).find('.blockDel').on('click', function() {
                  $(this).parent().remove();
             });
         });
         
          $.each($('#blockInput .controls'), function(i, n){
              $('#blockInput .controls').eq(i).find('.blockDel').on('click', function() {
                  $(this).parent().remove();
             });
         });
}

});