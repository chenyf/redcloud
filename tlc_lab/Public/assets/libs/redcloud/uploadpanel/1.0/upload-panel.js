define(function(require, exports, module) {

    require('swfupload');
    var Widget = require('widget');
    var ChunkUpload = require('redcloud.chunkupload');
    var UploadProgressBar = require('redcloud.uploadProgressBar');
    var Notify = require('common/bootstrap-notify');

    var UploadPanel = Widget.extend({
        attrs: {
            uploader: null,
            uploaderSettings: {},
            uploaderProgressbar: null,
            chooser: null
        },

        _convertPolyvFileToMedia: function(name,file) {
            var media = {};
            var time = file.duration;
            var timeArr = time.split(':');
            var h = parseInt(timeArr[0]);
            media.minute = parseInt(timeArr[1]);
            media.minute += h * 60;
            media.second = parseInt(timeArr[2]);
            media.polyvVideoSize = media.minute * 60 + media.second;
            media.vid = file.vid;
            media.name = name;
            file.name = name;
            file.uri = file.swf_link;
            file.source = 'polyv';
            media.videoInfo = JSON.stringify(file);
            return media;
        },
        _convertFileToMedia: function(file) {
            var media = {};
            media.id = file.id ? file.id : 0;
            media.status = file.convertStatus ? file.convertStatus : 'none';
            media.type = file.type;
            media.source = 'self';
            media.name = file.filename;
            media.length = file.length;
            return media;
        },
        setup: function() {

            var $btn = this.$('[data-role=uploader-btn]');
            var progressbar = new UploadProgressBar({
                element: $btn.data('progressbar')
            });
            this.set('uploaderProgressbar', progressbar);
            this.set('uploader', this._createUpload($btn, progressbar));
            this.set('js',$btn.data('sess'));
        },

        _createUpload: function($btn, progressbar) {
            var self = this;

            function getFileExt(str) { 
                var d=/\.[^\.]+$/.exec(str); 
                return d; 
            }

            // console.log("sessionId:" + $btn.data('sess'));
            var settings = $.extend({}, {
                file_types : "*.*",
                file_size_limit : "10 MB",
                file_upload_limit : 1,
                file_queue_limit: 1,
                file_post_name: 'file',

                button_placeholder_id : $btn.attr('id'),
                button_width: "75",
                button_height: "35",
                button_text: "<span class=\"btnText\" style=\"font-family: Arial,'Microsoft YaHei','宋体';\">上传</span>",
                button_text_style : ".btnText { color: #333; font-size:16px;font-family: Arial,'Microsoft YaHei','宋体';}",
                button_text_left_padding : 18,
                button_text_top_padding : 5,
                button_image_url: $btn.data('buttonImage'),
                button_window_mode: 'transparent',

                // post_params:{'PHPSESSION':$btn.data('sess')},

	            file_queue_error_handler:function(file,errCode,errInfo){
		            if(errCode == -130){
			            Notify.danger('当前文件类型不允许上传 ╮(╯▽╰)╭');
			            return ;
		            }
		            if(errCode == -110){
			            Notify.danger('上传文件大小超过限制 ');
		            }
		            if(errCode == -100 && errInfo == 0){
			            Notify.info('文件正在上传中，请等待本次上传完毕后，再上传吧。');
		            }
		            console.log(errCode+' : '+errInfo);
	            },

                file_dialog_complete_handler: function(numFilesSelected, numFilesQueued) {
                    if (numFilesSelected == 0) {
                        return;
                    }
                    if (numFilesSelected > 1) {
                        Notify.danger('一次只能上传一个文件，请重新选择。');
                        return ;
                    }

                    this.startUpload();
                },

                upload_start_handler: function(file) {
                    self.trigger("preUpload", self.get("uploader"), file);
                    progressbar.reset().show();
                },

                upload_progress_handler: function(file, bytesLoaded, bytesTotal) {
                    var percentage = Math.ceil((bytesLoaded / bytesTotal) * 100);
                    percentage -= 1;
                    progressbar.setProgress(percentage);
                },

                upload_error_handler: function(file, errorCode, message) {
	                console.log(file,errorCode,message);
                    Notify.danger('文件上传失败，请重试！');
                },

                upload_success_handler: function(file, serverData) {
                    progressbar.setComplete().hide();
                    serverData = $.parseJSON(serverData);

                    if ( serverData.error !== undefined ) {
                        if(serverData.error != 0){
                            console.log(serverData);
                            Notify.danger('文件上传失败');
                            return false;
                        }
                        var polyv_media = self._convertPolyvFileToMedia(file.name,serverData.data[0]);
                        self.trigger('change', polyv_media);
                        Notify.success('文件上传成功！');
                        return true;
                    }

                    if ('*.ppt;*.pptx'.indexOf(getFileExt(file.name)[0])>-1) {
                        serverData.mimeType='application/vnd.ms-powerpoint';
                    }
                    if ($btn.data('callback') && $btn.data('callback') != "nocb") {
                        $.post($btn.data('callback'), serverData, function(response) {
                            var media = self._convertFileToMedia(response);
                            self.trigger('change',  media);
                            Notify.success('文件上传成功！');
                        }, 'json');

                    }else if($btn.data('callback') == "nocb"){
                        self.trigger('change',  serverData);
                        Notify.success('文件上传成功！');
                    } else {
                        var media = self._convertFileToMedia(serverData);
                        self.trigger('change',  media);
                        Notify.success('文件上传成功！');
                    }

                }
            }, this.get('uploaderSettings'));

            if ($btn.data('filetypes')) {
                settings.file_types = $btn.data('filetypes');
            }
            this.set("uploaderSettings", settings);

            return new SWFUpload(settings);

        },
        
        _supportChunkUpload: function(){
            if(typeof(FileReader)=="undefined" || typeof(XMLHttpRequest)=="undefined"){
                return false;
            }
            return true;
        },

        destroy: function(){
            if(this._supportChunkUpload() && this.$('[data-role=uploader-btn]').data('storageType')=="cloud"){
                var uploader = this.get("uploader");
                uploader.destroy();
            }
        },

    });

    module.exports = UploadPanel;
});
