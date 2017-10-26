define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function () {
        console.log("student manage...");
        var remove_student_url = $("#student_remove_url").data('url');
        var ids_input = $("input[name='student_ids']");

        $(document).on("click","ul.student_list_ul li.user-select-item",function(){
            $(this).toggleClass('active');
            writeStudentIds();
        });

        $(document).on('click',"#select_all_btn",function(){
            if($(".user-select-item.active").length >= $(".user-select-item").length){
                $(".user-select-item").removeClass('active');
                $("#select_all_btn").text("全选");
            }else{
                $(".user-select-item").addClass('active');
                $("#select_all_btn").text("全不选");
            }
            writeStudentIds();
        });

        $(document).on('click',"#remove_student_confirm_btn",function(){
            if($(".user-select-item.active").length <= 0 || ids_input.val() == ""){
                Notify.danger("请选择学生后再进行操作！",2);
                return;
            }

            $("#confirm_modal").modal('show');
        });

        $(document).on('click',"#remove_student_btn",function(){
            if($(".user-select-item.active").length <= 0 || ids_input.val() == ""){
                Notify.danger("请选择学生后再进行操作！",2);
                return;
            }

            $("#confirm_modal").modal('hide');
            
            $.post(remove_student_url,{ids:ids_input.val()},function(result){
                result = $.parseJSON(result);
                if(result.status){
                    Notify.success(result.message,3);
                    $(".user-select-item.active").remove();
                    writeStudentIds();
                    if($(".user-select-item").length <= 0){
                        $(".student_list_ul").before("<div class='no-tip text-center'>暂未添加任何学生</div>");
                    }
                }else{
                    Notify.danger(result.message,3);
                }
            }).error(function(){
                Notify.danger("发生错误！操作失败！",3);
            });
        });

        var writeStudentIds = function(){
            var s = "";
            var count = 0;
            $(".user-select-item.active").each(function(){
                var sid = $(this).data('sid');
                if(!isNaN(sid)){
                    s += sid;
                }

                count++;
                if(count < $(".user-select-item.active").length){
                    s += "|";
                }
            });
            ids_input.val(s);

            var select_count = $(".user-select-item.active").length;
            $(".select-count").text(select_count);
            if(select_count > 0){
                $(".select-count-span").show();
            }else{
                $(".select-count-span").hide();
            }
        }

    }

});