define(function(require, exports, module) {
    exports.run = function() {
        $('select[name=testpaper]').on('change', function() {
            var $testId = $('select[name=testpaper]').val();
            var $refreshUrl = $('input[name=refreshUrl]');
            $refreshUrl.val($refreshUrl.val()+"/testId/"+$testId);
            console.log($(this).find("option:selected").attr('data-url'));
            window.questions = {};
            $.ajax({
                type: 'post',
                url: $('form').attr('action'),
                data: {testId: $testId},
                success: function(response) {
                    $('#group_list').html(response.list);
                    $('#page').html(response.page);
                    if (response.lists != null) {          
                        for (var i = 0; i < response.lists.length; i++) { 
                            var data = {score: response.lists[i]['score'], type: response.lists[i]['type']};
                            window.questions[response.lists[i]['id']] = data;
                        }
                        $('#selectedStatistic').load($('[name=refreshUrl]').data('url'));
                    }
                    var $groups = $('input[name=groups]');
                    $groups.each(function() {
                        selectGroup($(this).val());
                    });
                }
            });
        });
        function selectGroup(groupid) {
            var $groupDom = $("#group_" + groupid);
            var qid = $groupDom.data('id');
            var score = $groupDom.find('input[name=score]').val();
            var type = $groupDom.find('input[name=questionType]').val();
            var data = {score: score, type: type};
            $groupDom.find("input[type=checkbox]").prop('checked', true);
            if (!window.questions.hasOwnProperty(qid)) {
                window.questions[qid] = data;
            }
        }
    }
});