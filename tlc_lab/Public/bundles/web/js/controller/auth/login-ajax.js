define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require("jquery.autocomplete");
    require("jquery.autocomplete-css");
    $.AutoComplete('#login_username');

    exports.run = function() {
        var $modal = $('#login-ajax-form').parents('.modal');
        var validator = new Validator({
            element: '#login-ajax-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                $form.find('.alert-danger').hide();

                if (error) {
                    return ;
                }

                $.post($form.attr('action'), $form.serialize(), function(response) {
	                if(response.success){
                        $modal.modal('hide');
                        Notify.success('登录成功');
                        updateUserHeader();
	                }else{
                            $form.find('.alert-danger').html(response.message).show();
                            validator.removeItem('[name="captcha_num"]');
                            if(parseInt(response.errNum) >= 2){
                                $("#captcha_code").show(); 
                                validator.addItem({
                                    element: '[name="captcha_num"]',
                                    required: true,
                                    rule: 'alphanumeric remote',
                                });
                            }else{
                                $("#captcha_code").hide();
                            }
	                }
                });

            }
        });

        validator.addItem({
            element: '[name="username"]',
            required: true
        });

        validator.addItem({
            element: '[name="password"]',
            required: true
        });
        
        $("#getcode_num").click(function(){
            $(this).attr("src",$("#getcode_num").data("url")+ "?" + Math.random());
        });

    };

    function updateUserHeader(){
        var url = $('#headerUser').data('url');
        $.ajax({
            url:url,
            success:function(data){
                $('.navbar-right').html(data)
            }
        })
    }

});