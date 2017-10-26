define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('jquery.select2-css');
    require('jquery.select2');
    exports.run = function() {
        var $form = $("#daily-create-form");
        var $modal = $('#daily-create-form').parents('.modal');
        var validator = new Validator({
            element: '#daily-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#daily-create-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(info) {
                  //  console.info(typeof info);
                      if(info["status"] ==1000){
                            $modal.modal('hide');
                           Notify.success(info["msg"]);
                           window.location.reload();
                      }else{
                            $('#daily-create-btn').button('submiting').removeClass('disabled');
                             $('#daily-create-btn').button('submiting').text('提交');
                             Notify.info(info["msg"]);
                             return false;
                      }
                }).error(function() {
                    Notify.danger('添加失败');
                },'json');

            }
        });
        validator.addItem({
            element: '[name="email"]',
            required: true,

        });
    };

});