define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function(){
        
        //下载
        $(".download-course-resource").on("click",function(){
            var self = $(this);
            var url = self.data("url");
            var courseId = self.data("courseid");
            var id = self.data("id");
            var data = {courseId:courseId,id:id};
            if(self.data('lock') == 1) return false;
            self.data('lock',1);
            $.get(url,data,function(result){
                self.data('lock',0);
                if( result.status == 0 ){
                    Notify.danger(result.info);
                }else{
                    if(result.url){
                        $("#modal").load(result.url,function(){
                            $('#modal').modal({backdrop: 'static', keyboard: false,show:'show'});
                        });
                    }else{
                        window.open(result.info,'_self');
                        if($(".download-num-th").size()){
                            setTimeout(function(){window.location.reload();},1000);
                        }
                    }
                }
            },'json').error(function(){
                self.data('lock',0);
                Notify.danger('操作失败');
            });
        });
    
    };

});