define(function (require, exports, module) {

	var Validator = require('bootstrap.validator');
	require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
	require('jquery.select2-css');
	require('jquery.select2');

	exports.run = function () {

		require('./header').run();
		require('./select-range').run();

        var $form = $("#course-form");

        var course_category_choose = $('#course-category-choose');
        // var courseNumber = function(option){
        //     var number = option.element.val();
        //     var courseCode = $('#course-category-choose').data('catecode');
        //     if(courseCode == undefined || !courseCode){
        //         courseCode == "";
        //     }
        //
        //     if(number.indexOf(courseCode) == 0){
        //         return true;
        //     }
        //
        //     validator.addItem({
        //         element : '[name=number]',
        //         required : true,
        //         rule : 'courseNumber',
        //         errormessageRequired : '请输入课程编号',
        //         errormessage:'您所选分类：' + $('#course-category-choose').text() + " ,课程编号必须以'" + $('#course-category-choose').data('catecode') + "'开头",
        //     });
        //     return false;
        // }
        // Validator.addRule('courseNumber', courseNumber,"课程编号不符合规定");

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
                        $.post($form.attr('action'), $form.serialize(), function(result) {
                            if(result.status == 0){
                                $btn.data('lock',0).removeClass("disabled");
                                Notify.danger(result.info,1);
                            }else{
                                window.location.href = result.url;
                            }
                        });
                    },
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
			errormessageRequired : '请输入课程编号',
		});

		validator.addItem({
			element : '[name=subtitle]',
			rule : 'maxlength{max:70}'
		});

		validator.addItem({
			element : '[name=categoryId]',
			errormessageRequired : '请选择课程分类'
		});

        // $(function(){
        //    $('input[name=number]').on('blur',function(e){
        //         var options = {element:$(this)};
        //        if(!courseNumber){
        //            $(this).next(".help-block").html("<span class='text-danger'>您所选分类："+$('#course-category-choose').text()+" ,课程编号必须以"+$('#course-category-choose').data('catecode')+"开头</span>");
        //          ;
        //        }
        //    });
        // });


        // //切换模式
        // $('.select-course-mode').on('click',function(){
        //     var that = $(this);
        //     if(!that.hasClass('active') && that.find('em.selected-icon').length <= 0){
        //         that.siblings('.select-course-mode').removeClass('active').find('em.selected-icon').remove();
        //         that.addClass('active').append("<em class='fa fa-check-square-o selected-icon'></em>");
        //
        //         $("input[name='course_mode']").val(that.data('val'));
        //         if(that.data('val') == 2){
        //             $('#course-teacher-add').show();
        //         }else{
        //             $('#course-teacher-add').hide();
        //         }
        //     }
        // });

        // require('./add_teacher').run();

	};

});