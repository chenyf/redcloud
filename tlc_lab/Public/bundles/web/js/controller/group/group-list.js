define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        //记录上次选中的情况
        if (window.chk_value) {
            $(window.chk_value).each(function(k,v){
                selectGroup(v);
            });
        }
        
        //阻止点击分页超链接事件
        $(".pagination").on("click", "a", function(e){
            var urls = $(this).attr("href");
            $("#group_list").load(urls,{},function(){
                $("input[name=checkAll]").prop('checked',false);
            });

            if ( e && e.preventDefault ) 
                e.preventDefault(); 
            else 
                window.event.returnValue = false; 
            return false; 
        });
        
        //绑定删除所选班级的事件
        $("#current_choose_group").unbind('click').on('click', 'i', function(){
                var groupid = $(this).attr("groupid");
                cancelGroup(groupid);
            }
        );
        
        
        $("input[name=groups]").unbind('click').on('click',function(event){
            if ($(this).is(":checked")) {
                selectGroup($(this).val());
            } else {
                cancelGroup($(this).val());
            }
            event.stopPropagation();
        });
        
        $("input[name=checkAll]").unbind('click').on('click',function(){
            var _this = $(this);
            $("input[name=groups]").each( function() {
                if (_this.prop('checked'))
                    selectGroup($(this).val());
                else
                    cancelGroup($(this).val());
            });
        });
        
        $(".media").unbind('click').on('click',function(){
            var checkDom = $(this).find("input[name=groups]");
            if (checkDom.length == 0) {
                return false;
            }
            if (checkDom.is(":checked")) {
                cancelGroup($(checkDom).val());
            } else {
                selectGroup($(checkDom).val());
            }
        });

        function selectGroup(groupid){
            var $groupDom = $("#group_"+groupid);
            $groupDom.children("input").prop('checked', true);
            $groupDom.parent().addClass("active");
            if ($.inArray(groupid,window.chk_value) < 0) {
                window.chk_value.push(groupid);
                var group_name = $groupDom.children("p:eq(0)").children("span").html();
                var group_year = $groupDom.children("p:eq(1)").children("span").html();
                $("#current_choose_group").append('<li id="group_label_'+groupid+'"><i groupid="'+groupid+'">×</i><span>'+group_name+'</span><span>'+group_year+'</span></li>');

            }

        }
        
        function cancelGroup(groupid){
            var $groupDom = $("#group_"+groupid);
            $groupDom.children("input").prop('checked', false);
            if ($.inArray(groupid,window.chk_value) >= 0)
                window.chk_value.splice($.inArray(groupid,window.chk_value),1);
            $("#group_label_"+groupid).remove();
            $groupDom.parent().removeClass("active");
            if ($groupDom.size() > 0)
                $("input[name=checkAll]").prop('checked',false);
        }

    };

});