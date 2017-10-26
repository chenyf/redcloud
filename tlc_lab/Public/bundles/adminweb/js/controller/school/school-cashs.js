define(function(require, exports, module) {

	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');

	require('bootbox');

	require('jquery.bootstrap-datetimepicker');
        var now = new Date();
	exports.run = function() {
 
                $("[name=endTime]").datetimepicker({
                    language: 'zh-CN',
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    minView: 'month'
                });
                $("[name=startTime]").datetimepicker({
                    language: 'zh-CN',
                    autoclose: true,
                    format: 'yyyy-mm-dd',
                    minView: 'month'
                });     
               
	};

});