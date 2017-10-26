define(function(require, exports, module) {
var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        $("#theme-table").on('click', '.use-theme-btn', function(){
            if (!confirm('真的要使用该主题吗？')) {
                return false;
            }
            $.post($(this).data('url'), function(){
                window.location.reload();
            });
        });
        

        
        $("#theme-table").on('click', '.use-add-block-btn', function(){
            var title = $(this).attr("title");
            var code = $(this).attr("code");
            $.post("/Admin/Block/addBlockAction",{ "title": title, "code": code },function(data){
                if(data=="success"){
                    window.open("/Admin/Block/indexAction/code/"+code);
                    window.location.reload();
                }
            });
        });
            

    };

});