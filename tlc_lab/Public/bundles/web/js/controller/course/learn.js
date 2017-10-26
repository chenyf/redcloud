define(function(require, exports, module) {
    var Widget = require('widget'),
        Backbone = require('backbone'),
        //VideoJS = require('video-js'),
        swfobject = require('swfobject'),
        Scrollbar = require('jquery.perfect-scrollbar'),
        Notify = require('common/bootstrap-notify'),
        Util = require('util');

    require('mediaelementplayer');

    var Toolbar = require('../lesson/lesson-toolbar');

    var MediaPlayer = require('../widget/media-player4');
    var SlidePlayer = require('../widget/slider-player');
    var DocumentPlayer = require('../widget/document-player');
    var intervalTm = 10 * 1000;//上报时间参数默认10秒
    
    var iID = null;
    var recordWatchTimeId = null;

    var LessonDashboard = Widget.extend({

        _router: null,

        _toolbar: null,

        _lessons: [],

        _vmDesktops: [{"uid": "iframe1", "url": windowUrl.init}],

        _timerId: null,

        events: {
            'click [data-role=next-lesson]': 'onNextLesson',
            'click [data-role=prev-lesson]': 'onPrevLesson',
            'click [data-role=finish-lesson]': 'onFinishLesson'
        },

        attrs: {
            courseId: null,
            courseUri: null,
            playStatus: null,
            dashboardUri: null,
            lessonId: null
        },

        setup: function() {
            var me = this;
            this._readAttrsFromData();
            this._initToolbar();
            this._initRouter();
            this._initListeners();
            this._initDesktop();
            this._initTimer();            

            $('.prev-lesson-btn, .next-lesson-btn').tooltip();
        },
        createVm: function(options){
            var _html = ['<div style="border-left:1px solid #FFF;border-bottom:1px solid #FFF;',
                            'position:relative; float:left; height:'+ options.height +';width:' + options.width + '">',
                            '<div style="position: absolute; top:30px; left:-30px; height:150px;width:30px;">隐藏</div>',
                            '<iframe id="viewerIframe" allowfullscreen="" webkitallowfullscreen="" border="0"',
                            'src="'+ options.src +'" width="100%" height="100%" frameborder="no"></iframe>',
                         '</div>'
                        ].join('');
            return _html;
        },
        _initDesktop: function() {
            var me = this;
                _height = $(window).height(),
                _width = $(window).width(),
                _docWidth = _width * 0.7;

            $('.dashboard-content').append('<div style="position:absolute;right:-20px;bottom:0;z-index:99;"><span id="hidedoc" class="hide-desktop" style="border-radius:0 4px 4px 0;">隐藏文档</span><span id="showdoc" class="hide-desktop" style="border-radius:0 4px 4px 0;display:none;">显示文档</span></div>');
            $(".dashboard-content").css({"right" : _docWidth + "px"});
            $("#lesson-dashboard-toolbar").css({"width": _docWidth + "px"});
            $(".toolbar-pane-container").css({"overflow": "visible"}).append('<div style="position:absolute;left:-20px;"><span id="hidedesktop" class="hide-desktop">隐藏桌面</span><span id="showdesktop" class="hide-desktop" style="display:none;">显示桌面</span></div><iframe id="viewerIframe" allowfullscreen="" webkitallowfullscreen="" border="0" src="'+this._vmDesktops[0].url+'" width="100%" height="100%" frameborder="no"></iframe>');
            $('.lesson-dashboard').addClass('lesson-dashboard-open');

            $("#hidedesktop").on('click', function(){
                $(this).hide();
                $("#showdesktop").show();
                $(".dashboard-content").css({"right" : "0"});
                $("#lesson-dashboard-toolbar").css({"width": "0px"});
            });

            $("#hidedoc").on('click', function(){
                $(this).hide();
                $("#showdoc").show();
                $(".dashboard-content").css({"right" : _width + "px"});
                $("#lesson-dashboard-toolbar").css({"width": _width + "px"});
            });

            $("#hidetimer").on('click', function(){
                $(this).hide();
                $("#showtimer").show();
                $(".lesson-dashboard .toolbar-pane-container").css({'right': "0px"})
                $(".toolbar-nav").css({"width": "0"});
            });

            $("#showdesktop").on('click', function(){
                $(this).hide();
                $("#hidedesktop").show();
                $(".dashboard-content").css({"right" : _docWidth + "px"});
                $("#lesson-dashboard-toolbar").css({"width": _docWidth + "px"});
            });

            $("#showdoc").on('click', function(){
                $(this).hide();
                $("#hidedoc").show();
                $(".dashboard-content").css({"right" : _docWidth + "px"});
                $("#lesson-dashboard-toolbar").css({"width": _docWidth + "px"});
            });

            $("#showtimer").on('click', function(){
                $(this).hide();
                $("#hidetimer").show();
                $(".lesson-dashboard .toolbar-pane-container").css({'right': "60px"});
                $(".toolbar-nav").css({"width": "60px"});
            });

            $("#clearTimer").on('click', function(){
                me._clearTimer();
            });
        },
        _initTimer: function() {
            var second = 0,
                minute = 0;

            function timer() {
                second = second + 1;
                if (second >= 60) {
                    second = 0;
                    minute = minute + 1;
                }
                if (minute >= 60) {
                    minute = 0;
                }
                var s, m;
                second <= 9 ? s = "0" + second : s = second;
                minute <= 9 ? m = "0" + minute : m = minute;
                $("#tools").text(m + ":" + s)
            }
            this._timerId = setInterval(timer, 1000);
        },
        _clearTimer: function() {
            clearInterval(this._timerId);
        },
        onNextLesson: function(e) {
            var next = this._getNextLessonId();
            if (next > 0) {
                this._router.navigate('lesson/' + next, {trigger: true});
            }
        },

        onPrevLesson: function(e) {
            var prev = this._getPrevLessonId();
            if (prev > 0) {
                this._router.navigate('lesson/' + prev, {trigger: true});
            }
        },

        onFinishLesson: function(e) {
            var $btn = this.element.find('[data-role=finish-lesson]');
            if ($btn.hasClass('btn-success')) {
                this._onCancelLearnLesson();
            } else {
                this._onFinishLearnLesson();
            }
        },

        _startLesson: function() {
            var toolbar = this._toolbar,
                self = this;
            var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/start?center='+center;
            $.post(url, function(result) {
                if (result == true) {
                    toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
                }
            }, 'json');
        },

        _onFinishLearnLesson: function() {
            var $btn = this.element.find('[data-role=finish-lesson]'),
                toolbar = this._toolbar,
                playStatus = this.get('playStatus'),
                self = this;

                var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/finish?center='+center;
                $.post(url, function(response) {
                    if (response.isLearned) {
                        $('#course-learned-modal').modal('show');
                    }

                    if(response.error != undefined){
                        Notify.danger(response.error,4);
                        return;
                    }

                    $btn.addClass('btn-success');
                    $btn.find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                    toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'finished'});
                    Notify.success("操作成功！");
                }, 'json');

        },

        _onCancelLearnLesson: function() {
            var $btn = this.element.find('[data-role=finish-lesson]'),
                toolbar = this._toolbar,
                self = this;
            var url = '../../course/' + this.get('courseId') + '/lesson/' + this.get('lessonId') + '/learn/cancel?center='+center;
            $.post(url, function(json) {
                if(json.error != undefined){
                    Notify.danger(json.error,4);
                    return;
                }
                $btn.removeClass('btn-success');
                $btn.find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                toolbar.trigger('learnStatusChange', {lessonId:self.get('lessonId'), status: 'learning'});
                Notify.success("取消学习成功！");
            }, 'json');
        },

        _readAttrsFromData: function() {
            this.set('courseId', this.element.data('courseId'));
            this.set('courseUri', this.element.data('courseUri'));
            this.set('dashboardUri', this.element.data('dashboardUri'));

        },

        _initToolbar: function() {
            this._toolbar = new Toolbar({
                element: '#lesson-dashboard-toolbar',
                activePlugins:  app.arguments.plugins,
                courseId: this.get('courseId')
            }).render();

            $('#lesson-toolbar-primary li[data-plugin=lesson]').trigger('click');
        },

        _initRouter: function() {
            var that = this,
                DashboardRouter = Backbone.Router.extend({
                routes: {
                    "lesson/:id": "lessonShow"
                },

                lessonShow: function(id) {
                    that.set('lessonId', id);
                }
            });

            this._router = new DashboardRouter();
            Backbone.history.start({pushState: false, root:this.get('dashboardUri')} );
        },

        _initListeners: function() {
            var that = this;
            this._toolbar.on('lessons_ready', function(lessons){
                that._lessons = lessons;
                that._showOrHideNavBtn();
            });
        },

        _onChangeLessonId: function(id) {
            var self = this;
            if (!this._toolbar) {
                return ;
            }
            this._toolbar.set('lessonId', id);

            // FIX BUG: #1892
            if (this.get('videoPlayer')) {
                // this.get('videoPlayer').dispose();
                this.set('videoPlayer', null);
            }

            if (this.get('audioPlayer')) {
                this.get('audioPlayer').remove();
                this.set('audioPlayer', null);
            }

            if (this.get('mediaPlayer')) {
                this.get('mediaPlayer').dispose();
                this.set('mediaPlayer', null);
            }

            swfobject.removeSWF('lesson-swf-player');

            $('#lesson-iframe-content').empty();

            this.element.find('[data-role=lesson-content]').hide();

            var that = this;
            
            $.get(this.get('courseUri') + '/lesson/' + id, function(lesson) {

                $("#lesson-video-content,#lesson-audio-content").html("");
            
                that.element.find('[data-role=lesson-title]').html(lesson.title);

                function recordWatchTime(){
                    url="../../course/"+lesson.id+'/watch/time/2';
                    $.post(url);
                }

                that.element.find('[data-role=lesson-title]').html(lesson.title);
                $(".watermarkEmbedded").html('<input type="hidden" id="videoWatermarkEmbedded" value="'+lesson.videoWatermarkEmbedded+'" />');
                var $titleStr = "";
                $titleArray = document.title.split(' - ');
                var titleLength = $titleArray.length;
                
                for(var i=1; i <= titleLength-1; i++){
                    if(i==titleLength-1){
                        $titleStr += $titleArray[i];
                    }else{
                        $titleStr += $titleArray[i] + ' - ';
                    }
                    
                }
                document.title = lesson.title + ' - ' + $titleStr.substr(0,$titleStr.length-3);
                that.element.find('[data-role=lesson-number]').html(lesson.number);
                if (parseInt(lesson.chapterNumber) > 0) {
                    that.element.find('[data-role=chapter-number]').html(lesson.chapterNumber).parent().show().next().show();
                } else {
                    that.element.find('[data-role=chapter-number]').parent().hide().next().hide();
                }

                if (parseInt(lesson.unitNumber) > 0) {
                    that.element.find('[data-role=unit-number]').html(lesson.unitNumber).parent().show().next().show();
                } else {
                    that.element.find('[data-role=unit-number]').parent().hide().next().hide();
                }

                // if ( (lesson.status != 'published') && !/preview=1/.test(window.location.href)) {
                //     $("#lesson-unpublished-content").show();
                //     return;
                // }

                if ( (lesson.status != 'published') && lesson.previlege != 1) {
                    $("#lesson-unpublished-content").show();
                    return;
                }

                var number = lesson.number -1;

                if (lesson.canLearn.status != 'yes') {
                    $("#lesson-alert-content .lesson-content-text-body").html(lesson.canLearn.message);
                    $("#lesson-alert-content").show();
                    return;
                }

                if (lesson.mediaError) {
                    Notify.danger(lesson.mediaError);
                    return ;
                }

                if (lesson.mediaSource == 'iframe') {
                    var html = '<iframe src="' + lesson.mediaUri + '" style="position:absolute; left:0; top:0; height:100%; width:100%; border:0px;" scrolling="no"></iframe>';

                    $("#lesson-iframe-content").html(html);
                    $("#lesson-iframe-content").show();

                } else if ( (lesson.type == 'video' || lesson.type == 'audio') && lesson.mediaHLSUri ) {
               
                    $("#lesson-video-content").html('<div id="lesson-video-player"></div>');
                    $("#lesson-video-content").show();
                    
                    var mediaPlayer = new MediaPlayer({
                        element: '#lesson-video-content',
                        playerId: 'lesson-video-player'
                    });
                    mediaPlayer.on("timeChange", function(data){
                        var userId = $('#lesson-video-content').data("userId");
                        if(parseInt(data.currentTime) != parseInt(data.duration)){
                            DurationStorage.set(userId, lesson.mediaId, data.currentTime);
                        }
                    });
                    mediaPlayer.on("ready", function(playerId, data){
                        var player = document.getElementById(playerId);
                        var userId = $('#lesson-video-content').data("userId");
                        player.seek(DurationStorage.get(userId, lesson.mediaId));
                    });
                    mediaPlayer.setSrc(lesson.mediaHLSUri, lesson.type);
                    mediaPlayer.on('ended', function() {
                        var userId = $('#lesson-video-content').data("userId");
                        DurationStorage.del(userId, lesson.mediaId);
                        if(!isTeacher)
                            $.post("../../course/"+lesson.id+'/watch/paused');
                        that._onFinishLearnLesson();
                        that.set('playStatus', 'ended');
                        clearInterval(recordWatchTimeId);
                    });
                    mediaPlayer.on('ready', function() {
                        clearInterval(recordWatchTimeId);
                        recordWatchTimeId = setInterval(recordWatchTime, 120000);
                    });
                    mediaPlayer.on('paused', function() {
                        if(!isTeacher)
                            $.post("../../course/"+lesson.id+'/watch/paused');
                    });
                    mediaPlayer.on('playing', function() {
                        if(!isTeacher)
                            $.post("../../course/"+lesson.id+'/watch/play');
                    });
                    mediaPlayer.play();

                    that.set('mediaPlayer', mediaPlayer);

                } else {
                    if (lesson.type == 'video') {
                        if (lesson.mediaSource == 'self' || lesson.mediaSource == 'cloud') {
                            //$("#lesson-video-content").html('<video id="lesson-video-player" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" style="width:100%;height:100%;"></video>');

                            $('#lesson-video-content').html("<video id='lesson-video-player' controls='controls' preload='auto' width='100%' height='100%'>"
                                + "<source type='" + lesson.mimeType + "' src='"+lesson.mediaUri+"' /> "
                                // + "<source type='video/mp4' src='http://tlc.yanchuang.site/redisk/remote.php/webdav/2016818033648-170dk2.mp4' /> "
                                + "<object width='100%' height='100%' type='application/x-shockwave-flash' data='/Public/assets/libs/gallery2/mediaelement/2.22.0/flashmediaelement.swf'>"
                                + "<param name='movie' value='/Public/assets/libs/gallery2/mediaelement/2.22.0/flashmediaelement.swf' />"
                                + "<param name='flashvars' value='controls=true&file="+lesson.mediaUri +"' />"
                                + "</object>"
                                + "</video>");

                            if ((lesson.mediaConvertStatus == 'waiting') || (lesson.mediaConvertStatus == 'doing')) {
                                Notify.warning('视频文件正在转换中，稍后完成后即可查看');
                                return ;
                            }

                            var hasPlayerError = false;
                            var mediaPlayer = $('#lesson-video-player').mediaelementplayer({
                                success: function (mediaElement, domObject) {
                                    $("#lesson-video-content").show();
                                    mediaElement.play();
                                    console.log("视频加载成功...");
                                },
                                error: function (err) {
                                        hasPlayerError = true;
                                        console.log(err);
                                        var message = err + ' :播放出错，可能视频文件格式不支持，建议点击资料栏下载课程到本地观看';
                                        Notify.danger(message,8);
                                },
                                features: ['playpause','progress','current','duration','tracks','volume','fullscreen'],
                                loop: false,
                                // default if the <video width> is not specified
                                defaultVideoWidth: "100%",
                                // default if the <video height> is not specified
                                defaultVideoHeight: "100%",
                                videoWidth: "100%",
                                // if set, overrides <video height>
                                videoHeight: "100%",
                                enableAutosize: true,
                            });

                            mediaPlayer.currentTime = 1000;

                            mediaPlayer.on('play',function(){
                                if(!isTeacher)
                                    $.post("../../course/"+lesson.id+'/watch/play');
                            });
                            mediaPlayer.on('pause',function(){
                                if(!isTeacher)
                                    $.post("../../course/"+lesson.id+'/watch/paused');
                            });
                            mediaPlayer.on('ended',function(){
                                if(!isTeacher)
                                    $.post("../../course/"+lesson.id+'/watch/paused');
                                if (hasPlayerError) {
                                    return ;
                                }
                                that._onFinishLearnLesson();
                                that.set('playStatus', 'ended');
                            });
                            mediaPlayer.on('fullscreenchange',function(){
                                console.log("fullscreenchange!!");
                            });

                            that.set('videoPlayer', mediaPlayer);

                            // var player = videojs("lesson-video-player", {
                            //     techOrder: ['html5','flash'],
                            //     controlBar: {
                            //         captionsButton : false,
                            //         chaptersButton: false,
                            //         subtitlesButton:false,
                            //         liveDisplay:false,
                            //         descriptionsButton:false
                            //     }
                            // });
                            // var hasPlayerError = false;
                            //
                            // player.dimensions('100%', '100%');
                            // player.src(lesson.mediaUri);
                            // //player.src("/Course/CourseLesson/mediaAction/courseId/8/lessonId/10");
                            // player.on('ended', function() {
                            //     $.post("../../course/"+lesson.id+'/watch/paused');
                            //     if (hasPlayerError) {
                            //         return ;
                            //     }
                            //     that._onFinishLearnLesson();
                            //     that.set('playStatus', 'ended');
                            //     player.currentTime(0);
                            //     player.pause();
                            // });
                            //
                            // player.on('play',function(){
                            //     $.post("../../course/"+lesson.id+'/watch/play');
                            // });
                            // player.on('pause',function(){
                            //     $.post("../../course/"+lesson.id+'/watch/paused');
                            // });
                            // player.on('loadeddata',function(){
                            //     clearInterval(recordWatchTimeId);
                            //     recordWatchTimeId = setInterval(recordWatchTime, 120000);
                            // });
                            // player.on('error', function(error){
                            //     hasPlayerError = true;
                            //     console.log(error);
                            //     var message = '播放出错，请稍候重试！';
                            //     Notify.danger(message);
                            // });
                            // $("#lesson-video-content").show();
                            // player.play();
                            // player.on('fullscreenchange', function(e) {
                            //     if ($(e.target).hasClass('vjs-fullscreen')) {
                            //         $("#site-navbar").hide();
                            //     }
                            // });

                            // that.set('videoPlayer', player);

                        } else if(lesson.mediaSource == 'polyv'){
                            var polyvVid = lesson.polyvVid;
                            $("#lesson-swf-content").html("<div id='plv_"+polyvVid+"'></div>").show();

                            var player = polyvObject("#plv_"+polyvVid).videoPlayer({
                                'width' : '100%',
                                'height' : $('#lesson-swf-content').height(),
                                'vid' : polyvVid,
                                'flashvars' : {
                                    'setVolumeM' : 5
                                },
                                's2j_onVideoPlay':function(){
                                    StudyStatisticsReport.playReport();//播放时上报学习情况
                                },
                                's2j_onVideoPause':function(){
                                    StudyStatisticsReport.pauseReport();//暂停时上报学习情况
                                },
                                's2j_onPlayerInitOver':function(){
                                    var courseId = lesson.courseId;
                                    var lessonId = lesson.id;
                                    var pdata = {courseId:courseId ,lessonId:lessonId}
                                    $.get("/Course/Course/getReportPositionAction",pdata, function(position) {
                                          //console.log(position.data);
                                         if(typeof position.data == "number")
                                            polyvVidObject.j2s_seekVideo(position.data);
                                     },"json");
                                },

                            });
                            function getPlayer(movieName){
                                if (navigator.appName.indexOf("Microsoft") != -1) {
                                    var reObj = window[movieName];
                                    try{
                                        if(reObj.length>0){
                                            return reObj[0];
                                        }else{
                                            return reObj;
                                        }
                                    }catch(e){
                                    }
                                    return document[movieName];
                                }else{
                                    return document[movieName];
                                }
                            }
                            var polyvVidObject = getPlayer(polyvVid);
                            //高度变换
                            window.onresize = function(){
                                $(polyvVidObject).height($('#lesson-swf-content').height());
                            }
                            
                            /**
                            * 视屏学习上报
                            * @author tanhaitao 2015-09-15
                            */
                            var StudyStatisticsReport = {
                               getBrowerType: function() {
                                   function appInfo(){
                                    var browser = {appname: 'unknown', version: 0},
                                        userAgent = window.navigator.userAgent.toLowerCase();
                                //IE,firefox,opera,chrome,netscape
                                    if ( /(msie|firefox|opera|chrome|netscape)\D+(\d[\d.]*)/.test( userAgent ) ){
                                        browser.appname = RegExp.$1;
                                        browser.version = RegExp.$2;
                                    } else if ( /version\D+(\d[\d.]*).*safari/.test( userAgent ) ){ // safari
                                        browser.appname = 'safari';
                                        browser.version = RegExp.$2;
                                    }
                                    return browser;
                                }
                                return appInfo().appname+appInfo().version;

                               },
                               //播放时上报
                               playReport: function() {
                                   this.initReport({status: 'play'});
                               },
                               //暂停时上报
                               pauseReport: function() {
                                   this.initReport({status: 'pause'});
                               },
                               //快进快退时上报
                               seekReport: function() {
                                   this.initReport({status: 'seek'});
                               },
                               //初始化上报
                               initReport: function(param) {
                                   options = {
                                       'status': ''
                                   };
                                   options = $.extend(options, param);
                                   var courseId = lesson.courseId;
                                   var lessonId = lesson.id;
                                   var position = polyvVidObject.j2s_getCurrentTime();
                                   var duration = polyvVidObject.j2s_getDuration();
                                   position = parseInt(position);
                                   duration = parseInt(duration);
                                   if (courseId && lessonId && duration > 0 && position > 10 ) {
                                       var brower = this.getBrowerType();
                                       this.studyReport({courseId: courseId, lessonId: lessonId,position: position, duration: duration, status: options['status'], brower: brower});
                                   }
                               },
                               //开始上报
                               studyReport: function(param) {
                                   var options = {
                                       courseId: 0,
                                       lessonId: 0,
                                       position: 0,
                                       duration: 0,
                                       status: "",
                                       brower: ''
                                   };
                                   options = $.extend(options, param);
                                   window.lastStudyReportTm = window.lastStudyReportTm || 0;
                                   
                                   
                                   var nowTm = +new Date();
                                   if (nowTm - window.lastStudyReportTm < intervalTm)
                                       return false;
                                   window.lastStudyReportTm = +new Date();
                                   var data = {
                                       courseId: options['courseId'],
                                       lessonId: options['lessonId'],
                                       position: options['position'],
                                       duration: options['duration'],
                                       status: options['status'],
                                       brower: options['brower'],
                                       intervalTm: 10
                                   };
                                   $.ajax({
                                    type: 'post',
                                    timeout: 3000,
                                    data: data,
                                    url : "/Course/Course/studyStatisticsReportAction",
                                    dataType : 'json',
                                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                                           //console.log("report timeout out");
                                       },
                                    success : function (dataObj) {
//                                        if (dataObj['status'] == 1) {
//                                               console.log("StudyReport sucess");
//                                           } else {
//                                               console.log("StudyReport fail");
//                                           }  
                                        }
                                    })
                               }

                           }
                           window.PlayTime = window.PlayTime || 0;
                           setInterval(function(){
                                if(polyvVidObject!=undefined && polyvVidObject.j2s_getCurrentTime!=undefined && polyvVidObject.j2s_getCurrentTime()!=window.PlayTime){
                                   window.PlayTime = polyvVidObject.j2s_getCurrentTime();
                                   StudyStatisticsReport.playReport();//播放时上报学习情况
                               }

                           },500);
                            //片头时间
//                            setInterval(function(){
//                                if(polyvVidObject!=undefined && polyvVidObject.j2s_getCurrentTime!=undefined && polyvVidObject.j2s_getCurrentTime()>0){
//                                    console.info("当前片头播放时间 ： "+polyvVidObject.j2s_getCurrentTime());
//                                }
//                            },100);
                            //暂停 播放 结束 总时长
//                            setTimeout(function(){
//                                if(polyvVidObject!=undefined && polyvVidObject.j2s_pauseVideo!=undefined){
//                                    polyvVidObject.j2s_pauseVideo();
//                                }
//                                if(polyvVidObject!=undefined && polyvVidObject.j2s_getDuration!=undefined && polyvVidObject.j2s_getDuration()>0){
//                                     console.info("视频总时长 ： "+polyvVidObject.j2s_getDuration());
//                                }
//                            },10000);
                        }else {
                            $("#lesson-swf-content").html('<div id="lesson-swf-player"></div>');
                            swfobject.embedSWF(lesson.mediaUri, 
                                'lesson-swf-player', '100%', '100%', "9.0.0", null, null, 
                                {wmode:'opaque',allowFullScreen:'true'});
                            $("#lesson-swf-content").show();
                        }
                    } else if (lesson.type == 'audio') {
                        var html = '<audio id="lesson-audio-player" width="500" height="50">';
                        html += '<source src="' + lesson.mediaUri + '" type="audio/mp3" />';
                        html += '</audio>';

                        $("#lesson-audio-content").html(html);
         
                        var audioPlayer = new MediaElementPlayer('#lesson-audio-player', {
                            mode:'auto_plugin',
                            enablePluginDebug: false,
                            enableAutosize:true,
                            success: function(media) {
                                media.addEventListener("ended", function() {
                                    that.set('playStatus', 'ended');
                                    if(!isTeacher)
                                        $.post("../../course/"+lesson.id+'/watch/paused');
                                    that._onFinishLearnLesson();
                                });
                                media.addEventListener("pause", function() {
                                    if(!isTeacher)
                                        $.post("../../course/"+lesson.id+'/watch/paused');
                                });
                                media.addEventListener("play", function() {
                                    if(!isTeacher)
                                        $.post("../../course/"+lesson.id+'/watch/play');
                                });
                                media.addEventListener("loadeddata", function() {
                                    clearInterval(recordWatchTimeId);
                                    recordWatchTimeId = setInterval(recordWatchTime, 120000);
                                });
                                media.play();
                            }
                        });
                        that.set('audioPlayer', audioPlayer);
                        $("#lesson-audio-content").show();

                    } else if (lesson.type == 'text' ) {
                        $("#lesson-text-content").find('.lesson-content-text-body').html(lesson.content);
                        $("#lesson-text-content").show();
                        $("#lesson-text-content").perfectScrollbar({wheelSpeed:50});
                        $("#lesson-text-content").scrollTop(0);
                        $("#lesson-text-content").perfectScrollbar('update');

                    } else if (lesson.type == 'testpaper') {
//                        var url = '../../test/' + lesson.mediaId + '/do?targetType=lesson&targetId=' + id;
                        var url = '/My/Testpaper/doTestpaperAction/testId/' + lesson.mediaId + '0/targetType/lesson/targetId/' + id;
                        var html = '<span class="text-info">请点击「开始考试」按钮，在新开窗口中完成考试。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始考试</a></span>';
                        var html = '<span class="text-info">正在载入，请稍等...</span>';
                        $("#lesson-testpaper-content").find('.lesson-content-text-body').html(html);
                        $("#lesson-testpaper-content").show();

//                        $.get('../../testpaper/' + lesson.mediaId + '/user_result/json', function(result) {
                        $.get('/My/Testpaper/userResultJsonAction/id/' + lesson.mediaId+'?center='+center, function(result) {
                            if (result.error) {
                                html = '<span class="text-danger">' + result.error + '</span>';
                            } else {
                                if (result.status == 'nodo') {
                                    html = '欢迎参加考试，请点击「开始考试」按钮。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始考试</a>';
                                } else if (result.status == 'finished') {
//                                    var redoUrl = '../../test/' + lesson.mediaId + '/redo?targetType=lesson&targetId=' + id;
                                    var redoUrl = '/My/Testpaper/reDoTestpaperAction/testId/' + lesson.mediaId + '0/targetType/lesson/targetId/' + id+'?center='+center;
//                                    var resultUrl = '../../test/' + result.resultId + '/result?targetType=lesson&targetId=' + id;
                                    var resultUrl = '/My/Testpaper/testResultAction/id/' + result.resultId + '/targetType/lesson/targetId/' + id+'?center='+center;
                                   // html = '试卷已批阅。' + '<a href="' + redoUrl + '" class="btn btn-default btn-sm" target="_blank">再做一次</a>' + '<a href="' + resultUrl + '" class="btn btn-link btn-sm" target="_blank">查看结果</a>';
                                    html = '试卷已批阅。<a href="' + resultUrl + '" class="btn btn-link btn-sm" target="_blank">查看结果</a>';
                                } else if (result.status == 'doing' || result.status == 'paused') {
                                    html = '试卷未完全做完。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">继续考试</a>';
                                } else if (result.status == 'reviewing') {
                                    html = '试卷正在批阅。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">查看试卷</a>'
                                }
                            }
                            $("#lesson-testpaper-content").find('.lesson-content-text-body').html(html);

                        }, 'json');

                    }
                    else if (lesson.type == 'testtask') {
//                        var url = '../../test/' + lesson.mediaId + '/do?targetType=lesson&targetId=' + id;
                        var url = '/My/Testpaper/doTestpaperAction/testId/' + lesson.mediaId + '1/targetType/lesson/targetId/' + id+'?center='+center;
                        var html = '<span class="text-info">请点击「开始作业」按钮，在新开窗口中完成考试。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始作业</a></span>';
                        var html = '<span class="text-info">正在载入，请稍等...</span>';
                        $("#lesson-testtask-content").find('.lesson-content-text-body').html(html);
                        $("#lesson-testtask-content").show();

//                        $.get('../../testpaper/' + lesson.mediaId + '/user_result/json', function(result) {
                        $.get('/My/Testpaper/userResultJsonAction/id/' + lesson.mediaId+'?center='+center, function(result) {
                            if (result.error) {
                                html = '<span class="text-danger">' + result.error + '</span>';
                            } else {
                                if (result.status == 'nodo') {
                                    html = '欢迎参加作业练习，请点击「开始作业」按钮。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始作业</a>';
                                } else if (result.status == 'finished') {
//                                    var redoUrl = '../../test/' + lesson.mediaId + '/redo?targetType=lesson&targetId=' + id;
                                    var redoUrl = '/My/Testpaper/reDoTestpaperAction/testId/' + lesson.mediaId + '1/targetType/lesson/targetId/' + id;
//                                    var resultUrl = '../../test/' + result.resultId + '/result?targetType=lesson&targetId=' + id;
                                    var resultUrl = '/My/Testpaper/testResultAction/id/' + result.resultId + '/targetType/lesson/targetId/' + id;
                                    html = '作业已批阅。' + '<a href="' + redoUrl + '" class="btn btn-default btn-sm" target="_blank">再做一次</a>' + '<a href="' + resultUrl + '" class="btn btn-link btn-sm" target="_blank">查看结果</a>';
                                } else if (result.status == 'doing' || result.status == 'paused') {
                                    html = '作业未完全做完。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">继续作业</a>';
                                } else if (result.status == 'reviewing') {
                                    html = '作业正在批阅。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">查看作业</a>'
                                }
                            }
                            $("#lesson-testtask-content").find('.lesson-content-text-body').html(html);

                        }, 'json');

                    }
                    else if (lesson.type == 'practice') {
//                        var url = '../../test/' + lesson.mediaId + '/do?targetType=lesson&targetId=' + id;
                        var url = '/Course/CourseTestpaper/doPracticeAction/testId/' + lesson.mediaId + '/targetType/lesson/targetId/' + id+'?center='+center;
                        var html = '<span class="text-info">请点击「开始练习」按钮，在新开窗口中完成考试。<a href="' + url + '" class="btn btn-primary btn-sm" target="_blank">开始练习</a></span>';
                        // var html = '<span class="text-info">正在载入，请稍等...</span>';
                        $("#lesson-practice-content").find('.lesson-content-text-body').html(html);
                        $("#lesson-practice-content").show();

                    }
                    else if (lesson.type == 'ppt') {
                        $.get(that.get('courseUri') + '/lesson/' + id + '/ppt?center='+center, function(response) {
                            if (response.error) {
                                var html = '<div class="lesson-content-text-body text-danger">' + response.error.message + '</div>';
                                $("#lesson-ppt-content").html(html).show();
                                return ;
                            }

                            var html = '<div class="slide-player"><div class="slide-player-body loading-background"></div><div class="slide-notice"><div class="header">已经到最后一张图片了哦<button type="button" class="close">×</button></div></div><div class="slide-player-control clearfix"><a href="javascript:" class="goto-first"><span class="glyphicon glyphicon-step-backward"></span></a><a href="javascript:" class="goto-prev"><span class="glyphicon glyphicon-chevron-left"></span></a><a href="javascript:" class="goto-next"><span class="glyphicon glyphicon-chevron-right"></span></span></a><a href="javascript:" class="goto-last"><span class="glyphicon glyphicon-step-forward"></span></a><a href="javascript:" class="fullscreen"><span class="glyphicon glyphicon-fullscreen"></span></a><div class="goto-index-input"><input type="text" class="goto-index form-control input-sm" value="1">&nbsp;/&nbsp;<span class="total"></span></div></div></div>';
                            $("#lesson-ppt-content").html(html).show();

                            var player = new SlidePlayer({
                                element: '.slide-player',
                                slides: response
                            });

                        }, 'json');
                    }

                    else if (lesson.type == 'document' ) {

                        $.get(that.get('courseUri') + '/lesson/' + id + '/document', function(response) {
                            if (response.error) {
                                var html = '<div class="lesson-content-text-body text-danger">' + response.error.message + '</div>';
                                $("#lesson-document-content").html(html).show();
                                return ;
                            }
							$('.lesson-dashboard .lesson-content').css({'padding':0});
                            var html = '<iframe id=\'viewerIframe\' width=\'100%\'allowfullscreen webkitallowfullscreen height=\'100%\' frameborder=\'no\' border=\'0\' ></iframe>';
                            $("#lesson-document-content").html(html).show();

                            var player = new DocumentPlayer({
                                element: '#lesson-document-content',
                                swfFileUrl:response.swfUri,
                                pdfFileUrl:response.pdfUri
                            });

                        }, 'json');
                    }

                    else if (lesson.type == 'flash' ) {
                        
                            $("#lesson-swf-content").html('<div id="lesson-swf-player"></div>');
                            swfobject.embedSWF(lesson.mediaUri, 
                                'lesson-swf-player', '100%', '100%', "9.0.0", null, null, 
                                {wmode:'opaque',allowFullScreen:'true'});
                            $("#lesson-swf-content").show();

                    }
                }

                if (lesson.type == 'testpaper') {
                    that.element.find('[data-role=finish-lesson]').hide();
                } else {
                    if (!that.element.data('hideMediaLessonLearnBtn')) {
                        that.element.find('[data-role=finish-lesson]').show();
                    } else {
                        if (lesson.type == 'video' || lesson.type == 'audio') {
                            that.element.find('[data-role=finish-lesson]').hide();
                        } else {
                            that.element.find('[data-role=finish-lesson]').show();
                        }
                    }
                }

                that._toolbar.set('lesson', lesson);
                that._startLesson();

            }, 'json');

            $.get(this.get('courseUri') + '/lesson/' + id + '/learn/status?center='+center, function(json) {
                var $finishButton = that.element.find('[data-role=finish-lesson]');
                if (json.status != 'finished') {
                    $finishButton.removeClass('btn-success');
                    $finishButton.find('.glyphicon').removeClass('glyphicon-check').addClass('glyphicon-unchecked');
                } else {
                    $finishButton.addClass('btn-success');
                    $finishButton.find('.glyphicon').removeClass('glyphicon-unchecked').addClass('glyphicon-check');
                }
            }, 'json');

            this._showOrHideNavBtn();

        },

        _showOrHideNavBtn: function() {
            var $prevBtn = this.$('[data-role=prev-lesson]'),
                $nextBtn = this.$('[data-role=next-lesson]'),
                index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            $prevBtn.show();
            $nextBtn.show();

            if (index < 0) {
                return ;
            }

            if (index === 0) {
                $prevBtn.hide();
            }
            if (index === (this._lessons.length - 1)) {
                $nextBtn.hide();
            }

        },

        _getNextLessonId: function(e) {

            var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            if (index < 0) {
                return -1;
            }

            if (index + 1 >= this._lessons.length) {
                return -1;
            }

            return this._lessons[index+1];
        },

        _getPrevLessonId: function(e) {
            var index = $.inArray(parseInt(this.get('lessonId')), this._lessons);
            if (index < 0) {
                return -1;
            }

            if (index == 0 ) {
                return -1;
            }

            return this._lessons[index-1];
        }

    });

    var DurationStorage = {
        set: function(userId,mediaId,duration) {
            var durationTmps = localStorage.getItem("durations");
            if(durationTmps){
                durations = new Array();
                var durationTmpArray = durationTmps.split(",");
                for(var i = 0; i<durationTmpArray.length; i++){
                    durations.push(durationTmpArray[i]);
                }
            } else {
                durations = new Array();
            }

            var value = userId+"-"+mediaId+":"+duration;
            if(durations.length>0 && durations.slice(durations.length-1,durations.length)[0].indexOf(userId+"-"+mediaId)>-1){
                durations.splice(durations.length-1, durations.length);
            }
            if(durations.length>=20){
                durations.shift();
            }
            durations.push(value);
            localStorage["durations"] = durations;
        },
        get: function(userId,mediaId) {
            var durationTmps = localStorage.getItem("durations");
            if(durationTmps){
                var durationTmpArray = durationTmps.split(",");
                for(var i = 0; i<durationTmpArray.length; i++){
                    var index = durationTmpArray[i].indexOf(userId+"-"+mediaId);
                    if(index>-1){
                        var key = durationTmpArray[i];
                        return parseFloat(key.split(":")[1])-5;
                    }
                }
            }
            return 0;
        },
        del: function(userId,mediaId) {
            var key = userId+"-"+mediaId;
            var durationTmps = localStorage.getItem("durations");
            var durationTmpArray = durationTmps.split(",");
            for(var i = 0; i<durationTmpArray.length; i++){
                var index = durationTmpArray[i].indexOf(userId+"-"+mediaId);
                if(index>-1){
                    durationTmpArray.splice(i,1);
                }
            }
            localStorage.setItem("durations", durationTmpArray);
        }
    };


    exports.run = function() {
	    $('.lesson-dashboard .lesson-content').css({'padding':'20px'});
        var dashboard = new LessonDashboard({
            element: '#lesson-dashboard'
        }).render();

        function recordLearningTime(){
            if(!isTeacher) {
                url = "../../course/" + dashboard.attrs.lessonId.value + '/learn/time/2';
                $.post(url);
                setTimeout(recordLearningTime, 120000);
            }
        }
        if(!isTeacher) {
            setTimeout(recordLearningTime, 120000);
        }

    };

});
