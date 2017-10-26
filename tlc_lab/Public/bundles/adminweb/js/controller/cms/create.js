define(function(require, exports, module) {

    require("jquery.jplupload");
    require('jquery.jquery18');
    require('jquery.jscolor');
    
    exports.run = function() {
        jscolor.installByClassName('jscolors');
          var x =1;
        $("#user-create-btn").on("click", function() {
       
     
            var videoPath = document.getElementsByName("videoPath[]");
            var price = $.trim($("#yuanjia").val());
            var Preferentialprice = $.trim($("#Preferentialprice").val());
            var coursename = $.trim($("#coursename").val());
            
            var buyurl = $.trim($("#buyurl").val());
            var ButtonDescribe = $.trim($("#ButtonDescribe").val());
            
            var picPath = document.getElementsByName("picPath[]");
            var urlPath = document.getElementsByName("url[]");
            var videotitle = document.getElementsByName("videotitle[]");
            var hidden = document.getElementsByName("imgPath[]");
            var polyvVid = document.getElementsByName("polyvVid[]");
            var height = $("#height").val();
            var cmsType = $("#cmsType").val();
             var RegUrl = new RegExp();
            RegUrl.compile("^[A-Za-z]+://[A-Za-z0-9-_]+\\.[A-Za-z0-9-_%&\?\/.=]+$");
          
                
            var num = $("#navigation").children().length;
            var title = $("#title").val();
                      
                   
                     
            
          if(title==""){
               $(".con").text("标题不能为空");
               return  false;
          }else if(title.length>10){
                $(".con").text("标题不能大于10个字符");
                return  false;
          }else{
                if (cmsType == 2) { //图片模块
                    for (var i = 0; i < picPath.length; i++) {
                        if (picPath[i].value == "") {
                            $(".con").text("请填写完整路径");
                            return false;
                        }
                    }
                     for (var i = 0; i < urlPath.length; i++) {
                            if (urlPath[i].value != "") {
                                if (!RegUrl.test(urlPath[i].value)) {
                                    $(".con").text("请填写正确的链接地址!'http://'第"+(i+1)+"个");
                                    return false;
                                 }
                            }else{
                                
                                
                            }
                      }
                      
                } else if (cmsType == 3) {//购买
                     if(coursename == ""){
                       
                          $(".con").text("课程不能为空");
                                         return  false;
                    }else if(coursename.length>20){
                         $(".con").text("课程课程名称不能大于20个字");
                                         return  false;
                    }else if (Preferentialprice == "") {
                         
                        $(".con").text("优惠价不能为空");
                        return  false;
                    } else if (isNaN(Preferentialprice)==true) {
                        $(".con").text("优惠价必须为数字");
                        return  false;
                    }else if(ButtonDescribe == ""){
                       
                          $(".con").text("请填写按钮名称");
                                         return  false;
                    }else if(ButtonDescribe.length>100){
                         $(".con").text("按钮名称不能大于10个字");
                                         return  false;
                    } else if(price == "") {
                        
                          $(".con").text("原价格不能为空");
                          return false;
                      }else if(isNaN(price)==true){
                        $(".con").text("原价格必须为数字");
                          return  false;
                    }else if(buyurl == ""){
                       
                         $(".con").text("请填写按钮链接");
                                         return  false;
                    }else if(!RegUrl.test(buyurl)){
                       
                         $(".con").text("按钮链接格式错误!'http://'");
                                         return  false;
                    }else{
                          for (var i = 0; i < videoPath.length; i++) {
                            if(videoPath[i].value==""){
                                $(".con").text("请填写完整视频路径");
                                return  false;
                            }else if(!RegUrl.test(videoPath[i].value)){
                                  $(".con").text("请填写正确的视频链接地址!'http://'");
                                         return  false;
                            }
                        }
                        for (var i = 0; i < hidden.length; i++) {
                            if (hidden[i].value == "") {
                                $(".con").text("请选择照片");
                                return  false;
                            }
                        }
                        
                        for (var i = 0; i < polyvVid.length; i++) {
                            if (polyvVid[i].value == "") {
                                $(".con").text("请填写视频Id");
                                return  false;
                            }
                        }
                        
                        for (var i = 0; i < videotitle.length; i++) {
                            if(videotitle[i].value==""){
                                $(".con").text("请填写视频名称");
                                return  false;
                            }
                            
                            if(videotitle[i].value.length>20){
                                $(".con").text("视频名称不能大于20个字");
                                return  false;
                            }
                            
                        }
                      
                        
                        
                       
                    }
                      
                    
                } else if (cmsType == 1) {//轮播模块
                    for (var i = 0; i < picPath.length; i++) {
                        if (picPath[i].value == "") {
                            $(".con").text("请填写完整路径");
                            return false;
                            
                        }
                    }
                    
                  
                        for (var i = 0; i < urlPath.length; i++) {
                            if (urlPath[i].value == "") {
                                $(".con").text("请填写完整路径");
                                return false;
                            } else if (!RegUrl.test(urlPath[i].value)) {
                                $(".con").text("请填写正确的链接地址!'http://'");
                                return false;
                            }
                        }
                 

                } else if (cmsType == 0) {//导航
                    if (num == 0) {
                        $(".con").text("最少选择一个");
                        return false;
                    }
                }

          }
          
          
            if (x != 1) {
                $(".con").text("点击次数过多");
                return false;
            }
            x = 2;
        })


        photo();
        $("#add").live("click", function() {
            var cmsType = $("#cmsType").val();

            if(cmsType==2){
                var btnNum = $("#btnNum").val();
                var btn = btnNum - 1;
                var url = $("#url_" + btn).val();
               
                var path = btnNum - 1;
                var path_url = $("#url_" + path).val();

               
                    if (path_url == "") {
                        $(".con").html("请选择照片或者路径");
                        return false;
                    } else {
                        $(".con").html();

                        $("#increase").append(" <tr><td style ='width:30%;vertical-align: initial;' ><div class ='form-group' ><label for ='nickname' > 图片地址" + btnNum + " </label><input type ='text' id = 'url_" + btnNum + "' name ='picPath[]' data - type ='addUser' class ='form-control' style ='margin-left:0' ></div></td><td style ='width:20%; text-align: center'>  <span class ='btn' id ='btn_" + btnNum + "'><a href='javascript:void(0)'> <i class='glyphicon glyphicon-picture'></i></a></span><label for='nickname' style='margin-top:10px'>选择本地图片</label><br><hr></td><td style ='width:30%'><label for ='nickname'> 链接地址 </label><input type ='text' name ='url[]' placeholder='http://(选填)' id ='path_1' data - type ='addUser' class ='form-control' style ='margin-left:0'><br> <label for='nickname'>选择颜色</label> <img src='/Public/assets/libs/colorpicker/colorpicker.png' class ='color_" + btnNum + " {valueElement:"+ '"' + btnNum +'"'+ "} ' style='cursor:pointer;margin:0 0 0 10px;'><input id ='" + btnNum + "'  value ='000000' name ='color[]'  class ='form-control' style ='margin-left:0'></td><td ><label for = 'nickname' > 操作 </label><br><a src='javascript:void(0)' id ='del_" + btnNum + "'  class ='btn btn-default' title='删除'><i class ='glyphicon glyphicon-remove' ></i></a ><div style='height:50px'></div></td></tr>");

                      
                        photo();
                   jscolor.installByClassName('color_'+ btnNum );
                        removevind('del_' + btnNum);
                        down('down_'+ btnNum);
                        up('up_'+ btnNum);
                    }
                
            }else if(cmsType==1){//轮播
                var btnNum = $("#btnNum").val();
                var btn = btnNum - 1;
                var url = $("#url_" + btn).val();

                var path = btnNum - 1;
                var path_url = $("#url_" + path).val();


                if (path_url == "") {
                    $(".con").html("请选择照片或者路径");
                    return false;
                } else {
                    $(".con").html();

                    $("#increase").append(" <tr><td style ='width:30%;vertical-align: initial;' ><div class ='form-group' ><label for ='nickname' > 图片地址" + btnNum + " </label><input type ='text' id = 'url_" + btnNum + "' name ='picPath[]' data - type ='addUser' class ='form-control' style ='margin-left:0' ></div></td><td style ='width:20%; text-align: center'> <span class ='btn' id ='btn_" + btnNum + "'><a href='javascript:void(0)'> <i class='glyphicon glyphicon-picture'></i></a></span> <label for='nickname' style='margin-top:10px'>选择本地图片</label><br><hr></td><td style ='width:30%'><label for ='nickname'> 链接地址 </label><input type ='text' name ='url[]' value='http://' id ='path_1' data - type ='addUser' class ='form-control' style ='margin-left:0'><br> <label for='nickname'>选择背景颜色</label> <img src='/Public/assets/libs/colorpicker/colorpicker.png' class ='color_" + btnNum + " {valueElement:" + '"' + btnNum + '"' + "} ' style='cursor:pointer;margin:0 0 0 10px;'><input id ='" + btnNum + "'  value ='000000' name ='color[]'  class ='form-control' style ='margin-left:0'></td><td vertical-align: initial;><label for = 'nickname'> 操作 </label><br><a  src='javascript:void(0)' id ='del_" + btnNum + "'  class ='btn btn-default' title='删除'><i class ='glyphicon glyphicon-remove' ></i></a ></td></tr>");
                  
                    photo();
                    jscolor.installByClassName('color_' + btnNum);
                    removevind('del_' + btnNum);
                    down('down_' + btnNum);
                    up('up_' + btnNum);
                }
            }else {

                var btnNum = $("#btnNum").val();
                var btn = btnNum - 1;
                var url = $("#url_" + btn).val();
                
                var titlename = $("#title_" + btn).val();
                if (cmsType == 3) {
                    if (url == "") {
                        $(".con").html("请选择照片或者路径");
                        return false;
                    } else {
                        if(titlename==""){
                            $(".con").html("请填写视频名称");
                            return false;
                        }else{
                             $("#increase").append("<tr><td style='width:30%;vertical-align: initial;'><div class='form-group'><label for='nickname'>请添加视频地址</label><input type='text'   name='videoPath[]' class='form-control' style='margin-left:0'><br><label for='nickname'>视频图片地址</label><input type='text' id='" + btnNum + "' name='imgPath[]' data-type='addUser' class='form-control' style='margin-left:0'></div></td><td style ='width:20%; text-align: center'> <span class ='btn' id ='btn" + btnNum + "'><a href='javascript:void(0)'> <i class='glyphicon glyphicon-picture'></i></a></span> <label for='nickname' style='margin-top:10px'>选择本地图片</label><br><hr></td><td style='width:30%'><label for='nickname'>请添加视频播放Id</label><input type='text'   name='polyvVid[]' class='form-control' style='margin-left:0'><br><label for='nickname'>请添加视频名称</label><br><input type='text'   name='videotitle[]' class='form-control' style='margin-left:0' ></td><td><label for='nickname'>操作</label><br><a src='javascript:void(0)'  class='btn btn-default' id='del_" + btnNum + "' title='删除'> <i class='glyphicon glyphicon-remove'></i></a></td></tr>");
                              upload(btnNum);
                               removevind('del_' + btnNum);
                        bindListener();
                        hiddenPhoto(btnNum,'file'+btnNum);
                        num()
                        }
                       
                    }
                }

            }



        })
        
        
  
        
        
        $(".img").click(function(){
      
            $.ajax({
                type: "POST",
                url: $(this).data("url"),
                success: function(msg) {
               
                }
            });
             $(this).remove();
        })
        
        $("#file1").change(function(){
            var file = document.getElementById("file1").files[0];
          $("#filehidden").val(file.name)
        })
        
        function hiddenPhoto(hid,picId){
                $("#"+picId).change(function() {
                 var file = document.getElementById(picId).files[0];
              
                 if (!/image\/\w+/.test(file.type)) {
                 $(".con").html("请重新选择照片格式");
                 return false;
                 } else {
                 $("#"+hid).val(file.name)

                 }
                 })
             
             
        }
        
      
      
      //==========================================================================================================
      
        if ($("[data-url^=up_]").size()) {
            $("[data-url^=up_]").each(function(index, item) {
                up($(item).data("url"));
            })
        }

        if ($("[data-url^=down_]").size()) {
            $("[data-url^=down_]").each(function(index, item) {
                down($(item).data("url"));
            })
        }
      
           function up(obj){
               $("#" + obj).click(function() {
                var objParentTR = $("#" + obj).parent().parent();

                var nextTR = objParentTR.next();

                if (nextTR.length > 0) {
                    nextTR.insertBefore(objParentTR);
                }
            })
           }
          
         
          function down(obj){
              $("#" + obj).click(function() {
                var objParentTR = $("#" + obj).parent().parent();
                var prevTR = objParentTR.prev();

                if (prevTR.length > 0) {
                    prevTR.insertAfter(objParentTR);
                }

            })
          }
            
       
        
        
        
       
        if($("[data-url^=url_]").size()){
            $("[data-url^=url_]").each(function(index,item){
                
                upload($(item).data("url"));
            })
        }
        
        function upload(btnId){  
            var uploader = new plupload.Uploader({//创建实例的构造方法
                runtimes: 'html5,flash,silverlight,html4', //上传插件初始化选用那种方式的优先级顺序
                browse_button:"btn"+ btnId, // 上传按钮
                url: ajaxPhoto, //远程上传地址
               
                filters: {
                    max_file_size: '500kb', //最大上传文件大小（格式100b, 10kb, 10mb, 1gb）
                    mime_types: [//允许文件上传类型
                        {title: "files", extensions: "jpg,png,gif"}
                    ]
                },
                multi_selection: false, //true:ctrl多文件上传, false 单文件上传

                init: {
                    FilesAdded: function(up, files) { //文件上传前
                        if ($("#ul_pics").children("li").length > 30) {
                            alert("您上传的图片太多了！");
                            uploader.destroy();
                        } else {
                            var li = '';
                            plupload.each(files, function(file) { //遍历文件
                                li += "<li id='" + file['id'] + "'><div class='progress'><span class='bar'></span><span class='percent'>0%</span></div></li>";
                            });
                            $("#ul_pics").append(li);
                            uploader.start();
                        }
                    },
                    UploadProgress: function(up, file) { //上传中，显示进度条
                        var percent = file.percent;
                        $("#" + file.id).find('.bar').css({"width": percent + "%"});
                        $("#" + file.id).find(".percent").text(percent + "%");
                    },
                    FileUploaded: function(up, file, info) { //文件上传成功的时候触发
                        var data = eval("(" + info.response + ")");
                     
                     

                        $("#" + btnId).val(data.pic);
                    },
                    Error: function(up, err) { //上传出错的时候触发
                        alert(err.message);
                    }
                }
            });
            uploader.init();
        }
       
        
        
      
          


        
        
        
        $(".del").click(function(){
            var id = $(this).data("url");
          if(confirm("确定要删除吗？")==false){
             
          }else{
               $.ajax({
                type: "GET",
                url: ajaxDeleteNav,
                data: {id: id},
                success: function(msg) {
                  
                }
            });
            $(this).parent().parent().remove();
          }
          // var  val = $("#url_"+$(this).data("url")).val();
           
            
        })
        
   //=========================================================
        
   
        
        
        

        function num() {
            var num = parseInt($("#btnNum").val()) + 1;
            $("#btnNum").val(num);
        }


        function bindListener() {
            $("#increase div a").unbind().click(function() {
                //直接通过.remove() 方法移除掉li元素,页面自动就会刷新
                $(this).parent().parent().remove();


            });

        }

        $(".choose input[type=button]").on("click", function() {
            //var title =  document.getElementsByName("title[]");
            var self = $(this);
            var anchorId = self.attr('id');
      
            var nav = $("#nav").val();
            if(anchorId==nav){
                
                 $(".con").text("不能选择本导航");
                 self.css("background-color","blue");
                 return false;
            }else{
                  $(".con").text();
            }
            var title = $("#navigation span input[name='title[]']");
            for (var i = 0; i < title.length; i++) {
                
                if (title[i].value == self.val()) {
                
                    $(".con").text("已选择了该标签 不能再选择");
                    self.css("background-color","blue");
                    return false;
                }else {
                    $(".con").text("");
                }
            }

            var num = $("#navigation").children().length;
            if (num > 5) {
                $(".con").text("最多选择6个");
                return false;
            } else {
                $("#navigation").append("<span class='navigation' data-id='"+anchorId+"'><input type='hidden' value='"+ anchorId +"' name='anchorId[]'><input type='hidden' name='title[]' value='"+self.val()+"'><input  type='button' value='" + self.val() + "'>&nbsp;&nbsp;</span>");
                deleteSpan(self);
                self.css("background-color","blue");
            }

        })
        //删除动态添加的导航
        function deleteSpan(a) {
            var id = a.attr("id");
            $("#navigation span[data-id='"+id+"']").on("click", function() {
                a.css("background-color","");
             
                $(this).remove();
                $(".con").text("");
                
            })
        }

        $(".navigation").on("click", function() {
          
            var id = $(this).children().val();
           
$("#"+id).attr("disabled",false);
            var anchorId = $(".choose input[name='anchorId[]']")
            for (var i = 0; i < anchorId.length; i++) {
                
                if (id == anchorId[i].value) {
                    
                    $("#" + id).css("background-color", "");
                }
            }

            $.ajax({
                type: "POST",
                url: ajaxDeleteNav,
                data: {ancharId: id},
                success: function(msg){
                    
                }
            });
             
            $(this).remove();
          
            $(".con").text();
        })



        function removevind(z) {
           $("#"+z).click(function(){
               $(this).parent().parent().remove();
           })
        }
        
      

        function photo() {
            var uploader = new plupload.Uploader({//创建实例的构造方法
                runtimes: 'html5,flash,silverlight,html4', //上传插件初始化选用那种方式的优先级顺序
                browse_button: 'btn_' + $("#btnNum").val(), // 上传按钮
                url: ajaxPhoto, //远程上传地址
                flash_swf_url: 'plupload/Moxie.swf', //flash文件地址
                silverlight_xap_url: 'plupload/Moxie.xap', //silverlight文件地址
                filters: {
                    max_file_size: '500kb', //最大上传文件大小（格式100b, 10kb, 10mb, 1gb）
                    mime_types: [//允许文件上传类型
                        {title: "files", extensions: "jpg,png,gif"}
                    ]
                },
                multi_selection: false, //true:ctrl多文件上传, false 单文件上传

                init: {
                    FilesAdded: function(up, files) { //文件上传前
                        if ($("#ul_pics").children("li").length > 30) {
                            alert("您上传的图片太多了！");
                            uploader.destroy();
                        } else {
                            var li = '';
                            plupload.each(files, function(file) { //遍历文件
                                li += "<li id='" + file['id'] + "'><div class='progress'><span class='bar'></span><span class='percent'>0%</span></div></li>";
                            });
                            $("#ul_pics").append(li);
                            uploader.start();
                        }
                    },
                    UploadProgress: function(up, file) { //上传中，显示进度条
                        var percent = file.percent;
                        $("#" + file.id).find('.bar').css({"width": percent + "%"});
                        $("#" + file.id).find(".percent").text(percent + "%");
                    },
                    FileUploaded: function(up, file, info) { //文件上传成功的时候触发
                        var data = eval("(" + info.response + ")");
                        var url = parseInt($("#btnNum").val()) - 1;
                 
                        id = "url_" + url;

                        $("#" + id).val(data.pic);




                    },
                    Error: function(up, err) { //上传出错的时候触发
                        alert(err.message);
                    }
                }
            });
            uploader.init();
            var num = parseInt($("#btnNum").val()) + 1;
            $("#btnNum").val(num);
        }



    }
});