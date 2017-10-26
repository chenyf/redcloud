define(function (require, exports, module) {
    var Notify = require('common/bootstrap-notify');
    var NProgress = require('nprogress');

    require('./student_manage').run();

    NProgress.configure({showSpinner : true});

    exports.run = function () {

        $(document).on('click', '.dropdown-menu a.loadByAjax', function (event) {
            event.preventDefault();
            var url = $(this).attr('href');
            reloadPage(url,'.class-manage-panel','.class-student-panel');
        });

        $('.delete-class').on('click',function(){
            var stuNum = parseInt($(this).data('stunum'));
            var courseNum = parseInt($(this).data('coursenum'));

            var $this = $(this);
            var url = $(this).data('url');

            var askMsg = "改班级下有"+stuNum+"个学生，有"+courseNum+"个课程与之绑定。确定删除该班级吗？";
            if(confirm(askMsg)){
                $.post(url,{},function(result){
                    result = $.parseJSON(result);
                    if(result.status){
                        Notify.success("删除班级成功！",2);
                        $this.parents('tr').remove();
                    }else{
                        Notify.danger(result.message,2);
                    }
                }).error(function(){
                    Notify.danger("发生错误！",2);
                });
            }
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