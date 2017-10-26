define(function(require, exports, module) {

    var BaseChooser = require('./base-chooser-8');
    require('jquery.perfect-scrollbar');
    var Notify = require('common/bootstrap-notify');

    var resourceChooser = BaseChooser.extend({
        attrs: {
            uploaderSettings: {
                file_types : "*.ppt;*.pptx;*.zip;*.rar;*.tar.gz;*.rm;*.flv;*.avi;*.mp4;*.mp3;*.wma;*.txt;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.jpg;*.jpeg;*.gif;*.png",
                file_size_limit : "200 MB",
                // file_types_description: ".ppt,.pptx,.zip,.rar,.tar.gz,.rm,.flv,.avi,.mp4,.mp3,.wma,.txt,.doc,.docx,.xls,.xlsx,.pdf,.jpg,.jpeg,.gif,.png",
                button_text_style : ".btnText { color: #1C0202; font-size:16px;font-family: '微软雅黑';}",
            },
            preUpload: function(uploader, file) {
                var data = {};
                var self = this;
                $.ajax({
                    url: this.element.data('paramsUrl'),
                    async: false,
                    dataType: 'json',
                    data: data,
                    cache: false,
                    success: function(response, status, jqXHR) {
                        uploader.setUploadURL(response.url);
                    },
                    error: function(jqXHR, status, error) {
                        Notify.danger('请求上传授权码失败！');
                    }
                });
            }
        },

        setup: function() {
            resourceChooser.superclass.setup.call(this);
        },

    });

    module.exports = resourceChooser;

});


