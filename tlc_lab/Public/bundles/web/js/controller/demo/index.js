define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require("jquery.jwplayer");
    exports.run = function() {
        var thePlayer;
        thePlayer = jwplayer("playerzmblbkjP").setup({
            playlist: [{ 
                    sources: [{  
                       file: ''
                    },{ 
                       file: 'http://2107.liveplay.myqcloud.com/2107_0d3f23debf3a11e5b91fa4dcbef5e35a.m3u8'  // 这里可以写m3u8的url。 
                    }] 
                       }], 
            height: 800,
            width: 1280,
            screencolor: "#fff", //播放器颜色
            repeat: "always", //重复播放
            autostart: true, //自动播放
//            rtmp: {
//                bufferlength: 3
//            },
            events: {
                onReady: function() {
                },
                onBeforePlay: function() {
                },
                onPlaylistItem: function() { /*切换线路时触发*/
                },
                onBuffer: function() {
                },
                onPlay: function() {//播放时
                },
                onBufferChange: function() {
            console.log("onBufferChange");
                },
                onCaptionsChange: function() {
                },
                onPause: function() {
                },
                onSetupError: function(e) {
                },
                onError: function(e) {
                },
                onTime: function(e) {
                    console.log("thePlayer.bufferlength");
                },
                onComplete: function(e) {
                }
            }
        });
        

    }
});