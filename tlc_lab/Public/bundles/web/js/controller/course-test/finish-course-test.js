define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('bootbox');
    require('jquery.bootstrap-datetimepicker');
    var now = new Date();
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

        if($("input[name='mode']").val() != 'file') {
            $('#finishSelectedStatistic').load('/Course/CourseTestpaperManage/getselectedStatisticAction');
        }

        var $form       = $('#publish');
        var $saveBtn    = $('button[name=save]');
        var $publishBtn = $('button[name=publish]');
        $saveBtn.on('click', function() {
            $saveBtn.button('loading').addClass('disabled');
            $.ajax({
                type: 'post',
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function(data) {
                    if (data.status == 0) {
                        Notify.danger(data.info);
                        $saveBtn.button('reset');
                        return false;
                    } else {
                        Notify.success(data.info);
                    }
                    window.location.href = $form.data('list');
                }
            });
            return false;
        });
        
        //发布作业
        $publishBtn.on('click', function() {
            $publishBtn.button('loading').addClass('disabled');
            $.ajax({
                type: 'post',
                url: $form.attr('action'),
                data: $form.serialize(),
                success: function(data) {
                    if (data.status == 0) {
                        Notify.danger(data.info);
                        $publishBtn.button('reset');
                        return false;
                    } else {
                        $.ajax({
                            type: 'get',
                            url: $form.data('publish'),
                            success: function(data) {
                                if (data.status == 0) {
                                    Notify.danger(data.info);
                                    $publishBtn.button('reset');
                                    return false;
                                } else {
                                    Notify.success(data.info);
                                }
                                window.location.href = $form.data('list');
                            }
                        });
                    }
                }
            });
        });
        $('.select-time').on('click', function() {
            if ($(this).hasClass('active')) {   //置为限时
                $(this).removeClass('active').find('em').remove();
                $('input[name=startTime]').removeAttr('disabled');
                $('input[name=endTime]').removeAttr('disabled');
                $('input[name=timeLimit]').val('0');
            } else {    //置为不限时
                $(this).addClass('active').append('<em class="fa fa-check-square-o selected-icon"></em>');
                $('input[name=startTime]').attr('disabled', 'disabled');
                $('input[name=endTime]').attr('disabled', 'disabled');
                $('input[name=timeLimit]').val('1');
            }
        });
        $('.is-show').on('click', function() {
            var show = $('input[name=show]');
            isShow($(this), show);
        });
        var isShow = function(_this, _input) {
            _this.addClass('active').siblings('a').removeClass('active').find('.selected-icon').remove();
            _this.find('.selected-icon').remove();
            _this.append('<em class="fa fa-check-square-o selected-icon"></em>');
            _input.val(_this.data('val'));
            return _this.data('val');
        }
        var validator = new Validator();
        validator.addItem({
            element: '[name="startTime"]',
            required: true,
            errormessageRequired: '请选择开始时间'
        });
        validator.addItem({
            element: '[name="endTime"]',
            required: true,
            errormessageRequired: '请选择截止时间'
        });
//               
//               Validator.addRule('upper',/^([1-9]\d?|100)$/, '请输入1-100的整数');
//               validator.addItem({
//                        element:'[name="totalScore"]',
//                        required: true,
//                        rule: 'upper',
//                        errormessageRequired: '请填写总分数'
//               });


    };

});