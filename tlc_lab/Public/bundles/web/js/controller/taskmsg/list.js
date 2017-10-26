define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
   require("jquery.bootstrap-datetimepicker");
    var autoSubmitCondition=require("./autoSubmitCondition.js");
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    var now = new Date();
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
        
          $("[name=endTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 'month'
        });
        $('[name=endTime]').datetimepicker('setEndDate', now);
        $('[name=endTime]').datetimepicker('setStartDate', $('#registerStartDate').attr("value"));
        $("[name=startTime]").datetimepicker({
            language: 'zh-CN',
            autoclose: true,
            format: 'yyyy-mm-dd',
            minView: 'month'
        });
        $('[name=startTime]').datetimepicker('setEndDate', now);
        $('[name=startTime]').datetimepicker('setStartDate', $('#registerStartDate').attr("value"));
       
         var validator = new Validator({          
            element: '#daily-search-form'});

//        validator.addItem({
//            element: '[name=startTime]',
//            required: true,
//            rule:'date_check'
//        });

//        validator.addItem({
//            element: '[name=endTime]',
//            required: true,
//            rule:'date_check'
//        });
        validator.addItem({
            element: '[name=analysisDateType]',
            required: true
        });
        autoSubmitCondition.autoSubmitCondition();  
    };
});