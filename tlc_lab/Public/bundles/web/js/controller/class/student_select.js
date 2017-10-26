define(function (require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    var batProcess = require('web/batProcess/batProcess');
    var ajaxPager = require('ajax-pager');
    require('webuploader');
    require('jquery.form');
    window.chk_value = [];
    var param = $('#data-param');
    window.classid = param.data("classid");

    exports.run = function () {
        var file_url = $("#upload_file_url").val();
        $('#upload_file').on('change', function() {
            if ($(this).val() == '')
                return false;
            var options = {
                dataType: 'json',
                beforeSend: function() {
                },
                uploadProgress: function(event, position, total, percentComplete) {
                },
                success: function(dataObj) {
                    if (dataObj.status) {
                        Notify.success(dataObj.message, 1);
                        $('#file_name').val(dataObj.filePath);
                        $("#thelist").html(dataObj.fileName + '<a class="mlm" href="javascript:void(0);">删除</a>');

                    } else {
                        Notify.danger(dataObj.message, 1);
                        $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                        $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                        $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
                    }
                },
                error: function(xhr) {
                    Notify.danger('文件上传失败!', 2);
                    $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                    $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                    $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
                }
            };
            // 将options传给ajaxSubmit
            $('#file_form').ajaxSubmit(options);
        });

        $("#thelist").on('click', 'a', function() {

            if (!confirm("确定删除此文件么？"))
            {
                return false;
            }
            $("#thelist").html('');
            $("#file_name").val('');
        });

        $("#accounts_join_btn,#select_join_btn").click(function() {

            var id = $(this).attr('id');

            if(id == 'accounts_join_btn') { //填写学生学号
                var accounts = $("#accounts").val();
                if (accounts == '') {
                    $("#accounts").focus();
                    Notify.danger('请输入学生学号', 2);
                    return false;
                }

                if (accounts.indexOf("；") >= 0) {
                    Notify.danger('您输入的学号里面包含中文分号，请您仔细检查', 2);
                    return false;
                }
                var $btn = $("#accounts_join_btn");

            }else{  //选择学生
                var accounts = $("input[name='select_student_ids']").val();
                if (accounts == '') {
                    Notify.danger('请选择学生', 2);
                    return false;
                }
                var $btn = $("#accounts_join_btn");
            }

            $("#select_join_btn").attr("disabled", true).html("学生导入中...");
            $("#accounts_join_btn").attr("disabled", true).html("学生导入中...");
            $("#excel_join_btn").attr("disabled", true).html("学生导入中...");
            var classid = $('#data-param').data("classid");
            $.post($btn.data('goto'), {id: classid, accounts: accounts}, function(dataObj) {
                if (dataObj.status) {
                    Notify.success(dataObj.message, 1);
                    batProcess.itemPollList(dataObj.data);
                } else {
                    Notify.danger(dataObj.message, 1);
                }
                $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
            }, 'json').error(function() {
                Notify.danger('提交失败!', 2);
                $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
            });

        });


        $("#excel_join_btn").click(function() {

            var file_name = $("#file_name").val();

            if (file_name == '') {
                Notify.danger('请上传excel文件', 2);
                return false;
            }

            var $btn = $("#excel_join_btn");
            $("#select_join_btn").attr("disabled", true).html("学生导入中...");
            $("#accounts_join_btn").attr("disabled", true).html("学生导入中...");
            $("#excel_join_btn").attr("disabled", true).html("学生导入中...");
            var classid = $('#data-param').data("classid");
            $.post($btn.data('goto'), {id: classid, file_name: file_name}, function(dataObj) {
                if (dataObj.status) {
                    Notify.success(dataObj.message, 1);
                    batProcess.itemPollList(dataObj.data);
                } else {
                    Notify.danger(dataObj.message, 1);
                }
                $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
            }, 'json').error(function() {

                Notify.danger('提交失败!', 2);
                $("#select_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#accounts_join_btn").attr("disabled", false).html("添加学生到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学生到本班");
            });

        });

        $(".poll-member").click(function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            batProcess.itemPollList(param);
        });

        //查看上一次的批量任务
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

        //点击选择学生
        var ids_input = $("input[name='select_student_ids']");

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

        var writeStudentIds = function(){
            var s = "";
            var count = 0;
            $(".user-select-item.active").each(function(){
                var sid = $(this).data('num');
                s += sid;

                count++;
                if(count < $(".user-select-item.active").length){
                    s += ";";
                }
            });
            ids_input.val(s);

            var select_count = $(".user-select-item.active").length;
            $(".select-count").text(select_count);

            modalFill();
        };

        var modalFill = function(){
            var modalObj = $('#selected_list_modal').find('.modal-body');
            modalObj.html("");

            var idList = ids_input.val().trim().split(";");

            if(idList.length <= 0){
                modalObj.html("未选择学生");
                $('#selected_list_modal').find("#selected_student_count").text("0");
            }else{
                modalObj.html("");
                $('#selected_list_modal').find("#selected_student_count").text(idList.length);
                idList.forEach(function(id){
                    modalObj.append("<span style='margin: 5px;'>" + id + "</span>");
                });
            }
        };

        $('#college_select').on('change',function(){
            var collegeId = $(this).find("option:selected").data('id');
            var url = $(this).data('url');
            $.post(url, {collegeId:collegeId},function(resultObj){
                if(resultObj != undefined && $.isArray(resultObj)){
                    $('#major_select').html("<option data-id='0' value=''>全部专业</option>");
                    resultObj.forEach(function(major){
                        $('#major_select').append("<option data-id='"+major.major_id+"' value='"+major.major_name+"'>"+major.major_name+"</option>");
                    });
                }else{
                    $('#major_select').html("<option data-id='0' value=''>全部专业</option>");
                }
            },'json').error(function(){
                console.log('发生错误...');
            });
        });

        var pager = new ajaxPager({
            el:"#student_list_page_container",
            container:".select_student_main",
            totalPage:parseInt($("#student_list_page_container").data('totalpage')),
            total:parseInt($("#student_list_page_container").data('total')),
            url:$("#student_list_page_container").data('url'),
            otherData:function(){
                return {
                    college:$("#college_select").val(),
                    major:$("#major_select").val(),
                    key_word:$("#key_word").val()
                };
            }
        });

        pager.onClick(function(result){
            if(result.total != pager.el.data('total') || result.totalPage != pager.el.data('totalpage')){
                pager.setAttr({totalPage:result.totalPage,total:result.total});
            }

            pager.el.data('total',result.total);
            pager.el.data('totalpage',result.totalPage);

            var container = $("#selected_student_list ul.student_list_ul");
            container.html("");

            if(result.list.length <= 0){
                container.html("暂无结果");
            }else{
                var idList = ids_input.val().trim().split(";");
                result.list.forEach(function(s){
                    var li = $("<li class='f-clearfix userinfo user-select-item' data-num='"+s.userNum+"' data-sid='"+s.id+"'>");
                    if($.inArray(s.userNum,idList) >= 0){
                        li.addClass('active');
                    }

                    li.append("<span class='c-icon-checked' aria-hidden='true'></span>");
                    li.append("<a class='ava' href='javascript:;' target='_blank' title='"+s.nickname+"' data-placement='top'  data-toggle='tooltip'> \
                            <img loaderrimg='1' onerror=\"javascript:this.src='/Public/assets/img/default/pic-error.png?5.1.4';\" src='"+s.avatar+"' > \
                            </a>");

                    li.append("<p class='nickname'>"+s.nickname+"</p>");
                    li.append("<p class='number'>"+s.userNum+"</p>");

                    li.appendTo(container);
                });
            }

        });
        
        $("#search_student_btn").on('click',function(){
            pager.fetchData(1);
        });
    }

});