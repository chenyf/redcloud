define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	var CategorySelect = require('category-select');
	exports.run = function () {
		CategorySelect.newCatSelect({
			selectInput : 'input[name=categoryId]',
			parentNode : '#select-cate'
		});

		$('#create-btn').click(function () {
			var cateTree = {};
			var courseCodeMap = {};
			$('div#sort-choose-ok').find('span').each(function (i) {
				var id = $(this).find('i').data('id');
				cateTree[id] = $(this).find('i').data('name');
				courseCodeMap[id] = $(this).find('i').data('coursecode');
			});
			if($.isEmptyObject(cateTree)){
				Notify.danger('请至少选择一个课程分类');
				return false;
			}
			$('div.course-relevance-specialty').find('ul').empty();
			$.each(cateTree,function(id,value){
				$('div.course-relevance-specialty').find('ul').append('<li id="course-category-choose" data-cateCode="' + courseCodeMap[id] + '" data-id='+id+'>'+value+'</li>');
				// $('div.course-relevance-specialty').next('.tip-text-0').text("课程编号以 " + courseCodeMap[id] + " 开始");
			});
			$('input[name=categoryIds]').val($('input[name=categoryId]').val());
			$('.modal').modal('hide');
		})
	}

});