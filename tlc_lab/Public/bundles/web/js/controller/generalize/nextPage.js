define(function(require, exports, module) {
    require("jquery.jquery18");
    exports.run = function() {

        var getMyUsers=function(id,url){
            $.ajax({
                    type: "GET",
                    url: url,
                    success: function(msg) {
                      $('#nextList'+id).html(msg);
                    }
                });
        }
      
        $('.nextPageNew  .ajaxLoad').on("click", function() {
            var id = $(this).data("id");
            var url = $(this).data("url");
            getMyUsers(id,url);
        })





    };




});

