define(function(require, exports, module) {

	require('jquery.select2-4.0.0');
	require('jquery.select2-css-4.0.0');


	function formatState (state) {
		if (!state.id) { return state.text; }
		var opt = state.id;
		var logo = $('#course option[value='+opt+']').data('logo');
		if (!logo) { return state.text; }
		var $state = $(
			'<span><img src="' + logo + '" width="25px" /> ' + state.text + '</span>'
		);
		return $state;
	}

    exports.run = function() {

	    $('#course').select2({
		    templateResult : formatState,
		    placeholder: "请选择课程"
	    });

    };

});