define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    var batProcess = require('web/batProcess/batProcess');
    require('webuploader');
    require('jquery.form');
    window.chk_value = [];
    var param = $('#data-param');
    window.classid = param.data("classid");

    var taskdata = $("#taskdata");
    var issetintval = taskdata.data("issetintval");
    if (parseInt(issetintval) == 1) {
        btntask = setInterval(function() {
            $.get(taskdata.data("taskstatusurl"), {}, function(dataObj) {
                if (dataObj.code == 1000 && parseInt(dataObj.status) > 0) {
                    $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                    $("#accounts_join_btn").next().remove();
                    $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
                    $("#excel_join_btn").next().remove();
                    clearInterval(btntask);
                    Notify.success("导入班级成员全部成功！");
                }
            }, 'json');

        }, 5000);
    }

    exports.run = function() {
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
                        $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                        $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
                    }
                },
                error: function(xhr) {
                    Notify.danger('文件上传失败!', 2);
                    $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                    $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
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

        $("#accounts_join_btn").click(function() {

            var accounts = $("#accounts").val();

            if (accounts == '') {
                $("#accounts").focus();
                Notify.danger('请输入账号', 2);
                return false;
            }

            if (accounts.indexOf("；") >= 0) {
                Notify.danger('您输入的账号里面包含中文分号，请您仔细检查', 2);
                return false;
            }

            var $btn = $("#accounts_join_btn");
            $("#accounts_join_btn").attr("disabled", true).html("学员导入中...");
            $("#excel_join_btn").attr("disabled", true).html("学员导入中...");
            var classid = $('#data-param').data("classid");
            $.post($btn.data('goto'), {id: classid, accounts: accounts}, function(dataObj) {
                if (dataObj.status) {
                    Notify.success(dataObj.message, 1);
                    batProcess.itemPollList(dataObj.data);
                } else {
                    Notify.danger(dataObj.message, 1);
                }
                $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
            }, 'json').error(function() {
                Notify.danger('提交失败!', 2);
                $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
            });

        });


        $("#excel_join_btn").click(function() {

            var file_name = $("#file_name").val();

            if (file_name == '') {
                Notify.danger('请上传excel文件', 2);
                return false;
            }

            var $btn = $("#excel_join_btn");
            $("#accounts_join_btn").attr("disabled", true).html("学员导入中...");
            $("#excel_join_btn").attr("disabled", true).html("学员导入中...");
            var classid = $('#data-param').data("classid");
            $.post($btn.data('goto'), {id: classid, file_name: file_name}, function(dataObj) {
                if (dataObj.status) {
                    Notify.success(dataObj.message, 1);
                    batProcess.itemPollList(dataObj.data);
                } else {
                    Notify.danger(dataObj.message, 1);
                }
                $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
            }, 'json').error(function() {

                Notify.danger('提交失败!', 2);
                $("#accounts_join_btn").attr("disabled", false).html("添加学员到本班");
                $("#excel_join_btn").attr("disabled", false).html("导入学员到本班");
            });

        });
        
        $(".poll-member").click(function(){
            var code = $(this).data("code");
            var strId = $(this).data("strid");
            var microtime = $(this).data("microtime");
            var param = {code:code,strId:strId,microtime:microtime};
            batProcess.itemPollList(param);
        });

    };

});