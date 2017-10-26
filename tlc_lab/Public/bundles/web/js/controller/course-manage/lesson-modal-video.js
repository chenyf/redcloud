define(function (require, exports, module) {
	var Validator = require('bootstrap.validator');
	var Notify = require('common/bootstrap-notify');
	require('jquery.uploadify');
	require('jquery.uploadify.css');

	exports.run = function () {

		var title = $('input[name=title]').val();
		title = title.length > 0 ? title : '标题';

		(function (uploadFile, title) {

			var setting = $('#data-setting');
			var writetoken = setting.data('writetoken');
			var cataid = setting.data('polycatid');
			var uploader = setting.data('endpoint');

			var JSONRPC = {
				title : title,
				cataid : cataid
			};
			console.log("上传视频到保利威视。。。");
			$.fileupload1 = $(uploadFile).uploadify({
				'auto' : true,
				'formData' : {
					'fcharset' : 'ISO-8859-1',
					'writetoken' : writetoken,
					'JSONRPC' : JSONRPC
				},
				'buttonText' : '选择上传文件',
				'fileSizeLimit' : '2000MB',//上传文件大小限制
				'fileTypeDesc' : '视频文件',
				'fileTypeExts' : '*.avi; *.mp4; *.mov; *.wmv',//文件类型过滤
				'swf' : '/Public/assets/libs/jquery-plugin/uploadify/uploadify.swf',
				'uploader' : 'http://v.polyv.net/uc/services/rest?method=uploadfile',
				'onProgress':function(event,queueId,fileObj,data){
						console.log("上传视频到保利威视。。。onProgress");
						console.log(data)
				},
				'onUploadSuccess' : function (file, data) {
					var response = eval('(' + data + ')');

					if ( response.error != '0' ) {
						console.log(response);
						Notify.danger('文件上传失败');
						return false;
					}
					var time = response.data[0].duration;
					$('input[name=polyvVid]').val(vid);
					var timeArr = time.split(':');
					var h = parseInt(timeArr[0]);
					var m = parseInt(timeArr[1]);
					var s = parseInt(timeArr[2]);
					m += h * 60;
					$('input[name=polyvVideoSize]').val(m * 60 + s);
					$('input[name=minute]').val(m);
					$('input[name=second]').val(s);
					response.data[0].source = 'polyv';
					response.data[0].name = file.name;
					response.data[0].uri = getScheme() + "://player.polyv.net/videos/" + vid + '.swf';
					var videoInfo = JSON.stringify(response.data[0]);

					$('input[name=media]').val(videoInfo);

					$(uploadFile).parents('div.file-chooser-main').hide();
					$(uploadFile).parents('div.file-chooser-main').prev('div.file-chooser-bar').show();
					$(uploadFile).parents('div.file-chooser-main').prev('div.file-chooser-bar').find('span').text(file.name);
					Notify.success('文件上传成功');

				}
			});
		})('#upload-file', title);

	};
});
