define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('jquery.bootstrap-datetimepicker');
    exports.run = function() {
        var $teacherManage = $('.ask-manage-list');
        var $courseBox = $('.t-course-set-box');
        require('../../util/prevent-page')($teacherManage, $courseBox);

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

        var $searchForm = $('#search-form');
        var $askManageList = $('#ask-manage-list');
        $askManageList.load($searchForm.attr('action'));

        $('#search-form').submit(function(e) {
            var $askManageList = $('#ask-manage-list');
            $.get(
                    $(this).attr('action'),
                    $(this).serialize(),
                    function(html) {
                        $askManageList.html(html);
                    });

            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
        });

        $searchForm.find('i').on('click', function() {
            if ($(this).hasClass('fa-circle-thin')) {
                $('[name=' + $(this).attr('name') + ']').removeClass('fa-circle-o fa-circle-thin');
                $(this).addClass('fa-circle-o');
                $(this).parent().siblings().find('i').addClass('fa-circle-thin');
                $(this).parent().siblings('input[name = ' + $(this).attr('name') + ']').val($(this).data('val'));
            }
        });

        //区域显示（隐藏）
        $('.teacher-answer').on('mouseout', function() {
            $(this).removeClass('active');
            $('.option-block').hide();
        });

        $('.teacher-answer').on('mouseover', function() {
            $(this).addClass('active');
            $('.option-block').show();
        });

        $('.option-block').on('mouseover', function() {
            $('.teacher-answer').addClass('active');
            $('.option-block').show();
        });

        $('.option-block').on('mouseout', function() {
            $('.teacher-answer').removeClass('active');
            $('.option-block').hide();
        });

        $('#reset').on('click', function() {
            $('[name=check_manage]').click();
        });

    }
})
