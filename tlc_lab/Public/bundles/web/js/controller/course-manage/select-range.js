define(function (require, exports, module) {

	exports.run = function () {

		var payRangeRow = $('div#payRange-row');
		var priceRow = $('div#price-row');
		var openRange = $('input[name=openRange]').val();
		var payRange = $('input[name=payRange]').val();

		/**
		 * 课程开放范围选
		 */
		$(document).on('click','.select-openRange',function () {
			var openRange = selectRange($(this), $('input[name=openRange]'));
			payRangeOpenStrategy(openRange);
			if((openRange == 1 || openRange == 2) && $('input[name=payRange]').val() ==2){
				var payRange = selectRange($('.select-payRange').eq(0),$('input[name=payRange]'));
				priceOpenStrategy(payRange);
			}

			if(payRangeRow.hasClass('row-disabled')){
				$('.select-payRange').eq(0).find('.selected-icon').remove();
			}

		});

		/**
		 * 课程收费范围
		 */
		$(document).on('click','.select-payRange',function () {
			if($(this).hasClass('a-disabled') || payRangeRow.hasClass('row-disabled')) return false;
			var payRange = selectRange($(this), $('input[name=payRange]'));
			priceOpenStrategy(payRange);
		});

		var selectRange = function (_this, _input) {
			_this.addClass('active').siblings('a').removeClass('active').find('.selected-icon').remove();
			_this.find('.selected-icon').remove();
			_this.append('<em class="fa fa-check-square-o selected-icon"></em>');
			_input.val(_this.data('id'));
			return _this.data('id');
		};

		var payRangeOpenStrategy = function (openRange) {
			openRange = parseInt(openRange);
			payRangeRow.removeClass('row-disabled');
			payRangeRow.find('.select-payRange').removeClass('a-disabled');
			switch (openRange) {
				case 1:
					payRangeRow.find('.select-payRange[data-id=2]').addClass('a-disabled');
					break;
				case 2:
					payRangeRow.addClass('row-disabled');
					priceOpenStrategy(0);
					break;
				default:
			}
			if(payRangeRow.hasClass('row-disabled')){
				payRangeRow.find('.selected-icon').remove();
			}
		};

		var priceOpenStrategy = function (payRange) {
			payRange = parseInt(payRange);
			priceRow.removeClass('row-disabled');
			priceRow.find('input[name=price]').attr('disabled','disabled')
                        
			switch (payRange) {
				case 1:
					priceRow.find('input[name=price]').removeAttr('disabled');
					break;
				case 2:
					priceRow.find('input[name=price]').removeAttr('disabled');
					break;
				default:
					priceRow.addClass('row-disabled');
					priceRow.find('input[name=price]').val('');
					priceRow.find('.help-block').remove();
					priceRow.removeClass('has-error');
			}

		};
                if(priceRow.find('input[name=price]').data("banshow") != 1){
                    payRangeOpenStrategy(openRange);
                    
                    // edit fubaosheng 2016-03-01
                    openRange = parseInt(openRange);
                    if(openRange == 2) payRange = 0;
                        
                    priceOpenStrategy(payRange);
                }
	};

});