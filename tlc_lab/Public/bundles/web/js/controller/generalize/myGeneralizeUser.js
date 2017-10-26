define(function(require, exports, module) {
    exports.run = function() {
        var n = 1;

        var getMyUser=function(id,url){
            $.ajax({
                    type: "GET",
                    url: url,
                    success: function(msg) {
                      $('#nextList'+id).html(msg);
                    }
                });
        }
        $(".showMyUser").on("click", function() {
            var id = $(this).data("id");
            var url = $(this).data("url");
            $(this).parent().after("<tr class='nextList' id='nextList"+id+"'></tr>");
            if (n == 1) {
                getMyUser(id,url);
                n = n + 1;
            } else {
                $('.nextList').remove();
                n = 1;
            }
        })
      

    };




});

