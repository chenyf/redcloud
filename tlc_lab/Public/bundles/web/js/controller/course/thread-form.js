define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require('ckeditor');

    exports.run = function() {
        require('./common').run();
        require('../../util/char-remaining').char();
        // group: 'default'
        var editor = CKEDITOR.replace('thread_content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#thread_content').data('imageUploadUrl')
        });

        var validator = new Validator({
            element: '#thread-form',
            autoSubmit: false,
        });

        validator.addItem({
            element: '[name="thread[title]"]',
            required: true,
            rule: 'maxlength{max:40}'
        });

        validator.addItem({
            element: '[name="thread[content]"]',
            required: true,
            rule:'remote',
        });

        validator.addItem({
            element: '[name="thread[captcha]"]',
            required: true,
            rule: 'remote'
        });

        validator.on('formValidate', function(elemetn, event) {
            editor.updateElement();
        });

        validator.on('formValidated', function(err, msg, $form) {
            if (err == true) {
                return;
            }
            $form.find('[type=submit]').attr('disabled', 'disabled');
            $.post($form.attr('action'), $form.serialize(), function(data) {
                if (data.status) {
                    Notify.success(data.message);
                    location.href = data.url;
                } else {
                    Notify.danger(data.message);
                }
                $form.find('[type=submit]').removeAttr('disabled');
            });

        });

        // 点击验证码和刷新圈
        $(".fa-refresh").click(function() {
            $("#getcode_num").trigger("click");
        });

        $("#getcode_num").click(function() {
            $(this).attr("src", $("#getcode_num").data("url") + "?" + Math.random());
        });
    };

});