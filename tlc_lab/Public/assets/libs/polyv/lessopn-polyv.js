define(function (require, exports, module) {

	var $ = require('$');
	var polyv = require('jquery-polyv');
	var Notify = require('common/bootstrap-notify');

	var upload = null;
	var setting = $('span#data-setting');

	(function () {
		if ( typeof(FileReader) == "undefined" ) {
			// $('div#video-polyv-upload-pane').removeClass('active');
			// $('div#video-chooser-upload-pane').addClass('active');
			// $('div#video-chooser-upload-pane').removeClass('active');
			// $('div#video-polyv-upload-pane').addClass('active');
		}
	})();

	exports.run = function (isIE) {
		export_video();

		if(!isIE){
			// $('#video-chooser-upload-pane').removeClass('active');
			// $('#video-polyv-upload-pane').addClass('active');
			// $('#video-polyv-upload-pane').removeClass('active');
			// $('#video-chooser-upload-pane').addClass('active');
			// $('#upload-cloud-video').attr('href','#video-polyv-upload-pane');
			upload_video();
			$('#modal').on('hidden.bs.modal', function (e) {
				stop();
			});
		}

		$('.file-chooser-tabs [data-toggle="tab"]').on('shown.bs.tab', function (e) {
			if ( $(this).hasClass('polyv-cloud') ) {
				$('#lesson-minute-field').attr('disabled', true);
				$('#lesson-second-field').attr('disabled', true);
			} else {
				$('#lesson-minute-field').attr('disabled', false);
				$('#lesson-second-field').attr('disabled', false);
			}
		})
	};

	//保利威视视频上传
	function upload_video() {
		$('div#video-polyv-upload-pane').on('change', 'input[type=file]', function () {

			if ( upload && upload.status ) {
				Notify.info('当前有视频正在上传，请等待上传完成!');
				return false;
			}

			var _this = $(this);
			var $input = $(this);
			var $parent = $input.parent();
			var file = this.files[0];
			var re = /(?:\.([^.]+))?$/;
			var ext = re.exec(file.name)[1];
			var maxFileSize = 2 * 1024 * 1024 * 1024; //2G
			//var maxFileSize =  300*1024*1024; //300
			var allowType = ['mp4', 'avi', 'flv', 'wmv', 'mov'];

			if ( $.inArray(ext, allowType) == -1 ) {
				Notify.danger('当前文件类型不允许上传 ╮(╯▽╰)╭');
				return false;
			}

			if ( file.size > maxFileSize ) {
				Notify.danger('当前文件大小超出限制 ╮(╯▽╰)╭');
				return false;
			}
			//$('a.abort').show().removeClass('disabled');
			var title = $('input[name=title]').val();
			title = title.length > 0 ? title : file.name;
			var desc = $('textarea[name=summary]').text();

			var options = {
				endpoint : setting.data('endpoint'),
				resetBefore : $('#reset_before').prop('checked'),
				resetAfter : false,
				title : title.length > 0 ? title : "title",
				desc : desc.length > 0 ? desc : "desc",
				cataid : setting.data('polycatid'),
				ext : ext,
				writeToken : setting.data('writetoken')
			};

			$('#polyv-video-chooser-progress').show().addClass('active');

			upload = polyv.upload(file, options)
				.fail(function (error) {
					alert('Failed because: ' + error);
				})
				.always(function () {

					$input.val('');
					$('.js-stop').addClass('disabled');
					$('#polyv-video-chooser-progress').removeClass('active');
				})
				.progress(function (e, bytesUploaded, bytesTotal) {
					var percentage =  parseInt((bytesUploaded / bytesTotal * 100));
					percentage -= 1;
					$('#polyv-video-chooser-progress .progress-bar').css('width', percentage + '%');
					$('#polyv-video-chooser-progress .progress-bar').text(percentage + '%');
				})
				.done(function (url, file) {
					var vid = url.replace(setting.data('endpoint'), '');
					var readToken = setting.data('readtoken');
					$.ajax({
						url : getScheme() + "://v.polyv.net/uc/services/rest?method=getById",
						data : {readtoken : readToken, vid : vid},
						type : 'post',
						async : false,
						cache : false,
						success : function (response) {
							if ( response.error != '0' ) {
								console.log(response);
								Notify.danger('文件上传失败');
								return false;
							}
							var time = response.data[0].duration;

							var timeArr = time.split(':');
							var h = parseInt(timeArr[0]);
							var m = parseInt(timeArr[1]);
							var s = parseInt(timeArr[2]);
							m += h * 60;
							response.data[0].source = 'polyv';
							response.data[0].name = file.name;
							response.data[0].uri = getScheme() + "://player.polyv.net/videos/" + vid + '.swf';
							var videoInfo = JSON.stringify(response.data[0]);


							$('input[name=polyvVideoSize]').val(m * 60 + s);
							$('input[name=minute]').val(m);
							$('input[name=second]').val(s);
							$('input[name=polyvVid]').val(vid);
							$('input[name=media]').val(videoInfo);

							_this.parents('div.file-chooser-main').hide();
							$('#polyv-video-chooser-progress .progress-bar').css('width', '0%').text('');
							upload = null;
							_this.parents('div.file-chooser-main').prev('div.file-chooser-bar').show();
							_this.parents('div.file-chooser-main').prev('div.file-chooser-bar').find('span').text(file.name);
							Notify.success('文件上传成功');
						}
					});

				});
		});
	}

	//保利威视视频导入
	function export_video(){
		$('div#video-chooser-import-polyv-pane').on('click', 'button', function () {
			var _this = $(this);
			var vid = $('input.polyvid').val();
			if ( !vid ) {
				Notify.danger('请输入视频id或地址');
				return false;
			}
			vid = vid.replace(getScheme() == 'http' ? /http:\/\/player.polyv.net\/videos\// : /https:\/\/player.polyv.net\/videos\//, '');
			vid = vid.replace(/\.swf/, '');
			vid = $.trim(vid);
			var readToken = setting.data('readtoken');
			$.ajax({
				url : getScheme() + "://v.polyv.net/uc/services/rest?method=getById",
				data : {readtoken : readToken, vid : vid},
				type : 'post',
				async : false,
				cache : false,
				success : function (response) {
					if ( response.error != '0' ) {
						console.log(response);
						Notify.danger('视频导入失败,请检查视频id或地址是否正确');
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
					response.data[0].name = response.data[0].title;
					response.data[0].uri = getScheme() + "://player.polyv.net/videos/" + vid + '.swf';
					var videoInfo = JSON.stringify(response.data[0]);

					$('input[name=media]').val(videoInfo);

					_this.parents('div.file-chooser-main').hide();
					_this.parents('div.file-chooser-main').prev('div.file-chooser-bar').show();
					_this.parents('div.file-chooser-main').prev('div.file-chooser-bar').find('span').text(response.data[0].name);
					Notify.success('视频导入成功');
				}
			})

		});
	}

	function stop() {
		if ( upload && upload.status ) {
			upload.stop();
		}
	}

	function isUploading() {
		return upload.status
	}
});