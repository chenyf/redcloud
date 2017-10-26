define(function(require, exports, module) {

    exports.run = function() {
        
        /* 展开收起 start fubaosheng 2015-12-23 */
       
        window.toggleDeploy = function(item){
            if(item.hasClass("fa-minus")) var hide = 1;
            else var hide = 0;
            item.removeClass("fa-plus").removeClass("fa-minus");
            if(hide) item.addClass("fa-plus");  
            else item.addClass("fa-minus");
           
            var id = item.parents("li.clearfix").attr("data-id");
            $("li.clearfix").each(function(i,q){
                var pid = $(q).attr("data-pid");
                if(id == pid){ 
                    if(hide) $(q).addClass("hide");
                    else $(q).removeClass("hide");
                    if($(q).hasClass("item-chapter")){
                        var unitPid = $(q).attr("data-id");
                        var lessonLi = $("li.clearfix[data-pid='"+unitPid+"']");
                        if(hide) lessonLi.addClass("hide");
                        else lessonLi.removeClass("hide");
                    }
                }
            });
        }
        
        window.toggleHide = function(item){
            if(item.hasClass("fa-minus")){
                item.removeClass("fa-plus").removeClass("fa-minus");
                item.addClass("fa-plus");
                var id = item.parents("li.clearfix").attr("data-id");
                $("li.clearfix").each(function(i,q){
                    var pid = $(q).attr("data-pid");
                    if(id == pid){
                        $(q).addClass("hide");
                        if($(q).hasClass("item-chapter")){
                            var unitPid = $(q).attr("data-id");
                            $("li.clearfix[data-pid='"+unitPid+"']").addClass("hide");
                        }
                    }
                });
            }
        }
        
        $(document).find("em.deploy").on("click",function(){
            var self = $(this);
            $("em.deploy").not(self).each(function(index,Element){
                window.toggleHide($(Element));
            });
            window.toggleDeploy(self);
        });

        $(document).find(".item-content i.deploy").on('click',function(){
            $(this).parent('.item-content').prev('em.deploy').trigger('click');
            $(this).parent('.item-content').find('em.deploy').trigger('click');
        });
        
        /*  展开收起 end fubaosheng 2015-12-23  */
        
    }

});