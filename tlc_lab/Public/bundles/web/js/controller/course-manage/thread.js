define(function(require, exports, module) {

    (function() {
        $(document).on('click', '.delMessage', function() {
            var $btn = $(this);
            if (!confirm($btn.data('confirm-message'))) {
                return false;
            }

            $.post($btn.data('url'), function() {
                window.location.reload();
            });
        })
    })();

    exports.run = function() {
        require('../../util/collapse')('hide');
        var $courseBox = $('.t-course-set-box');
        var initUrl = $("#threadSelect li[name=check_manage]").data('url');
        $courseBox.load(initUrl);
        $('#threadSelect li').on('click', function() {
            var _self = $(this);
            var url = _self.data('url');
            _self.parent().find('li').removeClass('active');
            _self.addClass('active');
            $.get(
                    url,
                    function(html) {
                        $courseBox.html(html);
                    });
        });

    }


});