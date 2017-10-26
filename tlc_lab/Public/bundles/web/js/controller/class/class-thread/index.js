define(function(require, exports, module) {

	var  manageThread = '.manageThread';
	var mediaItem = ".media-item";
        var WidgetAppraise= require('../../appraise/appraise.js');
	require('./manageThread.js').run(manageThread,mediaItem);

	exports.run = function(){
                var threadAppraise = new WidgetAppraise({
                            'elemSelector': '.threadApprise',
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
                                    ele.find('span').html(zan);
                                    ele.find('i').removeClass('glyphicon-heart-empty').addClass('glyphicon-heart');
                                }else{
                                   var exitNum = goodInfo['good'];
                                   var exit = exitNum+'人赞';
                                    ele.find('span').html(exit);
                                    ele.find('i').removeClass('glyphicon-heart').addClass('glyphicon-heart-empty');
                                } 
                           },
                       }).render();
                threadAppraise.init();
		$(mediaItem).mouseenter(function(){
			$(this).find(manageThread).show();
			$(this).siblings(mediaItem).find(manageThread).hide();
		});
		$(mediaItem).mouseleave(function(){
			$(this).find(manageThread).hide();
		});
	}

});