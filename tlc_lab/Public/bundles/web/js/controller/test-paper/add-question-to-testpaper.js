define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var Widget = require('widget');
    var Validator = require('bootstrap.validator');
    window.list = '';
    exports.run = function() {
        window.questions2 = {};
        require('../test-paper/modal-search')();
        //获取题库试题列表
        var $form = $('form[name=addquestion]');
        var notids = objToArr(window.questions);
        $.ajax({
            type: 'post',
            url: $form.data('tiku'),
            data: {notids: notids},
            success: function(response) {
                $('#questionList').html(response);
            }
        });
        $.ajax({
            type: 'get',
            url: $form.data('add') + '/qestype/choice',
            success: function(response) {
                $('#choice').html(response);
            }
        });
        $('.modalSearchSubmit').on('click', function(e) {
            if (parseInt($('select[name=startTarget]').val()) > parseInt($form.find('select[name=endTarget]').val())) {
                Notify.danger('题目范围有误');
                return false;
            }
            if (e && e.preventDefault)
                e.preventDefault();
            else
                window.event.returnValue = false;
            $.ajax({
                type: 'post',
                url: $form.data('tiku'),
                data: $.param({notids: notids}) + "&" + $form.serialize(),
                success: function(response) {

                    $('#questionList').html(response);
                }
            });

        });

        $('.question-bank-insert').on('click', function() {
            $('.isShow').show();
        });

        $('.new-insert').on('click', function() {
            $('.isShow').hide();
            $.ajax({
                type: 'get',
                url: $form.data('add') + '/qestype/choice',
                success: function(response) {
                    $('#choice').html(response);
                }
            });
        });

        $('#new-tm').find('ul li a').on('click', function() {
            var type = $(this).data('type');
            $.ajax({
                type: 'get',
                url: $form.data('add') + '/qestype/' + type,
                success: function(response) {
                    $('#choice').html(response);
                }
            });
        });
        
        $('#create-btn').on('click', function() {
            if (objToArr(window.questions2).length > 0) {
                //选择试题库中的试题
                $.ajax({
                    type: 'post',
                    url: $('#tiku').data('url'),
                    data: window.questions2,
                    success: function(response) {
                        if (response) {
                            $.post($('input[name=refreshUrl]').val(), function(response) {
                                $('#group_list').html(response.list);
                                $("input[name=groups]").each(function() {
                                    deassignScore($(this).val());
                                });
                            });
                            extend(window.questions, window.questions2);
                            $('#selectedStatistic').load($('input[name=refreshUrl]').data('url'));
                            Notify.success('添加成功');
                            $('#modal').modal('hide');
                        }
                    }
                });
            } else {
                Notify.danger('请选择试题！');
            }
        });


        //移除重复试题
        var removeRepeatedQuestions = function(_groups, _objs) {
            _groups.each(function() {
                var qid = $(this).val();
                if (_objs.hasOwnProperty(qid)) {
                    $('#group_' + qid).remove();
                }
            });
        }

        var extend = function(o, n, override) {
            for (var p in n)
                if (n.hasOwnProperty(p) && (!o.hasOwnProperty(p) || override))
                    o[p] = n[p];
        };
        
        /*
         * 对象转换为数组
         * @param object obj
         * @returns arr
         */ 
        function objToArr(obj) {
            var arr = [];
            for (var item in obj) {
                arr.push(item);
            }
            return arr;
        }
        
        /*
         * 重新赋值分数到input框
         */
        function deassignScore(group_id) {
            var $groupDom = $("#group_" + group_id);
            var qid = $groupDom.data('id');
            for (wqid in window.questions) {
                if (qid == wqid) {

                    $groupDom.find('input[name=score]').val(questions[wqid].score);
                    console.log($groupDom.find('input[name=score]').val());
                }
            }
        }
    }
});