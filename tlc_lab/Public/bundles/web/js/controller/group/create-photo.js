define(function(require, exports, module) {

    exports.run = function() {





        $("#file").on("change", function() {
          
        
            if ((navigator.userAgent.indexOf('MSIE') >= 0) && (navigator.userAgent.substr(30, 2) < 10)) {

                var a = document.getElementById("file");
                a.select();
                a.blur();
                var file = document.selection.createRange().text;

                var fileExt = file.substring(file.lastIndexOf("."));

                if (!/.(jpe?g|gif|png|bmp)/.test(fileExt)) {
                    $(".help-block").html("<font color='red'>请重新选择照片格式</font>")
                    return false;
                } else {

                    var filename = file.substr(file.lastIndexOf('\\') + 1);
                    $("#filehidden").val(filename)
                    $(".help-block").html()
                }

                var result = document.getElementById("filecontent");

                //显示文件  


                result.innerHTML = '<div class="img divimg" style="filter:progid:DXImageTransform.Microsoft.AlphaImageLoader(sizingMethod=scale,src=\'' + file + '\'"></div>';


            } else {

                var file = document.getElementById("file").files[0];
                if (!/image\/\w+/.test(file.type)) {
                    $(".help-block").html("<font color='red'>请重新选择照片格式</font>")
                    return false;
                } else {
                    $("#filehidden").val(file.name)
                    $(".help-block").html()
                }
                var reader = new FileReader();
                //将文件以Data URL形式读入页面  
                reader.readAsDataURL(file);
                reader.onload = function(e) {
                    var result = document.getElementById("filecontent");

                    //显示文件  
                    result.innerHTML = '<img src="' + this.result + '" class="divimg" alt=""  />';
                }
            }



        })

        var x = 1;
        $("#save-confirm").click(function() {
            var title = $("#title").val();
            if (title == "") {
                $(".help-block").html("<font color='red'>请输入标题</font>")
                return false;
            }

            var photo = $("#filehidden").val();
            if (photo == "") {
                $(".help-block").html("<font color='red'>请选择照片</font>");
                return false;
            }
            var file = document.getElementById("file").files[0];
            var filesize = document.getElementById("file").files[0].size;
            if (filesize > 5242880) {
                $(".help-block").html("<font color='red'>文件不能超过5兆</font>")
                return false;
            }
            if (!/image\/\w+/.test(file.type)) {
                $(".help-block").html("<font color='red'>请重新选择照片格式</font>")
                return false;
            } else {
                $(".help-block").html("")
            }
            var reader = new FileReader();
            //将文件以Data URL形式读入页面  
            reader.readAsDataURL(file);
            reader.onload = function(e) {
                var result = document.getElementById("filecontent");
                //显示文件  
                result.innerHTML = '<img src="' + this.result + '" class="divimg" alt=""  />';
            }
            if (x != 1) {
                $(".help-block").html("<font color='red'>点击次数过多</font>")
                return false;
            }
            x = 2;

        })



    };

});

