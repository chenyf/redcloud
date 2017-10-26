define(function (require, exports, module) {
    require('zclip');
    require('jquery-cookie');
    var Notify = require('common/bootstrap-notify');
    exports.run = function () {
        $('.copybtn').zclip({
            path: '/Public/assets/libs/jquery-plugin/zclip/ZeroClipboard.swf',
            copy: function () {
                return $('#txt_CopyLink').val();
            },
            afterCopy: function () {
                //$("<span id='msg'/>").insertAfter($('#copy_input')).text('复制成功');
                Notify.success('复制成功');
            }
        });





    }
})
