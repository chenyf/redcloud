define(function(require, exports, module) {
	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

    exports.run = function() {
	    $('[data-toggle="popover"]').popover();
	    var $form = $('#apply-form');
	    var $modal = $form.parents('.modal');

	     new Validator({
		    element: $form,
		    autoSubmit: false,
		    onFormValidated: function(error, results, $form) {
			    if (error) {
				    return ;
			    }
			    $('#version-save-btn').button('submiting').addClass('disabled');
			    $.post($form.attr('action'), $form.serialize(), function(data){
				    $modal.modal('hide');
				    if(data.status == 'error'){
					    Notify.danger(data.info);
					    return false;
				    }
				    Notify.success('审核成功！');
				    window.location.reload();
			    });

		    }

	    });
    };
  
});