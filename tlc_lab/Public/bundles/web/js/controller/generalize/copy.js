define(function (require, exports, module) {
    require('zclip');
    var Notify = require('common/bootstrap-notify');
    exports.run = function () {

        setTimeout(function () {
            $('.copy-short').zclip({
                path: '/Public/assets/libs/jquery-plugin/zclip/ZeroClipboard.swf',
                copy: function () {
                    return $('#short-url').val();
                },
                afterCopy: function () {
                    //$("<span id='msg'/>").insertAfter($('#copy_input')).text('复制成功');
                    Notify.success('复制成功');
                }
            });
            $('.copy-long').zclip({
                path: '/Public/assets/libs/jquery-plugin/zclip/ZeroClipboard.swf',
                copy: function () {
                    return $('#long-url').val();
                },
                afterCopy: function () {
                    //$("<span id='msg'/>").insertAfter($('#copy_input')).text('复制成功');
                    Notify.success('复制成功');
                }
            });
        }, 300);


    }
})
