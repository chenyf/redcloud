define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    Notify = require('common/bootstrap-notify');
    exports.run = function() {
        require('../../util/char-remaining').char();
        var validator = new Validator({
            element: '#comment-post-form'
        });

        validator.addItem({
            element: '[name="comment[content]"]',
            rule: 'maxlength{max:200}',
            errormessageMaxlength: '评价反馈不能大于200个字'
        });

        $('.evaluate-stars').find('i').on('mouseover', function() {
            $(this).addClass('fa-star').removeClass('fa-star-o');
            $(this).prevAll().addClass('fa-star').removeClass('fa-star-o');
            $(this).nextAll().addClass('fa-star-o').removeClass('fa-star');
            $('[name="comment[satisficing]"]').val($(this).data('val'));
        });

        $('#buildModal').on('hidden.bs.modal', function() {
            document.location.reload();
        })

        $("#comment-submit").on('click', function() {
            if ($("[name='comment[satisficing]']").val() <= 2 && ($("[name='comment[content]']").val() == '')) {
                Notify.danger('请填写评价反馈！');
                return false;
            }
        });

    };

});