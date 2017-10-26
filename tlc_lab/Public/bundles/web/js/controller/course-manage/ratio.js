define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    require('jquery.select2-css');
    require('jquery.select2');
    require("jquery.bootstrap-datetimepicker");
    var Notify = require('common/bootstrap-notify');

    function checkRatio() {
        var ratio = parseInt($('#ratio').val()) + parseInt($('#deduct').val()) + parseInt($('#redcloudDeduct').val()) + parseInt($('#qdeduct').val());
        if (ratio > 100) {
            Notify.danger('提成总和不可以大于100')
            return false;
        }
    }

    exports.run = function() {
        require('./header').run();
        var validator = new Validator({
            element: '#price-form',
            failSilently: true,
            triggerType: 'change'
        });
        Validator.addRule('ratio',
                function(a) {
                    var ratios = $(a.element.selector).val();
                    var reg = /^([0-9]\d?|100)$/
                    var re = new RegExp(reg);
                    if (re.test(ratios)) {
                        var ratio = parseInt($('#ratio').val()) + parseInt($('#deduct').val()) + parseInt($('#qdeduct').val());
                        if (ratio > 100) {
                            return false;
                        } else {
                            $('#redcloudDeduct').val(100-ratio)
                            return  true
                        }
                    } else {
                        return false;
                    }
                  
                   
                }, '请输入0-100的整数,且三个提成之和不得大于100');
        validator.addItem({
            element: '[name="ratio"]',
            required: true,
            rule: 'ratio'
        });
        validator.addItem({
            element: '[name="deduct"]',
            required: true,
            rule: 'ratio'
        });
        validator.addItem({
            element: '[name="qdeduct"]',
            required: true,
            rule: 'ratio'
        });
    };

});