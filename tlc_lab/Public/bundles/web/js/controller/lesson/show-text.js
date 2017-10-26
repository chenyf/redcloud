define(function (require, exports, module) {

	var $ = require('jquery/1.11.2/jquery');

	var imglist = $('img');
	var _width;
	_width = $('.content').width();
	for (var i = 0, len = imglist.length; i < len; i++) {
		DrawImage(imglist[i], _width);
	}
	window.onresize = function () {
		//捕捉屏幕窗口变化，始终保证图片根据屏幕宽度合理显示
		_width = $('.content').width();
		for (var i = 0, len = imglist.length; i < len; i++) {
			DrawImage(imglist[i], _width);
		}
	}

	function DrawImage(ImgD, _width) {
		var image = new Image();
		image.src = ImgD.src;
		image.onload = function () {
			//限制，只对宽高都大于30的图片做显示处理
			if ( image.width > 30 && image.height > 30 ) {
				if ( image.width > _width ) {
					ImgD.width = _width;
					ImgD.height = (image.height * _width) / image.width;
				} else {
					ImgD.width = image.width;
					ImgD.height = image.height;
				}

			}
		}

	}

});