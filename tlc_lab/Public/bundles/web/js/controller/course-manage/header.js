define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        $('.course-publish-btn').click(function() {
            if (!confirm('您真的要发布该课程吗？')) {
                return ;
            }
            var goto = $(this).data('goto');
            $.post($(this).data('url'), function() {
                //window.location.reload();
                Notify.success('课程发布成功！');
                location.href = goto;
            });

        });
        
        
         $('.course-close-btn').click(function() {
            if (!confirm('您真的要关闭该课程吗？')) {
                return ;
            }
            $.post($(this).data('url'), function() {
                Notify.success('课程关闭成功！');
                window.location.reload();
            });
        });
        
    };

});