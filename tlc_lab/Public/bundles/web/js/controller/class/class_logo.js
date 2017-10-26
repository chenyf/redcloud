define(function (require, exports, module) {
	require('ajaxfileupload');
	require("jquery.jcrop-css");
	require("jquery.jcrop");
	require('jquery.form');

	exports.run = function () {

		var $form = $("#crop-form"),
			$picture = $("#avatar-crop");

		//上传图片
		var uploadFile = '#picture-field';
		var uploadUrl = $(uploadFile).data('url');
		$(uploadFile).ajaxfileupload({
			'action' : uploadUrl,
			'onComplete' : function (response) {
				$('#upload-form').hide();
				cropLogo(response);
			},
			'onStart' : function () {

			},
			'onCancel' : function () {
				console.log('no file selected');
			}
		});

		//裁剪图片
		function cropLogo(response) {
			$form.show();
			$picture.attr('src', response.pictureUrl);
			$picture.attr('width', response.scaledSizeWidth);
			$picture.attr('height', response.scaledSizeHeight);
			$form.find('[name=filename]').val(response.pictureUrl);
			$form.find('[name=type]').val(response.type);

			var scaledWidth = $picture.attr('width'),
				scaledHeight = $picture.attr('height'),
				naturalWidth = response.naturalSizeWidth,
				naturalHeight = response.naturalSizeHeight;
			if ( response.type == "logo" ) {
				var cropedWidth = 250,
					cropedHeight = 250;

			} else {
					cropedWidth = 1140,
					cropedHeight = 350;
			}
			var ratio = cropedWidth / cropedHeight,
				selectWidth = 300 * (naturalWidth / scaledWidth),
				selectHeight = 300 * (naturalHeight / scaledHeight);

			$picture.Jcrop({
				trueSize : [naturalWidth, naturalHeight],
				setSelect : [0, 0, selectWidth, selectHeight],
				aspectRatio : ratio,
				onSelect : function (c) {
					$form.find('[name=x]').val(c.x);
					$form.find('[name=y]').val(c.y);
					$form.find('[name=width]').val(c.w);
					$form.find('[name=height]').val(c.h);
				}
			});
		}

		//保存图片
		$form.ajaxForm({
			dataType : 'json',
			success : function (data) {
				$('.modal').modal('hide');
				if(data.type == "logo"){
					$('#classLogo').find('img').attr('src', data.url)
					$('input[name="logo"]').val(data.uri);
				}else{
					$('#classBackgroundLogo').find('img').attr('src', data.url)
					$('input[name="backgroundLogo"]').val(data.uri);
				}

			}
		});

	};

});

