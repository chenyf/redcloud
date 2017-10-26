define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    exports.run = function() {
        
                //过滤无效标签
            function filter(str){
               var retStr = str.replace(/<img src=\'file.*>.*<\/img>/ig,"");
               this.val(retStr);
            }
           
//            
//            var $content = $('#content');
//            $content.on('keyup',function(){
//                     var filStr = filter(this.value);
//                     $content.val(filStr);
//            });
//            $content.on('mouseup',function(){
//                     var filStr = filter(this.value);
//                     $content.val(filStr)
//            })
//            function bindChangeHandler(input,fun) {
//                    if("onpropertychange" in input) {//IE6、7、8，属性为disable时不会被触发
//                            input.onpropertychange = function() {
//                                    if(window.event.propertyName == "value") {
//                                            if(fun) {
//                                                    fun.call(this,window.event);
//                                            }
//                                    }
//                            }
//                    } else {
//                            //IE9+ FF opera11+,该事件用js改变值时不会触发，自动下拉框选值时不会触发
//                            input.addEventListener("input",fun,false);
//                    }
//            }
//            //使用
//            bindChangeHandler($content[0],function(){
//                     var filStr = filter(this.value);
//                     $content.val(filStr)
//            });
    
        
        var $form = $("#daily-create-form");
        var $modal = $('#daily-create-form').parents('.modal');
        window.sendTeacher = 'true';
        window.sendStudent = 'true';
        var editor = CKEDITOR.replace('content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#content').data('imageUploadUrl')
        });
        
        CKEDITOR.instances["content"].on("instanceReady", function () {  
            //set keyup event  
            this.document.on("keyup", AutoSave);  
            //and click event  
            this.document.on("click", AutoSave);  
            //and select event  
            //this.document.on("select", AutoSave);  
        });  

             function AutoSave() {//
               var str = editor.getData();
               var retStr = str.replace(/<img.*src=\"file:.* \/>/ig,"");
               //CKEDITOR.instances["content"].setData('');
               //CKEDITOR.instances["content"].insertHtml(retStr);//赋值
              return retStr;
            }  
        
        $(".modal-dialog").addClass("w-popupbox");
        var validator = new Validator({
            element: '#daily-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#daily-create-btn').button('submiting').addClass('disabled');
                $form.find('#content').val(AutoSave());
                $.post(   $form.attr('action'), $form.serialize(), function(info) {
                    //  console.info(typeof info);
                    if (info["status"] == 1000) {
                        $modal.modal('hide');
                        Notify.success(info["msg"]);
                        //window.location.reload();
                    } else {
                        $('#daily-create-btn').button('submiting').removeClass('disabled');
                        $('#daily-create-btn').button('submiting').text('提交');
                        Notify.info(info["msg"]);
                        return false;
                    }
                },'json').error(function() {
                    Notify.danger('添加失败');
                }, 'json');

            }
        });
        
 

    };

});