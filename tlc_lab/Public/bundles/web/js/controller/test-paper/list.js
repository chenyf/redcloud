define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    require("jquery.bootstrap-datetimepicker");
    require("$");

    window.questions = {};
    window.counts = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    window.scores = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    exports.run = function() {

        var $container = $('#quiz-table-container');
        var $searchForm = $('form[role="search-form"]');
        require('../../util/short-long-text')($container);
        require('../../util/batch-select')($container);
        require('../../util/batch-delete')($container);
        require('../course-manage/search')();
        require('./creator/batch-select');
        require('./creator/base').run($searchForm);
        require('../../util/collapse')('hide');
        //手动选择试题部分

        var $group_div = $('#group_list');

        $('.searchSubmit').on('click', function(e) {
            var startTarget = $('[name=startTarget]').val();
            var endTarget   = $('[name=endTarget]').val();
            var type        = $('[name=qestype]').val();
            var difficulty  = $('[name=difficulty]').val();
            var keyword     = $('input[name=keyword]').val();
            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
            $.get($searchForm.data('url'), $searchForm.serialize(), function(response) {
                $group_div.html(response.list);
                $('#page').html(response.page);
                $("input[name=groups]").each(function() {
                    deassignScore($(this).val());
                });
            });
        });


        /*
         * 重新赋值分数到input框
         */
        function deassignScore(group_id) {
            var $groupDom = $("#group_" + group_id);
            var qid = $groupDom.data('id');
            for (wqid in window.questions) {
                if (qid == wqid) {
                    $groupDom.find('input[name=score]').val(questions[wqid].score);
                }
            }
        }
        
    };
});