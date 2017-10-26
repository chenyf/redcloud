define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);

    var Notify = require('common/bootstrap-notify');

    exports.run = function(){

        var $modal = $('#coordinate-form').parents('.modal');

        var validator = new Validator({
            element: '#coordinate-form',
            failSilently : true,
            triggerType : 'change',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#save-coordinate-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(response) {
                    response = $.parseJSON(response);

                    if(response.status){
                        Notify.success(response.message);
                        $modal.modal('hide');
                        window.location.reload();
                    }else{
                        Notify.danger(response.message);
                    }

                    $('#save-coordinate-btn').button('reset').removeClass('disabled');

                }).error(function(){
                    Notify.danger('操作失败!');
                });
            }

        });

        validator.addItem({
            element: '[name="name"]',
            required: true,
            rule: 'minlength{min:1} maxlength{max:30}',
            errormessageRequired : '请输入坐标地点名称'
        });

        validator.addItem({
            element: '[name="longitude"]',
            required: false,
            rule: 'number',
            errormessage:'经度必须是大于0的小数'
        });

        validator.addItem({
            element: '[name="latitude"]',
            required: true,
            rule: 'number',
            errormessage:'纬度必须是大于0的小数'
        });

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