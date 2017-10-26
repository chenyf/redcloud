define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        $("#site-navbar").find('.notification-badge-container .badge').remove();
        
        //加入班级 @author fubaosheng 2015-10-12
        $("[name=joinGroup]").on('click',function(){
            var status = $(this).data('status');
            var id = $(this).data('id');
            if(!id){
                Notify.danger("非法操作");
                return false;
            }
            if(status!=1 && status!=2){
                Notify.danger("类型错误");
                return false;
            }
            var url = $(this).data("url");
            var self = this;
            $.post(url,{id:id,status:status},function(result){
                if(result['status'] == "error"){
                    Notify.danger(result['info']);
                    return false;
                }
                
                var applyid = $(self).parent('span').data('applyid');
                var goto = $(self).data('goto');
                $("span[data-applyid='"+applyid+"']").each(function(i,q){
                    if(result['status'] == "noPassSuccess"){
                        $(q).empty();
                        $(q).html('<span class="text-muted">未同意加入</span>&nbsp;&nbsp;<a href="'+goto+'" target="_blank">查看详情</a>');
                    }
                    if(result['status'] == "passSuccess"){
                        $(q).empty();
                        $(q).html('<span class="text-muted">已同意加入</span>&nbsp;&nbsp;<a href="'+goto+'" target="_blank">查看详情</a>');
                    }
                });
                
            },'json');
        })
    };

});