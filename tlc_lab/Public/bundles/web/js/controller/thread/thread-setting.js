define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        var $teacherManage = $('.ask-set-teacher');
        var $courseBox = $('.t-course-set-box');
        //阻止点击分页超链接事件
        require('../../util/prevent-page')($teacherManage, $courseBox);

        var validator = new Validator({
            element: '#thread-set-form'
        });

        validator.addItem({
            element: 'input[name="maxNumber"]',
            required: true,
//            rule: 'integer,max{max:1}'
        });

        validator.addItem({
            element: 'input[name="maxTime"]',
            required: true,
        });

        $('form[name=add-answer-form]').find('button[type=submit]').on('click', function() {
            if ($('.add-teacher-box').find('[name=teacherId]').val() != undefined) {
                $('#form[name=add-answer-form]').submit();
            } else {
                Notify.danger('请选择老师！');
                return false;
            }
        });

        $answerForm = $('form[name=add-answer-form]');
        $answerForm.find('[type=submit]').on('click', function() {
            $.post($answerForm.attr('action'), $answerForm.serialize(), function(data) {
                if (data.status == 'success') {
                    Notify.success(data.msg);
                } else {
                    Notify.danger(data.msg);
                }
                $('.t-course-set-box').load(data.url);
            })
            $('#myModal').modal('hide');
            return false;
        });

        $('#save').on('click', function() {
            var $form = $('#thread-set-form');
            $.post($form.attr('action'), $form.serialize(), function(data) {
                if (data.status) {
                    $('.t-course-set-box').load(data.url);
                    Notify.warning(data.info);
                    return false;
                } else {
                    Notify.warning(data.info);
                    return false;
                }
            });
            return false;
        });

        $form = $('[name=searchTea]');
        $('#searchTeacher').on('click', function() {
            var url = $(this).data('search');
            var email = $('[name=email]').val();
            $.get(
                    url,
                    {email: email},
            function(data) {
                if (data.status == 1000) {
                    $('.add-teacher-box').html(data.html);
                } else {
                    alert(data.msg);
                }

            });
        });

        $('.teacher-manage').find('[type=button]').on('click', function() {
            var url = $(this).data('url');
            if (confirm('确定要删除该老师吗？')) {
                $.get(
                        url,
                        function(data) {
                            if (data.status == 1000) {
                                $('.t-course-set-box').load(data.url);
                                alert(data.msg);
                            } else {
                                $('.t-course-set-box').load(data.url);
                                alert(data.msg);
                            }
                        });
            }
        });

        $('.ask-set-box .custom-check').on('click', function() {
            customCheck($(this), $(this).attr('name'));
        });

        function customCheck(_this, name) {
            $('input[name=' + name + ']').val(_this.data('is'));
            if (_this.hasClass('fa-check-square')) {
                $('[name=' + name + ']').removeClass('fa-check-square').removeClass('fa-square-o');
                _this.addClass('fa-square-o');
                _this.siblings().addClass('fa-check-square');
            } else {
                $('[name=' + name + ']').removeClass('fa-check-square').removeClass('fa-square-o');
                _this.addClass('fa-check-square');
                _this.siblings().addClass('fa-square-o');
            }
        }



    }
})
