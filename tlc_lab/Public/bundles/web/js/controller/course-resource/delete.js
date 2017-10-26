define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function(){
        
        //删除资料
        $(".del-resource").on("click",function(){
            var self = $(this);
            var url = self.data("url");
            var courseId = self.data("courseid");
            var id = self.data("id");
            var data = {courseId:courseId,id:id};
            if(self.data('lock') == 1) return false;
            self.data('lock',1);
            $.post(url,data,function(result){
                self.data('lock',0);
                if(result.status == 0){
                    Notify.danger(result.info);
                }else{
                    Notify.success(result.info);
                    window.location.reload();
                }
            },"json").error(function(){
                self.data('lock',0);
                Notify.danger('操作失败');
            });
        });
    
    };

});