define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    var NProgress = require('nprogress');
    var history = require('jquery.history');

    exports.run = function() {

        var remove_words_url = $("input[name='word_remove_url']").val();
        var list_words_url   = $("input[name='word_list_url']").val();
        var ids_input        = $("input[name='word_ids']");

        $("#word_list_div").on("click",".sensitive-word-item",function(){
            $(this).toggleClass('active');
            writeWordIds();
        });

        $(".all-word-select-btn").on('click',function(){
            if($(".sensitive-word-item.active").length >= $(".sensitive-word-item").length){
                $(".sensitive-word-item").removeClass('active');
                $(".all-word-select-btn").text("全选");
            }else{
                $(".sensitive-word-item").addClass('active');
                $(".all-word-select-btn").text("全不选");
            }
            writeWordIds();
        });

        //删除一堆词
        $(".word-select-remove-btn").on('click',function(){
            if($(".sensitive-word-item.active").length <= 0 || ids_input.val() == ""){
                Notify.danger("请选择敏感词后再进行操作！",2);
                return;
            }

           if(confirm("确定要删除这些敏感词吗？")){
               $.post(remove_words_url,{word_ids:ids_input.val()},function(result){
                   result = $.parseJSON(result);
                   if(result.status){
                       Notify.success(result.message,3);
                       $(".sensitive-word-item.active").remove();
                       writeWordIds();
                       if($(".sensitive-word-item").length <= 0){
                           $("#word_list_div").html("<div class='no-tip text-center'>暂无敏感词汇</div>");
                       }
                   }else{
                       Notify.danger(result.message,3);
                   }
               }).error(function(){
                   Notify.danger("发生错误！操作失败！",3);
               });
           }
        });

        //删除单个词
        $("#word_list_div").on("click",".sensitive-word-item .delete_link",function(event){
            event.stopPropagation();
            if(confirm("确定删除这个敏感词吗？")){
                var item = $(this).parents('.sensitive-word-item');
                var wordId = item.data('id');
                $.post(remove_words_url,{id:wordId},function(result){
                    result = $.parseJSON(result);
                    if(result.status){
                        Notify.success(result.message,3);
                        item.remove();
                        writeWordIds();
                        if($(".sensitive-word-item").length <= 0){
                            $("#word_list_div").html("<div class='no-tip text-center'>暂无敏感词汇</div>");
                        }
                    }else{
                        Notify.danger(result.message,3);
                    }
                }).error(function(){
                    Notify.danger("发生错误！操作失败！",3);
                });
            }
        });

        //添加一群词
        $("#words_add_btn").on('click',function(){
            var url = $(this).data('goto');
            var words = $("#words").val();
            if($.trim(words) == ""){
                alert("请输入敏感词再添加！");
                return ;
            }

            $.post(url,{words:words},function(result){
                result = $.parseJSON(result);
                if(result.status){
                    Notify.success(result.message,3);
                    $("#words").val("");
                    reloadWordList();
                }else{
                    Notify.danger(result.message,3);
                }
            }).error(function(){
                Notify.danger("发生错误！操作失败！",3);
            });
        });

        var reloadWordList = function(){
            NProgress.start();
            $("#word_list_div").load(list_words_url + " .word_list_div_main", {}, function () {
                NProgress.done();
            });
        }

        var writeWordIds = function(){
            var s = "";
            var count = 0;
            $(".sensitive-word-item.active").each(function(){
                var sid = $(this).data('id');
                if(!isNaN(sid)){
                    s += sid;
                }

                count ++;
                if(count < $(".sensitive-word-item.active").length){
                    s += "|";
                }
            });
            ids_input.val(s);

            var select_count = $(".sensitive-word-item.active").length;
            $(".select-count").text(select_count);
            if(select_count > 0){
                $(".select-count-span").show();
            }else{
                $(".select-count-span").hide();
            }
        }

    };

});