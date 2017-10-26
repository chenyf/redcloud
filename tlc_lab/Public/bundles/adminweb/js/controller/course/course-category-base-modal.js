define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function(options) {

        var $modal = $('#course-category-form').parents('.modal');

        $("input[name=level]").on("change",function(){
            if($(this).val() == "one"){
                $("#parentCategoryBlock").hide();
            }else{
                $("#parentCategoryBlock").show();
            }
        });

        var validator = new Validator({
            element: '#course-category-form',
            failSilently : true,
            triggerType : 'change',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#course-catagory-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(response) {
                    response = $.parseJSON(response);

                    if(response.status){
                        Notify.success(response.message);
                        $modal.modal('hide');
                        window.location.reload();
                    }else{
                        Notify.danger(response.message);
                    }

                    $('#course-catagory-btn').button('reset').removeClass('disabled');

                }).error(function(){
                    Notify.danger('操作失败!');
                });
            }

        });

        validator.addItem({
            element: '[name="name"]',
            required: true,
            rule: 'minlength{min:1} maxlength{max:30}',
            errormessageRequired : '请输入分类名称'
        });

        validator.addItem({
            element: '[name="code"]',
            required: false,
            rule: 'maxlength{max:30}'
        });

        validator.addItem({
            element: '[name="course_code"]',
            required: true,
            rule: 'minlength{min:1} maxlength{max:8}',
            errormessageRequired : '请输入分类对应的课程编号前缀'
        });

    };

});
