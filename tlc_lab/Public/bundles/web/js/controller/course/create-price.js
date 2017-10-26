define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');

	var CategorySelect = require('category-select');

	exports.run = function () {

		var $form = $('#apply-form');
		var $modal = $form.parents('.modal');
                $("#coursePrice").blur(function(){
                    var reg = /^[0-9]{1,5}$/;
                    var val = $("#coursePrice").val();
                    var redcloudlever = $("#redcloudlever").val();

                    price=val*(1-redcloudlever/100);

                   $("#course-Price").html(price.toFixed(2)+"="+val+"*"+"(1"+"-"+redcloudlever+"%)");
                    if(!reg.test(val)){
                        Notify.danger("请输入有效的课程价格，课程价格区间为0—99999的整数");
                        return false;
                    }
                });
                                
                $("#protocol").on("click",function(){
                    if(!$("#protocol").is(":checked")){
                        Notify.danger("协议必须勾选");
                        return false;
                    }
                })

		$('#save-btn').click(function () {
                        var data = $form.serialize();
                        var protocol;
                        if($("#protocol").is(":checked")) protocol = 1;
                        else protocol = 0;
                        data = data+"&protocol="+protocol;
                         var reg = /^[0-9]{1,5}$/;
                         var val = $("#coursePrice").val();
                        if(!reg.test(val)){
                            Notify.danger("请输入有效的课程价格，课程价格区间为0—99999的整数");
                            return false;
                         }
			$.post($form.attr('action'), data, function (response) {
				if ( response.status == 1 ) {
					$modal.modal('hide');
					Notify.success('申请成功');
                                        setTimeout(function(){
                                           
                                        },500);
				} else {
					Notify.danger(response.info);
				}

			}, 'json').error(function () {
				Notify.danger('申请失败');
			});
		});



	}

});