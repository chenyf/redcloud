define(function(require, exports, module) {

    var AutoComplete = require('autocomplete');
    var DynamicCollection = require('../widget/dynamic-collection4');
    var Notify = require('common/bootstrap-notify');
    var func = require('function');
    require('jquery.sortable');

    exports.run = function() {

    	require('./header').run();
    	
        var dynamicCollection = new DynamicCollection({
            element: '#teachers-form-group',
            onlyAddItemWithModel: true,
            beforeDeleteItem: function(e){
            	var teacherCounts=$("#teacher-list-group").children("li").length;
	            if(teacherCounts <= 1){
	                Notify.danger("课程至少需要一个教师！");
	                return false;
	            }
	            return true;
            }
        });

        var autocomplete = new AutoComplete({
            trigger: '#teacher-input',
            dataSource: $("#teacher-input").data('url'),
            filter: {
                name: 'stringMatch',
                options: {
                    key: 'nickname'
                }
            },
        selectFirst: true
        }).render();

        autocomplete.on('itemSelect', function(data){
            var error = '';
            dynamicCollection.element.find('input[name="ids[]"]').each(function(i, item) {
                    if (parseInt(data.id) == parseInt($(item).val())) {
                            error = '该教师已添加，不能重复添加！';
                    }
            });

            if (error) {
                    Notify.danger(error);
                    dynamicCollection.clearInput();
            } else {
                    dynamicCollection.addItemWithModel(data);
            }
        });

        dynamicCollection.on('beforeAddItem', function(value) {
            autocomplete.set('inputValue', null);
            autocomplete.setInputValue(value);
        });

        func.checkPlaceHolder();

	$(".teacher-list-group").sortable({
            'distance':20
	});
        
        $("#item-add").on("click",function(){
            var value = $("#teacher-input").val();
            var url = $(this).data('url');
            var jsonerror = '';
            $.post(url,{name:value},function(jsondata){
                if( jsondata.status == 'ok'){
                   dynamicCollection.element.find('input[name="ids[]"]').each(function(i, item) {
                            if (parseInt(jsondata.data.id) == parseInt($(item).val())) {
                                jsonerror = '该教师已添加，不能重复添加！';
                            }      
                    });
                   if (jsonerror) {
	    		Notify.danger(jsonerror);
	    		dynamicCollection.clearInput();
                    } else {
                        dynamicCollection.addItemWithModel(jsondata.data);
                        $('.ui-autocomplete').addClass('hide');
                    } 
                }else{
                   Notify.danger('无相关数据，请确认老师准确姓名'); 
                }
            },"json");
	});
    };

});