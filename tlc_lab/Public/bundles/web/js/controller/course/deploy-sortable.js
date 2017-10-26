define(function(require, exports, module) {

    exports.run = function() {
        
        /* 展开收起进行排序 start fubaosheng 2015-12-23 */
         window.deploy = function(idSelector){
            var liObj = $("#"+idSelector);
            var id = liObj.attr("data-id");
            var pid = liObj.attr("data-pid");
            var child =  $("li.clearfix[data-pid='"+id+"']");
            var deployObj = liObj.find("em.deploy");

            if(pid == "0" && child.size()){
                var hide = 1;
                if(deployObj.hasClass("fa-minus")) hide = 0;
                if(pid == "0") 
                    liObj.removeClass("hide");
                if(hide){
                    child.addClass("hide");
                    var liClass = "fa-plus";
                }else{
                    child.removeClass("hide");
                    var liClass = "fa-minus";
                }
                if(!liObj.hasClass("item-chapter-unit")){
                    child.each(function(i,q){
                        var qid = $(q).attr("data-id");
                        var qli = $("li.clearfix[data-pid='"+qid+"']");
                        if(hide) qli.addClass("hide");
                        else qli.removeClass("hide");
                    })
                }
                deployObj.remove();
                //var str = '<span style="top:17px;cursor:pointer" class="deploy pull-left glyphicon '+liClass+'"></span>';
                //liObj.find(".item-content i").before(str);
                var str='<em class="deploy pull-right fa  '+liClass+'"></em>';
                liObj.find(".item-content").before(str);
                liObj.find("em.deploy").bind("click",function(){
                    var self = $(this);
                    $("em.deploy").not(self).each(function(index,Element){
                        window.toggleHide($(Element));
                    });
                    window.toggleDeploy(self);
                });
            }else{
                if(pid == "0")
                    liObj.removeClass("hide");
                deployObj.remove();
            }
        }
        
        /*  展开收起进行排序 end fubaosheng 2015-12-23  */
        
    }

});