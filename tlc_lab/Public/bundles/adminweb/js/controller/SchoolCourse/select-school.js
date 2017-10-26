define(function(require, exports, module) {

	require('jquery.select2-4.0.0');
	require('jquery.select2-css-4.0.0');


	function formatStateSchool (state) {
		if (!state.id) { return state.text; }
		var opt = state.id;
		return state.text; 
	}

    exports.run = function() {

	    $('#school').select2({
		    templateResult : formatStateSchool,
		    placeholder: "请选择学校"
	    });

    };

});