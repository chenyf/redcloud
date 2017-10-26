define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("$");
    require('./creator/base').run();
    window.questions = {};//手动添加的试题
    window.counts = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    window.scores = {single_choice: 0, choice: 0, determine: 0, essay: 0, fill: 0};
    exports.run = function() {
        require('../course-manage/search')();
        require('../../util/collapse')('hide');
        var $form = $('[role=search-form]');
        getQuestionCount($form);
        
        $('#ok,#reset').on('click', function() {
            
            var testCount = parseInt($('input[name=testCount]').val());
            var qesCount = parseInt($('.qes-count').text());
            var reg = /^[1-9]\d*$|^!0$/;
            if (!reg.test(testCount)) {
                Notify.danger('请输入正确的数字');
                return false;
            }
            if (qesCount < testCount) {
                Notify.danger('输入的试题数量不能大于题库数量');
                return false;
            }
            $.ajax({
                type: 'post',
                url: $(this).data('url'),
                data: $.param({'reset': 1, 'testCount': testCount}) + '&' + $form.serialize(),
                success: function(response) {
                    responseProcess(response);
                }
            });
        });

        $form.find('.a-choose').on('click', function() {
            getQuestionCount($form);
        });
        $form.find('select').on('change', function() {
            getQuestionCount($form);
        });

        $('.drop-down-item').on('click', function(event) {
            //event.stopPropagation();
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

        function getQuestionCount(_form) {
            $.ajax({
                type: 'post',
                url: _form.data('get'),
                data: _form.serialize(),
                success: function(data) {
                    
                    $('.qes-count').text(data);
                }
            });
        }
        function selectGroup(groupid) {
            var $groupDom = $("#group_" + groupid);
            var qid = $groupDom.data('id');
            var score = $groupDom.find('input[name=score]').val();
            var type = $groupDom.find('input[name=questionType]').val();
            var data = {score: score, type: type};
            $groupDom.find("input[type=checkbox]").prop('checked', true);
            if (qid && !window.questions.hasOwnProperty(qid)) {
                window.questions[qid] = data;
            }
        }
        /*
         * 处理返回的响应数据
         */
        responseProcess = function(response) {
            window.questions = {}; //重置questions数据
            $('.random-question').remove();
            $('#group_list').append(response.list);
            $('#page').html(response.page);
            if (response.lists != null) {
                for (var i = 0; i < response.lists.length; i++) {
                    var data = {score: response.lists[i]['score'], type: response.lists[i]['type']};
                    window.questions[response.lists[i]['id']] = data;
                }
                if (window.questions) {
                    for (var v in window.questions) {
                        selectGroup(v);
                    }
                }
                $('#selectedStatistic').load($('[name=refreshUrl]').data('url'));
            }
        }
    }
});

