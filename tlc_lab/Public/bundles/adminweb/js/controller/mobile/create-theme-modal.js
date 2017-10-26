define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
	require('jquery.form');

    exports.run = function() {

        var $form = $('#theme-form');
        var $modal = $form.parents('.modal');

        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return ;
                }
                $('#theme-save-btn').button('submiting').addClass('disabled');
	            $form.ajaxSubmit({
		            dataType:"json",
		            success:function(data){
			            console.log(data);
			            if(data.status >= 1){
				            $modal.modal('hide');
				            Notify.success(data.info);
				            window.location.reload();
			            }else{
				            Notify.danger(data.info)
			            }
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