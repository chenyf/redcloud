define(function(require, exports, module) {
    require("jquery.bootstrap-datetimepicker");
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    var now = new Date();
    exports.run = function() {
        
        $.ajax({
            type:'post',
            url:$('#get-groupemaillist').attr('buildCheckUrl'),
            success:function(data){
                $('#get-groupemaillist').html(data);
                
                //搜索
                $('#groupemail-pagelist').on('click','#groupemail-search',function(e){
                    e.preventDefault();
                    var keyword   = $('#keyword').val();
                    var startTime = $("input[name='startTime']").val();
                    var endTime   = $("input[name='endTime']").val();
                    var $modal   = $(e.delegateTarget);
                    $.ajax({
                        type:'POST',
                        url:$('#get-groupemaillist').attr('buildCheckUrl')+"?keyword="+keyword+"&startTime="+startTime+"&endTime="+endTime,   
                        success:function(response){
                            $modal.html(response);
           
                        }
                    });
                })
                
                //分页
                $('#groupemail-pagelist').on('click','.pagination a',function(e){
                    //alert('ss');
                    e.preventDefault();
                    var $modal = $(e.delegateTarget);
                    $.ajax({
                        type:'POST',
                        url:$(this).attr('href'),
                        success:function(response){
                            $modal.html(response);                                          
                        }
                
                     });
                 });           
             }
        }); 
        
        function autoFlush(){
            $('#get-groupemaillist .pagination .active a').trigger('click');
            //$('#get-groupemaillist .pagination .active a').attr('href');
        }
        //自动刷新页面
        var Seconds = 5000;
        var flushTag;
        $('#autoflush').on('click',function(){
            if($(this).prop('checked')){
                //执行刷新
               flushTag = setInterval(autoFlush,Seconds);
            }else{
               //清除自动刷新
               clearInterval(flushTag);
            }
        });
  
    };
});