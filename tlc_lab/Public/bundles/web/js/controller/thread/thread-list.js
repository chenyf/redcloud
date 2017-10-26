define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        var $threadList = $('#ask-manage-list');
        require('../../util/prevent-page')($threadList, $threadList);



        //删除提问
        $('.delete-but').on('click', function() {
            $isDel = confirm('确定要删除该提问吗？');
            if ($isDel) {
                $.get($(this).data('url'), function(data) {
                    if (data) {
                        Notify.success('删除成功！');
                    } else {
                        Notify.danger('删除失败！');
                    }
                    $('#ask-manage-list').load($('#search-form').attr('action'));
                });
            }
        });


    }
})