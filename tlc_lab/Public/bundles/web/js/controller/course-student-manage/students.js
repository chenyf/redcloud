define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        
        var $list = $("#course-student-list");
        $list.on('click', '.student-remove', function(){
            
            if (!confirm('您真的要移除该学员吗？')) {
                return false;
            }
            var succurl = $(this).data('succurl');
            $.get($(this).data('url'), function(dataObj){
                
                if (dataObj.code == 1000) {
                    Notify.success(dataObj.msg,1,function(){
                        location.href = succurl;
                    });
                } else {
                    Notify.danger(dataObj.msg,1);
                }
            },'json').error(function(){
                Notify.danger('移除失败，请重试！');
            });
        });
        
         $list.on('click', '.black-list', function(){
            var self = $(this);
            var black = parseInt(self.data('black'));
            var str = black ? '将学员移出黑名单' : '将学员加入黑名单';
            var warn = black ? '确定'+str+'吗？' : '确定'+str+'吗？\n加入黑名单后学员将不能再学习本课程！';
            if (!confirm(warn)) {
                return false;
            }
            var url = self.data('url');
            
            $.get(url, {}, function(result) {
                if(result.status){
                    Notify.success(str+'成功!');
                    if(window.location.href.indexOf("black")<0)
                        window.location.href += "/black/"+$("[name=black]").val();
                    else
                        window.location.reload();
                }else{
                    Notify.danger(result.info);
                }
            },'json').error(function(){
                Notify.danger(str+'失败!');
            });
         });
         
        $list.on('click', '.remove-student', function(){
            if (!confirm('您真的要踢出该学员吗？')) {
                return false;
            }
            var url = $(this).data('url');
            $.get(url, function(dataObj){
                if(dataObj.status){
                    Notify.success(dataObj.info);
                    window.location.reload();
                }else{
                    Notify.danger(dataObj.info);
                }
            },'json').error(function(){
                Notify.danger('踢出失败，请重试！');
            });
        });
    }

});