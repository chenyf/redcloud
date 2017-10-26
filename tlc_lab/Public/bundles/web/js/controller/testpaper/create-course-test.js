define(function(require, exports, module) {
    var Widget = require('widget');
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        var TestpaperForm = new Validator({
            element: '#testpaper-form'
        });

        // if ($('#testpaper-form [name=classId]').length > 0) {
        //     TestpaperForm.addItem({
        //         element: '#testpaper-form [name=classId]',
        //         required: true,
        //         errormessageRequired: '请选择你的授课班。'
        //     })
        // }

        TestpaperForm.addItem({
            element: '#testpaper-name-field',
            required: true,
            rule: 'minlength{"min":1} maxlength{"max":80}'
        })
        TestpaperForm.on('formValidated', function(error, results, element) {
            var name = $('#testpaper-name-field').val();
            var re = new RegExp("^[ ]+$");
            if (error) {
                return false;
            }
            if (re.test(name)) {
                Notify.danger('名称不能空字符串');
                TestpaperForm.set('autoSubmit', false);
            } else {
                $('#create-btn').button('submiting').addClass('disabled');
                TestpaperForm.set('autoSubmit', true);
            }
        });

        $("input[name='correct']").on('click',function(){
            if($(this).is(':checked')){
                $(this).next('div').text("批阅");
            }else{
                $(this).next('div').text("不批阅");
            }
        });

        $('#create-btn').on('click', function() {
            if ($(this).hasClass('disabled'))
                return false;
        });

        var $method = $('input[name=mode]');
        $('.select-Method').on('click', function() {
            selectMethod($(this), $method);
        });
        var selectMethod = function(_this, _input) {
            _this.addClass('active').siblings('a').removeClass('active').find('.selected-icon').remove();
            _this.find('.selected-icon').remove();
            _this.append('<em class="fa fa-check-square-o selected-icon"></em>');
            _input.val(_this.data('val'));
            return _this.data('val');
        }

        //设置默认的作业名称
        var $tyepText = $('input[name=tyepText]');
        var nowDate = new Date();
        var month = nowDate.getMonth() + 1;
        $('[name=classId]').on('change', function() {
            var text = $(this).find('option:selected').text();
            if ($(this).val() != '') {
                $('input[name=name]').val(text + $tyepText.val() + nowDate.getFullYear() + (month < 10 ? "0" + month : month) + nowDate.getDate());
                $('#testpaper-name-field').blur();
            }
        });
    }
});