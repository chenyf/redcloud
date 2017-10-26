define(function (require, exports, module) {
	var Notify = require('common/bootstrap-notify');

	require('masonry');
	require('ajaxfileupload');
	require('imagesloaded');
	require('infinitescroll');
	var jBox = require('Jbox');
        var WidgetAppraise= require('../appraise/appraise.js');
	window.bd = true;
	exports.run = function () {
		var container = '.poster-list-ul';
		var likePoster = '.likePoster';
		var dislikePoster = '.dislikePoster';
		var deleteBtn = ".delete-poster";
		var item = $('#show-item');
		var loadImg = $('.load-img');
                var posterAppraise = new WidgetAppraise({
                            'elemSelector': 'span.posterApprise',
                            success:function(response,ele) {
                                var data = JSON.parse(response);
                                if(!data.success) {
                                      Notify.danger(data.message);
                                      return false;
                                 }
                                var goodInfo = data.goodInfo;
                                if(data.type == 'add'){
                                  var zanNum = goodInfo['good'];
                                  var zan = zanNum+'人赞';
                                    ele.html(zan);
                                    ele.siblings('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                                }else{
                                   var exitNum = goodInfo['good'];
                                   var exit = exitNum+'人赞';
                                    ele.html(exit);
                                    ele.siblings('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                                } 
                           },
                       }).render();
                posterAppraise.init();

		// 初始化baidu share的数据
		window.InitBaiduShare = function(){
			window._bd_share_main = null;
			window._bd_share_config = {
				common : {
					onAfterClick:function() {

					}
				},
				share : []
			}
		};

		/**
		 * 喜欢
		 */
		$(document).on('click', likePoster, function () {
			var _this = $(this);
			$.ajax({
				url : _this.data('url'),
				type : 'POST',
				success : function (response) {
					_this.hide();
					_this.siblings(dislikePoster).show().find('.likeNum').text(response.likes);
				}
			})
		});

		/**
		 * 取消喜欢
		 */
		$(document).on('click', dislikePoster, function () {
			var _this = $(this);
                       
			$.ajax({
				url : _this.data('url'),
				type : 'POST',
				success : function (response) {
					_this.hide();
					_this.siblings(likePoster).show().find('.likeNum').text(response.likes);
				}
			})
		});

		/**
		 * 瀑布流布局
		 */
		$(container).imagesLoaded(function () {
			loadImg.html(' ').slideUp('fast');
			$('.poster-item').css({opacity : 1});
			window.grid = $('.poster-list-ul').masonry({
				// options
				itemSelector : '.poster-item'
			});
		});

		/**
		 * 删除海报
		 */
		$(container).on('click', deleteBtn, function () {
			var _this = $(this);
			if ( confirm('是否要执行此操作？') ) {
				$.ajax({
					url : _this.data('url'),
					type : 'POST',
					success : function () {
						Notify.success('删除成功！');
						grid.masonry('remove', _this.parents('.poster-item')).masonry('layout');
					}
				});
			}
		});

		var maxPage = $('.page-nav').data('maxpage');

		/**
		 * 滚动条下拉加载
		 */
		$(container).infinitescroll({
			navSelector : '.page-nav',
			nextSelector : '.page-nav a#next-page',
			itemSelector : '.poster-list-ul .poster-item',
			maxPage : maxPage,
			loading : {
				img : '/Public/assets/img/common/loading35.gif',
				msgText : ' ',
				selector : '.loading',
				speed : 'fast'
			}

		}, function (arrayOfNewElems, opts) {
			var currentPage = opts.state.currPage;
			var $newElems = $(arrayOfNewElems).css({opacity : 0});
			$newElems.imagesLoaded(function () {
				$newElems.animate({opacity : 1});
				$(container).masonry('appended', $newElems, true);
				if ( currentPage >= maxPage ) {
					$('.loading').text('全部加载完成')
				}
			});
		});

		/**
		 * 展示详情页
		 */
		var itemId = item.data('id');
		var itemUrl = item.data('url');
		if ( parseInt(itemId) > 0 ) {
			showItem(itemUrl);
		}

		$(container).on('click', '.show-detail', function () {
			showItem($(this).data('url'));
		});



		function showItem(url) {
			window.InitBaiduShare();
			window.myModal = new jBox('Modal', {
				ajax : url,
				animation : 'zoomOut',
				closeOnEsc : false,
				reload : true
			});
			myModal.open();

			$('html').addClass('body-scroll')
		}
                
                
                
                
              
                
                
             
	};
});