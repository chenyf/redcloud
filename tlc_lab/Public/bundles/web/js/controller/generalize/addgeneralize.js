define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	exports.run = function () {

		$('#login-modal').on('hidden.bs.modal', function (e) {
			window.location.reload();
		});
                var n =1;
                $("#add-btn").click(function(){
                   if(n!=1){
                       return false;
                   }
                   n=n+1;
                })
                
                 var m =1;
                $(".add-btn").click(function(){
                   if(m!=1){
                       return false;
                   }
                   m=m+1;
                })



	}

});