define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var Uploader = require('upload');
    var batProcess = require('web/batProcess/batProcess');
    
    exports.run = function() {
        var $form = $('#approve-form');
        var callback = function(param){
            var data = param.data;
            var tr = "";
            for(var key in data){
                var item = data[key];
                tr += "<tr data-itemmicrotime="+item['itemMicrotime']+">";
                $("#importError").find("th").each(function(i,q){
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
                    tr += "<td>"+td+"</td>";
                });
                tr += "</tr>";
            }
            $("#importError").find("tbody").append(tr);
            $("#importError").children("div").scrollTop($("#importError .table-bordered").height());
        }
        var createPollHtml = function(conf){
            var th = '';
            for(var i in conf){
                th+= '<th data-key="'+i+'">'+conf[i]+'</th>';
            }
            var importPollStr = '<div style="height:500px;overflow:auto;width:1000px;margin-left:-190px">'+
                                '<table class="table table-bordered" >'+
                                     '<thead>'+
                                         '<tr>'+
                                             '<th data-key="num">序号</th>'+
                                             th+
                                         '</tr>'+
                                     '</thead>'+
                                     '<tbody>'+
                                     '</tbody>'+
                                 '</table>'+
                             '</div>';
            $("#importError").html(importPollStr);
        }
        var userGoBackFun = function(response){
            if(response.status){
                $("#sureImport").remove();
                createPollHtml(response.conf);
                batProcess.itemPollListData({
                    code : response.data.code,
                    strId : response.data.strId,
                    microtime : response.data.microtime,
                    callback : callback,
                });
            }else{
                Notify.danger(response.message);
            }
        }
        var groupGoBackFun = function(response){
            if(response.status == 'error') {
                Notify.danger(response.message);
                $("#intro").html('');
                $('#sureImport').attr('disabled', false);
             }else{
                $("#intro").html('');
                var data = response.message;
                var str = '';
                for(var o in data){
                    str = str + data[o]+"</br>";
                }
                if(str==''){
                    str = "班级导入完成。";
                }
                $("#importError").append(str);
                $('#sureImport').attr('disabled', false);
            }
        }
        $('#sureImport').click(function() {
            var filePath  = $("#filePath").val();
            var userType = $("#userType").val();
            var fileName = $("#fileName").val();
            var dataUrl = $(this).attr("data");
            var type = $(this).data("type");
            if (confirm("确认导入吗?")){
                if(type == "group"){
                    $("#intro").html('导入中...');
                    $('#sureImport').attr('disabled', 'disabled');
                }
                $.post(dataUrl, {"filePath":filePath, userType: userType,fileName:fileName}, function(response){
                    if(type == "user"){
                        userGoBackFun(response);
                    }
                    if(type == "group"){
                        groupGoBackFun(response);
                    }
                }, 'json').error(function() {
                    $("#intro").html('导入失败，请返回重新导入');
                });
            }else{
                return false;
            }
        });

        $("[data-toggle='tooltip']").tooltip({delay: { show: 400, hide: 100 },trigger:'hover', html:true}); 

        $(".dropdown-menu li").on("click",function(){
            var type = $(this).data("type");
            var key = $(this).data("key");
            if(type != "start" && type != "kill"){
                Notify.danger('非法操作');
                return false;
            }
            if(!key){
                Notify.danger('任务不存在');
                return false; 
            }
            var url = $(this).data("url");
            $.post(url, {type:type,key:key}, function(html) {
                if(html['success']){
                    Notify.success("操作成功");
                    window.location.reload();
                }else{
                    Notify.danger(html["message"]);
                }
            },'json').error(function(){
                Notify.danger('操作失败');
            });
        });
    };

});