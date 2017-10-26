define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("$");
    require('./creator/base.js').run();
    window.questions = {};//手动添加的试题
    window.counts = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    window.scores = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    exports.run = function() {
        require('../../util/collapse')('hide');
        //加载题库试题
        var $classId = $('form').find('select[name=classId]').val();
        var $type = $('form').find('select[name=type]').val();
        $.ajax({
            type: 'post',
            url: $('form').data('url'),
            data: {classId: $classId, type: $type},
            success: function(response) {
                $('.history-homework').html(response);
            }
        });
        //选择已经创建的试卷
        $('form').find('select[name=classId],select[name=type]').on('change', function() {
            var $classId = $('form').find('select[name=classId]').val();
            var $type = $('form').find('select[name=type]').val();
            $.ajax({
                type: 'post',
                url: $('form').data('url'),
                data: {classId: $classId, type: $type},
                success: function(response) {
                    $('.history-homework').html(response);
                }
            });
        });
    }
});

