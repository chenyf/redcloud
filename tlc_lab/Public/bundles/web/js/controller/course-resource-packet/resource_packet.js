define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        $("#deleteResourcePacket").on("click",function(){
            if(confirm("确定删除该资源包吗？")){
                var $btn = $(this);
                var url = $btn.data("url");
                var courseId = $btn.data("id");

                var goto_url = $btn.data("goto");

                $btn.text("删除中...");
                $btn.attr('disabled','disabled');
                $.ajax({
                    method:'POST',
                    url:url,
                    data:{
                        courseId:courseId
                    },
                    success:function(result){
                        result = JSON.parse(result);
                        if(result.success){
                            Notify.success(result.msg);
                            setTimeout(function(){
                                window.location.href = goto_url;
                            },2000);
                        }else{
                            Notify.danger(result.msg);
                        }
                        $btn.removeAttr('disabled');
                        $btn.text("删除资源包");
                    },
                    error:function(){
                        Notify.danger("发生错误！");
                    }
                });
            }
        });

       $("body").on("click","#sure-generate-packet",function(){
           var $btn = $(this);
           var url = $btn.data("url");
           var courseId = $btn.data("id");
           var goto_url = $btn.data("goto");

           $btn.text("资源包生成中...");
           $btn.attr('disabled','disabled');
           $.ajax({
               method:'POST',
               url:url,
               data:{
                   courseId:courseId
               },
               success:function(result){
                   result = JSON.parse(result);
                   if(result.success){
                       Notify.success("生成资源包成功！");
                       setTimeout(function(){
                           window.location.href = goto_url;
                       },3000);
                   }else{
                       Notify.danger(result.msg);
                   }
                   $btn.removeAttr('disabled');
                   $btn.text("确定生成");
               },
               error:function(){
                   Notify.danger("发生错误！");
               }
           });
       });

    };

});