define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	var Validator = require('bootstrap.validator');
	require('jquery.select2-css');
	require('jquery.select2');
	require('uploadPreview');
	require('jquery.form');

	exports.run = function () {

		/**
		 * 学生证上传
		 */
		new uploadPreview({UpBtn : "up_photo", DivShow : "imgdiv", ImgShow : "imgShow"});

		/**
		 * 表单提交
		 * @type {*|jQuery|HTMLElement}
		 */
		var $form = $('#pub-form');
		var $modal = $form.parents('.modal');
		var $btn = "#sub-btn";
		var validator = new Validator({
			element : $form,
			autoSubmit : false,
			onFormValidated : function (error, results, $form) {
				if ( error ) {
					return;
				}
				$($btn).button('submiting').addClass('disabled');
				$form.ajaxSubmit({
					success : function (data) {
						if ( data.status == 1 ) {
							Notify.success('申请提交成功');
							setTimeout(function(){
								$modal.modal('hide');
								window.location.reload();
							},800)
						} else {
							Notify.danger(data.info);
							$($btn).button('submiting').removeClass('disabled').text('提交申请');
						}
					}
				});
			}
		});

		validator.addItem({
			element : '#name',
			required : true,
			errormessageRequired : '请输入姓名'
		});

		validator.addItem({
			element : '#stud_num',
			required : true,
			rule : "stud_num,maxlength{max:20},integer",
			errormessageRequired : '请输入学号'
		});

		validator.addItem({
			element : '#remark',
			rule : 'maxlength{max:100}'
		});

		validator.addItem({
			element : '#up_photo',
			required : true,
			errormessageRequired : '请上传学生证照片'
		});

		var selectClass = '#select_class';
		if ( $(selectClass).length > 0 ) {
			validator.addItem({
				element : selectClass,
				required : true,
				errormessageRequired : '请选择您所在的班级'
			});

			/**
			 * 班级搜素
			 */
			$(selectClass).select2({
				placeholder : "请选择您所在的班级",
				minimumInputLength : 1,
				separator : ",", // 分隔符
				maximumSelectionSize : 10, // 限制数量
				initSelection : function (element, callback) { // 初始化时设置默认值
				},
				createSearchChoice : function (term, data) { // 创建搜索结果（使用户可以输入匹配值以外的其它值）
					return {
						id : term.id,
						text : term.title
					};
				},
				formatSelection : function (item) {
					return item.title;//注意此处的name，要和ajax返回数组的键值一样
				}, // 选择结果中的显示
				formatResult : function (item) {
					return item.title;//注意此处的name
				}, // 搜索列表中的显示
				ajax : {
					url : "getStudClassAction", // 异步请求地址
					dataType : "json", // 数据类型
					data : function (term, page) { // 请求参数（GET）
						return {
							className : term
						};
					},
					results : function (data, page) {
						return data;
					}, // 构造返回结果
					escapeMarkup : function (m) {
						return m;
					} // 字符转义处理
				}
			});
		}
	}
});