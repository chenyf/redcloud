define(function(require, exports, module) {
    exports.run = function() {
        //        判断全局的app是否为0，如果为0，则说明美欧进行登录，所以就不会触发ajax 
        $('.download').click(function() {
            var uid = parseInt(app.uid);
            var classid = $(this).data('classid');
            var id = $(this).data('id');
            var fileurl = $(this).data('fileurl');
            var url2 = $(this).data('url2');
            if (!uid == 0) {
                $.ajax({
                    url: fileurl,
                    data: {'Rid': classid, 'fileid': id},
                    success: function(msg) {
                        var num = msg['0'];
                        var url = msg['1'];
                        window.open(url, "_self");
                        $('#file_' + id).find('td').eq(4).html(num);
                    }
                })
            } else {
//   getScheme()     用于获取协议名。http 或者 https 
                location.href = url2;
            }
        })

    }

});