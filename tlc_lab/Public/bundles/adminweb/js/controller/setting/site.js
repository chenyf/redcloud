define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var Uploader = require('upload');
    require('colorpicker/colorpicker');
    
    //czq
    //2016/03/07
    require('ckeditor');
    require('control-row/control-row');

    require("jquery/1.11.2/jquery");
    exports.run = function() {
        var $form = $("#site-form");
        var ID = new Array(); 
        $form.on('submit', function(){
            var themeFontColor = $("#themeFontColor").val();
            var themeBackColor = $("#themeBackColor").val();
            var themeNavBackColor = $("#themeNavBackColor").val();
            var themeType = $("#themeType").val();
            if(themeType==1){
                if(!themeBackColor || !themeFontColor){
                    Notify.danger('通用主题配置前景色或背景色不能为空');
                    return false;
                } 
                
                if(!themeNavBackColor){
                    Notify.danger('导航条颜色值不能为空');
                    return false;
                }
            }
            
            if($("[name=friend_link_enable]:checked").val() ==1 ){
                var friendCheck = true;
                $(".box-lwy :text").each(function(){
                    var thisVal = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
                    if(!thisVal){
                        friendCheck = false;
                        return false;
                    }
                });
                if(!friendCheck){
                    Notify.danger('友情链接不能为空');
                    return false;
                }
                var nameCheck = true;
                $(".box-lwy .friend_name").each(function(){
                    var thisVal = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
                    if(thisVal.length > 30){
                        nameCheck = false; 
                        return false;
                    }
                });
                if(!nameCheck){
                    Notify.danger('友情链接名称长度不能超过30');
                    return false;
                }
                var urlCheck = true;
                $(".box-lwy .friend_url").each(function(){
                    var thisVal = $(this).val().replace(/(^\s*)|(\s*$)/g, "");
                    var strRegex = '^((https|http){1}://)';
                    var re=new RegExp(strRegex);
                    if (!re.test(thisVal)){
                        urlCheck = false;
                        return false;
                    }
                });
                if(!urlCheck){
                    Notify.danger('友情链接地址有错误请以http://或者https://开头');
                    return false;
                }
            }
            if($("[name=about_us_enable]:checked").val()==1){ 
                var aboutTitleCheck = $("[name=about_us_title]").val().replace(/(^\s*)|(\s*$)/g, "");
                if(!aboutTitleCheck){
                    Notify.danger('关于我们标题不能为空');
                    return false;
                }
                if(aboutTitleCheck.length > 30){
                    Notify.danger('关于我们标题长度不能超过30');
                    return false;
                }

                var aboutCheck = true;
                var aboutlengthCheck = true;
                $(".box-lwy-about :text").each(function(){
                    var thisVal = $(this).val().replace(/(^\s*)|(\s*$)/g, ""); 
                    if(!thisVal){
                        aboutCheck = false;
                        return false;
                    }
                    if(thisVal.length > 30){
                        aboutlengthCheck = false;
                        return false;
                    }
                });
                if(!aboutCheck){
                    Notify.danger('关于我们目录不能为空');
                    return false;
                }
                if(!aboutlengthCheck){
                    Notify.danger('关于我们目录长度不能超过30');
                    return false;
                }
            }
        });
        
        $('.about_us_error').eq(0).each(function(){
            Notify.danger('关于我们提交失败');
        });
    
        var uploader = new Uploader({
            trigger: '#site-logo-upload',
            name: 'logo',
            action: $('#site-logo-upload').data('url'),
            data: {'_csrf_token': $('meta[name=csrf-token]').attr('content') },
            accept: 'image/*',
            error: function(file) {
                Notify.danger('上传网站LOGO失败，请重试！')
            },
            success: function(response) {
                response = $.parseJSON(response);
                $("#site-logo-container").html('<img height="60px" src="' + response.url + '">');
                $form.find('[name=logo]').val(response.path);
                $("#site-logo-remove").show();
                Notify.success('上传网站LOGO成功！');
            }
        });
        $("#site-logo-remove").on('click', function(){
            if (!confirm('确认要删除吗？')) return false;
            var $btn = $(this);
            $.post($btn.data('url'), function(){
                $("#site-logo-container").html('');
                $form.find('[name=logo]').val('');
                $btn.hide();
                Notify.success('删除网站LOGO成功！');
            }).error(function(){
                Notify.danger('删除网站LOGO失败！');
            });
        });

        var uploader1 = new Uploader({
            trigger: '#site-favicon-upload',
            name: 'favicon',
            action: $('#site-favicon-upload').data('url'),
            data: {'_csrf_token': $('meta[name=csrf-token]').attr('content') },
            accept: 'ico',
            error: function(file) {
                Notify.danger('上传网站浏览器图标失败，请重试！')
            },
            success: function(response) {
                response = $.parseJSON(response);
                $("#site-favicon-container").html('<img src="' + response.url + '" style="margin-bottom: 10px;">');
                $form.find('[name=favicon]').val(response.path);
                $("#site-favicon-remove").show();
                Notify.success('上传网站浏览器图标成功！');
            }
        });

        $("#site-favicon-remove").on('click', function(){
            if (!confirm('确认要删除吗？')) return false;
            var $btn = $(this);
            $.post($btn.data('url'), function(){
                $("#site-favicon-container").html('');
                $form.find('[name=favicon]').val('');
                $btn.hide();
                Notify.success('删除网站浏览器图标成功！');
            }).error(function(){
                Notify.danger('删除网站浏览器图标失败！');
            });
        });
        
        //@author czq 控制行操作
        var friendRow = $("#friendRowHidden").text();
        var options = {
                box           :".box-lwy",
                row           :".row-lwy",
                add_buttom    :".plus",
                del_buttom    :".remove",
                up_buttom     :".up",
                down_buttom   :".down",
                replace       :"{replace}",
                add_content   :friendRow
        };
        $(".box-lwy").control_row(options); 

        //强制获取一个token
        var upload_token = $("[data-image-upload-url]").eq(1).attr("data-image-upload-url");

        var aboutRow = $("#aboutRowHidden").text();
        var textarea_str = $("#textarea_str").text();
        
        //@author Czq 控制文本编辑器移动内容不变
        var textareaFunc = function(options,current){
            var current_id = current.find("textarea").attr("id");
            if( CKEDITOR.instances[current_id] !== undefined){
                var iframe_body_html = CKEDITOR.instances[current_id].getData();
            }
            if( iframe_body_html !== undefined ){
                current.find(":text").attr("name","about_us["+options.line_num+"][title]");
                current.find(":hidden").attr("name","about_us["+options.line_num+"][sequence]");
                current.find("[id*=cke],textarea").remove();
                var textarea_str = options.textarea_str;
                if(options.textarea){
                    var textarea_str = textarea_str.replace(new RegExp(options.textarea,"gm") , "textarea");
                }
                textarea_str = textarea_str.replace(new RegExp("{iframe_body_html}","gm") , iframe_body_html);
                textarea_str = textarea_str.replace(new RegExp(options.replace,"gm") , options.line_num);
                current.find(".about-textarea").append(textarea_str);
            }
        }
         var textareaHideFunc = function(options){
            $('.about-textarea').hide();
         }
        
        var options = {
                box               :".box-lwy-about",
                row               :".row-lwy",
                add_buttom        :".plus",
                del_buttom        :".remove",
                up_buttom         :".up",
                down_buttom       :".down",
                replace           :"{replace}",
                textarea          :"{textarea}",
                control           :1,
                upload_token      :upload_token,
                add_content       :aboutRow,
                up_append_func    :textareaFunc,
                down_append_func  :textareaFunc,
                hide_append_func  :textareaHideFunc,
                textarea_str      :textarea_str
                };
        $(".box-lwy-about").control_row(options); 
        $(".about-textarea").hide();
	$(".box-lwy-about").on("click",'.edit',function(event){
            var edit_parent = $(this).closest('.edit-parent');
            var edit_row = $(this).closest('.row-lwy');
            if(edit_parent.next().is(':visible')){
                return false;
            }
            edit_parent.next().show();
            edit_row.siblings().find(".about-textarea").hide();
            var textarea = edit_row.siblings().find("textarea");
            var line_num = edit_parent.next().find("textarea").attr("id");
            
            if( CKEDITOR.instances[line_num] ){ 
            }else{
                ID[line_num] = CKEDITOR.replace(line_num, {
                    toolbar: 'Simple',
                    filebrowserImageUploadUrl: $('#' + line_num).data('imageUploadUrl')
                });
            }
            event.preventDefault();
	});
    };
    
    $(function(){
        $("#imgFontColor").colorpicker({
            fillcolor:true,
            event:'click',
            target:$("#themeFontColor")
        });
    });
    
    $(function(){
        $("#imgBackColor").colorpicker({
            fillcolor:true,
            event:'click',
            target:$("#themeBackColor")
        });
    });
    
    $(function(){
        $("#imgNavBackColor").colorpicker({
            fillcolor:true,
            event:'click',
            target:$("#themeNavBackColor")
        });
    });
    
    //主题图片颜色配置  yjl 2015-07-22
    $(function(){
        $("#showColor").on("mouseenter",function(){
            $("#colorBord").removeClass("hide");
        })    
        
        $("#closeColorBord").on("click",function(){
            $("#colorBord").addClass("hide");
        })
        
        $(".colorTd").on("click",function(){
            var val = ($(this).attr("data-color"));
            if(!val) {return false;}
            $("#showColor").css("background","#"+val);
            $("#iConColorVal").val(val);
        })
        
    });
    
    

});