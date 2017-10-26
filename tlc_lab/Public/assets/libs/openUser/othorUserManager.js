/*
 * 高校云互联弹窗
 * @author 褚兆前
 */
define(function(require,exports,module){
    
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator); 
    var Notify = require('common/bootstrap-notify');
    
    var $form = $("#othorOpenUserForm");
    
    $('#othorOpenUserBtn').click(function(event){
        var $id = '#'+ $(this).attr('id');
        if( $($id).hasClass('disabled')){return false;}
        $($id).button('submiting').addClass('disabled'); 
        
        event.preventDefault();
        
        $.ajax({
            url         :   $form.attr('action'),
            data        :   $form.serialize(),
            type        :   'POST',
            dataType    :   'json',
            timeout     :   20000,
            success    :   function(data){
                if(!data.code){
                    Notify.danger('失败');
                    $($id).button('submiting').removeClass('disabled').text('提交');
                }
                if(data.code==1000){
                    Notify.success(data.messge);
                    $form.parents('.modal').modal('hide');
                    window.location.reload();
                }else{
                    Notify.danger(data.messge);
                    $($id).button('submiting').removeClass('disabled').text('提交');
                }
            },
            error      :   function(){
                Notify.danger('失败');
                $($id).button('submiting').removeClass('disabled').text('提交');
            }
        });
        
        return false;
    });
    
    
});