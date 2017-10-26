define(function(require, exports, module) {
    
    module.exports = function() {
        //记录上次选中的情况
        if (window.questions) {
            for(var v in window.questions){
              selectGroup(v);
            }
        }
        //阻止点击分页超链接事件
        $(".pagination").on("click", "a", function(e){
            var urls = $(this).attr("href");
//            $("#group_list").load(urls,{},function(){
//                $("input[name=checkAll]").prop('checked',false);
//            });

            if ( e && e.preventDefault ) 
                e.preventDefault(); 
            else 
                window.event.returnValue = false; 
            return false; 
        });
        $("input[name=groups]").unbind('click').on('click', function(event) {
            if ($(this).is(":checked")) {
                selectGroup($(this).val());
            } else {
                cancelGroup($(this).val());
            }
            $('#selectedStatistic').load($('[name=refreshUrl]').data('url'));
            event.stopPropagation();
        });

        $("input[name=checkAll]").unbind('click').on('click', function() {
            var _this = $(this);
            $("input[name=groups]").each(function() {
                if (_this.prop('checked')) {
                    selectGroup($(this).val());
                } else {
                    cancelGroup($(this).val());
                }
            });
            $('#selectedStatistic').load($('[name=refreshUrl]').data('url'));
        });
        
        function selectGroup(groupid){
            var $groupDom = $("#group_"+groupid);
            var qid = $groupDom.data('id');
            var score = $groupDom.find('input[name=score]').val() ;
            score = regScore(score) ? score : 0;
            var type  = $groupDom.find('input[name=questionType]').val();
            var data = {score:score,type:type};
            $groupDom.find("input[type=checkbox]").prop('checked', true);
            if (qid && !window.questions.hasOwnProperty(qid)) {
                  window.questions[qid] = data;
            }
        }
        function regScore(score){
            var reg = /^(100|[1-9]\d?\.\d{1,2}|[1-9]\d?)$/;
            return reg.test(score);
        }
        function cancelGroup(groupid){
            var $groupDom = $("#group_"+groupid);
            var qid = $groupDom.data('id');
            $groupDom.find("input[type=checkbox]").prop('checked', false);
            if (qid && window.questions.hasOwnProperty(qid)){
                delete window.questions[qid];
            if ($groupDom.size() > 0)
                $("input[name=checkAll]").prop('checked',false);
            }
            if (window.questions) {
                for(var groupid in window.questions){
                  selectGroup(groupid);
                }
            }
        }
    };
});