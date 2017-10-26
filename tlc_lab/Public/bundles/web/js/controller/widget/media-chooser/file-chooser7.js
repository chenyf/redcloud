define(function(require, exports, module) {

    var BaseChooser = require('./base-chooser-8');
    require('jquery.perfect-scrollbar');
    var Notify = require('common/bootstrap-notify');

    var documentChooser = BaseChooser.extend({
        attrs: {
            uploaderSettings: {
                file_types : "*.ppt;*.pptx;*.zip;*.rar;*.tar.gz;*.rm;*.flv;*.avi;*.mp4;*.mp3;*.wma;*.txt;*.doc;*.docx;*.xls;*.xlsx;*.pdf;*.jpg;*.jpeg;*.gif;*.png",
                file_size_limit : "1024 MB",
                file_types_description: "ppt;pptx;zip,rar,tar.gz,rm,flv,avi,mp4,mp3,wma,txt,doc,docx,xls,xlsx,pdf,jpg,jpeg,gif,png"
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

                        var paramsKey = {};
                        paramsKey.data=data;
                        paramsKey.targetType=self.element.data('targetType');
                        paramsKey.targetId=self.element.data('targetId');

                        response.postParams.paramsKey = JSON.stringify(paramsKey);

                        uploader.setUploadURL(response.url);
                        uploader.setPostParams(response.postParams);
                    },
                    error: function(jqXHR, status, error) {
                        Notify.danger('请求上传授权码失败！');
                    }
                });
            }
        },

        setup: function() {
            documentChooser.superclass.setup.call(this);
        }

    });

    module.exports = documentChooser;

});


