define(function (require, exports, module) {

var Notify = require('common/bootstrap-notify');

exports.run = function () {

    var $form = $('#apply-form');
    var $modal = $form.parents('.modal');

    $('#apply-save-btn').click(function () {
        $.post($form.attr('action'), $form.serialize(), function (response) {
            $modal.modal('hide');
            if(response.status == 1)
                Notify.success(response.info);
            else
                Notify.danger(response.info);
            window.location.reload();
        }, 'json').error(function () {
            Notify.danger('审核操作失败!');
        });
    });

    $('select[name=status]').change(function(){
        if($(this).val() == 2){
            $('div.failType').removeClass('hidden');
        }else{
            $('div.failType').addClass('hidden');
        }
    })

}

});