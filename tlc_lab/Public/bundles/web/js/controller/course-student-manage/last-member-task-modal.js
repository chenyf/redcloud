define(function(require, exports, module) {

    exports.run = function() {
       
        $("#lay_member_paginator ul > li > a").removeClass("ajaxLoad");
        
        $("#status").on({
            change:function(){
                $form = $("#task_form");
                $.get($form.attr('action'), $form.serialize(), function(html){
                    $("#modal").html(html);
                });
                return false;
            }
        });
        
        var wait_num = $("#wait_num").html();
        var get_task_status_url = $("#wait_num").data("task-status-url");
        if (parseInt(wait_num) > 0 && (typeof window.task == 'undefined' || window.task == 0)) {
            window.task = setInterval(function(){
                $.get(get_task_status_url,{},function(dataObj){
                    
                    if (dataObj.code == 1000) {
                        $("#complete_num").html(dataObj.data.completeNum);
                        $("#wait_num").html(dataObj.data.waitNum);
                        $("#succ_num").html(dataObj.data.succNum);
                        $("#fail_num").html(dataObj.data.failNum);
                        if (parseInt(dataObj.data.waitNum) == 0) {
                            clearInterval(window.task);
                            window.task = 0;
                        }
                    }
                    
                },'json');
                
            }, 2000);
            
            $('#modal').on('hide.bs.modal', function (event) {
                if (typeof window.task == 'number') {
                    clearInterval(window.task);
                    window.task = 0;
                }
            });
        }
       
    };

});