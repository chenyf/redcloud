define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');

    exports.run = function() {
        var xhr;
        window.pollTaskUser = function(){
            if(window.scanPoll) return false;
            if(window.pollTaskUserLock) return false;
            window.pollTaskUserLock = 1;
            var url = $("#poll-task-user").data('url');
            var key = $("#poll-task-user").data('key');
            window.pollTaskUserTm = window.pollTaskUserTm ? window.pollTaskUserTm : '';
            xhr = $.ajax({
                url : url,
                type : 'POST',
                data : {key:key,tm:window.pollTaskUserTm},
                dataType : 'json',
                timeout : 60000,
                error:function(XMLHttpRequest,textStatus,errorThrown){
                    window.pollTaskUserLock = 0;
                    if(textStatus == "timeout"){
                        console.info("超时");
                        pollTaskUser();
                    }
                },
                success:function(dataObj){
                    window.pollTaskUserLock = 0;
                    if(dataObj['success']){
                        window.pollTaskUserTm = dataObj["data"]['tm'];
                        var str = "";
                        for(var i in dataObj["data"]["data"]){
                            var user = dataObj["data"]["data"][i];
                            var emailSend = user["emailSend"]=="1" ? "成功" : "失败";
                            var mobileSend = user["mobileSend"]=="1" ? "成功" : "失败";
                            str+= "<tr><td>"+user["uname"]+"</td><td>"+user["email"]+"</td><td>"+emailSend+"</td><td>"+user["mobile"]+"</td><td>"+mobileSend+"</td></tr>";
                        }
                        if(str) $("#poll-task-user tbody").append(str);
                        pollTaskUser();
                    }else{
                        console.info(dataObj);
                        pollTaskUser();
                    }
                }
            });
        }
        pollTaskUser(window.scanPoll = 0);
        $('#modal').on('hide.bs.modal', function () {
            xhr.abort();
            window.pollTaskUserTm = "";
            window.pollTaskUserLock = 0;
            window.scanPoll = 1;
        });
        $(".clear-poll").on("click",function(){
            $("#poll-task-user tbody").html("");
        });
        
    };

});