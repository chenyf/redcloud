define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var validator = new Validator({
            element: '#bind-new-form'
        });

        validator.addItem({
            element: '[name="user_bind_new[email]"]',
            required: true,
            rule: 'email email_remote'
        });

        validator.addItem({
            element: '[name="user_bind_new[nickname]"]',
            required: true,
            rule: 'chinese_alphanumeric minlength{min:2} maxlength{max:20} remote'
        });
    };

});