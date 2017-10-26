define(function(require, exports, module) {
    exports.run = function() {
        $('.promoted-teacher-con').hover(function(){
            $(this).find('.btn-sixin').show();
        },function(){
             $(this).find('.btn-sixin').hide();
        });
    };

});