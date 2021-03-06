define(function(require, exports, module) {

    module.exports = function() {
        var $selectDifficulty = $(".select-Difficulty");
        var $selectRange = $(".select-Range");
        var $type = $('input[name=qestype]');
        var $difficulty = $('input[name=difficulty]');
        var $selectAll = $('.select-all');
        var question = ($('#questionTypeInput').val()).split(',');
        var difficulty = ($('input[name=difficulty]').val()).split(',');
        var $difficultyDiv = $('#difficultyDiv');
        var $questionTypeDiv = $('#questionTypeDiv');
        $('.searchSubmit').on('click', function() {
            if (parseInt($('select[name=startTarget]').val()) > parseInt($('select[name=endTarget]').val())) {
                Notify.danger('题目范围有误');
                return false;
            }
        });

        $questionTypeDiv.find('.select-Range').on('click', function() {
            if ($(this).hasClass('select-all')) {
                selectAll($(this), $type, true);
            } else {
                selectRange($(this), $type, $questionTypeDiv, question);
            }
        });
        
        $selectDifficulty.on('click', function() {
            if ($(this).hasClass('select-all')) {
                selectAll($(this), $difficulty, false);
            } else {
                selectRange($(this), $difficulty, $difficultyDiv, difficulty);
            }
        });

        if (question == '') {

            $questionTypeDiv.find('.select-all').addClass('active');
            $('#questionTypeDiv .select-all').append('<em class="fa fa-check-square-o selected-icon"></em>');
        } else {
            $selectRange.each(function() {
                if ($.inArray($(this).data('val'), question) != '-1') {
                    $(this).addClass('active');
                    $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
                }
            });
            $questionTypeDiv.find('.select-all').removeClass('active');
            $questionTypeDiv.find('.select-all').find('.selected-icon').remove();
        }

        if (difficulty == '') {
            $('#difficultyDiv .select-all').addClass('active');
            $('#difficultyDiv .select-all').append('<em class="fa fa-check-square-o selected-icon"></em>');
        } else {
            $selectDifficulty.each(function() {
                if ($.inArray($(this).data('val'), difficulty) != '-1') {
                    $(this).addClass('active');
                    $(this).append('<em class="fa fa-check-square-o selected-icon"></em>');
                }
            });
            $('#difficultyDiv .select-all').removeClass('active');
            $('#difficultyDiv .select-all').find('.selected-icon').remove();
        }

        $('#toggle').on('click', function() {
            if ($(this).find('.glyphicon').hasClass('glyphicon-menu-down')) {
                $(this).find('span').html('收起').next('i').addClass('glyphicon-menu-up').removeClass('glyphicon-menu-down');
            } else {
                $(this).find('span').html('展开').next('i').addClass('glyphicon-menu-down').removeClass('glyphicon-menu-up');
            }
        });
        
        //全选
        var selectAll = function(_this, _input, _isType) {
            _this.addClass('active').siblings('a').removeClass('active').find('.selected-icon').remove();
            _this.find('.selected-icon').remove();
            _this.append('<em class="fa fa-check-square-o selected-icon"></em>');
            if (_isType) {
                question = [];
                _input.val(question);
            } else {
                difficulty = [];
                _input.val(difficulty);
            }
            return _this.data('id');
        }

        //选择范围
        var selectRange = function(_this, _input, _select, _var) {
            _select.find('.select-all').removeClass('active');
            _select.find('.select-all').find('.selected-icon').remove();
            if (_this.hasClass('active')) {
                _this.removeClass('active');
                _this.find('.selected-icon').remove();
                _var.splice($.inArray(_this.data('val'), _var), 1);
            } else {
                _this.addClass('active');
                _this.append('<em class="fa fa-check-square-o selected-icon"></em>');
                _var.push(_this.data('val'));
            }
            _input.val(_var);
            return _this.data('id');
        };

        /*
         * 题目范围
         */
        $('select[name=startTarget]').on('change', function() {
            if ($(this).val() == '0') {
                $('select[name=endTarget],[name=to]').hide();
                $('select[name=endTarget]').attr('disabled', 'disabled');
            } else {
                $('select[name=endTarget],[name=to]').show();
                $('select[name=endTarget]').removeAttr('disabled');
            }
        });
        
    };

});