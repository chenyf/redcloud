define(function (require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('bootbox');
    require('jquery.bootstrap-datetimepicker');
    exports.run = function () {
        $('#start_time').datetimepicker({
            language: 'zh-CN',
            //autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 'month'
        });

        $('#end_time').datetimepicker({
            language: 'zh-CN',
            //autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 'month'
        });

        $('.pull-right li').click(function () {
            var url = $(this).data('url');
            location.href = url;
        })

    }
})
