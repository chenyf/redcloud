define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var VideoChooser = require('../widget/media-chooser/video-chooser7');
    var DocumentChooser = require('../widget/media-chooser/document-chooser7');
    // var FileChooser = require('../widget/media-chooser/file-chooser7');
    var Notify = require('common/bootstrap-notify');
    var Util = require('util');
    require('jquery.sortable');
    require('ckeditor');


    function getEditorContent(editor){
        editor.updateElement();
        var z = editor.getData();
        var x = editor.getData().match(/<embed[\s\S]*?\/>/g);
        if (x) {
            for (var i = x.length - 1; i >= 0; i--) {
               var y = x[i].replace(/\/>/g,"wmode='Opaque' \/>");
               var z =  z.replace(x[i],y);
            };
        }
        return z;
    }

    function compare(tmp, local){
        if($.isEmptyObject(tmp)){
            return false;
        }
        for(var key in tmp){
            if(key!="courseId" 
                && key!="lessonId" 
                && key!="createdTime" 
                && tmp[key] != "" 
                && tmp[key] != local[key]){
                return true;
            }
        }
        return false;
    }
    function objClone(jsonObj){
         var txt=JSON.stringify(jsonObj);
         return JSON.parse(txt);
    }

    var sortList = function($list) {
        var data = $list.sortable("serialize").get();
        $.post($list.data('sortUrl'), {ids:data}, function(response){
            var lessonNum = chapterNum = unitNum = 0;

            for(var key in response.data){
                var newPid = response.data[key];
                $("li#"+key).removeAttr("data-pid").attr("data-pid", newPid);
            }
            
            $list.find('.item-lesson, .item-chapter').each(function() {
                var $item = $(this);
                if ($item.hasClass('item-lesson')) {
                    lessonNum ++;
                    $item.find('.number').text(lessonNum);
                } else if ($item.hasClass('item-chapter-unit')) {
                    unitNum ++;
                    $item.find('.number').text(unitNum);
                    if(response.info) window.deploy($item.attr("id"));
                } else if ($item.hasClass('item-chapter')) {
                    chapterNum ++;
                    unitNum = 0;
                    $item.find('.number').text(chapterNum);
                    if(response.info) window.deploy($item.attr("id"));
                }

            });
        },'json');

        caclHour();
    };

    var caclHour = function(){
        $("li.c-item-chapter.item-chapter-chapter").each(function(){
            var liObj = $(this);
            var childUnit = $("li.item-chapter-unit[data-pid='"+liObj.data('id')+"']");
            var childLesson = $("li.item-lesson[data-pid='"+liObj.data('id')+"']");
            // var unitNum = childUnit.length;
            var lessonNum = childLesson.length;
            childUnit.each(function(){
                var that = $(this);
                lessonNum += $("li.clearfix[data-pid='" + that.data('id') + "']").length;
            });

            liObj.find('span.lesson-hour').text(lessonNum);
        });
    }

    function createValidator ($form, $choosers) {

        Validator.addRule('mediaValueEmpty', function(options) {
            var value = options.element.val();
            if (value == '""' || value == '') {
                return false;
            }

            var value = $.parseJSON(value);
            if (!value || !value.source || !value.name) {
                return false;
            }

            return true;
        }, '请选择或上传{{display}}文件');

        Validator.addRule('timeLength', function(options) {
            return /^\d+:\d+$/.test(options.element.val())
        }, '时长格式不正确');
        
        validator = new Validator({
            element: $form,
            failSilently: true,
            autoSubmit: false
        });

        validator.on('formValidated', function(error, msg, $form) {
            if (error) {
                return;
            }
            for(var i=0; i<$choosers.length; i++){
                if($choosers[i].isUploading()){
                    Notify.danger('文件正在上传，等待上传完后再保存。');
                    return;
                }
            }

            $('#course-lesson-btn').button('submiting').addClass('disabled');

            if($('#course-lesson-btn').data('lock') == 1) return false;
            $('#course-lesson-btn').data('lock',1);
            var $panel = $('.reload-container');
            $.post($form.attr('action'), $form.serialize(), function(html) {
                var id = '#' + $(html).attr('id'),
                    $item = $(id);
                  
                if(!html.length){ //@author Czq 添加课程失败的提示 
                    Notify.danger('添加课程内容失败');
                    $('#course-lesson-btn').data('lock',0);
                    $("#course-lesson-btn").attr({"disabled":false,"data-submiting-text":"提交"}).text("提交").removeClass("disabled");
                    return false;
                }
                var $parent = $('#'+$form.data('parentid'));
                if ($item.length) {
                    $item.replaceWith(html);
                    Notify.success('课程内容已保存');
                    var $list = $("#course-item-list");
                    sortList($list);
                } else {
                    $panel.find('.empty').remove();

                    if($parent.length){
                        var add = 0;

                        if($parent.hasClass('item-chapter-chapter')){   //章
                            $parent.nextAll('li').each(function() {
                                if ($(this).hasClass('item-chapter-chapter')) {
                                    $(this).before(html);
                                    add = 1;
                                    return false;
                                }
                            });
                        }else if($parent.hasClass('item-chapter-unit')){  //节
                            $parent.nextAll('li').each(function() {
                                if ($(this).data('pid') != $parent.data('id')) {
                                    $(this).before(html);
                                    add = 1;
                                    return false;
                                }
                            });
                        }

                        if(add != 1) {
                           $("#course-item-list").append(html);
                        }
                    }else{
                        $("#course-item-list").append(html);
                    }
                    Notify.success('添加课程内容成功');
                    var $list = $("#course-item-list");
                    sortList($list);
                    var $emDeploy = $(document).find("[data-id=" + window.tempChapterId + "]").find("em.deploy");
                    if (typeof(window.tempChapterId) != 'undefined' && $emDeploy.hasClass("fa-plus")) {
                        window.toggleDeploy($emDeploy);
                    } 
                }
                $(id).find('.btn-link').tooltip();
                $form.parents('.modal').modal('hide');
                $('#course-lesson-btn').data('lock',0);
                $('li.chapter-create-class').on('click', function() {
                    window.tempChapterId = $(this).parents('.item-chapter').data('id');
                });
            });
        });

        return validator;
    };

    function switchValidator(validator, type) {
        validator.removeItem('#lesson-title-field');
        validator.removeItem('#lesson-content-field');
        validator.removeItem('#lesson-media-field');
        validator.removeItem('#lesson-second-field');
        validator.removeItem('#lesson-minute-field');
        validator.removeItem('#needGoldNum');

        validator.addItem({
            element: '#lesson-title-field',
            required: true ,
            rule: "maxlength{max:400}",
            display : '标题',
            errormessageRequired : '请输入标题'
        });


        switch (type) {
            case 'video':
            case 'audio':
                validator.addItem({
                    element: '#lesson-media-field',
                    required: true,
                    rule: 'mediaValueEmpty',
                    display: type == 'video' ? '视频' : '音频'
                });

                validator.addItem({
                    element: '#lesson-minute-field',
                    required: true,
                    rule: 'integer',
                    display: '时长'
                });

                validator.addItem({
                    element: '#lesson-second-field',
                    required: true,
                    rule: 'second_range',
                    display: '时长'
                });

                break;
            case 'text':
                validator.addItem({
                    element: '#lesson-content-field',
                    required: true
                });

                break;
            case 'document':
                validator.addItem({
                    element: '#lesson-media-field',
                    required: true,
                    rule: 'mediaValueEmpty',
                    display: '文档'
                });
                break;
        }

    }

    exports.run = function() {
        if(!Util.flashChecker()){
            Notify.danger("您的浏览器未安装Flash，请更换高版本支持FLash的浏览器进行上传操作",0);
        }
        
        var Timer;
        var editor;
        var tmpContents = {};
        var localContent = {};
        var $form = $("#course-lesson-form");

        function getTmpContents(){
            var date = new Date(); //日期对象
            var now = "";
            now = now + date.getHours()+"时";
            now = now + date.getMinutes()+"分";
            now = now + date.getSeconds()+"秒";
            tmpContents["title"] = $("#lesson-title-field").val();
            tmpContents["summary"] = $("#lesson-summary-field").val();
            tmpContents["courseId"]  = $("#course-lesson-form").data("courseId");
            tmpContents["lessonId"]  = $("#course-lesson-form").data("lessonId");
            tmpContents["content"] = getEditorContent(editor);
            tmpContents["createdTime"] = now;


            var lessonId = 0;
            if(compare(tmpContents, localContent)){
                var titleName = "添加课程内容";
                if(tmpContents["lessonId"] != undefined){
                    titleName = "编辑课程内容";
                    lessonId = tmpContents["lessonId"];
                }
                $.post($form.data("createDraftUrl"), tmpContents, function(data){
                    localContent = objClone(tmpContents);
                    $(".modal-title").text(titleName + '(草稿已于' + tmpContents['createdTime'] + '保存)');
                });
            }
        }

        var updateDuration = function (length) {
            length = parseInt(length);
            if (isNaN(length) || length == 0) {
                return ;
            }
            var minute = parseInt(length / 60);
            var second = length - minute * 60;
            $("#lesson-minute-field").val(minute);
            $("#lesson-second-field").val(second);
        }

        var $content = $("#lesson-content-field");

        var choosedMedia = $form.find('[name="media"]').val();
        choosedMedia = choosedMedia ? $.parseJSON(choosedMedia) : {};

        // require('polyv').run(window.isIE());
        var videoChooser = new VideoChooser({
            element: '#video-chooser',
            choosed: choosedMedia,
        });

        var documentChooser = new DocumentChooser({
            element: '#document-chooser',
            choosed: choosedMedia,
            uploaderSettings: {
                button_text: "<span class=\"btnText\" style=\"font-family: '微软雅黑';\">选择文件</span>",
                button_text_style : ".btnText { color: #1C0202; font-size:16px;font-family: '微软雅黑';}",
                button_text_left_padding : 4,
                button_text_top_padding : 7,
            },
        });

        // var fileChooser = new FileChooser({
        //     element: '#resource-chooser',
        //     choosed: choosedMedia,
        //     uploaderSettings: {
        //         button_text: "<span class=\"btnText\">选择文件</span>",
        //         button_text_style : ".btnText { color: #1C0202; font-size:16px;}",
        //         button_text_left_padding : 4,
        //         button_text_top_padding : 7,
        //     },
        // })

        videoChooser.on('change', function(item) {
           var value = item ? JSON.stringify(item) : '';
           $form.find('[name="media"]').val(value);
           // updateDuration(item.length);
        });

        documentChooser.on('change', function(item) {
            var value = item ? JSON.stringify(item) : '';
            $form.find('[name="media"]').val(value);
        });

        $('.modal').unbind("hide.bs.modal");
        $(".modal").on("hide.bs.modal", function(){
            videoChooser.destroy();
            documentChooser.destroy();
        });

        var validator = createValidator($form, [videoChooser,documentChooser]);
       
        $form.on('change', '[name=type]', function(e) {
            var type = $(this).val();

            $form.removeClass('lesson-form-video').removeClass("lesson-form-audio").removeClass("lesson-form-text").removeClass("lesson-form-ppt").removeClass("lesson-form-document").removeClass("lesson-form-flash")
            $form.addClass("lesson-form-" + type);
            $('#course-lesson-btn').removeAttr('disabled');
            
            if (type == 'text'){
                Timer = setInterval(getTmpContents, 5000);
            }

            if (type == 'video') {
                videoChooser.show();
                documentChooser.hide();
                clearInterval(Timer);
            } else if (type == 'audio') {
                videoChooser.hide();
                documentChooser.hide();
                clearInterval(Timer);
            } else if (type == 'ppt') {
                videoChooser.hide();
                documentChooser.hide();
                clearInterval(Timer);
            } else if (type == 'document') {
                documentChooser.show();
                videoChooser.hide();
                clearInterval(Timer);
            } else if (type == 'flash') {
                documentChooser.hide();
                videoChooser.hide();
                clearInterval(Timer);
            }

            $(".modal").on('hidden.bs.modal', function (){
                clearInterval(Timer);
            });

            switchValidator(validator, type);
        });

        $form.find('[name="type"]:checked').trigger('change');

        // course
        editor = CKEDITOR.replace('lesson-content-field', {
            toolbar: 'Full',
            filebrowserImageUploadUrl: $('#lesson-content-field').data('imageUploadUrl'),
            filebrowserFlashUploadUrl: $('#lesson-content-field').data('flashUploadUrl'),
            height: 300
        });

        validator.on('formValidate', function(elemetn, event) {
            var content = getEditorContent(editor);
            $content.val(content);
        });

        $(".close,#cancel-btn").on('click',function(e){
            if($form.find('[name="type"]:checked').val()=='text'){
                getTmpContents();
            }
        });

        $("#see-draft-btn").on('click',function(e) {
            tmpContents["courseId"]  = $("#course-lesson-form").data("courseId");
            var courseId = tmpContents["courseId"];
            tmpContents["lessonId"]  = $("#course-lesson-form").data("lessonId");
            var lessonId;
            if(tmpContents["lessonId"] == undefined)  {
                lessonId =0;
            } else{
                lessonId = tmpContents["lessonId"];
            }
            $.get($(this).data("url"), {courseId: courseId, lessonId:lessonId}, function(response){
                $("#lesson-title-field").val(response.title);
                $("#lesson-summary-field").val(response.summary);
                editor.updateElement();
                editor.setData(response.content);
                $("#lesson-content-field").val(response.content);
            });
            $("#see-draft-btn").hide();
        });

        //上传、导入tab切换
        $('.file-chooser-main').on('click','ul>li',function(e){
            $target = $($(this).find('.file-chooser-uploader-tab').attr('href'));
            $target.siblings().removeClass('active');
            $target.addClass('active');
            $(this).siblings().removeClass('active');
            $(this).addClass('active');
        });

        //网盘导入点击导入按钮
        $('.file-chooser-main').on('click','.import-btn',function(e){
            var type = $form.find('[name="type"]:checked').val();
            if(type == 'document'){
                var fileCheckedInput = $(this).parents('.tb_file').find('[name="doc_file"]:checked');
                var chooser = documentChooser;
            }else if(type == 'video'){
                var fileCheckedInput = $(this).parents('.tb_file').find('[name="video_file"]:checked');
                var chooser = videoChooser;
            }else{
                return;
            }

            if(fileCheckedInput.length <= 0){
                return;
            }

            var json = {};
            json['fromcloud'] = 1;
            json['source'] = 'self';
            json['filepath'] = fileCheckedInput.val();
            json['name'] = fileCheckedInput.data('name');
            json['filesize'] = fileCheckedInput.data('size');
            json['fileext'] = fileCheckedInput.data('ext');

            $('#lesson-media-field').val(JSON.stringify(json));

            chooser.onChanged({
                'status':0,
                'name':json['name']
            });
        });
    };
});
