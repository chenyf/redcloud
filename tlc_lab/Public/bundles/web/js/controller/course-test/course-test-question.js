define(function (require, exports, module) {

	var QuestionBase = require('../quiz-question/creator/question-base');
	var Choice = require('../quiz-question/creator/question-choice');
	var Determine = require('../quiz-question/creator/question-determine');
	var Essay = require('../quiz-question/creator/question-essay');
	var Fill = require('../quiz-question/creator/question-fill');

	require('jquery.form');
	require('bootbox');

	exports.run = function () {
                //$('.identifier').tooltip(options)
		var type = $('#question-creator-widget').find('[name=question_type]').val().replace(/\_/g, "-");

		var QuestionCreator;
		switch (type) {
			case 'single-choice':
			case 'uncertain-choice':
			case 'choice':
				QuestionCreator = Choice;
				break;
			case 'determine':
				QuestionCreator = Determine;
				break;
			case 'essay':
				QuestionCreator = Essay;
				break;
			case 'fill':
				QuestionCreator = Fill;
				break;
			default:
				QuestionCreator = QuestionBase;
		}

		var creator = new QuestionCreator({
			element : '#question-creator-widget',
			'form-ajax' : true
		});

		creator.get('validator').on('formValidated', function (error, msg, $form) {
                        
			if ( error ) {
				return false;
			}

			creator.get('validator').set('autoSubmit', false);
//			if ( type == 'choice' ) {
//				if ( !creator._prepareFormData() ) {
//					return false;
//				}
//			}
                        
			$form.ajaxSubmit({
				dataType : 'json',
				success : function (data) {
					if ( data.needCount > 0 ) {
                                           
						$('.submit-btn').removeClass('disabled');
						$('span.needCount').text(data.needCount);
						bootbox.dialog({
							title : "<strong >题目添加成功 <span class='text-success'>(" + data.itemCount + "/" + data.testCount + ")</span></strong>",
							message : "还需要 <strong class='text-info'>" + data.needCount + "</strong> 道题即可完成，请继续添加题目",
                                                        //取消 回调函数
                                                        onEscape: function() {
                                                            $("input[name='answer[]']").each(function(){
                                                                $(this).remove();
                                                            });
                                                            $('#questionsSelect li').each(function(){
                                                                if($(this).attr('class') != 'active'){
                                                                    $(this).css('display','none');
                                                                }
                                                            });
                                                            $('#isModify').val(data.id);

                                                        },
							'buttons' : {
								choice : {
									label : "<i class='glyphicon glyphicon-plus'></i> 选择题",
									className : "btn-info",
									callback : function () {
										window.location.href = $('a[data-url="choice"]').attr('href');
									}
								},
								panduan : {
									label : "<i class='glyphicon glyphicon-plus'></i> 判断题",
									className : "btn-info",
									callback : function () {
										window.location.href = $('a[data-url="determine"]').attr('href');
									}
								},
								fill : {
									label : "<i class='glyphicon glyphicon-plus'></i> 填空题",
									className : "btn-info",
									callback : function () {
										window.location.href = $('a[data-url="fill"]').attr('href');
									}
								},
								wenda : {
									label : "<i class='glyphicon glyphicon-plus'></i> 问答题",
									className : "btn-info",
									callback : function () {
										window.location.href = $('a[data-url="essay"]').attr('href');
									}
								}
							}
						});
					} else {
                                                console.info($('button.next-step'));
                                                $('#clearDisabled').removeAttr('disabled');
                                                $('.submit-btn').button('submiting').addClass('disabled');
						$('button.next-step').removeAttr('disabled');
						$('span.item-alert').remove();
                                                if(data.type==0){
                                                    var str='<p style="text-align: left">1.请选择当前添加的考试，并发布考试。<span class="text-danger">（注：未发布的考试，学生无法查看）</span></p><p  style="text-align: left">2.考试发布后，学生在“课程考试“中找到相应的考试</p>';
                                                }
                                                if(data.type==1){
                                                    var str='<p style="text-align: left">1.请选择当前添加的作业，并发布作业。<span class="text-danger">（注：未发布的作业，学生无法查看）</span></p><p  style="text-align: left">2.作业发布后，学生在“课程作业“中找到相应的作业';
                                                }
                                                if(data.type==2){
                                                    var str='<p style="text-align: left">1.请选择当前添加的练习，并发布练习。<span class="text-danger">（注：未发布的练习，学生无法查看）</span></p><p  style="text-align: left">2.练习发布后，学生在“课程内容“中找到相应的练习</p>';
                                                }
						bootbox.dialog({
							title : "<strong >题目添加成功 <span class='text-success'>(" + data.itemCount + "/" + data.testCount + ")</span></strong>",
							message : str,
							'buttons' : {
								success : {
									label : '下一步',
									className : "btn-info",
									callback : function () {
										window.location.href = $('a.next-step').attr('href');
									}
								}
							}
						});
					}
				},
				error : function () {
					bootbox.alert({
						message : '操作执行失败',
						title : "提示信息"
					});
					return false;
				}
			})
		});

	};

});