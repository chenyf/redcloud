define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        var validator = new Validator({
            element: '#refund-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $.post($form.attr('action'), $form.serialize(), function(html) {
                    if(html.status==0){
                         Notify.danger('申请失败');
                    }else{
                         Notify.success('申请成功');
                         setTimeout(function(){
                             location.href=$form.attr('data-url');
                         },500);
                    }
                }).error(function() {
                    Notify.danger('申请失败');
                });
            }
        });
         
         $('input[name=coin]').click(function(){
               var coin=$(this).val();
               if(coin==1){
                    validator.addItem({
                        element: '[name="blank"]',
                        required: true,
                        errormessageMaxlength: '请选择退款方式'
                    });
                    validator.addItem({
                        element: '[name="phone"]',
                        required: true,
                        rule:'phone'
                    });
                     validator.addItem({
                        element: '[name="name"]',
                        required: true,
                        rule: 'maxlength{max:10}',
                        errormessageMaxlength: '名称不可以大于10个字'
                    });
                   
                    validator.addItem({
                        element: '[name="blankSn"]',
                        required: true,
                        errormessageMaxlength: '请输入退款账号'
                    });
                   $('#ziliao').show();
               }else{
                   validator.removeItem('[name="blank"]');
                   validator.removeItem('[name="phone"]');
                   validator.removeItem('[name="name"]');
                   validator.removeItem('[name="blankSn"]');
                   $('#ziliao').hide();
               }
           });
    };
});