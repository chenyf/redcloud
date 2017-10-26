define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
	var NProgress = require('nprogress');

    exports.run = function(){
            $("input[name='vps-choose']").on('click',function(){
				$("#confirm_select_btn").removeAttr("disabled");
			});
			
			$("#select_vps_btn").on("click",function(){
				var $this = $(this);
				if($("input[name='vps-choose']:checked").length > 0){
					var vpsid = $("input[name='vps-choose']:checked").val();
					console.log(vpsid);
					$.post($this.data('posturl'),{vpsid:vpsid},function(data){
						data = $.parseJSON(data);
						var errorCode = parseInt(data.error);
						if(errorCode == 1){
							Notify.danger(data.message,3);
							reloadPage($("input[name='vps_list_url']").val(),"#vps_body","#vps_list_body");
						}else if(errorCode > 0){
							Notify.danger(data.message,3);
						}else{					
							Notify.success(data.message,3);							
							reloadPage($("input[name='vps_show_url']").val(),"#vps_body","#my_vps_body");
							window.location.reload();	//刷新页面
						}
						
						$this.parents(".modal").modal('hide');
					});
				}
			});
			
			var reloadPage = function(url,container,block,callback){
				NProgress.start();
				$(container).load(url + " " + block,function(){
					NProgress.done();
					callback && callback();
				});
			}

			//开机、关机、重启
			$(".vps-option-btn").on('click',function(){
				 $this = $(this);
				 var key = $this.data('key');
				 var btntxt = $this.text();
				if(!confirm("确定要" + btntxt + "吗？")){
					return;
				}
				 var dotxt = $this.data('dotxt');
				 $this.text(dotxt).attr("disabled","disabled");
				 $.post($this.data('url'),{key:key},function(response){
					 if(parseInt(response.code) > 0){
						 Notify.danger(response.message,3);
					 }else{
						 Notify.success(response.message,3);
					 }
					 $this.text(btntxt).removeAttr("disabled");
				 },"json").error(function(){
					 Notify.danger("发生错误，稍候重试！",3);
					 $this.text(btntxt).removeAttr("disabled");
				 });
			});
    }
});
