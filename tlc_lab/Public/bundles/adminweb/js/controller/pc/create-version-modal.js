define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);

    exports.run = function() {
        var $form = $('#version-form');
        var $modal = $form.parents('.modal');
        var $table = $('#version-table');

        var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return ;
                }
                $('#version-save-btn').button('submiting').addClass('disabled');
                $.post($form.attr('action'), $form.serialize(), function(html){
                    $modal.modal('hide');
                    Notify.success('保存版本成功！');
                    window.location.reload();
                });

            }

        });

        validator.addItem({
            element: '[name="name"]',
            required: true
        });

        validator.addItem({
            element: '[name="url"]',
            required: true
            
        });

	    validator.addItem({
		    element: '[name="type"]',
		    required: true

	    });

    };
    
    $(function(){
        $("#selectSchool").on("blur",function(){
                var selectVal = $(this).val();
                var optionAll = $('#siteSelect1 option');
//                optionAll.css('color', '');
                optionAll.removeClass("hide");
                selectVal = selectVal.toLowerCase();
                if(!selectVal) return false;

                optionAll.each(function(){
                    var val = $(this).val();
                    var txt = $(this).text();
                    val && (val = val.toLowerCase());
                    txt && (txt = txt.toLowerCase());
                    if(txt.indexOf(selectVal)!=-1 || val.indexOf(selectVal)!=-1){
//                        $(this).css({'color': 'red'});
                        $(this).removeClass("hide");
                    }else{
                        $(this).addClass("hide");
                    }
                })
                return true;
        })
    })
    


});