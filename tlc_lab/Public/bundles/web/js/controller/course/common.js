define(function(require, exports, module) {
    var WidgetAppraise = require('../appraise/appraise.js');
    var Share = require('../../util/share.js');
    // require('../my/homepage.js').run();
    exports.run = function() {
        Share.create({
            selector: '.share',
            icons: 'itemsAll',
            display: ''
        });
        
        var posterAppraise = new WidgetAppraise({
            'elemSelector': 'span.course_threadApprise',
            success: function(response, ele) {
                var data = JSON.parse(response);
                if (!data.success) {
                    Notify.danger(data.message);
                    return false;
                }
                var goodInfo = data.goodInfo;
                if (data.type == 'add') {
                    var zanNum = goodInfo['good'];
                    var zan = zanNum + '人赞';
                    ele.html(zan);
                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                } else {
                    var exitNum = goodInfo['good'];
                    var exit = exitNum + '人赞';
                    ele.html(exit);
                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                }
            },
        }).render();
        posterAppraise.init();

        $('.course-exit-btn').on('click', function() {
            var $btn = $(this);

            if (!confirm('您真的要退出学习吗？')) {
                return false;
            }

            $.post($btn.data('url'), function() {
                window.location.href = $btn.data('goto');
            });
        });

        $('#next-learn-btn').tooltip({placement: 'top'});
        $('#question-sign').tooltip({placement: 'right'});
    };

});