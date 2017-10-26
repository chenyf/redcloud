define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    require("$");
    exports.run = function() {

        require('../course-manage/header').run();
        //require('../course-manage/search')();

        var $container = $('#quiz-table-container');
        require('../../util/batch-select')($container);
        require('../../util/batch-delete')($container);
        require('../../util/item-delete')($container);
        require('../../util/collapse')('hide');

        $('.test-paper-reset').on('click','',function(){
        	if (!confirm('重置会清空原先的题目,确定要继续吗？')) {
        	    return ;
        	}
            window.location.href=$(this).data('url');
        });

        var $select = $('.screening-condition select');
        var $form_test2 = $('#form-test2');
        var $button = $('#button-id');
        
        $select.on('change',function(){
            $button.click();
        });
        $form_test2.on('change',function(){
            $button.click();
        });
        var $myId = $('#myId');
        $myId.on('click',function(){
            $myId.attr('href',$myId.attr('href')+"/classId/"+$select.val());
        });
       
        
        var $table = $('#quiz-table');

        $table.on('click', '.open-testpaper, .close-testpaper', function() {
            var $trigger = $(this);
            var $oldTr = $trigger.parents('tr');

            if (!confirm('真的要' + $trigger.attr('title') + '吗？ 试卷发布后无论是否关闭都将无法修改。')) {
                return ;
            }

            $.post($(this).data('url'), function(html){
                Notify.success($trigger.attr('title') + '成功！');

                var $tr = $(html);
                $oldTr.replaceWith($tr);
            }).error(function(){
                Notify.danger($trigger.attr('title') + '失败');
            });
        });
       $('button.close,button.btn.btn-link').click(function(){
           $('#modal').hide();
           $('.modal-backdrop').remove();
       })

        $("a.delete_homework_link").on('click',function(){
            var $parent = $(this).parents('.homework-item');
            if (!confirm("您确定要删除" + $(this).data('name') + '吗，将同时删除所有的提交结果和成绩！')) {
                return ;
            }
            
            $.post($(this).data('url'),{vid:$parent.data('vid')}, function(response) {
                if (response == 'true') {
                    $parent.remove();
                    Notify.success('删除' + name + '成功!');
                } else {
                    Notify.danger('删除' + name + '失败...')
                }
            });
        });

    };


});