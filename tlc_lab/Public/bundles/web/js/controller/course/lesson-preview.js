define(function(require, exports, module) {

	var VideoJS = require('video-js'),
		swfobject = require('swfobject');

	require('mediaelementplayer');

	var MediaPlayer = require('../widget/media-player4');
	var SlidePlayer = require('../widget/slider-player');
    var DocumentPlayer = require('../widget/document-player');

    exports.run = function() {

        if ($("#lesson-preview-video-player").length > 0) {
            if ($("#lesson-preview-video-player").data('hlsUrl')) {
                $("#lesson-preview-video-player").html('<div id="lesson-video-player"></div>');

                var mediaPlayer = new MediaPlayer({
                        element: '#lesson-preview-video-player',
                        playerId: 'lesson-video-player',
                        height: '360px',
                });

                mediaPlayer.setSrc($("#lesson-preview-video-player").data('hlsUrl'), 'video');
                mediaPlayer.play();

                $('#modal').one('hidden.bs.modal', function () {
                    mediaPlayer.dispose();
                });
            } else {
                $("#lesson-preview-video-player").html('<video id="lesson-video-player" class="video-js vjs-default-skin" controls preload="auto"  width="100%" height="360"></video>');

                var videoPlayer = VideoJS("lesson-video-player", {
                    techOrder: ['flash','html5']
                });
                videoPlayer.width('100%');
                videoPlayer.src($("#lesson-preview-video-player").data('url'));
                videoPlayer.play();

                $('#modal').one('hidden.bs.modal', function () {
                    videoPlayer.dispose();
                    $("#lesson-preview-video-player").remove();
                });
            }
    }

    if ($("#lesson-preview-audio-player").length > 0) {
        var audioPlayer = new MediaElementPlayer('#lesson-preview-audio-player',{
                mode:'auto_plugin',
                enablePluginDebug: false,
                enableAutosize:true,
                success: function(media) {
                        media.play();
                }
        });

        $('#modal').one('hidden.bs.modal', function () {
            audioPlayer.remove();
            $("#lesson-preview-audio-player").remove();
        });
    }

    if ($("#lesson-preview-document-player").length > 0) {
        var $player = $("#lesson-preview-document-player");
        var html = '';
        $.get($player.data('url'), function(response) {
            if (response.error) {
                html = '<div class="lesson-content-text-body text-danger">' + response.error.message + '</div>';
            } else {
                var html = '<iframe id=\'viewerIframe\' width=\'100%\'allowfullscreen webkitallowfullscreen height=\'500\' frameborder=\'no\' border=\'0\' ></iframe>';
            }

            $player.html(html);

            var player = new DocumentPlayer({
                element: '#lesson-preview-document-player',
                swfFileUrl:response.swfUri,
                pdfFileUrl:response.pdfUri
            });
        }, 'json');
    }

    var lessonSwfObject = $("#lesson-preview-swf-player");
    if (lessonSwfObject.length > 0) {
        if(lessonSwfObject.data("mediasource") == "polyv"){
            var polyvVid = lessonSwfObject.data('vid');
            var player = polyvObject("#lesson-preview-swf-player").videoPlayer({
                'width' : '100%',
                'height' : '360',
                'vid' : polyvVid,
                'flashvars' : {
                    'setVolumeM' : 5
                }
            });
            $('#modal').one('hidden.bs.modal', function () {
                player.j2s_pauseVideo();
            });
        }else{
            swfobject.embedSWF(lessonSwfObject.data('url'), 'lesson-preview-swf-player', '100%', '360', "9.0.0", null, null, {wmode: 'transparent'});
            $('#modal').one('hidden.bs.modal', function () {
                swfobject.removeSWF('lesson-preview-swf-player');
            });
        }
    }

    if ($("#lesson-preview-iframe").length > 0) {
        var html = '<iframe src="' + $("#lesson-preview-iframe").data('url') + '" style="height:360px; width:100%; border:0px;" scrolling="no"></iframe>';
        $("#lesson-preview-iframe").html(html).show();

        $('#modal').one('hidden.bs.modal', function () {
            $("#lesson-preview-iframe").remove();
        });
    }

    $('#buy-btn').on('click', function(){
        $modal = $('#modal');
        $modal.modal('hide');
        $.get($(this).data('url'), function(html) {
            $modal.html(html);
            $('#join-course-btn').click();
        });
    });

};

});