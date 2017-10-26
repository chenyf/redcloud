define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    exports.run = function() {
       var editor = CKEDITOR.replace('content', {
            toolbar: 'Simple',
            readonly:true,
            filebrowserImageUploadUrl: $('#content').data('imageUploadUrl')
        });

         
    };
});