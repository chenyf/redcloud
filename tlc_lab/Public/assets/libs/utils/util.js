define(function(require, exports, module) {

    exports.flashChecker = function(){
        var hasFlash = 0; //是否安装了flash
        var flashVersion = 0; //flash版本
        if (document.all) {
            var swf = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
            if (swf) {
                hasFlash = 1;
            }
        } else {
            if (navigator.plugins && navigator.plugins.length > 0) {
                var swf = navigator.plugins["Shockwave Flash"];
                if (swf) {
                    hasFlash = 1;
                }
            }
        }
        return hasFlash == 1;
    }

});