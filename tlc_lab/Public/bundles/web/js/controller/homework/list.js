define(function(require, exports, module) {
	var Notify = require('common/bootstrap-notify');
    exports.run = function() {

	    $('select[name=classId]').change(function(){
		    $('form').submit()
	    });

	    $('.del-homework').click(function(){
		    if(confirm('是否要删除作业？删除后如果想找回,请联系网站管理员')){
			    var url = $(this).data('url');
			    $.ajax({
				    url:url,
				    type:'POST',
				    success:function(data){
					    if(data.status=1){
						    Notify.success(data.message);
						    setTimeout(function(){
							    window.location.reload();
						    },800);
					    }
				    }
			    })
		    }
	    })
    };

});