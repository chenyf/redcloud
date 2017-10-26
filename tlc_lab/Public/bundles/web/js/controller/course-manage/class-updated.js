define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        
        var validator = new Validator({
            element: '#class-updated-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                
                var $btn = $("#class-updated-form-submit");
                $btn.button('submiting').addClass('disabled');
                
                $.post($form.attr('action'), $form.serialize(), function(dataObj) {
                    if (dataObj.code == 1000) {
                        Notify.success(dataObj.msg,1,function(){
                            location.href = $btn.data('goto');
                        });
                    } else {
                        Notify.danger(dataObj.msg,1);
                        $btn.button('reset').removeClass('disabled');
                    }
                    
                },'json').error(function(){
                    
                    Notify.danger('保存失败!',3);
                    $btn.button('reset').removeClass('disabled');
                });

            }
        });

        validator.addItem({
            element: '#class_name',
            required: true,
            rule: 'visible_character'
        });

    };

});