define(function(require, exports, module) {

    exports.run = function() {
        $('.remove-collection').on('click', function() {
            if (confirm("你确定要移除该收藏吗？")) {
                $.get($(this).data('url'), function() {
                    document.location.reload();
                });
            }
        });
        
        $("select[name=courseId]").on('change',function(){
            $("#search-course").submit();
        });
    }
})
