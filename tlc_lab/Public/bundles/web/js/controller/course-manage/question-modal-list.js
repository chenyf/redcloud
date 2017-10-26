define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    window.questions2 = {};//选择试卷试题
    exports.run = function() {
        var $form = $('form[name=addquestion]');
        //记录上次选中的情况
        if (window.questions2) {
            for (v in window.questions2) {
                selectGroup(v);
            }
        }

        //阻止点击分页超链接事件
        $("#modal-page").find(".pagination").on("click", "a", function(e) {
            var urls = $(this).attr("href");
            var notids = objToArr(window.questions);
            $.ajax({
                type: 'post',
                url: urls,
                data: $.param({notids: notids}) + "&" + $form.serialize(),
                success: function(response) {
                    $('#questionList').html(response);
                    $("input[name=checkAll2]").prop('checked', false);
                }
            });

            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
            return false;
        });

        $("input[name=items]").unbind('click').on('click', function(event) {
            if ($(this).is(":checked")) {
                selectGroup($(this).val());
            } else {
                cancelGroup($(this).val());
            }
            event.stopPropagation();
        });

        $("input[name=modalCheckAll]").unbind('click').on('click', function() {
            var _this = $(this);
            $("input[name=items]").each(function() {
                if (_this.prop('checked')) {
                    selectGroup($(this).val());
                } else {
                    cancelGroup($(this).val());
                }
            });
        });
        function objToArr(obj) {//对象转换为数组
            var arr = [];
            for (var item in obj) {
                arr.push(item);
            }
            return arr;
        }

        function selectGroup(groupid) {
            var $groupDom = $("#items_" + groupid);
            var qid = $groupDom.data('id');
            var score = $groupDom.find('input[name=score]').val();
            var type = $groupDom.find('input[name=questionType]').val();
            var data = {score: score, type: type};
            $groupDom.find("input[type=checkbox]").prop('checked', true);
            if (qid && !window.questions2.hasOwnProperty(qid)) {
                window.questions2[qid] = data;
                addItem(qid);
            }
        }

        function cancelGroup(groupid) {
            var $groupDom = $("#items_" + groupid);
            var qid = $groupDom.data('id');
            $groupDom.find("input[type=checkbox]").prop('checked', false);
            if (qid && window.questions2.hasOwnProperty(qid)) {
                delete window.questions2[qid];
                minusItem(qid);
            }
            if ($groupDom.size() > 0)
                $("input[name=checkAll2]").prop('checked', false);
        }
        function addItem(groupid) {
            var $groupDom = $("#items_" + groupid);
            var questionType = $groupDom.find("input[name=questionType]").val();
            var score = Number($groupDom.find("input[name=score]").val());
            switch (questionType) {
                case 'single_choice':
                    counts.single_choice = counts.single_choice + 1;
                    scores.single_choice = scores.single_choice + score;
                    break;
                case 'choice':
                    counts.choice = counts.choice + 1;
                    scores.choice = scores.choice + score;
                    break;
                case 'determine':
                    counts.determine = counts.determine + 1;
                    scores.determine = scores.determine + score;
                    break;
                case 'essay':
                    counts.essay = counts.essay + 1;
                    scores.essay = scores.essay + score;
                    break;
                case 'fill':
                    counts.fill = counts.fill + 1;
                    scores.fill = scores.fill + score;
                    break;
            }
            setSelectItem();
            //console.log(window.scores);
        }
        function minusItem(groupid) {
            var $groupDom = $("#item_" + groupid);
            var questionType = $groupDom.find("input[name=questionType]").val();
            var score = Number($groupDom.find("input[name=score]").val());
            switch (questionType) {
                case 'single_choice':
                    counts.single_choice = counts.single_choice - 1;
                    scores.single_choice = scores.single_choice - score;
                    break;
                case 'choice':
                    counts.choice = counts.choice - 1;
                    scores.choice = scores.choice - score;
                    break;
                case 'determine':
                    counts.determine = counts.determine - 1;
                    scores.determine = scores.determine - score;
                    break;
                case 'essay':
                    counts.essay = counts.essay - 1;
                    scores.essay = scores.essay - score;
                    break;
                case 'fill':
                    counts.fill = counts.fill - 1;
                    scores.fill = scores.fill - score;
                    break;
            }
            setSelectItem();
        }
        
        function setSelectItem() {
            $('#count .single_choice').text(window.counts.single_choice);
            $('#count .choice').text(window.counts.choice);
            $('#count .determine').text(window.counts.determine);
            $('#count .fill').text(window.counts.fill);
            $('#count .essay').text(window.counts.essay);
            $('#count .count').text(window.counts.single_choice + window.counts.choice + window.counts.determine + window.counts.fill + window.counts.essay);

            $('#score .single_choice').text(window.scores.single_choice);
            $('#score .choice').text(window.scores.choice);
            $('#score .determine').text(window.scores.determine);
            $('#score .fill').text(window.scores.fill);
            $('#score .essay').text(window.scores.essay);
            $('#score .score').text(window.scores.single_choice + window.scores.choice + window.scores.determine + window.scores.fill + window.scores.essay);

        }
        
        $('.modal-drop-down-item').on('click', function(event) {
            if ($(this).is('.tk-arrow-right')) {
                $(this).removeClass('tk-arrow-right');
                $(this).addClass('tk-arrow-down');
                $(this).parent().nextAll('[class=td-item-class]').hide();
                $(this).parent().nextAll('[class=td-Detaile-class]').show();
            } else {
                $(this).removeClass('tk-arrow-down');
                $(this).addClass('tk-arrow-right');
                $(this).parent().nextAll('[class=td-item-class]').show();
                $(this).parent().nextAll('[class=td-Detaile-class]').hide();
            }
        });


    };

});