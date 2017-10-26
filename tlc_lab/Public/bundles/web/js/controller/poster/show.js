define(function (require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	var Share = require('../../util/share.js');

	var WidgetComment= require('../comment/comment.js');
        var WidgetAppraise= require('../appraise/appraise.js');
        
	exports.run = function () {
                var Comment = new WidgetComment({
                     cmtSelector:'.comment'
                }).render();
		Comment.init();
                        
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
            
            
		var BaiduShareRun = function () {
			var shareParams = $('#poster-share-params');
			var bdDesc = shareParams.data('title');
			var bdPic = shareParams.data('picture');
			var bdUrl = shareParams.data('url');
			bdUrl = window.site_url+bdUrl;
			if(bdPic){
				bdPic = window.site_url+bdPic;
			}
			Share.create({
				selector : '.share',
				icons : 'itemsAll',
				display : '',
				bdDesc : bdDesc,
				bdPic : bdPic,
				bdUrl : bdUrl,
			});
		};

		BaiduShareRun();

		$(document).on('click', '.poster-closedbtn', function () {
			myModal.destroy();
			Comment.destroy();
			window.InitBaiduShare();

			$('html').removeClass('body-scroll')
		});

		$(document).ajaxError(function (event, jqxhr, settings, exception) {

			if ( error.name == 'Unlogin' ) {
				myModal.destroy();
				$('html').removeClass('body-scroll')
			}

		});

		var switchBtn = true;

		$('.reload-data').click(function () {
			if ( !switchBtn ) {
				return false;
			}
			switchBtn = false;
			var _this = $(this);
                        
			$(this).find('img').show();
			var action = $(this).data('action');
                        
			var reloadParam = ".reload-param";
			var reloadUrl = $(reloadParam).data('next');
                        
			if ( action == 'prev' ) {
				reloadUrl = $(reloadParam).data('prev');
			}
                       
			window.InitBaiduShare();
			if ( reloadUrl ) {
				$('.poster-body').load(reloadUrl + " .poster-details-main, .bot-fixed", function () {
					Comment.init();
                                        posterAppraise.init();
					BaiduShareRun();
					_this.find('img').hide();
					$('.poster-nextbtn,.poster-prevbtn').show();
					var reloadParam = ".reload-param";
					var nextUrl = $(reloadParam).data('next');
					var prevUrl = $(reloadParam).data('prev');
					if ( !nextUrl ) {
						$('.poster-nextbtn').hide();
					}
					if ( !prevUrl ) {
						$('.poster-prevbtn').hide();
					}
					switchBtn = true;
				})
			}
		});
	}
});