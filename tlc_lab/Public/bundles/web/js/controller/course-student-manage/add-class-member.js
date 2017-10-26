define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('webuploader');
    window.chk_value =[];
    var param =   $('#data-param');
    window.courseId = param.data("courseid");
    window.classId = param.data("classid");
    var categoryTreeUrl = param.data("categorytreeurl");
    
    var taskdata = $("#taskdata");
    var issetintval = taskdata.data("issetintval");
    if (parseInt(issetintval) == 1) {
        btntask = setInterval(function(){
            $.get(taskdata.data("taskstatusurl"),{},function(dataObj){
                if (dataObj.code == 1000 && parseInt(dataObj.status) > 0) {
                    $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                    $("#group_join_btn").next().remove();
                    $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                    $("#accounts_join_btn").next().remove();
                    $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                    $("#excel_join_btn").next().remove();
                    clearInterval(btntask);
                }
            },'json');

        }, 2000);
    }
    
    exports.run = function() {
        
        var $searchForm = $('form[role="search-form"]');
        var $group_div = $('#group_list');

        $.get($searchForm.attr('action'),{},function(html){
                $group_div.html(html);
            });
 
        $("#year").on('change',function(){
            
            var categoryId = 0;
            var collegeId = $("#collegeid").val();
            var departmentId = $("#departmentid").val();
            var professionalId = $("#professionalid").val();
            
            if (collegeId > 0) {
                categoryId  = collegeId;
            }
            if (departmentId > 0) {
                categoryId  = departmentId;
            }
            if (professionalId > 0) {
                categoryId  = professionalId;
            }
          
            $.get($searchForm.attr('action'),{year:$(this).val(),categoryId:categoryId},function(html){
                $group_div.html(html);
                $("input[name=checkAll]").prop('checked',false);
            });
        });
        
        $("#collegeid").on('change',function(){
            var optionHtml = '';
            if ($(this).val() != 0) {
                $.get(categoryTreeUrl,{categoryId:$(this).val()},function(dataObj){
                    $(dataObj.children).each(function(k, v){  
                        optionHtml += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    if (optionHtml != '') {
                        $("#departmentid").html('<option value="0">全部系</option>'+optionHtml);
                        $("#departmentid").attr("disabled",false);
                    } else {
                        $("#departmentid").html('<option value="0">全部系</option>'+optionHtml);
                        $("#departmentid").attr("disabled",true);
                    }
                    $("#professionalid").attr("disabled",true);

                },'json');
            } else {
                $("#departmentid").html('<option value="0">全部系</option>'+optionHtml);
                $("#professionalid").html('<option value="0">全部专业</option>');
                $("#professionalid").attr("disabled",true);
                $("#departmentid").attr("disabled",true);
            }
          
            $.get($searchForm.attr('action'),{year:$("#year").val(),categoryId:$(this).val()},function(html){
                $group_div.html(html);
                $("input[name=checkAll]").prop('checked',false);
            });
        });
        
        $("#departmentid").on('change',function(){
            var optionHtml = '';
            if ($(this).val() != 0) {
                $.get(categoryTreeUrl,{categoryId:$(this).val()},function(dataObj){
                    $(dataObj.children).each(function(k, v){  
                        optionHtml += '<option value="'+v.id+'">'+v.name+'</option>';
                    });
                    if (optionHtml != '') {
                        $("#professionalid").html('<option value="0">全部专业</option>'+optionHtml);
                        $("#professionalid").attr("disabled",false);
                    } else {
                        $("#professionalid").html('<option value="0">全部专业</option>');
                        $("#professionalid").attr("disabled",true);
                    }
                },'json');
            } else {
                $("#professionalid").html('<option value="0">全部专业</option>');
                $("#professionalid").attr("disabled",true);
            }

            var categoryId = 0;
            var collegeId = $("#collegeid").val();
            var departmentId = $("#departmentid").val();
            
            if (departmentId > 0) {
                categoryId  = departmentId;
            } else {
                categoryId  = collegeId;
            }
            
            $.get($searchForm.attr('action'),{year:$("#year").val(),categoryId:categoryId},function(html){
                $group_div.html(html);
                $("input[name=checkAll]").prop('checked',false);
            });
        });
        
        $("#professionalid").on('change',function(){
            var categoryId = 0;
            var departmentId = $("#departmentid").val();
            var professionalId = $("#professionalid").val();
            
            if (professionalId > 0) {
                categoryId  = professionalId;
            } else {
                categoryId = departmentId;
            }
           
            $.get($searchForm.attr('action'),{year:$("#year").val(),categoryId:categoryId},function(html){
                $group_div.html(html);
                $("input[name=checkAll]").prop('checked',false);
            });
        });

        $("#title").on({
            keydown:function(event){
                if (event.which == 13) {
                    $.get($searchForm.attr('action'),{title:$("#title").val()},function(html){
                        $group_div.html(html);
                        $("input[name=checkAll]").prop('checked',false);
                    });
                    return false;
                }
            }
        });
        
        $("#search_btn").on('click',function(){
            
            $.get($searchForm.attr('action'),{title:$("#title").val()},function(html){
                $group_div.html(html);
                $("input[name=checkAll]").prop('checked',false);
            });
            return false;
        });
        

        function get_modal_tpl(dataObj){
            
            var member_list = '';
            var succ_account = dataObj.succAccount.length;
            var fail_account = dataObj.failAccount.length;
            
            $("#succ_num").html(succ_account);
            $("#fail_num").html(fail_account);
            if (succ_account > 0 && fail_account == 0) {
                $("#modal_header").html('<h2 class="t-text-succeed"><i class="glyphicon glyphicon-ok-sign mrm"></i>账号导入成功</h2>');
            } else if (succ_account > 0 && fail_account > 0) {
                $("#modal_header").html('<h2 class="t-text-error"><i class="glyphicon glyphicon-info-sign mrm"></i>部分账号导入失败</h2>');
            } else if (succ_account == 0 && fail_account > 0) {
                $("#modal_header").html('<h2 class="t-text-error-all"><i class="glyphicon glyphicon-remove-sign mrm"></i>账号导入失败</h2>');
            }
            
            $(dataObj.succAccount).each(function(k,v){
                member_list += '<tr><td>'+v['account']+'</td><td><span class="text-success">成功</span></td></tr>';
            });
            $(dataObj.failAccount).each(function(k,v){
                member_list += '<tr><td>'+v['account']+'</td><td><span class="text-danger">失败</span></td></tr>';
            });
            
            $("#detail_list").html(member_list);
            
            return $("#modal_tpl").html();
        }
      
        
        $("#group_join_btn").on('click',function(){
            
            if (window.chk_value.length == 0) {
                Notify.danger('请选择要导入的自然班',2);
                return false;
            }
            
            if(!confirm("确定要将此班级学员导入到授课班么？"))
            {
                return false;
            }
            
            var $btn = $("#group_join_btn");
            $("#group_join_btn").attr("disabled",true).html("学员导入中...");
            $("#accounts_join_btn").attr("disabled",true).html("学员导入中...");
            $("#excel_join_btn").attr("disabled",true).html("学员导入中...");
            var courseId = $('#data-param').data("courseid");
            var classId = $('#data-param').data("classid");
            $.post($btn.data('goto'), {courseId:courseId,classId:classId,groupIds:window.chk_value.join(';')}, function(dataObj) {
                    if (dataObj.code == 1000) {
                        $("#modal").load($btn.data('status-url')+"/taskId/"+dataObj.data.taskId,function(){
                            $('#modal').on('hide.bs.modal',function(){location.reload();});
                            $('#modal').modal({backdrop: 'static', keyboard: false,show:'show'});
                        });
                    } else {
                        Notify.danger(dataObj.msg,1);
                        $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                        $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                        $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                    }
                    
                },'json').error(function(){
                    
                    Notify.danger('提交失败!',2);
                    $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                    $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                    $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                });
            
        });
        
        //绑定删除所选班级的事件
        $("#old_choose_group").on('click', 'a', function(){
            
            if(!confirm("确定要将此班级学员从授课班删除？"))
            {
                return false;
            }
            var groupId = $(this).data('groupid');
            var courseId = $('#data-param').data("courseid");
            var classId = $('#data-param').data("classid");
            $.post($(this).data("url"),{courseId:courseId,classId:classId,groupId:groupId},function(dataObj){
                
                if (dataObj.code == 1000) {
                    Notify.success(dataObj.msg,1,function(){
                        location.reload();
                    });
                } else {
                    Notify.danger(dataObj.msg,2);
                }
                
            },'json').error(function(){
                Notify.danger('提交失败!',2);
            });
                
        });
        
        
        $("#accounts_join_btn").on('click',function(){
            
            var accounts = $("#accounts").val();
            
            if (accounts == '') {
                $("#accounts").focus();
                Notify.danger('请输入账号',2);
                return false;
            }
            
            if(accounts.indexOf("；") >= 0 ){
                Notify.danger('您输入的账号里面包含中文分号，请您仔细检查',2);
                return false;
            }
            
            var $btn = $("#accounts_join_btn");
            $("#group_join_btn").attr("disabled",true).html("学员导入中...");
            $("#accounts_join_btn").attr("disabled",true).html("学员导入中...");
            $("#excel_join_btn").attr("disabled",true).html("学员导入中...");
            var courseId = $('#data-param').data("courseid");
            var classId = $('#data-param').data("classid");
            $.post($btn.data('goto'), {courseId:courseId,classId:classId,accounts:accounts}, function(dataObj) {
                    if (dataObj.code == 1000) {
                        $("#modal").load($btn.data('status-url')+"/taskId/"+dataObj.data.taskId,function(){
                            $('#modal').on('hide.bs.modal',function(){location.reload();});
                            $('#modal').modal({backdrop: 'static', keyboard: false,show:'show'});
                        });
                    } else {
                        Notify.danger(dataObj.msg,1);
                        $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                        $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                        $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                    }
                    
                },'json').error(function(){
                    
                    Notify.danger('提交失败!',2);
                    $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                    $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                    $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                });
            
        });
      
      
        $("#excel_join_btn").on('click',function(){
            
            var file_name = $("#file_name").val();
            
            if (file_name == '') {
                Notify.danger('请上传excel文件',2);
                return false;
            }
            
            var $btn = $("#excel_join_btn");
            $("#group_join_btn").attr("disabled",true).html("学员导入中...");
            $("#accounts_join_btn").attr("disabled",true).html("学员导入中...");
            $("#excel_join_btn").attr("disabled",true).html("学员导入中...");
            var courseId = $('#data-param').data("courseid");
            var classId = $('#data-param').data("classid");
            $.post($btn.data('goto'), {courseId:courseId,classId:classId,file_name:file_name}, function(dataObj) {
                if (dataObj.code == 1000) {

                    $("#modal").load($btn.data('status-url')+"/taskId/"+dataObj.data.taskId,function(){
                        $('#modal').on('hide.bs.modal',function(){location.reload();});
                        $('#modal').modal({backdrop: 'static', keyboard: false,show:'show'});
                    });
                } else {
                    Notify.danger(dataObj.msg,1);
                    $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                    $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                    $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
                }

            },'json').error(function(){

                Notify.danger('提交失败!',2);
                $("#group_join_btn").attr("disabled",false).html("添加自然班学员到授课班");
                $("#accounts_join_btn").attr("disabled",false).html("添加学员到授课班");
                $("#excel_join_btn").attr("disabled",false).html("导入学员到授课班");
            });
            
            
        });
        
        
        //检测是否已经安装flash，检测flash的版本
        var flashVersion = ( function() {
            var version;
            try {
                version = navigator.plugins[ 'Shockwave Flash' ];
                version = version.description;
            } catch ( ex ) {
                try {
                    version = new ActiveXObject('ShockwaveFlash.ShockwaveFlash')
                            .GetVariable('$version');
                } catch ( ex2 ) {
                    version = '0.0';
                }
            }
            version = version.match( /\d+/g );
            return parseFloat( version[ 0 ] + '.' + version[ 1 ], 10 );
        } )();
        
        var $uploader = $("#uploader");
        var $thelist = $("#thelist");
        var $progress = $(".t-upload-loading");
        var upload_file_url = $("#upload_file_url").val();

        if ( !WebUploader.Uploader.support('flash') && WebUploader.browser.ie ) {
            //没有安装flash
            if ( !flashVersion ) {
                $uploader.html('<a href="http://www.adobe.com/go/getflashplayer" target="_blank" border="0"><img alt="get flash player" src="http://www.adobe.com/macromedia/style_guide/images/160x41_Get_Flash_Player.jpg" /></a>');
            }
            return;
        } else if (!WebUploader.Uploader.support()) {
            Notify.danger('上传插件不支持您的浏览器，请您更换浏览器！',2);
            return;
        } else {
            var uploader = WebUploader.create({
                auto: true,//自动上传
                chunked:true,//分片上传
                chunkSize:5242880,//5M
                chunkRetry:2,//失败重试次数
                threads:3,//上传并发数
                fileNumLimit:1,//验证文件总数量, 超出则不允许加入队列
                formData:{ftype:'bbs'},
                swf: require.resolve("webuploader").match(/[^?#]*\//)[0] + "Uploader.swf?"+Math.random(),// swf文件路径
                server: upload_file_url,// 文件接收服务端。
                fileVal: 'file',
                pick: '#picker',// 选择文件的按钮。
                accept: {
                    title: 'excel',
                    extensions: 'xls,xlsx',
                    mimeTypes: 'application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
                }
            });

            // 当有文件添加进来的时候
            uploader.on( 'fileQueued', function( file ) {
                var progressHtml = '<div class="progress text-center">上传中</div>';
                $progress.html(progressHtml).show();
            });

            // 文件上传过程中创建进度条实时显示。
            uploader.on( 'uploadProgress', function( file, percentage ) {

                var per_cent = percentage*100;
                var progressHtml = '<div class="progress">\n\
                                        <div class="progress-bar" role="progressbar" style="width: '+per_cent.toFixed(1) + '%;">'+per_cent.toFixed(1) + '%</div>\n\
                                    </div>';
                $progress.html(progressHtml);
            });

            uploader.on( 'uploadError', function( file ) {
                $progress.html('').hide();
                Notify.danger('上传失败',2);
            });

            uploader.on( 'error', function( handler ) {});

            uploader.on( 'uploadSuccess', function( file, response ) {
                $progress.html('').hide();
                if (response.status) {
                    Notify.success('上传成功',1);
                    $("#file_name").val(response.data.filesrc);
                    $thelist.html(file.name+'<a class="mlm" href="javascript:void(0);">删除</a>');
                } else {
                    Notify.danger(response.data,2);
                }
            });

            $thelist.on('click', 'a', function(){

                if(!confirm("确定删除此文件么？"))
                {
                    return false;
                }
                $thelist.html('');
                $("#file_name").val('');
                uploader.reset();
            });
        }
        
        
    };

});