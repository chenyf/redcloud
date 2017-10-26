define(function(require, exports, module) {
    
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
   
    exports.run = function() {
        var validator = new Validator({
            element: '#create-form',
            autoSubmit: false,
                onFormValidated: function(error, results, $form) {
                if (error) {
                    return;
                } 
                $('#create-btn').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(data) {
                    if(data.status >= 1){
                        Notify.success(data.info);
//                        window.location.reload();
                          location.href = data.url;
                }else{
                        Notify.danger(data.info);
                }
                });
            }
        }); 
         validator.addItem({
            element: '[name="title"]',
//          设置为true意思是，不为空就可以通过
            required: true,
            rule: 'minlength{min:2} maxlength{max:20}'
//            规则的错误信息在公共的文件中已经进行了详细的罗列，所以可以省略
//            errormessageMinlength: '标题长度至少为2位',
//            errormessageMaxlength: '标题长度最大为20位'
        });

        validator.addItem({
            element: '[name="keywords"]',
            required: true,
            rule: 'minlength{min:5} maxlength{max:35}'
        });

//        $("#create-btn").click(function(){
//             var values = $("#description").val();
//             if(values.length > 500 ){
//                  Notify.danger('字数过多，重新进行编辑！最多500字');
//                 return false;
//             }
//        })


    $(document).ready(function(){
       var doc=document,inputs=doc.getElementsByTagName('input'),supportPlaceholder='placeholder'in doc.createElement('input'),placeholder=function(input){var text=input.getAttribute('placeholder'),defaultValue=input.defaultValue;
       if(defaultValue==''){
           input.value=text}
           input.onfocus=function(){
               if(input.value===text){this.value=''}};
               input.onblur=function(){if(input.value===''){this.value=text}}};
               if(!supportPlaceholder){
                   for(var i=0,len=inputs.length;i<len;i++){var input=inputs[i],text=input.getAttribute('placeholder');
                   if(input.type==='text'&&text){placeholder(input)}}}});

        $("#description").keyup(function(){
            var values = $("#description").val();
            var aa = values.length;
            var num = 500-aa;
            if(aa >= 0){
                 $("#description").val(values.substring(0,500));
                 $("#re").html("<div style='float:right;margin-right:110px'>还可以再输入<strong style='font-size:20px;color:red'>"+num+"</strong>个字符</div>");
            }   
        })

    };
});