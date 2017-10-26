define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('jquery.bootstrap-datetimepicker');
    exports.run = function() {
        $("[name=endTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'month'
        });

        $("[name=startTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'month'
        });

        var $form = $('#search-form');
        $.post($form.attr('action'), function(html) {
            $('#scrollbar').html(html);
        });

        $('[name=startTime],[name=endTime]').on('change', function() {
            $.post($form.attr('action'), $form.serialize(), function(html) {
                $('#scrollbar').html(html);
            });
        });

        $form.find('i').on('click', function() {
            if ($(this).hasClass('fa-circle-thin')) {
                $('[name=' + $(this).attr('name') + ']').removeClass('fa-circle-o fa-circle-thin');
                $(this).addClass('fa-circle-o');
                $(this).parent().siblings().find('i').addClass('fa-circle-thin');
                $(this).parent().siblings('input[name = ' + $(this).attr('name') + ']').val($(this).data('val'));
            }
            $.post($form.attr('action'), $form.serialize(), function(html) {
                $('#scrollbar').html(html);
            });
        });

        function customCheck(_this, name) {
            $('input[name=' + name + ']').val(_this.data('is'));
            if (_this.hasClass('fa-check-square')) {
                $('[name=' + name + ']').removeClass('fa-check-square').removeClass('fa-square-o');
                _this.addClass('fa-square-o');
                _this.siblings().addClass('fa-check-square');
            } else {
                $('[name=' + name + ']').removeClass('fa-check-square').removeClass('fa-square-o');
                _this.addClass('fa-check-square');
                _this.siblings().addClass('fa-square-o');
            }
        }

    }
})
