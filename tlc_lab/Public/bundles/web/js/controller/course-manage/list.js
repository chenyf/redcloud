define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    require("jquery.bootstrap-datetimepicker");
    require("$");
    exports.run = function() {
        var $container = $('#quiz-table-container');
        require('../../util/short-long-text')($container);
        require('../../util/batch-select')($container);
        require('../../util/batch-delete')($container);
        require('../../util/collapse')('hide');
        require('./search')();


        //点击显示大图
        $('.td-Detaile-class').find('img').on('click', function() {
            var imgURL = $(this).attr('src');
            $('#showBigImage').find('img').attr('src', imgURL);
            $('#showBigImage').modal();
        });

        //删除试题
        $container.on('click', '[data-role=item-delete]', function() {
            var $btn = $(this);
            $.post($btn.data('url'), function(data) {
                if (data == '1') {
                    Notify.success("删除成功！");
                    window.location.reload();
                } else {
                    Notify.danger("删除失败！");
                }
            }, "json");
        });

        $('.drop-down-item').on('click', function() {
            if ($(this).is('.tk-arrow-right')) {
                $(this).removeClass('tk-arrow-right');
                $(this).addClass('tk-arrow-down');
                $(this).parent().nextAll('[class=td-item-class]').hide();
                $(this).parent().nextAll('[class=td-Detaile-class]').show();
            } else {
                $(this).removeClass('tk-arrow-down');
                $(this).addClass('tk-arrow-right');
                $(this).parent().nextAll('[class=td-item-class]').show();
                $(this).parent().nextAll('[class=td-Detaile-class]').hide();
            }
        });

        //恢复试题
        $('#RecoveryTest').on('click', function() {
            var ids = [];
            $('[data-role=batch-item]').each(function() {
                if ($(this).is(":checked") == true) {
                    ids.push($(this).val());
                }
            });
            if (ids.length > 0) {
                var $btn = $(this);
                $.post($btn.data('url'), {ids: ids}, function(response) {
                    if (response != 0) {
                        Notify.success("恢复成功！");
                    } else {
                        Notify.danger("恢复失败！");
                    }
                    window.location.reload();
                }, "json");
            } else
                Notify.danger("请选择恢复的试题！");
        });

        $('.recycle-but').on('click', function() {
            var j = 0;
            $('.s-input').find('input[type=checkbox]').each(function() {
                if ($(this).is(':checked')) {
                    j++;
                }
            });
            if (j == 0)
                return false;
        });
    };



});