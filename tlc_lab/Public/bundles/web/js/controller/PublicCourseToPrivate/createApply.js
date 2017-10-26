
define(function(require, exports, module){

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);

	exports.run = function() {

		require('../course-manage/select-range').run();

		Validator.addRule('price', /^[1-9][0-9]{0,4}$/, '请输入有效的课程价格，课程价格区间为1—99999的整数');

		var validator = new Validator({
			element: '#pub-form'
		});

		validator.addItem({
			element : '[name=title]',
			required : true,
			rule : 'minlength{min:1} maxlength{max:60}',
			display : '课程名称',
			errormessageRequired : '请输入课程名称'
		});

		validator.addItem({
			element : '[name=price]',
			required : true,
			errormessageRequired : '请输入课程价格',
			rule : 'price'
		});

		validator.addItem({
			element : '[name=categoryId]',
			required : true,
			errormessageRequired : '请选择课程分类',
		});


		validator.on('formValidated', function(error, msg, $form) {
			if (error) {
				return;
			}

		});


	};
});