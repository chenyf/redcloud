define(function(require, exports, module) {
	var Notify = require('common/bootstrap-notify');
	exports.run = function() {
		$('#selectStu').on('change', function() {
                        var $btn = $(this);
//			$.get($btn.data('url'),{type:$(this).val()}, function(response) {
//                            location.reload();
//			});
                window.location.href = $btn.data('url')+'/type/'+$(this).val();
		});
                
	};

});