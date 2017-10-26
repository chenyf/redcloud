define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    var QCloudCos = require("jquery.qcloudCos");
    exports.bucketName = "cloud";
    exports.appid = "10011123";
    exports.secondDown = exports.sliceInterval = "";
    exports.t0 = exports.t1 = 0;
    exports.m = 3*1024*1024;
    exports.run = function(){
        
        //初始化cos
        var qcos = new QCloudCos({
            'bucketName':  exports.bucketName,
            'appid': exports.appid
        }).render();
        qcos.init();
        
        var $modal = $('#upload-form').parents('.modal');
        var uploader = $(".file-uploader-now");
        var fileNameObj = uploader.find(".file-name");
        var uploaderInfoObj = uploader.find(".data-uploader-info");
        var progressBar = uploaderInfoObj.find(".progress-bar");
        var progressObj = uploaderInfoObj.find(".progress");
        var textMutedObj = uploaderInfoObj.find(".text-muted");
        var fileUploaderName = uploader.find(".file-uploader-name");
        var resourceBtn = $('#create-resource-btn');
        
        //金币数
        Validator.addRule('goldNum', /^[1-9][0-9]{0,3}$/, '请输入有效的金币数，范围为1—9999的整数');
        
        //提交
        var validator = new Validator({
            element: '#upload-form',
            autoSubmit: false,
            failSilently: true,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                var url = $("[name=url]").val();
                if(!url){
                    Notify.danger('请上传资料');
                    return false;
                }
                resourceBtn.addClass('disabled');
                if(resourceBtn.data('lock') == 1) return false;
                resourceBtn.data('lock',1);
                $.post($form.attr('action'), $form.serialize(), function(result) {
                    if(result.status == 0){
                        resourceBtn.data('lock',0);
                        resourceBtn.removeClass('disabled');
                        Notify.danger(result.info);
                    }else{
                        $modal.modal('hide');
                        Notify.success(result.info);
                        window.location.reload();
                    }
                }).error(function(){
                    resourceBtn.data('lock',0);
                    resourceBtn.removeClass('disabled');
                    Notify.danger('操作失败');
                });
            }
        });
        
        //标题
        validator.addItem({
            element: '[name="title"]',
            required: true,
            rule: 'remote'
        });
        
        //下载权限
        $("[name='power']").on("click",function(){
            var self = $(this);
            var power = self.val();
            if(parseInt(power) == 2){
                $("#goldNum").prop("disabled",false);
                validator.addItem({
                    element: '[name="goldNum"]',
                    required: true,
                    rule : 'goldNum',
                    display : '金币数'
                });
            }else{
                $(".download-power .help-block").html("");
                $("#goldNum").prop("disabled",true).val("");
                validator.removeItem({
                    element: '[name="goldNum"]'
                });
            }
        });
        
        //获取字节大小，保留1位小数
        var fileSizeFilter = function(size) {
            var currentValue,currentUnit;
            var unitExps = [0,1,2,3];
            var bExps = ['B','KB','MB','GB'];
            for(var unit in unitExps){
                var exp = unitExps[unit];
                var divisor = Math.pow(1000,exp);
                currentUnit  = unit;
                currentValue = size / divisor;
                if (currentValue < 1000) {
                    break;
                }
            }
            currentValue = currentValue.toFixed(1);
            return currentValue+bExps[currentUnit];
        }
    
        //获取字节大小，保留1位小数
        var bytesToSize = function(bytes){
            if (bytes === 0) return '0B';
            var k = 1024;
            var sizes = ['B','KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
            var i = Math.floor(Math.log(bytes) / Math.log(k));
            var byte = (bytes / Math.pow(k, i));
            byte = byte.toFixed(1);
            return byte + sizes[i];
        };
        
        //获取类型
        var getType = function(ext){
            var type = "fa-file-o";
            if( $.inArray(ext,['xls','xlsx']) != -1 )
                type = "fa-file-excel-o";
            if( $.inArray(ext,['doc','docx']) != -1 )
                type = "fa-file-word-o";
            if( $.inArray(ext,['pdf']) != -1 )
                type = "fa-file-pdf-o";
            if( $.inArray(ext,['ppt','pptx']) != -1 )
                type = "fa-file-powerpoint-o";
            if( $.inArray(ext,['jpg','jpeg','gif','png']) != -1 )
                type = "fa-file-image-o";
            if( $.inArray(ext,['flv','avi','mp4','rm','rmvb']) != -1 )
                type = "fa-file-video-o";
            if( $.inArray(ext,['mp3','wma']) != -1 )
                type = "fa-file-audio-o";
            if( $.inArray(ext,['zip','rar','tar.gz']) != -1 )
                type = "fa-file-archive-o";
            return type;
        };
        
        //清除定时器
        var clearUploadInterval = function(){
            clearInterval(exports.sliceInterval);
            clearInterval(exports.secondDown);
        }
             
        //成功返回回调
        var successCallBack = function(success,size){
            success = $.parseJSON(success);
            if(success.code == 0){
                successFun(success.data.resource_path,size);
            }
        };
        
        //成功后的处理函数
        var successFun = function(resourcePath,size){
            //添加清除定时器，为了不影响下一次上传计时
            clearUploadInterval();
            progressBar.css('width',"100%");
            textMutedObj.html("<span class='process-text'>100%</span>，剩余时间：<span class='process-down'>00:00:00</span>");
            $("[name=url]").val(resourcePath);
            setTimeout(function(){
                progressObj.addClass("hide");
                var byte = fileSizeFilter(size);
                textMutedObj.html(byte+"<em class='text-success mlm'>上传完成</em>");
            },1000);
        }
               
        //错误返回回调
        var errorCallBack = function(error,size){
            //添加清除定时器，为了不影响下一次上传计时
            clearUploadInterval();
            error = $.parseJSON(error.responseText);
            //相同文件已经上传过，提示成功
            if(error.code == -4018){
                successFun(error.data.resource_path,size);
            }else{
                Notify.danger(error.message);
                progressObj.addClass("hide");
                textMutedObj.html("上传失败");
            }
        };
        
        //倒计时
        var countdown = function(total){
            //下一个总秒数来临时，清除上一个定时器
            clearInterval(exports.secondDown);
            exports.secondDown = setInterval(function(){
                if(total < 0){
                    clearInterval(exports.secondDown);
                    return false;
                }
                var hour = 0, minute = 0, second = 0;
                if(total > 0){
                    var hour = Math.floor(total/3600);
                    var minute = Math.floor(total%3600/60);
                    var second = Math.floor(total%3600%60);
                }
                if(hour <=9 ) hour = '0'+hour;
                if(minute <=9 ) minute = '0'+minute;
                if(second <=9 ) second = '0'+second;
                textMutedObj.find(".process-down").html(hour+":"+minute+":"+second);
                total--;
            },1000);
        };
        
        //片数消耗秒数
        var sliceUseSecond = function(){
            exports.t1 = 0;
            exports.sliceInterval = setInterval(function(){
                exports.t1++;
            },1000);
        };
        
        //进度返回回调
        var processCallBack = function(result,size){
            if(result.data.offset != undefined){
                //下一片来临时，清除上一个的消耗时间
                clearInterval(exports.sliceInterval);
                exports.t0 = exports.t1;
                sliceUseSecond();
                var s =  parseInt( ( size-result.data.offset ) / exports.m );
                var second = exports.t0 * s;
                if( second > 0 ) countdown(second);
                var process = parseInt(result.data.offset/size*100);
                progressBar.css('width',process+"%");
                textMutedObj.find(".process-text").html(process+'%');
            }
        };
    
        //删除上传文件
        $(".del-upload-file").on("click",function(){
            //添加清除定时器，为了不影响下一次上传计时
            clearUploadInterval();
            uploader.addClass("hide");
            $("[name=url]").val("");
            $("#uploda-resource-chooser").val("");
            $(".file-chooser-uploader-control").removeClass("hide");
            //中断上传
            if('killUpload' in qcos) qcos.killUpload();
        });
        
        //取消modal
        $("#modal").on('hidden.bs.modal', function () {
            //添加清除定时器，为了不影响下一次上传计时
            clearInterval(exports.sliceInterval);
            clearInterval(exports.secondDown);
            if('killUpload' in qcos) qcos.killUpload();
            exports.t0 = exports.t1 = 0;
        });
    
        //目录
        var getDir = function(){
            var date = new Date();
            var r = Math.floor(Math.random()*99);
            var env = $('#upload-form').data("env");
            var dir = "/"+env+"/"+app.webCode+"/course/"+date.getFullYear()+"-"+(date.getMonth()+1)+"/"+date.getDate()+"_"+r+"/";
            return dir;
        };
    
        //判断是否是IE10以下版本
        var getIeVesion = function() {
            var browser = navigator.appName;
            if( browser == "Microsoft Internet Explorer" ){
                var b_version = navigator.appVersion;
                var version = b_version.split(";");
                var trim_Version = version[1].replace(/[ ]/g, "");
                trim_Version = trim_Version.replace(/MSIE/g,"");
                if( parseInt(trim_Version) < 10 ){
                    return 0;
                }
            }
            return 1;
        }
    
        //上传
        $("#uploda-resource-chooser").on("change",function(){
            var self = $(this);
            var tmp_file = self.val();
            if(tmp_file){
                //IE提示换浏览器
                if( !getIeVesion() ){
                    Notify.danger("浏览器版本过低，请更换其他浏览器或升级浏览器！",5);
                    return false;
                }
                
                var file = self[0].files[0];
                var file_name = file['name'];
                var dir = getDir()+file_name;
                var file_ext = file_name.substr(file_name.lastIndexOf('.')+1);
                var type = getType(file_ext);
                var file_size = file['size']/1024/1024/1024;
                if(file_size>1){
                    Notify.danger("文件大小不超过1GB,超过会删除！");
                    return false;
                }
                self.parent().addClass("hide");
                uploader.removeClass("hide");
                fileNameObj.attr("class","fa mrs file-name "+type);
                fileUploaderName.html(fileNameObj[0].outerHTML+file_name);
                progressObj.removeClass("hide");
                progressBar.css('width',"0%");
                textMutedObj.html("<span class='process-text'>0%</span>，剩余时间：<span class='process-down'>计算中</span>");
                $("[name=url]").val("");
            
                //文件大于10MB分片上传，小于10MB正常上传
                if( (file['size']/1024/1024) <= 10 ){
                    qcos.uploadFile(file,dir,function(success){
                        successCallBack(success,file['size']);
                    },function(error){
                        errorCallBack(error,file['size']);
                    });
                }else{
                    sliceUseSecond();
                    qcos.sliceUploadFile(file,dir,function(success){
                        successCallBack(success,file['size']);
                    },function(error){
                        errorCallBack(error,file['size']);
                    },function(result){
                        processCallBack(result,file['size']);
                    });
                }
            }
        });
        
    };

});