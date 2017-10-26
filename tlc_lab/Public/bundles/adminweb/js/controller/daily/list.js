define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    exports.run = function() {
        var $form = $("#daily-search-form");
        $(".searchNotice").on("click",function(){
            $form.submit();
        });
         options={
        delay: { show: 500, hide: 100 },
        trigger:'hover',
        html:true
        };
        $("[data-toggle='tooltip']").tooltip(options); 
       

         
    };
});