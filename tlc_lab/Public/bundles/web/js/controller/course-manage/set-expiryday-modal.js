define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var $modal = $('#expiryday-set-form').parents('.modal');
        var $table = $('#course-student-list');
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

        var validator = new Validator({
            element: '#expiryday-set-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                var expiryDay = parseInt($("#set-more-expiryday").val());
                var user_name = $('#submit').data('user');
                $.post($form.attr('action'), {expiryDay:expiryDay}, function(result) {
                    if(result){
                        Notify.success('增加'+user_name+'可学习天数成功!');
                        $modal.modal('hide');
                        window.location.reload();
                    }else{
                        Notify.danger('学员总计学习天数不得超过2000天');
                    }
                },'json').error(function(){
                    Notify.danger('增加'+user_name+'可学习天数失败!');
                });

            }
        });

        validator.addItem({
            element: '#set-more-expiryday',
            required : true,
            errormessageRequired : '请输入增加课程可学天数',
            rule: 'expiryDay'
        });

        $(document).find("#set-more-expiryday").on("blur",function(){
            var self = $(this);
            var day = self.val();
            var url = self.data("url");
            
            $.get(url, {day:day}, function(result) {
                if(result.status){
                    $(".count-expiryDay").html(result.info);
                }
            },'json')
        })

    };

});