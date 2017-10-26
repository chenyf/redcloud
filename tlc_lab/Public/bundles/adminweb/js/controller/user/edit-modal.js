define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');

    exports.run = function() {

        var $modal = $('#user-edit-form').parents('.modal');

        var validator = new Validator({
             element: '#user-edit-form',
             autoSubmit: false,
             failSilently: true,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#edit-user-btn').button('submiting').addClass('disabled');

                $.post($form.attr('action'), $form.serialize(), function(response) {
                    response = JSON.parse(response);
                    if(!response.status){
                        Notify.danger(response.msg,3);
                        $('#edit-user-btn').button('reset').removeClass('disabled');
                        return false;
                    }else{
                        $modal.modal('hide');
                        Notify.success('用户信息保存成功',2,function(){
                            window.location.reload();
                        });
                    }
                }).error(function(){
                    Notify.danger('操作失败');
                });
            }
        });

        };

});