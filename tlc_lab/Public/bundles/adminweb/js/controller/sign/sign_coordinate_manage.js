define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    var Notify = require('common/bootstrap-notify');

    exports.run = function(){

        $(document).on('click','#remove-coordinate-btn',function(){
            $this = $(this);
            $this.button('submiting').addClass('disabled');
            $.post($this.data('url'),{'cid' : $this.data('id')},function(response){

                response = $.parseJSON(response);

                if(response.status){
                    Notify.success(response.message);
                    $modal.modal('hide');
                    $this.parents('tr.coordinate-tr').remove();

                    if($('tr.coordinate-tr').length < 3){
                        window.location.reload();
                    }
                }else{
                    Notify.danger(response.message);
                }
                $this.button('reset').removeClass('disabled');
            }).error(function(){
                Notify.danger("发生错误，稍后重试！",5);
                $this.button('reset').removeClass('disabled');
            });
        });

    }

});