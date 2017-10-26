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
       
    };

});