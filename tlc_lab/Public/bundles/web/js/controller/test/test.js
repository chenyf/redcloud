define(function(require, exports, module) {
     var Notify = require('common/bootstrap-notify');
    exports.run = function() {
        
       $('#btn').click(function(){
           $('#modal').show();
           $('body').append('<div class="modal-backdrop  in"></div>');
       })
       $('button.close,button.btn.btn-link').click(function(){
           $('#modal').hide();
           $('.modal-backdrop').remove();
       })
       $("#create-course").click(function(){
           Notify.danger('请输入正确的手机号码',10);
       });
       
    };

});