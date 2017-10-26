define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        require('../test-paper/creator/batch-select')();
        //分数改变时更新保存的分数
        $('input[name=score]').on('blur', function() {
            var reg = /^(100|[1-9]\d?\.\d{1,2}|[1-9]\d?)$/;
            if (!reg.test($(this).val())) {
                Notify.danger('请输入1-100之间的分数');
                $(this).focus();
                $('#build-testpaper').addClass('disabled');
                return false;
            }else{
                $('#build-testpaper').removeClass('disabled');
            }
            var $parent = $(this).parents('tr');
            var qid = $parent.data('id');
            if ($parent.find('input[name=groups]').prop('checked'))
                window.questions[qid].score = parseFloat($(this).val());
            $('#selectedStatistic').load($('input[name=refreshUrl]').data('url'));
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
    };

});