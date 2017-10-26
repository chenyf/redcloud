define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function () {

        $(".removeApply").click(function () {
            
            var applyId = $(this).data("applyid");
            var url = $(this).data("url");
            
            $.get(url, {applyId:applyId}, function (response) {
                if ( response.status == 1 ) {
                    Notify.success('取消申请成功');
                    setTimeout(function(){
                        window.location.reload();
                    },1000);
                } else {
                    Notify.danger(response.info);
                    if(response.data == "ERROR_STATUS"){
                        setTimeout(function(){
                            window.location.reload();
                        },1000);
                    }
                }
            }, 'json').error(function () {
                Notify.danger('取消申请失败');
            }); 
            
        });
    }
});