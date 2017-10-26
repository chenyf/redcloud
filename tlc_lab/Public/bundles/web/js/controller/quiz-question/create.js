define(function(require, exports, module) {

    var QuestionBase = require('./creator/question-base');
    var Choice = require('./creator/question-choice');
    var Determine = require('./creator/question-determine');
    var Essay = require('./creator/question-essay');
    var Fill = require('./creator/question-fill');
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        var type = $('#question-creator-widget').find('[name=type]').val().replace(/\_/g, "-");
        var QuestionCreator;
        switch (type) {
            case 'single-choice':
            case 'uncertain-choice':
            case 'choice':
                QuestionCreator = Choice;
                break;
            case 'determine':
                QuestionCreator = Determine;
                break;
            case 'essay':
                QuestionCreator = Essay;
                break;
            case 'fill':
                QuestionCreator = Fill;
                break;
            default:
                QuestionCreator = QuestionBase;
        }

        var creator = new QuestionCreator({
            element: '#question-creator-widget'
        });

        //鼠标移入图片
        $('#imgdiv').on('mouseover', function() {
            $('#delImg').show();
        });
        $('#imgdiv').on('mouseout', function() {
            $('#delImg').hide();
        });
        var tmpImgURL = $('#tmpImgURL').val();
        if (tmpImgURL != "") {
            $('#imgdiv').show();
            str = removalImgTags(tmpImgURL);
            $('.stemImg').attr('src', '/' + window.app.globalData.DATA_FETCH_URL_PREFIX + '/' + str);
        }
        function removalImgTags(str) {
            str = str.replace('[image]', '');
            str = str.replace('[/image]', '');
            return str;
        }
        //删除图像
        var $delBtutton = $('#delImg');
        $delBtutton.on('click', function() {
            var imgURL = removalImgTags($('#tmpImgURL').val());
            $.post($delBtutton.data('del'), {url: imgURL}, function(response) {
                if (response['status'] == 'ok') {
                    $('#tmpImgURL').val('');
                    $('#imgdiv').hide();
                    $('.stemImg').attr('src', '');
                    Notify.success(response['msg']);

                } else {
                    Notify.danger(response['msg']);
                }
            }, "json");
        });
        //试题难度选择
        $select = $("#difficultyDiv .select-openRange");

        $select.each(function() {
            $(this).removeClass('active');
            $(this).children('em').remove();

            if ($('#difficulty').val() == $(this).attr('value')) {
                $(this).addClass('active');
                $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
            }
        });
        $select.on('click', function() {
            $select.each(function() {
                $(this).removeClass('active');
                $(this).find('.selected-icon').remove();
            });
            $(this).addClass('active');
            $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
            $('#difficulty').val($(this).attr('value'));
        });

        //选择答案
        $determine = $('#answerDiv .select-openRange');
        $determine.each(function() {
            $(this).removeClass('active');
            $(this).children('em').remove();
            if ($('#determine').val() == $(this).attr('value')) {
                $(this).addClass('active');
                $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
            }
        });
        
        $determine.on('click', function() {
            $determine.each(function() {
                $(this).removeClass('active');
                $(this).find('.selected-icon').remove();
            });
            $(this).addClass('active');
            $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
            $('#determine').val($(this).attr('value'));
        });
    };

});