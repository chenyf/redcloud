define(function (require, exports, module) {
	var NProgress = require('nprogress');
	var history = require('jquery.history');

	NProgress.configure({showSpinner : true});
        
	/**
	 * IE版本过低，无刷新时会出错
	 * @author fubaosheng 2016-01-27
	 */
	if(window.isIE()){
		var appVersion = window.navigator.appVersion.split(";");
		var version = appVersion[1].replace(/[ ]/g,"");
		var ieArr = ['MSIE5.0','MSIE6.0','MSIE7.0','MSIE8.0','MSIE9.0'];
		if($.inArray(version,ieArr) != -1){
			$(".ajaxLoad").removeClass("ajaxLoad");
		}
	}

	/**
	 * form无刷新
	 */
	$(document).on('submit', 'form.ajaxLoad', function (event) {
		event.preventDefault();
		var url = $(this).attr('action') ? $(this).attr('action') : window.location.href;
		var data = $(this).serialize();
		reloadPage({
			url : url,
			container : 'div.reload-container',
			block : '#panel-body',
			data : data
		});
	});

	/**
	 * a链接无刷新
	 */
	$(document).on('click', 'a.ajaxLoad', function (event) {
		event.preventDefault();
		var url = $(this).attr('href');
		reloadPage({
			url : url,
			container : 'div.reload-container',
			block : '#panel-body'
		});
	});

	/**
	 * 重新载入页面区块
	 * -----------
	 * params.url 请求的页面地址
	 * params.data 请求的参数
	 * params.container 需要重新载入的页面容器
	 * params.block 需要返回页面的哪一块
	 * -----------
	 * @param params
	 */
	var reloadPage = function (params) {
		var url = params.url;
		var container = params.container;
		var block = params.block;
		var data = params.data;
		NProgress.start();
		if ( url.length > 0 ) {
			History.pushState({state : 2}, $("title").text(), url);
			$(container).load(url + ' ' + block, data, function () {
				var controllerJs =$(block).find('span.controller-js').data('name');
				if(controllerJs){
					app.load(controllerJs);
				}
				NProgress.done();
			})
		}
	};

});