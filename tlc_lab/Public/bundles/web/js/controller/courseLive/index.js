define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        require('../../util/collapse')('hide');
        $("form.form-inline").find("select").change(function() {
            if ($(this).attr('noautochange') != 1)
                $(this).parents('form').submit();
        })

        $(".close-live").on('click', function() {
            if (!confirm('您真的要关闭此直播吗？')) {
                $(this).closest('.btn-group').removeClass('open');
        		return false;
            }
            $.post($(this).data('url'), '', function(response) {
                var data = $.parseJSON(response);
                if (data.status == false) {
                    Notify.danger(data.message);
                    return false;
                }
                Notify.success(data.message);
                window.location.reload();
            });
        });

        $(".back-live").on('click', function() {
            $.post($(this).data('url'), '', function(response) {
                var data = $.parseJSON(response);
                if (data.status == false) {
                    Notify.danger(data.message);
                    return false;
                }
                Notify.success(data.message);
                window.location.reload();
            });
        });

        $(".del-live").on('click', function() {
            if (!confirm('您真的要删除此直播吗？')) {
                $(this).closest('.btn-group').removeClass('open');
        		return false;
            }
            $.post($(this).data('url'), '', function(response) {
                var data = $.parseJSON(response);
                if (data.status == false) {
                    Notify.danger(data.message);
                    return false;
                }
                Notify.success(data.message);
                window.location.reload();
            });
        });



    };

});