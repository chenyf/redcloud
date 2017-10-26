define(function(require, exports, module) {
    var Widget = require('widget');
    var Notify = require('common/bootstrap-notify');
 /*
  * 
  * 调用方法
  * 页面加上
  * <div class="redcloud_comment" data-cmttype="页面类型参数" data-cmtid="页面唯一标识"></div>
  *  
  *    js文件加上
  * var WidgetComment= require('../comment/comment.js');
        
        //seajs.use('comment/comment.js');
	exports.run = function () {
            var CommentA = new WidgetComment({
                 cmtSelector:'.redcloud_comment'
            }).render();
            CommentA.init();
  * 
  * 
  * 
  */
 
        var WidgetAppraise = Widget.extend({
          attrs: {
             'elemSelector': 'b.cmtApprise',
             'type': 'poster',    //注册类型
             'appraiseid': 234,   //参数
             'vcode':  'asdasdfa',//由注册类型+参数生成校验码
             'success': null,  //支持成功回调及必要参数（当前总数）
             'error': null, //失败回调（网络原因或服务器端异常）
             'eachSuccess':null,//遍历回调,返回总数与uid，username等
             'eachError':null,//遍历回调（网络原因或服务器端异常）
          },
          ele:'',
          appraise: null,
          type: '',
          appraiseid: '',
          vcode:'',
          arsLock: 1,
          eachLock: 1,
          load:true,
          init: function(){
             this.ele = $(this.get('elemSelector'));
             this.eachAppraise();
             this.clickAppraise();
          },
          eachAppraise:function(){
                elem = this.ele;
                var _this = this;
//                $(elem).each(function(i , item){
//                   var appraise = $(this);
//                   $.post("/System/Appraise/eachAppraiseAction",{type: $(this).data('type'),appraiseid:$(this).data('appraiseid'),vcode:$(this).data('vcode')}, function(response){
//                        if($.isFunction(_this.get('eachSuccess'))) _this.get('eachSuccess')(response,appraise);
//                    }).error(function(req) {
//                            if($.isFunction(_this.get('eachError'))) _this.get('eachError')(req);
//                    });
//                })
          },
          clickAppraise:function(){
             elem = this.ele;
             var _this = this;
             elem.unbind("click");
             elem.on('click',function(){
                 _this.appraise = $(this);
                 _this.type =  $(this).data('type');
                 _this.appraiseid =  $(this).data('appraiseid');
                 _this.vcode =  $(this).data('vcode');
                 if(_this.arsLock != 1) return false;
                  _this.arsLock = 2;
                 $.post("/System/Appraise/goodAction",{type: _this.type,appraiseid:_this.appraiseid,vcode:_this.vcode}, function(response){
                        _this.arsLock = 1;
                        if($.isFunction(_this.get('success'))) _this.get('success')(response,_this.appraise);
                }).error(function(req) {
                        _this.arsLock = 1;
                        if($.isFunction(_this.get('error'))) _this.get('error')(req);
                });
                 
                 
             }); 
          },
          bind: function(callType, method){
              if(calltype=='success') this.success = method;
              if(calltype=='error') this.error = method;
          },
          
            
        });
        module.exports = WidgetAppraise;
 
    
   
      
    

});