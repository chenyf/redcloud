define(function(require, exports, module) {
    
    exports.run = function() {
        require('./second-down').run();
        $.post($('[name=verifyUrl]').val());
    }

});