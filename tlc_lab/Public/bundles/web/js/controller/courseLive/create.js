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
            minView: 'hour',
            todayBtn: true
        });
        $("[name=startTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'hour',
            todayBtn: true
        });
        $("[name=endTime]").on('click', function() {
            if ($(this).val() == "") {
                var str = now.getFullYear() + "-" + (now.getMonth() + 1) + "-" + now.getDate();
                $(this).val(str);
            }
        });
//		$('.datetimepicker').datetimepicker({
//			language: 'zh-CN',
//                        autoclose: true,
//                        format: 'yyyy-mm-dd hh:ii:ss',
//                        minView: 'month'
//		});


        $("#from-create-live").on('click', '[name=viewRange]', function() {
            var value = $(this).val();
            var room = $("#from-create-live .live-chatRoomStatus");
            if (value != 1) {
                room.removeClass("c-disabled");
                room.find('[name=chatRoomStatus]').removeAttr('disabled');
            } else {
                room.addClass("c-disabled");
                room.find('[name=chatRoomStatus]').attr('disabled', 'disabled');
                $("#from-create-live .room-checked").trigger("click");
            }
        });
        
        $('#liveTitle').on('input propertychange', function() {
                var textVal = $(this).val();
                if (textVal.length > 20) {
                    var textar = textVal.substring(0, 20);
                    $(this).val(textar);
                    return false;
                }
            });


        //开始截止时间判断
        function startTmLessEndTm() {
            var startTM = datetime_to_unix($("[name=startTime]").val().trim());
            var endTM = datetime_to_unix($("[name=endTime]").val().trim());
            if (startTM > endTM) {
                Notify.danger("截止时间必须大于开始时间");
                return false;
            }
            return true;
        }
        function datetime_to_unix(datetime) {
            var tmp_datetime = datetime.replace(/:/g, '-');
            tmp_datetime = tmp_datetime.replace(/ /g, '-');
            var arr = tmp_datetime.split("-");
            var now = new Date(Date.UTC(arr[0], arr[1] - 1, arr[2], arr[3] - 8, arr[4], arr[5]));
            return parseInt(now.getTime() / 1000);
        }


        var $form = $('#from-create-live');
        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                }
                $('[type="submit"]').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(response) {
                    $('[type="submit"]').removeClass('disabled').text('确定创建直播');
                    var data = $.parseJSON(response);
                    if (data.status == false) {
                        Notify.danger(data.message);
                        return false;
                    }
                    Notify.success(data.message);
                    $('.close').trigger('click');
                    window.location.reload();
                });

            }

        });

        validator.addItem({
            element: '[name="liveTitle"]',
            required: true,
            errormessageRequired: '请填写直播主题'
        });

        validator.addItem({
            element: '[name="startTime"]',
            required: true,
            errormessageRequired: '请选择开始时间'
        });

        validator.addItem({
            element: '[name="endTime"]',
            required: true,
            errormessageRequired: '请选择截止时间'
        });



    };

});