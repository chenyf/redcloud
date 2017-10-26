define(function(require, exports, module) {

    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('colorpicker/colorpicker');
    require('common/validator-rules').inject(Validator);
    require('bootstrap.datetimepicker');
    require('jquery.form');

    exports.run = function() {
        
        $("#powerFile").on("change",function(){
                    if($(this).val() =='') return false;
                    var webcode = $(this).data("webcode");
                    var schid = $(this).data("schid");
                $("#app-base-from").attr('action','/Center/SchoolCourse/powerFileAction/webcode/'+webcode+'/schid/'+schid);  
                var options = {   
                     dataType:  'json',
                     beforeSend: function() {
//                             
                     },
                     uploadProgress: function(event, position, total, percentComplete) {
//                            
                     },
                     success: function(data) {
                                $("#app-base-from").attr('action',''); 
                                if (data.status == false) {
                                    Notify.danger(data.message);
                                    return false;
                                }
                                Notify.success(data.message);
                                $("#powerBook").val(data.filePath);
                                $("#powerFile").addClass('hide');
                                $('#power-show .power-name').text(data.fileName);
                                $('#power-show .power-name').attr('data-href',data.filePath);
                                $('#power-show .power-dow').attr('data-href',data.filePath);
                                $('#power-del').attr('data-filepath',data.filePath);
                                $("#power-show").removeClass('hide');
                                dowloadFile(); 
                     },
                     error:function(xhr){
                            $("#app-base-from").attr('action',''); 
                             Notify.danger('文件上传失败');
                     }
                  };
              // 将options传给ajaxSubmit
                $("#app-base-from").ajaxSubmit(options);
                });

        $('#signDate').datetimepicker({
            locale: 'zh_cn',
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    
        var delLoak = 1;
        $('.delAppversion').on('click', function() {
            if (!confirm("确定要删除此课程吗")) 
                    return false;
            var url = $(this).data('url');
            var id = $(this).data('id');
            if(delLoak != 1) return false;
            delLoak = 2 ;
            $.ajax({
                url: url,
                type: 'post',
                data: {id : id },
                dataType: 'json',
                success: function(data) {
                    delLoak = 1;
                    if (data.status == false) {
                        Notify.danger(data.message);
                        return false;
                    }
                    Notify.success(data.message);
                    window.location.reload();
                    }

            })
            return false;
        });
        
        var delFileLoak = 1;
        $('#power-del').on('click', function() {
            if (!confirm("确定要删除此授权书？")) 
                    return false;
            var url = $(this).data('url');
            var filepath = $(this).data('filepath');
            if(delFileLoak != 1) return false;
            delFileLoak = 2 ;
            $.ajax({
                url: url,
                type: 'post',
                data: {filepath : filepath },
                dataType: 'json',
                success: function(data) {
                    delFileLoak = 1;
                    if (data.status == false) {
                        Notify.danger(data.message);
                        return false;
                    }
                    Notify.success(data.message);
                    window.location.reload();
                    }

            })
            return false;
        });
        var dowloadFile = function (){
            var dowLock = 1 ;
            $('#power-show .power-dow').on('click', function() {
                var url = $(this).data('url');
                var filepath = $(this).data('href');
                window.open(filepath ,'_self');
                if(dowLock != 1) return false;
                dowLock = 2 ;
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {filepath : filepath },
                    dataType: 'json',
                    success: function(data) {
                       dowLock = 1;
                        }

                })
                return false;
            });
            
            var nameLock = 1 ;
            $('#power-show .power-name').on('click', function() {
                var url = $(this).data('url');
                var filepath = $(this).data('href');
                window.open(filepath ,'_self');
                if(nameLock != 1) return false;
                nameLock = 2 ;
                $.ajax({
                    url: url,
                    type: 'post',
                    data: {filepath : filepath },
                    dataType: 'json',
                    success: function(data) {
                       nameLock = 1 ;
                        }

                })
                return false;
            });  
        }
       dowloadFile(); 

        $(function() {
            $("#appMainColor").colorpicker({
                fillcolor: true,
                event: 'click',
                target: $("#themeFontColor")
            });
        });

        $(function() {
            $("#appSubColor").colorpicker({
                fillcolor: true,
                event: 'click',
                target: $("#themeBackColor")
            });
        });


    };
});