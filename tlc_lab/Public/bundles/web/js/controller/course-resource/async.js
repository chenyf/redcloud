define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function(){

        //同步资料到云盘
        $(".async-resource").on("click",function(){
            var self = $(this);
            var url = self.data("url");
            var courseId = self.data("courseid");
            var id = self.data("id");
            var data = {courseId:courseId,id:id};
            if(self.data('lock') == 1) return false;
            self.data('lock',1);
            $.post(url,data,function(result){
                // result = $.parseJSON(result);
                self.data('lock',0);
                if(parseInt(result.error) > 0){
                    Notify.danger(result.message);
                }else{
                    Notify.success(result.message);
                    window.location.reload();
                }
            },"json").error(function(){
                self.data('lock',0);
                Notify.danger('操作失败');
            });
        });

    };

});