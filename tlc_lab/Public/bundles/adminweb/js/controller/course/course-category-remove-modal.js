define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');

    exports.run = function(options) {

        $("#remove-category-btn").on('click',function(){
            var id = $(this).data('id');
            $.post($(this).data('url'),{cid:id},function(response){
                response = $.parseJSON(response);
                if(response.status){
                    Notify.success(response.message);
                    $('.modal').modal('hide');
                    $('#category-tr-' + id).remove();
                }else{
                    Notify.danger(response.message);
                }
            }).error(function(){
                Notify.danger('删除失败!');
            });
        });

    };

});
