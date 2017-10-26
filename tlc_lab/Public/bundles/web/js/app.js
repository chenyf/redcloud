define(function(require, exports, module) {
	window.$ = window.jQuery = require('jquery');
	require('bootstrap');
	require('common/bootstrap-modal-hack2');
        func = require('function');
  
	exports.load = function(name) {
            if (seajs.data['paths'][name.split('/', 1)[0]] == undefined) {
                name = seajs.data['basePath'] + '/bundles/web/js/controller/' + name;
            }
            seajs.use(name, function(module) {
                if ($.isFunction(module.run)) module.run();
            });
	};

    exports.loadJsScript = function(scripts) {
            for(var index in scripts) {
                    exports.load(scripts[index]);
            }
	};

    exports.globalData = {

        DATA_FETCH_URL_PREFIX : "myfiles"

    };

	window.app.load = exports.load;

    window.app.globalData = exports.globalData;

	if(seajs.data["controller"]) exports.load(seajs.data["controller"]);
        if(seajs.data["dashboardJs"]) exports.load(seajs.data["dashboardJs"]);
	if(seajs.data["scripts"]) exports.loadJsScript(seajs.data["scripts"]);

        //检测版本
        func.checkVersion();
        //右击浮动
        func.floatConsult();
        //全局点击
        func.globalClick();
        //ajax全局发送事件
        func.ajaxSend();
        //ajax全局错误事件
        func.ajaxError();
        //a标签ajax方式点击
        func.ajaxLoadATag();
        //图片出错 默认图片显示
        //func.imgErrorDefault();

        $('.login-ajax').click(function(){
            $("#login-modal").modal('show');
            $.get($('#login-modal').data('url'), function(html){
                    $("#login-modal").html(html);
            });
        });

        $("[data-toggle='tooltip']").tooltip();

});