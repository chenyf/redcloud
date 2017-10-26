define(function(require, exports, module) {
	window.$ = window.jQuery = require('jquery');

	require('placeholder');
	var Notify = require('common/bootstrap-notify');
	require('bootstrap');
	require('common/bootstrap-modal-hack2');
        var func = require('function');

	exports.load = function(name) {
		if (seajs.data['paths'][name.split('/', 1)[0]] == undefined) {
			name = seajs.data['basePath'] + '/bundles/adminweb/js/controller/' + name;
		}

		seajs.use(name, function(module) {
			if ($.isFunction(module.run)) {
				module.run();
			}
		});

	};

	window.app.load = exports.load;

	if(seajs.data["controller"]) exports.load(seajs.data["controller"]);

	$( document ).ajaxSend(function(a, b, c) {
            if (c.type == 'POST') {
                    b.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'));
            }
	});

	$("form.well").find("select").change(function(){
            if($(this).attr('noautochange') != 1)
		$(this).parents('form').submit();
	})

        //a标签ajax方式点击
        func.ajaxLoadATag();

});