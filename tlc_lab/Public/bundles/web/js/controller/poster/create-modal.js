define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	var Validator = require('bootstrap.validator');
        var WidgetAppraise= require('../appraise/appraise.js');
	require('ajaxfileupload');

	exports.run = function () {
                
                var posterAppraise = new WidgetAppraise({
                            'elemSelector': 'span.posterApprise',
                            success:function(response,ele) {
                                var data = JSON.parse(response);
                                if(!data.success) {
                                      Notify.danger(data.message);
                                      return false;
                                 }
                                var goodInfo = data.goodInfo;
                                if(data.type == 'add'){
                                  var zanNum = goodInfo['good'];
                                  var zan = zanNum+'人赞';
                                    ele.html(zan);
                                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                                }else{
                                   var exitNum = goodInfo['good'];
                                   var exit = exitNum+'人赞';
                                    ele.html(exit);
                                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                                } 
                           },
                       }).render();
                
		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			if($(this).attr('href') == '#imgText'){
				$("#text-save-btn").hide();
				$("#imgText-save-btn").show();
			}else if($(this).attr('href') == '#text'){
				$("#text-save-btn").show();
				$("#imgText-save-btn").hide();
			}
		});

		/**
		 * 海报图片上传
		 */
		$('#course-picture-field').ajaxfileupload({
			'action' : '/Poster/Poster/uploadPosterAction',
			'onComplete' : function (response) {
				if ( response.error == 1 ) {
					$(this).val('');
					Notify.danger(response.message);
				} else {
					Notify.success('上传成功');
					$('#preview_pic').find('img').hide().attr('src', response.url).fadeIn();
					$('input[name=picture]').val(response.url)
				}
			},
			'onStart' : function () {

			},
			'onCancel' : function () {
				console.log('no file selected');
			}
		});

		/**
		 * 选择背景色
		 */
		$('.poster-template-img .pic-list').click(function(){
			var color =$(this).data('color');
			var id =$(this).data('id');
			if(id == 1){
				$('#preview_text .bgcolor-text').addClass('text-black').removeClass('text-white')
			}else{
				$('#preview_text .bgcolor-text').addClass('text-white').removeClass('text-black')
			}
			$(this).addClass('active').siblings('li').removeClass('active');
			$('#preview_text').css({'background':color})
			$('input[name=bgColorId]').val(id)
		})


		/**
		 * 海报简介输入事件监听
		 */
		$('textarea[name=desc]').keyup(function(){
			var val = $(this).val();
			var length = val.length;
			if(length >= 500){
				val = val.substring(0,500);
				$(this).val(val);
				length = val.length;
			}
			val = val.replace(/\n/g,'<br/>')
			$(this).parents('.tab-pane').find('#preview_text').find('p').html(val)
			$(this).next('p').text(length+'/500');

		});

		var masonryAddItem = function(html){
			$('.empty').remove();
			grid.prepend( html).imagesLoaded(function(){
				grid.masonry('prepended',html)
			});
                        
		};

		Validator.addRule('maxDesc',/^[\s\S]{0,500}$/, '海报内容不能超过500个字');

		/**
		 * 纯文字表单提交
		 * @type {*|jQuery|HTMLElement}
		 */
		var $textform = $('#poster-form-text');
		var $modal = $textform.parents('.modal');
		var textValidator = new Validator({
			element: $textform,
			autoSubmit: false,
			onFormValidated: function(error, results, $form) {
				if (error) {
					return ;
				}
				$.ajax({
					url:$form.attr('action'),
					data: $form.serialize(),
					type:'POST',
					dataType:"html",
					success:function(data){
						if(data.error != 1){
							$modal.modal('hide');
							Notify.success('海报添加成功');
							masonryAddItem($(data));
                                                        posterAppraise.init();
						}else{
							Notify.danger(data.info)
						}

					}
				});

			}

		});

		textValidator.addItem({
			element: '#poster-form-text [name=cid]',
			required: true,
			errormessageRequired: '请选择海报分类'
		});

		textValidator.addItem({
			element: '#poster-form-text [name=desc]',
			required: true,
			errormessageRequired: '请输入海报内容',
			rule: 'maxDesc'
		});


		/**
		 * 图文表单提交
		 */
		var $imgTextform = $('#poster-form-imgText');

		var imgTextValidator = new Validator({
			element: $imgTextform,
			autoSubmit: false,
			onFormValidated: function(error, results, $form) {
				if (error) {
					return ;
				}
				$.ajax({
					url:$form.attr('action'),
					data: $form.serialize(),
					type:'POST',
					dataType:"html",
					success:function(data){
						if(data.error != 1){
							$modal.modal('hide');
							Notify.success('海报添加成功');
							masonryAddItem($(data));
                                                        posterAppraise.init();
						}else{
							Notify.danger(data.info)
						}

					}
				});

			}

		});

		imgTextValidator.addItem({
			element: '#poster-form-imgText [name=pic]',
			required: true,
			errormessageRequired: '请选择一张图片作为海报'
		});

		imgTextValidator.addItem({
			element: '#poster-form-imgText [name=cid]',
			required: true,
			errormessageRequired: '请选择海报分类'
		});

		imgTextValidator.addItem({
			element: '#poster-form-imgText [name=desc]',
			rule: 'maxDesc'
		});


	}
});