define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var poll = require('./poll');

    exports.itemTaskListUrl = "/System/BatTask/itemTaskListAction";
    exports.itemTaskListDataUrl = "/System/BatTask/itemTaskListDataAction";
    exports.taskListUrl = "/System/BatTask/taskListAction";
    exports.taskListDataUrl = "/System/BatTask/taskListDataAction";
    exports.startTaskUrl = "/System/BatTask/startTaskAction";
    exports.stopTaskUrl = "/System/BatTask/stopTaskAction";
    exports.pollUrl = "/System/BatTask/pollAction";
    
    //子项列表接口（返回页面）
    exports.itemTaskList = function(param){
        if(typeof(param.url) === "undefined" || param.url == ""){
            var url = exports.itemTaskListUrl+"/code/"+param.code+"/strId/"+param.strId+"/microtime/"+param.microtime+"/limit/"+param.limit;
        }else{
            var url = param.url;
        }
        if($('#'+param.microtime+'-item-modal').size()<1)
            $('body').append("<div id='"+param.microtime+"-item-modal' class='modal'></div>");
        $('#'+param.microtime+'-item-modal').load(url, function() {
            $('#'+param.code+'-task-modal').hide();
            $('#'+param.microtime+'-item-modal').modal({backdrop: true, keyboard: false, show: 'show'})
            .on('hidden.bs.modal', function () {
                $('#'+param.code+'-task-modal').show();
            });
            exports.ajaxLoad(param,'item');
        });
    };
    
    //子项列表接口（返回数据）
    exports.itemTaskListData = function(param){
        var data = {code:param.code,strId:param.strId,microtime:param.microtime,page:param.page,limit:param.limit};
        $.get(exports.itemTaskListDataUrl, data, function(res) {
            if(res.status){
                param.callback(res["data"]);
            }else{
                return false;
            }
        },'json').error(function(){
            return false;
        });
    };
    
    //触发子项列表
    exports.triggerItemTaskList = function(obj){
        if ((typeof(obj) === "undefined") || (obj.size()<1) )
            var obj = $(document);
        obj.find(".get-item-list").on("click",function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            exports.itemTaskList(param);
        });
    }
    
    //子项轮训接口（返回页面）
    exports.itemPollList = function(param){
        var url = exports.pollUrl+"/code/"+param.code+"/strId/"+param.strId+"/microtime/"+param.microtime;
        if($('#'+param.microtime+'-poll-modal').size()<1)
            $('body').append("<div id='"+param.microtime+"-poll-modal' class='modal'></div>");
        $('#'+param.microtime+'-poll-modal').load(url, function() {
            $('#'+param.code+'-task-modal').hide();
            $('#'+param.microtime+'-poll-modal').modal({backdrop: true, keyboard: false, show: 'show'});
            poll.run(param);
        });
    };
    
    //子项轮训接口（返回数据）
    exports.itemPollListData = function(param){
        var itemXhr;
        var paramObj = {code:param.code,strId:param.strId,microtime:param.microtime};
        window.pollItemTask = function(paramObj){
            if(window.pollItemLock) return false;
            window.pollItemLock = 1;
            window.pollTaskItemTm = window.pollTaskItemTm ? window.pollTaskItemTm : '';
            paramObj.pollTaskTm = window.pollTaskItemTm;
            itemXhr = $.ajax({
                url : exports.pollUrl,
                type : 'POST',
                data : paramObj,
                dataType : 'json',
                timeout : 60000,
                error : function(XMLHttpRequest,textStatus,errorThrown){
                    window.pollItemLock = 0;
                    if(textStatus == "timeout"){
                        console.info('error:数据请求超时');
                        pollItemTask(paramObj);
                    }
                },
                success : function(dataObj){
                    window.pollItemLock = 0;
                    if(dataObj['success']){
                        window.pollTaskItemTm = dataObj["data"]['tm'];
                        param.callback(dataObj["data"]);
                        pollItemTask(paramObj);
                    }else{
                        console.info('success:数据请求超时');
                        pollItemTask(paramObj);
                    }
                }
            });
        }
        pollItemTask(paramObj);
    };
    
    //触发子项轮训
    exports.triggerItemPollList = function(obj){
        if ((typeof(obj) === "undefined") || (obj.size()<1) )
            var obj = $(document);
        obj.find(".poll-item-list").on("click",function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            exports.itemPollList(param);
        });
    }
    
    //父项列表接口（返回页面）
    exports.taskList = function(param){
        if(typeof(param.url) === "undefined" || param.url == ""){
            var url = exports.taskListUrl+"/code/"+param.code+"/strId/"+param.strId+"/limit/"+param.limit;
        }else{
            var url = param.url;
        }
        if($('#'+param.code+'-task-modal').size()<1)
            $('body').append("<div id='"+param.code+"-task-modal' class='modal'></div>");
        $('#'+param.code+'-task-modal').load(url, function() {
            $('#'+param.code+'-task-modal').modal({backdrop: true, keyboard: false, show: 'show'});
            exports.ajaxLoad(param,'task');
        });
    };
    
    //ajax防止点击分页超链接事件
    exports.ajaxLoad = function(param,type){
        if(type == "task")
            var obj = $("#"+param.code+"-task-modal");
        if(type == "item")
            var obj = $("#"+param.microtime+"-item-modal");
        obj.find(".pagination").on("click", "a", function(e){
            param.url =  $(this).attr("href");
            if(type == "task")
                exports.taskList(param);
            if(type == "item")
                exports.itemTaskList(param);
            if (e && e.preventDefault)
                e.preventDefault(); 
            else
                window.event.returnValue = false; 
            return false; 
        });
    }
     
    //父项列表接口（返回数据）
    exports.taskListData = function(param){
        var data = {code:param.code,strId:param.strId,page:param.page,limit:param.limit};
        $.get(exports.taskListDataUrl, data, function(res) {
            if(res.status){
                param.callback(res["data"]);
            }else{
                return false;
            }
        },'json').error(function(){
            return false;
        });
    };
    
    //父项开始任务接口
    exports.startTask = function(param){
        var url = exports.startTaskUrl+"/code/"+param.code+"/strId/"+param.strId+"/microtime/"+param.microtime;
        $.get(url, function(res) {
            if(res.status){
                Notify.success(res.info);
                $("#"+param.code+"-"+param.strId+"-"+param.microtime).replaceWith(res.data);
                exports.triggerItemTaskList($("#"+param.code+"-"+param.strId+"-"+param.microtime));
                exports.triggerItemPollList($("#"+param.code+"-"+param.strId+"-"+param.microtime));
                exports.triggerStopTask($("#"+param.code+"-"+param.strId+"-"+param.microtime));
            }else{
                Notify.danger(res.info);
            }
        },'json').error(function(){
            Notify.danger('操作失败');
        });
    }
    
    //触发开始任务
    exports.triggerStartTask = function(){
        $(document).find(".start-task").on("click",function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            exports.startTask(param);
        });
    }
    
    //父项停止任务接口
    exports.stopTask = function(param){
        var url = exports.stopTaskUrl+"/code/"+param.code+"/strId/"+param.strId+"/microtime/"+param.microtime;
        $.get(url, function(res) {
            if(res.status){
                Notify.success(res.info);
                $("#"+param.code+"-"+param.strId+"-"+param.microtime).replaceWith(res.data);
                exports.triggerItemTaskList($("#"+param.code+"-"+param.strId+"-"+param.microtime));
            }else{
                Notify.danger(res.info);
            }
        },'json').error(function(){
            Notify.danger('操作失败');
        });
    }
    
    //触发停止任务
    exports.triggerStopTask = function(obj){
        if ((typeof(obj) === "undefined") || (obj.size()<1) )
            var obj = $(document);
        obj.find(".stop-task").on("click",function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            exports.stopTask(param);
        });
    }

});