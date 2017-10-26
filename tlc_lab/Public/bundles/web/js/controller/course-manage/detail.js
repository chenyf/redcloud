define(function(require, exports, module) {

    var DynamicCollection = require('../widget/dynamic-collection4');
    require('jquery.sortable');
    require('ckeditor');
    require('control-row/control-row'); //@auther Czq 2016/03/11
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
     
    exports.run = function() {
        require('./header').run();
        require('./select-range').run();
        require('../../util/collapse')('show');
        var editor;
        Validator.addRule('price', /^[1-9][0-9]{0,4}$/, '请输入有效的课程价格，课程价格区间为1—99999的整数');
        var expiryDay = function(option){
             var day = option.element.val();
             var reg = /^[0-9]{1,4}$/;
             if(!reg.test(day)){
                 return false;
             }
             day = parseInt(day);
             var r = (day>=0) && (day<=2000);
             return r;
         };
         Validator.addRule('expiryDay', expiryDay, '请输入有效的课程可学习天数，范围为0~2000的整数');
        
        //@author Czq 定义一个去除空格的验证rule
        var requireLenSwitch = function(option){
            var thisVal = option.element.val().replace(/(^\s*)|(\s*$)/g, "");
			return thisVal;			
        };
	Validator.addRule('requireLenSwitch', requireLenSwitch, '请输入正确名称');
        var $form = $("#course-form");
        var validator = new Validator({
            element : '#course-form',
            failSilently : true,
            triggerType : 'change',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if(error)  return false;
                var $btn = $("#course-create-btn");
                $btn.addClass('disabled');
                if($btn.data('lock') == 1) return false;
                $btn.data('lock',1);
                var about = "&about="+editor.getData();
                var data = $form.serialize()+about;
                $.post($form.attr('action'), data, function(result) {
                    if(result.status == 0){
                        $btn.data('lock',0).removeClass("disabled");
                        Notify.danger(result.info,1);
                    }else{
                        window.location.href = result.url;
                    }
                });
            },
        });
        
        //@author Czq 自动添加验证
        var validateItems;
        var virtualFunc = function(){
            $("[id*=virtualLabName]").each(function(){
                var thisId = $(this).attr("id");
                validateItems = validateItems = validator.addItem({
                        element : '#'+thisId,
                        required : true,
                        //required : "requireLenSwitch",  //这样ok
                        rule : 'requireLenSwitch minlength{min:1} maxlength{max:30}',
                        display : '名称',
                        errormessageRequired : '请输入正确名称'
                });
            });
            $("[id*=virtualLabUrl]").each(function(){
                var thisId = $(this).attr("id");
                validateItems = validator.addItem({
                        element : '#'+thisId,
                        required : true,
                        rule :"minlength{min:1} maxlength{max:1000} url",
                        display : '地址',
                        errormessageRequired : '请输入地址'
                });
            });
        }
        if($("[name=virtualLabStatus]:checked").val() == 1){
            $(".virtualLabBox").show();
            $(".virtualLabHint").show();
            virtualFunc();
        }
         //@author Czq 解除验证
        var removeFunc = function(nameId){
            for( key in validateItems.items){
                var selector = validateItems.items[key].element.selector;
                if( "#"+nameId == selector ){
                    validator.removeItem(validateItems.items[key]);
                }
            }
        }
        //@author Czq 虚拟实验室 开启关闭操作
        $("[name=virtualLabStatus]").change(function(){
            var virtualCheck = $(this).val();
            if(virtualCheck == 1){
                $(".virtualLabBox").show();
                $(".virtualLabHint").show();
                virtualFunc();
            }else{
                $(".virtualLabBox").hide();
                $(".virtualLabHint").hide();
                $("[id*=virtualLabName]").each(function(){
                    var thisId = $(this).attr("id");
                    removeFunc(thisId);
                });

                $("[id*=virtualLabUrl]").each(function(){
                    var thisId = $(this).attr("id");
                    removeFunc(thisId);
                });
            }
        });
        validator.addItem({
            element : '[name=title]',
            required : true,
            rule : 'minlength{min:1} maxlength{max:60}',
            display : '课程名称',
            errormessageRequired : '请输入课程名称'
        });

        validator.addItem({
            element : '[name=number]',
            required : true,
            rule : 'minlength{min:1} maxlength{max:20}',
            errormessageRequired : '请输入课程编号'
        });
        

        // group:'course'
        editor  = CKEDITOR.replace('course-about-field', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#course-about-field').data('imageUploadUrl')
        });
        
        if($("#course-goals-form-group").size()){
            var goalDynamicCollection = new DynamicCollection({
                element: '#course-goals-form-group',
            });
        }
        if($("#course-audiences-form-group").size()){
            var audiencesDynamicCollection = new DynamicCollection({
                element: '#course-audiences-form-group',
            });
        }
        
        $(".sortable-list").sortable({
            'distance':20
        });

        $("#course-base-form").on('submit', function() {
            goalDynamicCollection.addItem();
            audiencesDynamicCollection.addItem();
            
        });
        
        //@author Czq 虚拟实验室 行操作添加验证函数
        var addVirtualLab = function (options, add_content){
            validateItems = validator.addItem({
                element : '#virtualLabName'+options.line_num,
                required : true,
                rule : 'requireLenSwitch minlength{min:1} maxlength{max:30}',
                display : '名称',
                errormessageRequired : '请输入正确名称'
            });
            validateItems = validator.addItem({
                element : '#virtualLabUrl'+options.line_num,
                required : true,
                rule :"minlength{min:1} maxlength{max:1000} url",
                display : '地址',
                errormessageRequired : '请输入地址'
            });
        }
        //@author Czq 虚拟实验室 行操作删除验证 函数
        var delVirtualLab = function (options, current){
            var currentNameId = $(current).find("[id*=virtualLabName]").attr("id");
            var currentUrlId = $(current).find("[id*=virtualLabUrl]").attr("id");
            for( key in validateItems.items){
                    var selector = validateItems.items[key].element.selector;
                    if( "#"+currentNameId == selector || "#"+currentUrlId == selector){
                            validator.removeItem(validateItems.items[key]);
                            delVirtualLab(options,current);
                    }
            }
        }
        //@author Czq 虚拟实验室 初始行操作
        var virtualLabRow = $("#virtualLabRow").text();
        var options = {
                box               :".virtualLabBox",
                row               :".virtualLabRow",
                add_buttom        :".plus",
                del_buttom        :".remove",
                up_buttom         :".up",
                down_buttom       :".down",
                replace           :"{replace}",
                max_row           :10,
                add_content       :virtualLabRow, 
                add_append_func   :addVirtualLab,
                del_append_func   :delVirtualLab,
                del_all           :false
        };
        $(".virtualLabBox").control_row(options); 
        
    };
    
     (function(){
        $(document).on('click','#course-pic .pic-list',function(){
            $(this).find('.c-icon-checkd').removeClass('hide');
            $(this).siblings().find('.c-icon-checkd').addClass('hide');
            $(this).addClass('active');
            $(this).siblings().removeClass('active');
           var val =  $(this).find('.course-picture').data('value');
           $("#selectPicture").val(val);
        }) 
     })();
   

//           $('#course-form').submit(function() {
//                 
//            $.post($('#course-form').attr('action'), $('#course-form').serialize(), function(response) {
//                if (response.status == 'ok') {
//                        Notify.success('更新成功！');p
//                }
//            }, 'json');
//            return false;
//        });


    require('./add_teacher').run();
         


});