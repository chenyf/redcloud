define(function(require, exports, module) {

    exports.run = function($form) {

        //控制input框的输入
        $('input[type=text]').on('keypress', function() {
            verifyIsNumber(event);
        });

        //获取已添加的试题
        var $formData = typeof($form) != 'undefined' ? $form.serialize() : {};
        var $group_div = $('#group_list');
        $.get($('input[name=refreshUrl]').val(), $formData, function(response) {
            if (response) {
                responseProcess(response);
                $("input[name=groups]").each(function() {
                    deassignScore($(this).val());
                });
            }
        });

        //生成作业
        $('#build-testpaper').on('click', function() {
            var $btn = $(this);
            var finishUrl = $(this).data('url');
            var buildUrl = $(this).data('build');
            if (objToArr(questions).length == 0) {
                alert('请选择要添加的试题！');
                return false;
            }
            $btn.button('loading').addClass('disabled');
            /*
             * 选择历史试卷
             * @historyTid 历史试卷id
             */
            if ($('[name=testpaper]').size() > 0)
                buildUrl = buildUrl + '/historyTid/' + $('[name=testpaper]').val();

            $.post(buildUrl, questions, function(response) {
                if (response.type == 'lx') {
                    window.document.location = response.url;
                } else {
                    $('#buildModal').load(finishUrl, function() {
                        $('#buildModal').on('hide.bs.modal', function() {
                            $('.perfect').attr('disabled', 'disabled');
                            $btn.removeClass('disabled');
                            $btn.button('reset');
                        });
                        $('#buildModal').modal({backdrop: true, keyboard: false, show: 'show'});
                    });
                }
            });
        });

        /*
         * 处理响应数据
         */
        var responseProcess = function(response) {
            window.questions = {}; //重置questions数据

            $('#group_list').html(response.list);
            $('#page').html(response.page);
            if (response.lists != null) {
                for (var i = 0; i < response.lists.length; i++) {
                    var data = {score: response.lists[i]['score'], type: response.lists[i]['type']};
                    window.questions[response.lists[i]['id']] = data;
                }
                $('#selectedStatistic').load($('[name=refreshUrl]').data('url'));

            }
        }

        /*
         * 验证是否是数字
         */
        var verifyIsNumber = function(e) {
            var k = window.event ? e.keyCode : e.which;
            if (((k >= 48) && (k <= 57)) || k == 8 || k == 0) {

            } else {
                if (window.event) {
                    window.event.returnValue = false;
                }
                else {
                    e.preventDefault(); //for firefox 
                }
            }
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
                }
            }
        }
        /*
         * 对象To数组
         */
        function objToArr(obj) {
            var arr = [];
            for (var item in obj) {
                arr.push(item);
            }
            return arr;
        }
    }
});


