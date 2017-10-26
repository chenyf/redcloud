define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function () {

        $('.follow-btn').click(function () {
            var url = $(this).data('url');
            var valUrl = $('.mrl').val();
            generalizeCode = $('.generalizeCode').html();
            $.ajax({
                type: "post",
                url: url,
                data: {'mrl': valUrl, 'generalizeCode': generalizeCode},
                success: function (data) {
                    if (data == 0) {
                        Notify.danger('链接不正确!');
                        return false;
                    }
                    $('#modal').html(data).modal();
                }
            });
        })


    }
})
