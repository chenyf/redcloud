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
                
            $('#binding-change .again').on('click',function(){
                $('#binding').removeClass('hide');
                $('#binding-change').addClass('hide');
                $('#prompt').addClass("hide");
                
                $('#nickname')[0].focus();
            });
            
            $('#binding-change .assist').on('click',function(){
                $('#studnum-form').addClass("hide");
                $("#teacher-from").removeClass('hide');
            });
            
            $('#confirm-true').on('click',function(){
                $('.modal-header .close').trigger("click");
                window.location.reload();
            });
            
            $('#confirm-true-teacher').on('click',function(){
                $('.modal-header .close').trigger("click");
                window.location.reload();
            });
            
           
           $('#studnum-form').submit(function() {
            $.post($(this).attr('action'), $(this).serialize(), function(response) {
                var html = '';
                $('#binding').addClass('hide');
                
                if(response.status == 'success'){
                   html = '<span class="c-text-ok">'+ response.info +'</span>';
                   $('#prompt').removeClass("hide");
                   $('#prompt').html(html); 
                   $('#confirm-true').removeClass('hide');
                   var countdown = setInterval("confirmClose()", 1000); 
                }else{
                   html = '<span class="c-text-error">'+ response.info +'</span>';
                   $('#prompt').removeClass("hide");
                   $('#prompt').html(html);
                   $('#binding-change').removeClass('hide');
                   
                }
                
            }, 'json');
            return false;
        });
        
        $("input").focus(function(){
            $('#prompt').addClass("hide");
            $('#prompts').addClass("hide");
          });
        
//        $('#user-picture').on('change', function(){
//           if($(this).val() =='')
//                    return false;
//           $('#teacher-from').attr('action','/User/Settings/applyPictureAction');
//           $('#teacher-from').submit();
//           $('#teacher-from').attr('action','/User/Settings/studApplyAction');
//      });
      
                var bar = $('#bar em');
                var percent = $('#bar i');
                var progress = $("#progress");
                
                $("#user-picture").on("change",function(){
                    if($(this).val() =='') return false;
                $('#teacher-from').attr('action','/User/Settings/applyPictureAction');
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
                            if(data.status == 'success'){
                               $('.c-student-photo img').attr('src',data.pictureUrl);
                               $('#userpic').val(data.pictureUrl); 
                               $('.c-student-photo img').css('width',data.width+'px');
                               $('.c-student-photo img').css('height',data.height+'px');
                               progress.addClass("hide");
                            }
                            
                               
                     },
                     error:function(xhr){
                             Notify.danger('图片上传失败');
                     }
                  };
              // 将options传给ajaxSubmit
                $("#teacher-from").ajaxSubmit(options);
                $('#teacher-from').attr('action','/User/Settings/studApplyAction');
                });
                $("#teacher-submit").on('click',function(){
                   $("#teacher-from").submit(function() {
                       
                    $.post($("#teacher-from").attr('action'),$("#teacher-from").serialize(), function(data) {
                        
                        var html = '';
                            if(data.status == 'OK'){
                                $('#teacher-change').addClass("hide");
                                 html = '<span class="c-text-ok">'+ data.info +'</span>';
                                 $('#prompts').removeClass("hide");
                                 $('#prompts').html(html); 
                                 $('#confirm-true-teacher').removeClass('hide');
                                 var countdown = setInterval("confirmClose()", 1000);
                            }

                            if(data.status == 'error'){
                                html = '<span class="c-text-error">'+ data.info +'</span>';
                            $('#prompts').removeClass("hide");
                            $('#prompts').html(html);
                            
                            }
                    },'json');
                     return false;
                    }); 
                })
                
      
      //上传搜索回调函数
             window.upload_search_callback = function(response){
                try{
                   var data = $.parseJSON(response);
                   if(data.status == 'success'){
                      $('.c-student-photo img').attr('src',data.pictureUrl);
                      $('#userpic').val(data.pictureUrl); 
                      $('.c-student-photo img').css('width',data.width+'px');
                      $('.c-student-photo img').css('height',data.height+'px');
                      
                   }
                   var html = '';
                   if(data.status == 'OK'){
                       $('#teacher-change').addClass("hide");
                        html = '<span class="c-text-ok">'+ data.info +'</span>';
                        $('#prompts').removeClass("hide");
                        $('#prompts').html(html); 
                        $('#confirm-true-teacher').removeClass('hide');
                        var countdown = setInterval("confirmClose()", 1000);
                   }
                   
                   if(data.status == 'error'){
                       html = '<span class="c-text-error">'+ data.info +'</span>';
                   $('#prompts').removeClass("hide");
                   $('#prompts').html(html);
                   }
                   
                } catch (e){
                   Notify.danger('图片上传失败');
                }
             } 
        
//    $('#teacher-from').submit(function() {
//            $.post($(this).attr('action'), $(this).serialize(), function(response) {
//                var html = '';
//                
//                if(response.status == 'OK'){
//                   $('#teacher-change').addClass("hide");
//                   html = '<span class="c-text-ok">'+ response.info +'</span>';
//                   $('#prompts').removeClass("hide");
//                   $('#prompts').html(html); 
//                   $('#confirm-true-teacher').removeClass('hide');
//                   var countdown = setInterval("confirmClose()", 1000); 
//                }else{
//                   html = '<span class="c-text-error">'+ response.info +'</span>';
//                   $('#prompts').removeClass("hide");
//                   $('#prompts').html(html);
//                }
//                
//            }, 'json');
//            return false;
//        });
        
        
    $('#studRemark').select2({
	placeholder: "请输入班级名称",
	minimumInputLength: 1,
	separator: ",", // 分隔符
	maximumSelectionSize: 10, // 限制数量
	initSelection: function(element, callback) { // 初始化时设置默认值
	},
	createSearchChoice: function(term, data) { // 创建搜索结果（使用户可以输入匹配值以外的其它值）
		return {
			id: term.id,
			text: term.title
		};
	},
	formatSelection: function(item) {
		return item.title;//注意此处的name，要和ajax返回数组的键值一样
	}, // 选择结果中的显示
	formatResult: function(item) {
		return item.title;//注意此处的name
	}, // 搜索列表中的显示
	ajax: {
		url: "getStudClassAction", // 异步请求地址
		dataType: "json", // 数据类型
		data: function(term, page) { // 请求参数（GET）
			return {
				className: term
			};
		},
		results: function(data, page) {
			return data;
		}, // 构造返回结果
		escapeMarkup: function(m) {
			return m;
		} // 字符转义处理
	}
});    
        
        
        
        
    };
    
   
    

});