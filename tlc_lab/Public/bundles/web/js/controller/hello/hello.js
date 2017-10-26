define(function(require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    exports.run = function() {

        // Notify.warning("哈哈哈哈",100);
        var arr = [];
        arr.push(11);
        arr.push(345);
        arr.push(79);
        $.ajax({
            method:'POST',
            url:"",
            data:{
                arr:arr
            },
            success:function(result){
                result = JSON.parse(result);
                if(result.status){

                }else{
                    Notify.danger(result.message,5);
                }

            },
            error:function(){
                Notify.danger("发生错误！",5);
            }
        });
    };

});