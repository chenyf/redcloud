define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var jQuery = require('$');
    var JqueryCookie = require('jquery-cookie');
    /* 
     * 语音播放
     * @Author Yangjinlong  2015-06-01
     */
    exports.playAudio = {
        intvalInit: "",
        playerStatus: 0,
        playerId: 0,

        loadJs: function () {
            //        jQuery.getScript("http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.2.min.js")
            jQuery.getScript("/Public/assets/libs/jquery/1.11.2/jquery.js")
                .done(function () {
                    jQuery.getScript("/Public/assets/libs/recoder/js/swfobject.js")
                        .done(function () {
                            jQuery.getScript("/Public/assets/libs/recoder/js/recorder.js")
                                .done(function () {
                                    jQuery.getScript("/Public/assets/libs/recoder/js/main.js")
                                        .done(function () {
                                            playAudio.loadPlayer();
                                        })
                                })
                        })
                })
        },

        loadPlayer: function () {
            var oDiv = $("#wavPlayer");
            if (oDiv.length <= 0) {
                $("body").eq(0).append("<div style='display:none' id='wavPlayer'></div>");
            }
            swfobject.embedSWF("/Public/assets/libs/recoder/wavplayer.swf", "wavPlayer", 24, 24, "10.1.0", "",
                {'upload_image': '/Public/assets/libs/recoder/images/upload.png'},
                {}, {'id': "audio1", 'name': "audio1"});
            intvalInit = setInterval(function () {
                playAudio.initPlayer();
            }, 100)
        },

        doPlay: function (fname, id) {
            if (playAudio.playerStatus == 0) {
                var player = playAudio.getPlayer("audio1");
                player.play(fname);
                playAudio.playerStatus = 1;
                playAudio.playerId = id;
                $('#palyId_' + id).addClass('yy-txt2');
            }
        },

        getPlayer: function (pid) {
            var obj = document.getElementById(pid);
            if (obj.doPlay) {
                return obj;
            } else {
                for (i = 0; i < obj.childNodes.length; i++) {
                    var child = obj.childNodes[i];
                    if (child.tagName == "EMBED")
                        return child;
                }
            }
        },

        initPlayer: function () {
            var player = playAudio.getPlayer('audio1');
            if (player) {
                player.attachHandler("PLAYER_STOPPED", "playAudio.SoundState", "STOPPED");
                clearInterval(intvalInit);
            }
        },

        SoundState: function () {
            if (playAudio.playerStatus == 1) {
                $("#palyId_" + playAudio.playerId).removeClass('yy-txt2');
                playAudio.playerStatus = 0;
            }
        }

    }

    /**
     * 错误调试
     * @author fubaosheng 2015-06-03
     */
    window.onerror = function (msg, url, l) {
        var txt = "";
        txt = "There was an error on this page.\n\n";
        txt += "Error: " + msg + "\n";
        txt += "URL: " + url + "\n";
        txt += "Line: " + l + "\n\n";
        txt += "Click OK to continue.\n\n";
        try {
            debug(txt);
        } catch (e) {
        }
        return true;
    }

    window.debug = function (data) {
        if (typeof console != 'undefined') {
            console.info(data);
        }
    }

    window.isIE = function () { //ie?
        if (!!window.ActiveXObject || "ActiveXObject" in window) {
            return true;
        }
        return false;
    }

    /**
     * @author qzw 2015-09-17
     */
    window.getScheme = function () {
        return location.href.indexOf('https') > -1 ? 'https' : 'http';
    }

    //qzw
    exports.checkVersion = function () {
        if ($('html').hasClass('lt-ie8')) {
            var message = '<div class="alert alert-warning" style="margin-bottom:0;text-align:center;">';
            message += '您的浏览器版本太低，不能正常使用本站，请使用';
            message += '<a href="http://windows.microsoft.com/zh-CN/internet-explorer/downloads/ie" target="_blank">IE8浏览器</a>、';
            message += '<a href="http://www.baidu.com/s?wd=%E8%B0%B7%E6%AD%8C%E6%B5%8F%E8%A7%88%E5%99%A8" target="_blank">谷歌浏览器</a><strong>(推荐)</strong>、';
            message += '<a href="http://firefox.com.cn/download/" target="_blank">Firefox浏览器</a>，访问本站。';
            message += '</div>';

            $('body').prepend(message);
        }
    }

    //qzw
    exports.globalClick = function () {
        $("i.hover-spin").mouseenter(function () {
            $(this).addClass("md-spin");
        }).mouseleave(function () {
            $(this).removeClass("md-spin");
        });
        //by qzw
        $('#mobileDown').mouseenter(function () {
            $("div.n-ewm-pic").removeClass('hide')
        }).mouseleave(function () {
            $("div.n-ewm-pic").addClass('hide')
        });
    }

    //qzw
    exports.ajaxSend = function () {
        $(document).ajaxSend(function (a, b, c) {
            if (c.type == 'POST') {
                //b.setRequestHeader('X-CSRF-Token', $('meta[name=csrf-token]').attr('content'));
            }
        });
    }

    //qzw
    exports.ajaxError = function () {
        $(document).ajaxError(function (event, jqxhr, settings, exception) {
            try {
                var json = jQuery.parseJSON(jqxhr.responseText);
            } catch (e) {
                // console.info("报错提醒：" + e);
                return false;
            }
            error = json.error;
            if (!error) {
                return;
            }

            if (error.name == 'Unlogin') {
                $('.modal').modal('hide');

                $("#login-modal").modal('show');
                $.get($('#login-modal').data('url'), function (html) {
                    $("#login-modal").html(html);
                });
            }

            if (error.name == 'UnClass') {
                $('.modal').modal('hide');
                Notify.danger('您还没有加入任何班级，请先加入班级！');
            }

            if (error.name == 'UnPermission') {
                Notify.danger(error.message);
            }
        });
    }

    //qzw
    exports.ajaxLoadATag = function () {
        //a标签ajax方式点击
        $(".ajax-click").click(function (event) {
            event.preventDefault();
            var _this = $(this);
            if (confirm('是否要执行此操作？')) {
                $.ajax({
                    url: _this.attr('href'),
                    type: 'get',
                    success: function (data) {
                        if (data.status == 1) {
                            Notify.success(data.info);
                            setTimeout(function () {
                                if (data.url) {
                                    window.location.href = data.url;
                                } else {
                                    window.location.reload();
                                }
                            }, 1000);
                            return true;
                        }
                        Notify.danger(data.info);
                        window.location.reload();
                    }
                })
            }
        });
    }

    exports.floatConsult = function () {
        var $element = $('#float-consult');
        if ($element.length == 0) {
            return;
        }

        if ($element.data('display') == 'off') {
            return;
        }

        var marginTop = (0 - $element.height() / 2) + 'px';

        var isIE10 = /MSIE\s+10.0/i.test(navigator.userAgent)
            && (function () {
                "use strict";
                return this === undefined;
            }());

        var isIE11 = (/Trident\/7\./).test(navigator.userAgent);

        if (isIE10 || isIE11) {
            $element.css({marginTop: marginTop, visibility: 'visible', marginRight: '16px'});
        } else {
            $element.css({marginTop: marginTop, visibility: 'visible'});
        }

        $element.find('.btn-group-vertical .btn').popover({
            placement: 'left',
            trigger: 'hover',
            html: true,
            content: function () {
                return $($(this).data('contentElement')).html();
            }
        });
    }
    /**
     * 图片出错 显示默认图片
     * 
     * @author 谈海涛
     */
    exports.imgErrorDefault = function(){
        $('img.course-picture').each(function(){
            if($(this).attr('loaderrimg') != 1 )
                return false;
            if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) { 
                  this.src = '/Public/assets/img/default/loading-error.jpg?5.1.4'; 
              } 
         });
         
         $('.user-avatar-link img').each(function(){
            if($(this).attr('loaderrimg') != 1 )
                return false;
            if (!this.complete || typeof this.naturalWidth == "undefined" || this.naturalWidth == 0) { 
                  this.src = '/Public/assets/img/default/pic-error.png?5.1.4'; 
              } 
         });
    }



    /**
     * 检查placeholer(针对ie下placeholder无效)
     *
     * @returns {undefined}
     */
    exports.checkPlaceHolder = function () {
        // 判断浏览器是否支持 placeholder属性
        var isPlaceholder = function () {
            var input = document.createElement('input');
            return 'placeholder' in input;
        }
        if (isPlaceholder()) return;

        $("input[placeholder]").each(
            function () {
                var placeholder = $(this).attr('placeholder');
                var type = $(this).attr('type');
                if (type == 'password') {
                    $(this).attr('type', 'text');
                }
                $(this).val(placeholder);
                $(this).focus(function () {
                    $(this).attr('placeholder', '');
                    var position = 0;
                    var txtFocus = this;
                    if (!+[1,]) {
                        var range = txtFocus.createTextRange();
                        range.move("character", position);
                        range.select();
                    } else {
                        txtFocus.setSelectionRange(position, position);
                        txtFocus.focus();
                    }
                });
                $(this).click(function () {
                    if ($(this).val() != placeholder)
                        return;
                    var position = 0;
                    var txtFocus = this;
                    if (!+[1,]) {
                        var range = txtFocus.createTextRange();
                        range.move("character", position);
                        range.select();
                    } else {
                        txtFocus.setSelectionRange(position, position);
                        txtFocus.focus();
                    }
                });
                $(this).blur(function () {
                    $(this).attr('placeholder', placeholder);
                    if ($(this).val() == '')
                        $(this).val(placeholder);
                    if (type == 'password') {
                        $(this).attr('type', 'text');
                    }
                });
                $(this).keydown(
                    function () {
                        if (type == 'password') {
                            $(this).attr('type', 'password');
                        }
                        if ($(this).attr('placeholder') == ''
                            && $(this).val() == placeholder)
                            $(this).val('');
                    });
            });
    };

    //js获取url地址栏参数方法
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg);  //匹配目标参数
        if (r != null) return unescape(r[2]);
        return null; //返回参数值
    }

    if (getUrlParam('tg') != null) {
        $.cookie('generalize', getUrlParam('tg'), {path: '/', expires:365});
    }


});