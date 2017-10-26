define(function(require, exports, module) {
    module.exports = function($element, $container) {
        //阻止点击分页超链接事件
        $element.find(".pagination").on("click", "a", function(e) {
            var urls = $(this).attr("href");
            $.ajax({
                type: 'post',
                url: urls,
                success: function(html) {
                    $container.html(html);
                }
            });
            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
            return false;
        });
    };
});