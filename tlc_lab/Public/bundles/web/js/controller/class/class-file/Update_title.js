define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        var validator = new Validator({
            element: '#file-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                }
                $('#file-create-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(data) {
//                    console.log($form.serialize());
                    if (data.status >= 1) {
                        Notify.success(data.info);
                        window.location.reload();
                    } else {
                        Notify.danger(data.info)
                    }
                });
            }
        });
        validator.addItem({
            element: '[name="title"]',
            required: true,
            rule: 'minlength{min:2} maxlength{max:20}'
        });
    };


});