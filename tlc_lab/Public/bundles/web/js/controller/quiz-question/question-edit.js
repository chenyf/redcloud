define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    exports.run = function() {

        var $form = $('form[id=question-create-form]');
//        validator = new Validator({
//            element: $form,
//            autoSubmit: true,
//        });
        $('button[type=submit]').on('click', function() {
            window.validator.execute(function(error, results, element) {
                if (!error) {
                    $.ajax({
                        url: $form.attr('action'),
                        type: 'post',
                        data:$form.serialize(),
                        success: function(response) {
                            if (response) {
                                var url;
                                $(".pagination li").each(function() {
                                    if ($(this).hasClass('active')) {
                                       url = $(this).find('a').attr('href');
                                    }
                                });
                                $.ajax({
                                    type: 'post',
                                    url: url,
                                    success: function(response) {
                                        $('.random-question').remove();
                                        $('#group_list').append(response.list);
                                        $('#page').html(response.page);
                                        $('#modal').modal('hide');
                                    }
                                });
                            }
                        }
                    });
                }
            });

        });


        _prepareFormData = function($form) {
            var answers = [];
            $form.find(".answer-checkbox").each(function(index) {
                if ($(this).prop('checked')) {
                    answers.push(index);
                }
            });
            if (0 == answers.length) {
                return false;
            }
            return true;
        }


    }

});