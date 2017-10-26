define(function(require, exports, module) {
    exports.run = function() {
        function removeArr(arr,qid){
            var str=arr.join();
            str=str.replace(qid+",","");
            str=str.replace(","+qid,"");
            str=str.replace(qid,"");
            return str.split(",");
        }
        function selectType(type){
            if(type){
                $("#quiz-table tbody tr").hide();
                $("#quiz-table tbody tr[data-type="+type+"]").show();
            }else{
                 $("#quiz-table tbody tr").show();
            }
        }
        var testCount=$("#testCount").val();
        $("#testCount").blur(function(){
            testCount=$(this).val();
        })
        function filterArr(arr){
            for (i = arr.length - 1;  i >=0; i--) {
                if (arr[i] == '') {
                    arr.splice(i, 1);
                }
            }
            return arr;
        }


        var question = (($('#questionId').val()).split(','));
        var selectCount = (filterArr(question)).length;
        //console.log(question);
        $("#quiz-table tbody tr").click(function(){ 
            var qid=$(this).find("input[type='checkbox']").val();
            if($(this).find("input[type='checkbox']").is(":checked")){
                 $(this).find("input[type='checkbox']").prop("checked",!$(this).find("input[type='checkbox']").prop("checked"));
                 selectCount=selectCount-1;
                 question=removeArr(question,qid);
            }else{  
                 if(selectCount<testCount){
                     $(this).find("input[type='checkbox']").prop("checked",!$(this).find("input[type='checkbox']").prop("checked"));
                     selectCount=selectCount+1;
                     question.push(qid);
                 }
            }
            if(selectCount>=testCount){
                $("input[type='checkbox']").attr("disabled",true);
            }else{
                 $("input[type='checkbox']").attr("disabled",false);
            }
            $('#questionId').val(question.join());
        })

    }

});