define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {

        //删除职业或阶段
        $('body').on('click','.remove-career,.remove-stage',function(){

            if($(this).hasClass('remove-career')){
                if(!confirm("确定删除该职业吗？将会同时删除课程下的所有阶段及阶段课程规划！（不会删除课程）")){
                    return false;
                }
            }

            if($(this).hasClass('remove-stage')){
                if(!confirm("确定删除该阶段吗？将会同时该阶段下所有课程规划！（不会删除课程）")){
                    return false;
                }
            }

            var url = $(this).data('url');
            var id = $(this).data('id');

            $.ajax({
                method:'POST',
                url:url,
                data:{
                    id:id
                },
                success:function(result){
                    if(result.status){
                        window.location.reload();
                    }else{
                        Notify.danger(result.message,5);
                    }

                },
                error:function(){
                    Notify.danger("发生错误！",5);
                }
            });
        });
    };

});