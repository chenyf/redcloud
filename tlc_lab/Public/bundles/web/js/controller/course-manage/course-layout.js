define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
                 require('./course-layout-reload');


    $('#t-toggle').on('click',function(){
        if($(this).find('i').hasClass('fa-angle-double-down')){
            $(this).find('i').addClass('fa-angle-double-up').removeClass('fa-angle-double-down');
        }else{
            $(this).find('i').addClass('fa-angle-double-down').removeClass('fa-angle-double-up');
        }
    });
    /**
     * 编辑课程标题
     * @author fubaosheng 2015-10-25
     */
    $(document).on("mouseenter",".t-course-name",function(){
        if($(this).find("input").is(":hidden"))
            $(this).find(".c-icon-edit").show();
    })
    $(document).on("mouseleave",".t-course-name",function(){
        $(this).find(".c-icon-edit").hide();
    })
    $(document).on("click",'.c-text-name .c-icon-edit',function(){
        $(this).siblings("input").show().focus().val($(this).siblings("span").text());
        $(this).siblings("span").hide();
        $(this).hide();
        return false;
    });
    $(document).on("blur",'.t-course-edit-top .c-text-name input',function(){
        $(this).hide();
        $(this).siblings("span").show();
        $(this).siblings(".c-icon-edit").hide();
        var inputName = $(this).val();
        var spanName = $(this).siblings("span").text();
        if(!inputName || (inputName == spanName) ){
            return false;
        }
        var url = $(this).data('url');
        var id = $(this).data('id');
        var _this = $(this);
        var len = Number(inputName.length);
        if(len<1 || len> 60){
            Notify.danger("课程名称的长度范围为1~60个字符");
            return false;
        }
        $.post(url,{id:id,title:inputName},function(result){
            if(result["status"] == "success"){
                _this.val(inputName);
                _this.siblings("span").text(inputName);
                $(".c-panel-heading").children("span").text(inputName);
                $("#course_title").val(inputName);
            }else{
                Notify.danger(result["info"]);
            }
        },'json');
    })

    /**
     * 发布/关闭课程
     * @author fubaosheng 2015-10-25
     */
    $(document).on('click','#courseStatus',function(){
        var self = $(this);
        var publish = parseInt(self.data('publish'));
        var publishurl = $("#publishurl").val();
        var closeurl = $("#closeurl").val();
        var str = publish ? "您真的要关闭该课程吗？" : "您真的要发布该课程吗？";
        if( !confirm(str)){
            return ;
        }
        var url = publish ? closeurl : publishurl;
        $.post(url, function() {
            var success =  publish ? "课程关闭成功！" : "课程发布成功！";
            Notify.success(success);
            var html = publish ? "发布课程" : "关闭课程";
            self.html(html);
            publish = publish ? 0 : 1;
            self.data("publish",publish);
        });
        return false;
    })
    
    $("#course-category-name em").each(function(){
        var top = $(this).offset().top;
        var height = $(this).height();
        var y = parseInt(top+height);
        var parentTop = $(this).parent("#course-category-name").offset().top;
        var parentHeight = $(this).parent("#course-category-name").height();
        var parentY = parseInt(parentTop+parentHeight);
        if(y > parentY){
            $(this).nextAll(".more-specialty").children("span").show();
            return false;
        }
    })

    function getIeVesion() {
        var browser = navigator.appName
        var b_version = navigator.appVersion
        if(b_version.indexOf(';') < 0){
            return 'firefox';
        }
        var version = b_version.split(";");
        var trim_Version = version[1].replace(/[ ]/g, "");
        if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE6.0")
        {
            return "IE 6.0";
        }
        else if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE7.0")
        {
            return "IE 7.0";
        }
        else if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE8.0")
        {
            return "IE 8.0";
        }
        else if (browser == "Microsoft Internet Explorer" && trim_Version == "MSIE9.0")
        {
            return "IE 9.0";
        }
    }
    /*
     * IE 9以下取消无刷新功能
     */
    var ie = getIeVesion();
    if (ie == 'IE 6.0' || ie == 'IE 7.0' || ie == 'IE 8.0' || ie == 'IE 9.0' || ie == 'firefox') {
        $('.t-course-set-tit').find('a').removeClass('ajaxLoad');
    }

});