define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("jquery.jwplayer");
    require('bootbox');
    exports.run = function() {
        var thePlayer;
        thePlayer = jwplayer("playerzmblbkjP").setup({
            file: liveUrl,
            screencolor: "#fff", //播放器颜色
            autostart: playAuto, //自动播放
//            rtmp: {
//                bufferlength: 3
//            },

        });
        window.sortKey = poolKey ? poolKey : '';

        window.failTryCnt = 0;
        window.requestLock = 0; //ajax锁，保证每次只有一个ajax请求
        window.isLiving = 1;
        if (liveStatus == 1)
            window.isLiving = 3;
        function livePolling() {
            if (window.requestLock)
                return false;
            window.requestLock = 1;
            var center = center ? 1 : 0;
            var data = {liveId: liveId, deviceId: deviceId, sortKey: window.sortKey};

            $.ajax({
                url: liveReport,
                type: "post",
                data: data,
                dataType: "json",
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    window.requestLock = 0;
                    try {
                        if (typeof console != 'undefined')
                            console.error("[state: " + textStatus + ", error: " + errorThrown + " ]<br/>");
                    } catch (e) {
                        console.error('catch error[' + err.name + ']' + err.message);
                    }
                    if (window.failTryCnt > reportNum) {
                        //$.weeboxs.open('<p style="color:red">请检查网络是否正常<p>', {showOk: false, showCancel: false});
                        return false;
                    }
                    window.failTryCnt++;
                    setTimeout(livePolling(), 1000);
                },
                success: function(dataObj, textStatus) {
                    window.failTryCnt = 0;
                    window.requestLock = 0;

                    if (dataObj.status == false) {
                        thePlayer.pause();
                        clearInterval(window.report);
                        if (dataObj.box == 'alert') {
                            bootbox.alert({
                                buttons: {
                                    ok: {
                                        label: '确定',
                                        className: 'btn-myStyle'
                                    },
                                },
                                message: dataObj.message,
                                callback: function(result) {
                                    window.location.href = $('#back-liveList').attr('href');
                                },
                                title: '提示',
                            });
                        } else {
                            bootbox.confirm({
                                buttons: {
                                    confirm: {
                                        label: '退出',
                                        className: 'btn-myStyle'
                                    },
                                    cancel: {
                                        label: '在当前设备观看',
                                        className: 'btn-default'
                                    }
                                },
                                message: dataObj.message,
                                callback: function(result) {
                                    if (result) {
                                        window.location.href = $('#back-liveList').attr('href');
                                    } else {
                                        window.location.reload();
                                    }
                                },
                                title: '提示',
                            });
                        }
                        return false;
                    }

                    if (dataObj.liveStatus == 1) {
                        $(".livechat-tit .mrs").text('直播中：');
                        if (window.isLiving == 1) {
                            window.isLiving = 2;
                            jwplayer("playerzmblbkjP").load([{file: dataObj.liveUrl}]);
                        }
                        if (dataObj.okey == 1) {
                            window.sortKey = dataObj.sortKey;
                            jwplayer("playerzmblbkjP").load([{file: dataObj.liveUrl}]);
                        }
                    } else if (dataObj.liveStatus == 2) {
                        $(".livechat-tit .mrs").text('直播结束：');
                    } else {
                        $(".livechat-tit .mrs").text('等待直播：');
                    }
                }
            });
        }

        window.report = setInterval(function() {
            livePolling();
        }, reportTime);
        //隐藏滚动条
        $("html").css("overflow", "hidden");


        //点击显示/隐藏聊天室
        $('#lesson-toolbar-secondary').on('click', function() {
            if ($('#lesson-dashboard').hasClass('lesson-dashboard-open')) {
                $('#lesson-dashboard').removeClass('lesson-dashboard-open');
                $('.dashboard-content').css('right', 0);
                $(this).find('span').removeClass('glyphicon-chevron-right');
                $(this).find('span').addClass('glyphicon-chevron-left');
            } else {
                $('#lesson-dashboard').addClass('lesson-dashboard-open');
                $('.dashboard-content').css('right', '420px');
                $(this).find('span').removeClass('glyphicon-chevron-left');
                $(this).find('span').addClass('glyphicon-chevron-right');
            }

        });

        //直播超过上限
        if (restrict == 'false') {
            clearInterval(window.report);
            bootbox.confirm({
                buttons: {
                    confirm: {
                        label: '退出',
                        className: 'btn-myStyle'
                    },
                    cancel: {
                        label: '刷新重试',
                        className: 'btn-default'
                    }
                },
                message: '当前观看直播的人数已达到上限，您可以',
                callback: function(result) {
                    if (result) {
                        window.location.href = $('#back-liveList').attr('href');
                    } else {
                        window.location.reload();
                    }

                },
                title: '提示',
            });
            return false;
        }


    }
});