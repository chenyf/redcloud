define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
         require('ckeditor');
	var Notify = require('common/bootstrap-notify');



    exports.run = function() {


        var validator = new Validator({
            element: '#task-form',
            autoSubmit: false
        });

        validator.addItem({
            element: '#task_content',
            required: true
        });

	    validator.addItem({
		    element: '[name=title]',
		    required: true
	    });

	    validator.addItem({
		    element: '#class',
		    required: true,
		    errormessageRequired: '请选择班级'
	    });

        // group: 'course'
        var editor = CKEDITOR.replace('task_content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#task_content').data('imageUploadUrl')
        });

        validator.on('formValidate', function(elemetn, event) {
            editor.updateElement();
        });

        validator.on('formValidated', function(error, msg, $form) {
            if (error) {
                return;
            }
	        $('#save-btn').button('submiting').addClass('disabled');
	        $.ajax({
		       url: $form.attr('action'),
		        type:'POST',
		        data:$form.serialize(),
		        success:function(data){
					if(data.status > 0){
						$('#modal').modal('hide');
						Notify.success(data.info);
						window.location.reload();
						return true;
					}
			        Notify.success(data.info);
		        }
	        });
        });


    };

});