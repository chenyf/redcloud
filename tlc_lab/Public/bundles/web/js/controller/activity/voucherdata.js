/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(function(require, exports, module){
     var Notify = require('common/bootstrap-notify');
       exports.run = function(){
           $("#conform").click(
                  function(){
               var code=$("#code").val();
               var url=$("#url").val();
        
               //var msg;
               $.ajax({
                   type:"get",
                   url:url,
                   async:true,
                   data:"code="+code,
                   success:function(tip){
                     $("#tip").text(tip);
                     
                   }
               });
               }
               );
       };
});

