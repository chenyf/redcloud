define(function(require, exports, module) {
    exports.pollUrl = "/System/BatTask/pollAction";
    exports.run = function(param) {
        var pollTaskxhr;
        var paramObj = {code:param.code,strId:param.strId,microtime:param.microtime};
        window.scanPoll = 0;
        window.pollTask = function(paramObj){
            if(window.scanPoll) return false;
            if(window.pollTaskLock) return false;
            window.pollTaskLock = 1;
            window.pollTaskTm = window.pollTaskTm ? window.pollTaskTm : '';
            paramObj.pollTaskTm = window.pollTaskTm;
            pollTaskxhr = $.ajax({
                url : exports.pollUrl,
                type : 'POST',
                data : paramObj,
                dataType : 'json',
                timeout : 60000,
                error:function(XMLHttpRequest,textStatus,errorThrown){
                    window.pollTaskLock = 0;
                    if(textStatus == "timeout"){
                        console.info("超时");
                        pollTask(paramObj);
                    }
                },
                success:function(dataObj){
                    console.info(dataObj);
                    window.pollTaskLock = 0;
                    if(dataObj['success']){
                        window.pollTaskTm = dataObj["data"]['tm'];
                        var table = "";
                        var data = dataObj["data"]["data"];
                        var pollObj = $("#poll-task-"+param.microtime);
                        for(var key in data){
                            var item = data[key];
                            table += "<tr>";
                            pollObj.find("th").each(function(i,q){
                                var itemKey = $(q).data("key");
                                var td = item[itemKey];
                                if(itemKey == "status"){
                                    td = (td == "nostart") ? "未开始" : (td == "failure" ? "失败" : "成功");
                                }
                                if(itemKey == "itemData"){
                                    if(Object.prototype.toString.call(td) == '[object Object]'){
                                        td = "";
                                        for(var itemData in item[itemKey]){
                                            td+= item[itemKey][itemData]+';';
                                        }
                                    }
                                    td = "<pre style='font-weight:bold'>"+td+"</pre>";
                                }
                                table += "<td>"+td+"</td>";
                            });
                            table += "</tr>";
                        }
                        if(table){
                            pollObj.find("#add-poll-"+param.microtime).remove();
                            pollObj.find("tbody").append(table);
                        }
                        pollTask(paramObj);
                    }else{
                        console.info("超时了");
                        pollTask(paramObj);
                    }
                }
            });
        }
        pollTask(paramObj);
        $('#'+param.microtime+'-poll-modal').on('hidden.bs.modal', function () {
            window.pollTaskTm = "";
            window.pollTaskLock = 0;
            window.scanPoll = 1;
            $('#'+param.code+'-task-modal').show();
            pollTaskxhr.abort();
        });
        $(".clear-poll").on("click",function(){
            $("#poll-task-"+param.microtime+" tbody").html("");
        });
        
    };

});