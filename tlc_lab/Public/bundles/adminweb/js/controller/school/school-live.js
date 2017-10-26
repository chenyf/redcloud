define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');

    require('bootbox');
    require('jquery.bootstrap-datetimepicker');
    var now = new Date();
    exports.run = function() {

        $("[name=endTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'day',
            todayBtn: true
        });
        $("[name=startTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'day',
            todayBtn: true
        });
        $("[name=endTime]").on('click', function() {
            if ($(this).val() == "") {
                var str = now.getFullYear() + "-" + (now.getMonth() + 1) + "-" + now.getDate();
                $(this).val(str);
            }
        });

        $("#school-live-form").on('click', '[name=isClose]', function() {
            var value = $(this).val();
            var numLimit = $("#school-live-form .number-limit");
            var liveTime = $("#school-live-form .live-time");
            if (value == 1) {
                numLimit.find('[name=numLimit]').removeAttr('disabled');
                
                liveTime.find('[name=startTime]').removeAttr('disabled');
                liveTime.find('[name=endTime]').removeAttr('disabled');
            } else {
                numLimit.find('[name=numLimit]').attr('disabled', 'disabled');
                
                liveTime.find('[name=startTime]').attr('disabled', 'disabled');
                liveTime.find('[name=endTime]').attr('disabled', 'disabled');
            }
        });
        


        var $form = $('#school-live-form');
        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                }
                $('[type="submit"]').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(response) {
                    $('[type="submit"]').removeClass('disabled').text('保存');
                    var data = $.parseJSON(response);
                    if (data.status == false) {
                        Notify.danger(data.message);
                        return false;
                    }
                    Notify.success(data.message);
                    $('.close').trigger('click');
                });

            }

        });



    };

});