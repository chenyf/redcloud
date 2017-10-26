/*
 * 后台管理员设置用户金币
 * @author 褚兆前 2016-03-29
 */
define(function(require,exports,module){
    
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    
    function createValidator ($form, $choosers) {
        validator = new Validator({
            element: $form,
            failSilently: true,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) { return; }
                $('#user-gold-control-btn').button('submiting').addClass('disabled');
                if($('#user-gold-control-btn').data('lock') == 1) return false;
                $('#user-gold-control-btn').data('lock',1);
                var $panel = $('.reload-container');
                $.ajax({
                    url:$form.attr('action'),
                    data:$form.serialize(), 
                    timeout:20000,
                    type:'POST',
                    dataType:'json',
                    success :function(data){
                        if(!data.code){
                            Notify.danger('失败');
                            $('#user-gold-control-btn').data('lock',0);
                            $('#user-gold-control-btn').button('submiting').removeClass('disabled').text('提交');
                        }
                        if(data.code==1000){
                            Notify.success(data.message);
                            $form.parents('.modal').modal('hide');
                            $('#user-gold-control-btn').data('lock',0);
                        }else{
                            Notify.danger(data.message);
                            $('#user-gold-control-btn').data('lock',0);
                            $('#user-gold-control-btn').button('submiting').removeClass('disabled').text('提交');
                        }
                    },
                    error:function(){
                        Notify.danger('失败');
                        $('#user-gold-control-btn').data('lock',0);
                        $('#user-gold-control-btn').button('submiting').removeClass('disabled').text('提交');
                    }
                });
            }
        });
        
        validator.addItem({
            element: '#goldNum',
            required: true,
            rule: 'arithmetic_number integer maxNum',
            display : '金币',
            errormessageRequired : '请输入正确金币数'
        });
        return validator;
    };
    function switchValidator(validator, type) {
        
        validator.removeItem('#goldNum');
        var typeVal = $('[name=type]').val();
        
        if(typeVal == 'dec'){
            validator.addItem({
                element: '#goldNum',
                required: true,
                rule: 'arithmetic_number integer maxNum lessThan',
                display : '金币',
                errormessageRequired : '请输入正确金币数'
            });
        }else{
            validator.addItem({
                element: '#goldNum',
                required: true,
                rule: 'arithmetic_number integer maxNum',
                display : '金币',
                errormessageRequired : '请输入正确金币数'
            });
        }
    }
    exports.run = function(){
        var $form = $("#user-gole-conrol-form");
        var validator = createValidator($form);
        var userGoldNumVal = $("#userGoldNum").text();
        
        Validator.addRule('maxNum', /^[1-9][0-9]{0,3}$/, '金币介于0~10000');
        
        var lessThan = function(option){
             var value = option.element.val();
             var r = parseInt(value) > parseInt(userGoldNumVal);
             return !r;
         };
        Validator.addRule('lessThan', lessThan, '金币应小于'+userGoldNumVal);
        $form.on('change', '[name=type]', function(e) {
            var type = $(this).val();
            switchValidator(validator, type);
        });
    };
});

