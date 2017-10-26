define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');

    exports.run = function () {

        $(".student_choose_box").on('click',".user-select-item",function(){
            $(this).toggleClass('active');

            if($('#un_selected_student_list .choose_box_list').find(".user-select-item.active").length > 0){
                $("#select_student_btn").removeAttr("disabled");
            }else{
                $("#select_student_btn").attr("disabled","disabled");
            }

            if($('#selected_student_list .choose_box_list').find(".user-select-item.active").length > 0){
                $("#remove_student_btn").removeAttr("disabled");
            }else{
                $("#remove_student_btn").attr("disabled","disabled");
            }
        });

        $("#select_student_btn").on('click',function(){
            $('#un_selected_student_list .choose_box_list').find(".user-select-item.active").each(function(){
                $('#selected_student_list .choose_box_list').prepend($(this).clone());
                $(this).remove();
            });
            setStudentIds();
            $('#selected_student_list .choose_box_list').find(".user-select-item.active").removeClass('active');
            $("#select_student_btn").attr("disabled","disabled");
        });

        $("#remove_student_btn").on('click',function(){
            $('#selected_student_list .choose_box_list').find(".user-select-item.active").each(function(){
                $('#un_selected_student_list .choose_box_list').prepend($(this).clone());
                $(this).remove();
            });
            setStudentIds();
            $('#un_selected_student_list .choose_box_list').find(".user-select-item.active").removeClass('active');
            $("#remove_student_btn").attr("disabled","disabled");
        });

        var setStudentIds = function(){
            var s = "";
            $('#selected_student_list .choose_box_list').find(".user-select-item").each(function(){
                var sid = $(this).data('sid');
                if(!isNaN(sid)){
                    s += sid;
                }
                if($(this).index() + 1 < $('#selected_student_list .choose_box_list').find(".user-select-item").length){
                    s += "|";
                }
            });
            $("input[name='student_ids']").val(s);
        }

        //提交
        $("#student-choose-save-btn").on('click',function(){
            $("#student-choose-save-btn").button('submiting').addClass('disabled');
            $.post("",{student_ids:$("input[name='student_ids']").val()},function(result){
                if(result == "true"){
                    Notify.success("保存成功！");
                    document.getElementById('go_back_link').click();
                }else{
                    Notify.success("失败");
                    $("#student-choose-save-btn").removeClass('disabled');
                }
            });
        });

    }

});