define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $modal = $('#studentNum-form').parents('.modal'); 
        
        Validator.addRule('studentNum', /^[0-9]{1,5}$/, '请输入有效的人数，人数范围为0~20000的整数');
        
        var validator = new Validator({
            element: '#studentNum-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                var inventedStudentNum = $("#inventedStudentNum").val();
                if(inventedStudentNum<0 || inventedStudentNum>20000){
                    Notify.danger('请输入有效的人数，人数范围为0~20000的整数');
                    return false;
                }
                var courseId = $("#inventedStudentNum").data("courseid");
                var webCode = $("#inventedStudentNum").data("webcode");
                $('#save-btn').button('submiting').addClass('disabled');

                $.post($form.attr('action'),{num:inventedStudentNum,courseId:courseId,webCode:webCode}, function(res) {
                    if(res.status == 1){
                        $modal.modal('hide');
                        Notify.success(res.info);
                    }else{
                        $('#save-btn').button('submit').removeClass('disabled').text("确定");
                        Notify.danger(res.info);
                        return false;
                    }
                },'json').error(function(){
                    $('#save-btn').button('submit').removeClass('disabled').text("确定");
                    Notify.danger('提交失败');
                });

            }
        });

        validator.addItem({
            element: '[name="inventedStudentNum"]',
            required: true,
            errormessageRequired : '请输入虚拟学习人数',
            rule: 'studentNum'
        });


    };

});