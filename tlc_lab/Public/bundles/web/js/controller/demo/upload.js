define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var QCloudCos = require("jquery.qcloudCos");
    exports.run = function() {
        var cos = new QCloudCos({
            'bucketName': "cloud", //参数
            'appid': '10011123', //参数
        }).render();
        QCloudCos.init();



        $("#uploadFile").click(function() {
            $("#result").val('');
            var selectFunc = function() {
                var files = document.getElementById("file").files;
                if (files && files.length == 1) {
                    cos.uploadFile(successCallBack, errorCallBack, bucketName, "/tel.txt", files[0]);
                }
                else {
                    alert("请选择一个文件");
                }
            };

            if (/msie/.test(navigator.userAgent.toLowerCase())) {
                $('#file').click(function(event) {
                    setTimeout(function() {
                        if ($('#file').val().length > 0) {
                            selectFunc();
                        }
                    }, 0);
                });
            }
            else {
                $('#file').change(selectFunc);
            }
            $("#file").trigger("click");
        });














    }





});