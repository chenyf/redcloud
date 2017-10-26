define(function (require, exports, module) {

	var searchGuide = ".s-search-guide";
	var searchInput = "input[name=keyword]";
	var searchForm = "#search-form";
	var searchGuideSize = $(searchGuide).find('li').size();
	var selectYear = $("select[name=year]");

	selectYear.on('change', function () {
		$(searchInput).val('');
		$(searchForm).submit();
	});

	//搜索框事件监听
	(function (searchGuide, searchInput) {
		$(searchInput).keyup(function (e) {
			inputHandle(this, e);
		});
		$(searchInput).keydown(function (e) {
			searchGuideShowOrClose(this);
		});
		$(searchInput).click(function (e) {
			event.stopPropagation()
		});
		$(searchInput).focus(function (e) {
			inputHandle(this, e);
		});
		$('html').click(function () {
			$(searchGuide).hide();
		});
		$(searchGuide).find('li').click(function (e) {
			$('input[name=keywordType]').val($(this).data('type'));
			selectYear.val("");
			$(searchForm).submit();
		});
	})(searchGuide, searchInput);

	/**
	 * 输入按键控制
	 * @param ele
	 * @param e
	 */
	function inputHandle(ele, e) {
		if ( e.which == 38 ) {
			loopClass(-1);//↑按键
		} else if ( e.which == 40 ) {
			loopClass(1);//↓按键
		} else if ( e.which == 13) {
			//回车键
			var type = $(searchGuide).find('.active').data('type')
			$('input[name=keywordType]').val(type);
			selectYear.val("");
			$(searchForm).submit();
		}else{
			//正常输入
			searchGuideShowOrClose(ele);
		}
	}

	/**
	 * 联想词框是否显示
	 * @param ele
	 */
	function searchGuideShowOrClose(ele) {
		var value = $.trim($(ele).val());
		value.length > 0 ? $(searchGuide).show() : $(searchGuide).hide();
		$(searchGuide).find('.search-guide-keyword').text(value);
	}

	function loopClass(num) {
		var cnum = $(searchGuide).find('.active').index();
		var newNum = cnum + num;
		newNum = newNum % searchGuideSize;
		$(searchGuide).find('.active').removeClass('active');
		$(searchGuide).find("li:eq(" + newNum + ")").addClass('active');
	}
});