define(function(require, exports, module) {
    var Widget = require('widget');
        require("./qcloud.js");
        require("./swfobject.js");
    
   	
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
 
        var QCloudCos = Widget.extend({
          attrs: {
             'bucketName': "cloud" ,   //参数
             'appid' : '10011123',//参数
             'success': null,  //支持成功回调及必要参数（当前总数）
             'error': null, //失败回调（网络原因或服务器端异常）
          },
          bucketName:'',
          appid: '',
          cos: null,
          init: function(){
             this.bucketName = this.get('bucketName');
             this.appid = this.get('appid');
             this.cos = new CosCloud(this.appid);	
          },
          //文件上传
          uploadFile:function(file , remotePath , successCallBack, errorCallBack){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             cos.uploadFile(successCallBack, errorCallBack, bucketName, remotePath ,file);
          },
          //文件删除
          deleteFile:function(remotePath , successCallBack, errorCallBack){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             cos.deleteFile(successCallBack, errorCallBack, bucketName, remotePath);
          },
          //文件分片上传
          sliceUploadFile:function( file , remotePath , successCallBack, errorCallBack, processCallBack){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             if(!$('#qs').length) {
                $('body').append('<object id="qs" width="0" height="0" type="application/x-shockwave-flash" data="/Public/assets/libs/jquery-plugin/qcloudcos/Somethingtest.swf" style="visibility: visible;"></object>');
             }
             cos.sliceUploadFile(successCallBack, errorCallBack, bucketName, remotePath , file, processCallBack);
          },
          //终止上传，@author fbs
          killUpload : function(){
             this.cos.killUpload();
          },
          //文件查询
          getFileStat:function( remotePath , successCallBack, errorCallBack){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             cos.getFileStat(successCallBack, errorCallBack, bucketName, remotePath);
          },
          //文件属性更新
          updateFile:function( remotePath ,bizAttr , successCallBack, errorCallBack){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             cos.updateFile(successCallBack, errorCallBack, bucketName, remotePath, bizAttr);
          },
          hasFlashVersionOrBetter:function(major, minor){
             var _this = this;
             var bucketName = _this.bucketName;
             var cos = _this.cos;
             return cos.hasFlashVersionOrBetter(major, minor);
          }
                  
                  
                  
          
            
        });
        module.exports = QCloudCos;
 
    
   
      
    

});