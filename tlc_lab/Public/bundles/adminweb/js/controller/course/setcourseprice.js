define(function(require, exports, module) {
	
	exports.run = function() {
               
           
                $("#course-recommend-btn").bind("click",function(){
                   var  price = $("#price").html();
                   var  num = $("#number").val();
                   var reg = /^[0-9]{1,5}$/;
                    if(isNaN(num)==true){
                        $(".help-block").html("不能输入汉字字符！请输入整数！");
                        return false;  
                    }else if(num==""){
                        $(".help-block").text("不能为空");
                        return false;
                    }else if(!reg.test(num)){
                            $(".help-block").text("价格区间为0—99999的整数");
                            return false;
                         }else{
                         $(".help-block").html("");
                         return true;
                     }
                     
                    
                   
                    
                })
                
                
                $("#submit").bind("click",function(){
                  radio =   $('input:radio[name="status"]:checked').val()
                   if(radio==null){
                       $("#span").text("请选择一项")
                       return false;
                   }  else{
                       return true;
                   }
                })
                
               

	};

});

















