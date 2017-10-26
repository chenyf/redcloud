define(function(require, exports, module) {

    module.exports = function($isShow) {
        var $tToggle = $('#t-toggle');
        if ($isShow == 'show') {
            $('#t-collapseExample').collapse('show');
            $tToggle.find('i').addClass('fa-angle-double-up').removeClass('fa-angle-double-down');
        } else if ($isShow == 'hide') {
            $('#t-collapseExample').collapse('hide');
            $tToggle.find('i').addClass('fa-angle-double-down').removeClass('fa-angle-double-up');
        }
    };

});