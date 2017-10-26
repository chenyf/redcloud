define(function(require, exports, module) {
    require('jquery.bootstrap-datetimepicker');
    exports.run = function() {
        $("[name=endTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'month'
        });

        $("[name=startTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd hh:ii:ss',
            minView: 'month'
        });

        $('#refresh').on('click', function() {
            document.location.reload();
        });

        $('.recover-thread').on('click', function() {
            $.get($(this).data('url'), function() {
                document.location.reload();
            });
        });

        $('.delete-thread').on('click', function() {
            if (confirm("你确定要删除吗？")) {
                $.get($(this).data('url'), function() {
                    document.location.reload();
                });
            }
        });

        $('.giveup-grabpost').on('click', function() {
            if (confirm("你确定要放弃吗？")) {
                $.get($(this).data('url'), function() {
                    document.location.reload();
                });
            }
        });

        var $searchForm = $('#search-course');
        $searchForm.find('i').on('click', function() {

            $('[name=' + $(this).attr('name') + ']').removeClass('fa-circle-o fa-circle-thin');
            $(this).addClass('fa-circle-o');
            $(this).parent().siblings().find('i').addClass('fa-circle-thin');

            $(this).parent().siblings('input[name = ' + $(this).attr('name') + ']').val($(this).data('val'));

            $('#search-course').submit();
        });

        $searchForm.find('[name=courseId],[name=startTime],[name=endTime]').on('change', function() {
            $('#search-course').submit();
        });
    }
})


