;(function(){
	var checkeJquer =  window.jQuery && window.$; 
	checkeJquer || jQuery.noConflict();
	window.openUser = jQuery;
})();
;(function($){
	$.fn.extend({
		Login:function(options){
			
			var BUTTON_STYLE = {
				A_XL: {
					size: '230_48'
				},
				A_L: {
					size: '170_32'
				},
				A_M: {
					size: '120_24'
				},
				A_S: {
					size: '105_16'
				},
				B_M: {
					size: '63_24'
				},
				B_S: {
					size: '50_16'
				},
				C_S: {
					size: '16_16'
				}
			};
			
			var options=$.extend({
				box : this,
				size: "B_S",
				debugBaseUrl:"https:\/\/oauth.gkk.com",
                                debug:0,
				appId:'',
				appKey:'',
				redirectURI:'',
				target:'_self',
				aClass:'openUserAClass',
				imgClass:'openUserImgClass',
				costom :'',
				w:'',
				h:''
			},options);
			
			
			var url = options.debugBaseUrl+"\/User\/LoginOpenUser\/checkOpenUser\/?appKey="+options.appKey;
			url = options.debug ? url+"&url="+options.debugBaseUrl : url;
			var imgUrl = options.debugBaseUrl+"/Public/assets/open/open-user/images/";
			
			var img = options.costom ? "<img calss='"+options.imgClass+"' src='"+options.costom+"' width='"+options.w+"' height='"+options.h+"'>":"<img calss='"+options.imgClass+"' src='"+imgUrl+BUTTON_STYLE[options.size]['size']+".png'>";
			
			var aStart = "<a href='"+url+"' target='"+options.target+"' class='"
			+options.aClass+"'>";
			var aEnd = "<\/a>";
			
			var openUserButton = aStart+img+aEnd;
			
			$(openUserButton).appendTo(options.box);
			
		}	//Login
	});
})(openUser);

