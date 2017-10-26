define(function(require, exports, module) {
    
    var Notify = require('common/bootstrap-notify');
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        $('.order-list').click(function(){
            $("#order-table").toggle();
            $(this).find('i').toggleClass("fa-angle-double-down");
        });
    };

    
});