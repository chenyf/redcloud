define(function(require, exports, module) {

    require("jquery.bootstrap-datetimepicker");
    var Validator = require('bootstrap.validator');
    require("$");
    require("./jquery.cookie.js");
    var Notify = require('common/bootstrap-notify');
        require('masonry');
        require('jquery.jquery-ui');
         require('jquery.unslider');
    exports.run = function() {
                 var container = '.list-group';
        	var deleteBtn = ".delete-cms";
                var listitem = ".list-group-item";
                
        //
         var name =$.cookie('name');
         if(name != "set"){
             $("#aclick").click();
         }
        $.cookie('name', 'set'); 
       
       
        
        
        var banner = document.getElementsByName("banner[]");
        var lb = {};
        for (var i = 0; i < banner.length; i++) {

            lb[i] = $('#' + banner[i].value).unslider({
                dots: true
            }),
            lb['data' + banner[i].value] = lb[i].data('unslider');
            $('.unslider-arrow' + banner[i].value).click(function() {
                var t = $(this).data('id');
                var fn = this.className.split(' ')[1];
                lb['data' + t][fn]();
            });
        }
             
                
                
                     
                     
                     
                     
                     
                   var price =  $("#price").text()
                    var preferentialPrice = $("#preferentialPrice").text()
               $("#economize").text(price -preferentialPrice );
                
        $("#startDateTime, #endDateTime").datetimepicker({
            language: 'zh-CN',
            // autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 'month'
        });


        $("#catalog a").draggable({
          
            appendTo: "body",
            helper: "clone"
        });
        
        
        
        
        $("#cart ul").droppable({
            activeClass: "ui-state-default",
            hoverClass: "ui-state-hover",
            accept: ":not(.ui-sortable-helper)",
            drop: function(event, ui) {
                
                var cmsid = $("#cmsid").val()
                var cmsType = ui.draggable.find("input.demo").val()
                var title = ui.draggable.text();

                $.ajax({
                    type: "POST",
                    url: url,
                    data: {cmsId: cmsid, cmsType: cmsType, title: title},
                    success: function(msg) {
                        if(msg==1){
                            alert("已经添加了");
                            return false;
                        }else{
                             history.go(0)
                              $("<li class='list-group-item'></li>").html("<div class='modular' style='background-color:#33CCFF;width: 100%;height: 100px;'>" + ui.draggable.text() + "<a  class='delete-cms' href='javascript:void(0)'>删除</a><a data-toggle='modal' data-target='#modal'  data-url=" + "'" + create + "'" + "  class='editor' href=''>编辑</a>" + "</div>").appendTo(this);
                        }
                        
                    }
                });
               /* if (cmsType == 3) {
                    ui.draggable.css("display", "none");
                }*/
                $(this).find(".placeholder").remove();
               
                 
            }
        })
        
        $("#cart ul").sortable({
            items: "li:not(.placeholder)",
          update: function() {
                $(this).removeClass("ui-state-default");
               //console.log($("#sortable").sortable('serialize'));return false;
               var sort = $("#sortable").sortable('serialize');
                $.ajax({
                    url: $("#cart ul li").data('url'),
                    type: 'POST',
                    data:sort,
                    success: function(msg) {
                     
                    }
                })
                 
                
            }
        });

        $(container).on('click', deleteBtn, function() {
            var _this = $(this);
             var  length = $("#cart ul li").length;
           
            if (confirm('是否要执行此操作？')) {
                $.ajax({
                    url: _this.data('url'),
                    type: 'POST',
                    success: function(msg) {
                        Notify.success('删除成功！');
                       
                     if(msg==3){
                           $("#catalog").append("<a href='###'  class='list-group-item  ui-draggable ui-draggable-handle'><input type='hidden' value='3' class='demo'/>价格模块</a>");
                           history.go(0);                         
                        }
                        if(length==1){
                            $("#cart ul").append("<div style='height: 300px ;border: 1px #ccc solid' > <center>请拖拽您想要的模块到本区域</center></div>");
                        }
                       _this.parent().parent().remove();
                      
                       
                    }
                });
            }
        });
        
        var validator = new Validator({
            element: '#create-form',
            autoSubmit: false,
                onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                } 
                $('#create-btn').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(data) {
                    if(data.status >= 1){
                        Notify.success(data.info);
                        window.location.reload();
//                          location.href = data.url;
                }else{
                        Notify.danger(data.info);
                }
                });
            }
        });
        
        $("#create-btn").click(function(){
        var values = $("#description").val();
            if(values.length < 0 ){
                 Notify.danger('你没有进行任何编辑，请重新进行编辑');
                return false;
               }
        })
        
        
        $(document).ready(function(){
            var doc=document,inputs=doc.getElementsByTagName('input'),supportPlaceholder='placeholder'in doc.createElement('input'),placeholder=function(input){var text=input.getAttribute('placeholder'),defaultValue=input.defaultValue;
            if(defaultValue==''){
                input.value=text}
                input.onfocus=function(){
                    if(input.value===text){this.value=''}};
                    input.onblur=function(){if(input.value===''){this.value=text}}};
                    if(!supportPlaceholder){
                        for(var i=0,len=inputs.length;i<len;i++){var input=inputs[i],text=input.getAttribute('placeholder');
                        if(input.type==='text'&&text){placeholder(input)}}}});
            
             if(!placeholderSupport()){   // 判断浏览器是否支持 placeholder
              $('[placeholder]').focus(function() {
                  var input = $(this);
                  if (input.val() == input.attr('placeholder')) {
                      input.val('');
                      input.removeClass('placeholder');
                  }
              }).blur(function() {
                  var input = $(this);
                  if (input.val() == '' || input.val() == input.attr('placeholder')) {
                      input.addClass('placeholder');
                      input.val(input.attr('placeholder'));
                  }
              }).blur();
          };
          function placeholderSupport() {
              return 'placeholder' in document.createElement('input');
          }

        $("#description").keyup(function(){
            var values = $("#description").val();
            var aa = values.length;
            var num = 500-aa;
            if(aa >= 0){
                 $("#description").val(values.substring(0,500));
                 $("#re").html("<div style='float:right;margin-right:110px'>还可以再输入<strong style='font-size:20px;color:red'>"+num+"</strong>个字符</div>");
            }   
        })

    };

});