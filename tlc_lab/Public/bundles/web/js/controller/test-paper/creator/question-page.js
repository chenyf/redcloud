define(function(require, exports, module) {
    exports.run = function() {
        var $group_div = $('#group_list');
        var $form = $('[role=search-form]');
        var $testCount = $('input[name=testCount]').val();
        var $testId = $('select[name=testpaper]').val();
        $(".pagination").on("click", "a", function(e) {

            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
            var urls = $(this).attr("href");
            var notids = objToArr(window.questions);
            $.ajax({
                type: 'post',
                url: urls,
                data: $.param({testId: $testId, testCount: $testCount, notids: notids}) + "&" + $form.serialize(),
                success: function(response) {
                    var urlpage = urls.substr(urls.indexOf('page=') + 5);
                    var n = 0;
                    $group_div.html(response.list);
                    $("input[name=groups]").each(function() {
                        deassignScore($(this).val());
                        var qid = $(this).val();
                        if (qid && !window.questions.hasOwnProperty(qid))
                            $(this).prop('checked', false);
                        if (!$(this).is(':checked'))
                            n++;
                    });
                    if (n > 0)
                        $('input[name=checkAll]').prop('checked', false);
                    else
                        $('input[name=checkAll]').prop('checked', true);
                    $('#page').html(response.page);
                }
            });
            return false;
        });

        function objToArr(obj) {//对象转换为数组
            var arr = [];
            for (var item in obj) {
                arr.push(item);
            }
            return arr;
        }

        function deassignScore(group_id) {//重新赋值分数到input框
            var $groupDom = $("#group_" + group_id);
            var qid = $groupDom.data('id');
            for (wqid in window.questions) {
                if (qid == wqid) {
                    $groupDom.find('input[name=score]').val(questions[wqid].score);
                }
            }
        }
    }
});