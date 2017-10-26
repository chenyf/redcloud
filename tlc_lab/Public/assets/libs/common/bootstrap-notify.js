define(function(require, exports, module) {

    var showMessage = function(type, message, duration, callback) {
        var $exist = $('.bootstrap-notify-bar');
        if ($exist.length > 0) {
            $exist.remove();
        }

        var html = '<div class="alert alert-' + type + ' bootstrap-notify-bar" style="display:none;z-index:10001;">'
        html += '<button type="button" class="close" data-dismiss="alert">×</button>';
        html += message;
        html += '</div>';

        var $html = $(html);
        $html.appendTo('body');

        $html.slideDown(100, function(){
            duration = $.type(duration) == 'undefined' ? 3 :  duration;
            var endCallback = callback || null;
            if (duration > 0) {
                setTimeout(function(){
                    $html.remove();
                    if ($.isFunction(endCallback))
                        callback();
                }, duration * 1000);
            }
        });

    }

    var Notify = {
        primary: function(message, duration, callback) {
            showMessage('primary', message, duration, callback);
        },

        success: function(message, duration, callback) {
            showMessage('success', message, duration, callback);
        },

        warning: function(message, duration, callback) {
            showMessage('warning', message, duration, callback);
        },

        danger: function(message, duration, callback) {
            showMessage('danger', message, duration, callback);
        },

        info: function(message, duration, callback) {
            showMessage('info', message, duration, callback);
        }
    };

    module.exports = Notify;
});
