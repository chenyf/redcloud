
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
	require('bootstrap.datetimepicker');

	exports.run = function() {

		$('.datetimepicker').datetimepicker({
			locale : 'zh_cn',
			format : 'YYYY-MM-DD ',
			viewMode: 'days'
		});


		var validator = new Validator({
			element: '#schoolCourse-form',
			autoSubmit: false
		});

		validator.addItem({
			element: '#endTm',
			required: true,
			errormessageRequired: '请选择截止时间'
		});

		validator.addItem({
			element: '[name=startTm]',
			required: true,
			errormessageRequired: '请选择生效时间'
		});
                
		validator.addItem({
			element: '[name=maxNum]',
			required: true
		});

                var id=$('[name="id"]').val();
                if(id==0){
                    $('#school').change(function(){
                       $('#selectCourse').load('/Center/Course/SelectCourseAction/courseId/0/webCode/'+this.value);
                    });
//                    validator.addItem({
//                            element: '#course',
//                            required: true,
//                            errormessageRequired: '请选择课程'
//                    });
                }
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
					$('#save-btn').removeClass('disabled');
					if(data.status > 0){
						$('#modal').modal('hide');
						Notify.success(data.info);
						window.location.reload();
						return true;
					}
					Notify.danger(data.info);
				}
			});
		});


	};
});