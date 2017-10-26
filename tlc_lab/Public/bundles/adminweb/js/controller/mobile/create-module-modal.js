define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {

        var $form = $('#module-form');
        var $modal = $form.parents('.modal');

        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return ;
                }
                $('#module-save-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(data){
	                console.log(data);
	                if(data.status >= 1){
		                $modal.modal('hide');
		                Notify.success(data.info);
		                window.location.reload();
	                }else{
		                Notify.danger(data.info)
	                }

                });

            }

        });

        validator.addItem({
            element: '[name="name"]',
            required: true
        });

        validator.addItem({
            element: '[name="title"]',
            required: true
            
        });


    };

});