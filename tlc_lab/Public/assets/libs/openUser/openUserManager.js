/*
 * 高校云互联弹窗
 * @author 褚兆前
 */
define(function(require,exports,module){
    
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    
    //exports.run = function(){
        function createValidator ($form) {
        validator = new Validator({
            element: $form,
            failSilently: true,
            autoSubmit: false,
            triggerType : 'blur',
            onFormValidated: function(error, results, $form) {
                if (error) { return; }
                
                if( $('#openUserBtn').hasClass('disabled')){return false;}
                $('#openUserBtn').button('submiting').addClass('disabled');
                
                $.ajax({
                    url:$form.attr('action'),
                    data:$form.serialize(), 
                    timeout:20000,
                    type:'POST',
                    dataType:'json',
                    success :function(data){
                        if(!data.code){
                            Notify.danger('失败');
                            $('#openUserBtn').button('submiting').removeClass('disabled').text('提交');
                        }
                        if(data.code==1000){
                            Notify.success(data.messge);
                            $form.parents('.modal').modal('hide');
                            window.location.reload();
                        }else{
                            Notify.danger(data.messge);
                            $('#openUserBtn').button('submiting').removeClass('disabled').text('提交');
                        }
                    },
                    error:function(){
                        Notify.danger('失败');
                        $('#openUserBtn').button('submiting').removeClass('disabled').text('提交');
                    }
                });
                return false;
            }
        });
        
        validator.addItem({
            element: '#name-field',
            required: true,
            rule: 'requireLenSwitch minlength{min:1} maxlength{max:40}',
            display : '名称',
            errormessageRequired : '请填写正确的名称'
        });
        validator.addItem({
            element: '#des-field',
            required: true,
            rule: 'requireLenSwitch minlength{min:1} maxlength{max:80}',
            display : '简介',
            errormessageRequired : '请填写正确的简介'
        });
        validator.addItem({
            element: '#provider-field',
            required: true,
            rule: 'requireLenSwitch minlength{min:1} maxlength{max:40}',
            display : '提供方',
            errormessageRequired : '请填写正确的提供方'
        });
        validator.addItem({
            element: '#url-field',
            required: true,
            rule: 'requireLenSwitch minlength{min:1} maxlength{max:255} url',
            display : '网站地址',
            errormessageRequired : '请填写正确的网站地址'
        });
        validator.addItem({
            element: '#validate-field',
            required: true,
            display : '检测失败',
            errormessageRequired : '检测失败'
        });
        validator.addItem({
            element: '#backUrl-field',
            required: true,
            rule: 'requireLenSwitch minlength{min:1} maxlength{max:255} url',
            display : '回调地址',
            errormessageRequired : '请填写正确的回调地址'
        });
        return validator;
    };
        var $form = $("#openUserForm");
        var validator = createValidator($form);
        
        var requireLenSwitch = function(option){
            var thisVal = option.element.val().replace(/(^\s*)|(\s*$)/g, "");
            return thisVal;			
        };
	Validator.addRule('requireLenSwitch', requireLenSwitch, '请输入正确名称');
        
        $form.on('keydown', '[name=url]', function(e) {
            $('.validateFieldGroup').removeClass('has-error').end().find('.help-block').hide();
            $('#validate-field').val('');
        });
        
        $('.validateBut').click(function(){
            var url = $('#url-field');
            var urlVal = url.val();
            var id = $('#id-field').val();
            var hasError = url.closest('.form-group').hasClass('has-error');
            
            if(hasError){
                Notify.danger('请填写正确网站地址');
                return false;
            }
            
            if (!urlVal){
                Notify.danger('请填写正确网站地址');
                return false;
            }
            
            
            var $id = '#'+ $(this).attr('id');
            
            if( $($id).hasClass('disabled')){return false;}
            $($id).button('submiting').addClass('disabled'); 
            
            $.ajax({
                    url         :   $form.attr('checkAddUrlMeta'),
                    data       :   {url:urlVal,id:id},
                    type        :   'POST',
                    dataType    :   'json',
                    timeout     :   20000,
                    success    :   function(data){
                        if(data.code==1000){
                            Notify.success(data.messge);
                            $('.validateFieldGroup').removeClass('has-error').end().find('.help-block').hide();
                            $('#validate-field').val(1);
                            $($id).removeClass('disabled');
                        }else{
                            Notify.danger(data.messge);
                            $('.validateFieldGroup').addClass('has-error').end().find('.help-block').show();
                            $('#validate-field').val('');
                            $($id).removeClass('disabled');
                        }
                    },
                    error       :   function(){
                        Notify.danger('失败');
                        $('.validateFieldGroup').addClass('has-error').end().find('.help-block').show();
                        $('#validate-field').val('');
                        $($id).removeClass('disabled');
                    }
                });
        });
    //};
});