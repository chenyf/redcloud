define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    require('bootstrap.datetimepicker');
    exports.run = function() {
        $('.datetimepicker').datetimepicker({
            locale: 'zh_cn',
            format: 'YYYY-MM-DD HH:mm'
        });
        var $form = $('#config-form');
        var $modal = $form.parents('.modal');

        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                }
                $('#config-save-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(data) {
                    if (data.status == '1') {
                        //$modal.modal('hide');
                        Notify.success(data.info.info);
                        setTimeout(function() {
                            window.location.href = data.info.url;
                        }, 1000);
                    } else {
                        $('#config-save-btn').removeClass('disabled').text('保存');
                        Notify.danger(data.info)
                    }

                });

            }

        });



        validator.addItem({
            element: '[name="title"]',
            required: true
        });
        validator.addItem({
            element: '[name="testId"]',
            required: true,
            errormessageRequired: '请选择试卷'
        });

    };

});