define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    require('./course-layout-reload');
    require('../class/student_manage').run();
    var NProgress = require('nprogress');

    exports.run = function (){

        $(".quit_course_class").on('click',function(){
           if(confirm("确定退出该班级吗？")){
               $.get($(this).data('url'),{},function(data, status){
                    data = $.parseJSON(data);
                    if(data.error == 0){
                        Notify.success(data.message,3);
                        $("#nav-content li.active a").trigger("click");
                    }else{
                        Notify.danger(data.message,3);
                    }
               });
           }
        });

        $(document).on('click', '.dropdown-menu a.loadByAjax', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            reloadPage(url,'#panel-main-box','.main-body');
        });

        var reloadPage = function (url,container,block) {
            NProgress.start();
            if ( url.length > 0 ) {
                $(container).load(url + ' ' + block, {}, function () {
                    NProgress.done();
                })
            }
        };
    }

});
