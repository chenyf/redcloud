define(function(require, exports, module) {

    var Notify = require('common/bootstrap-notify');
    require('jquery.select2-css');
    require('jquery.select2');
    
    exports.run = function() {
        var $form = $("#user-roles-form"),
            isTeacher = $form.find('input[value=ROLE_TEACHER]').prop('checked'),
            currentUser = $form.data('currentuser'),
            editUser = $form.data('edituser');

        if (currentUser == editUser) {
            $form.find('input[value=ROLE_SUPER_ADMIN]').attr('disabled', 'disabled');
        };
        
         /**
         * 获取管理员分类
         * @author fubaosheng 2015-04-28
         */
         $('#admin-category').select2({
            ajax: {
                url: '/Home/Category/topCategoryAction/#',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page_limit: 10
                    };
                },
                results: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            index: item.id,
                            id: item.name,
                            name: item.name
                        });
                    });
                    return {
                        results: results
                    };
                }
            },
            initSelection: function(element, callback) {
                var data = [];
                var adminCategoryVal = $('#admin-category').val();
                if( adminCategoryVal.indexOf(',') == -1 )
                    var categoryArr = [adminCategoryVal];
                else
                    var categoryArr = adminCategoryVal.split(",");
                for(var q in categoryArr){
                    data.push({
                        id : categoryArr[q],
                        name : categoryArr[q]
                    })
                }
                var adminCategoryDataVal = $('#admin-category').data('val');
                adminCategoryDataVal = adminCategoryDataVal.toString();
                if( adminCategoryDataVal.indexOf(',') == -1 )
                    var indexArr = [adminCategoryDataVal];
                else
                    var indexArr = adminCategoryDataVal.split(",");
                for(var i in indexArr){
                    data[i]['index'] = indexArr[i];
                }
                callback(data);
            },
            formatSelection: function(item) {
                return item.name;
            },
            formatResult: function(item) {
                return item.name;
            },
            width: 'off',
            multiple: true,
            maximumSelectionSize: 20,
            placeholder: "请输入标签",
            createSearchChoice: function() {
                return null;
            },
        });
        $('#admin-category').on("change", function(e) { 
            var rolesObj = $form.find(".c-role-result .c-role-con");
            var adminObj = $form.find('input[value=ROLE_ADMIN]');
            if( typeof(e.added) !== "undefined" ){
               var str = "";
               var values = $('#admin-category').data("val").toString();
               if(values) str = ",";
               str+= e.added.index;
               values+=str;
               $('#admin-category').data("val",values);
               rolesObj.find("[type=admin]").parent().remove();
               adminObj.prop('checked',true);
               rolesObj.html(rolesObj.html()+"<span><i type='admin' onclick='delRole(this)'>×</i>管理员</span>");
            }
            if( typeof(e.removed) !== "undefined" ){
                var dataVal = $('#admin-category').data("val").toString();
                if( dataVal.indexOf(',') == -1 )
                    var indexArr = [dataVal];
                else
                    var indexArr = dataVal.split(",");
                for(var i in indexArr){
                    if(e.removed.index == indexArr[i]){
                       indexArr.splice(i,1);
                    }
                }
                var str = indexArr.join();
                $('#admin-category').data("val",str);
                if(!str){
                    rolesObj.find("[type=admin]").parent().remove();
                    adminObj.prop('checked',false);
                }
            }
        });
        
        
        /**
         * 切换系统默认角色和自定义角色
         * @author fubaosheng 2015-04-28
         */
        $form.find(".nav-tabs").find("li").on('click',function(){
            $form.find(".nav-tabs").find("li").removeClass("active");
            $(this).addClass("active");
            var type = $(this).attr("type");
            $form.find('.choose-role').children("div").hide();
            $form.find('.choose-role').children("."+type).show();
        });
        
        /**
         * 选择分类切换角色
         * @author fubaosheng 2015-04-28
         */
        $form.find('.c-customs-box').find("select.form-control").on('change',function(){
            var id = $(this).val();
            $form.find('.c-customs-box').find("div.c-customs-con").find("ul").hide();
            $form.find('.c-customs-box').find("div.c-customs-con").find(".categery-"+id).show();
        });

        $form.find('input[value=ROLE_USER]').on('change', function(){
            if ($(this).prop('checked') === false) {
                $(this).prop('checked', true);
                var user_name = $('#change-user-roles-btn').data('user') ;
                Notify.info('用户必须拥有'+user_name+'角色');
            }
        });
        
        /* 点击删除已选择的角色
         * @author fubaosheng 2015-04-29
         */
        window.delRole = function(obj){
            var type = $(obj).attr("type");
            var rolesObj = $form.find(".c-role-result .c-role-con");
            if(type == "user"){
                $form.find('input[value=ROLE_USER]').prop('checked', true);
                var user_name = $('#change-user-roles-btn').data('user') ;
                Notify.info('用户必须拥有'+user_name+'角色');
            }else if(type == "super_admin"){
                $form.find('input[value=ROLE_SUPER_ADMIN]').prop('checked', false);
                rolesObj.find("[type=super_admin]").parent().remove();
            }else if(type == "gold_admin"){
                $form.find('input[value=ROLE_GOLD_ADMIN]').prop('checked', false);
                rolesObj.find("[type=gold_admin]").parent().remove();
            }else if(type == "market"){
                $form.find('input[value=ROLE_MARKET]').prop('checked', false);
                rolesObj.find("[type=market]").parent().remove();
            }else if(type == "teacher"){
                $form.find('input[value=ROLE_TEACHER]').prop('checked', false);
                $('#course_categoryId').val("0");
                rolesObj.find("[type=teacher]").parent().remove();
            }else if(type == "admin"){
                $form.find('input[value=ROLE_ADMIN]').prop('checked', false);
                $('#admin-category').val("");
                $('#admin-category').data("val","");
                $(".select2-choices .select2-search-choice").remove();
                rolesObj.find("[type=admin]").parent().remove();
            }else{
                $form.find(".defineRole[data-type='"+type+"']").prop('checked', false);
                rolesObj.find("[type='"+type+"']").parent().remove();
            }
        };

        /**
         * 选择神管 
         * @author fubaosheng 2015-07-13
         */
        $form.find('input[value=ROLE_GOLD_ADMIN]').on('change', function(){
            var rolesObj = $form.find(".c-role-result .c-role-con");
            rolesObj.find("[type=gold_admin]").parent().remove();
            if($(this).prop('checked') === true){
                rolesObj.html(rolesObj.html()+"<span><i type='gold_admin' onclick='delRole(this)'>×</i>神管</span>");
            }
        });
        
        /**
         * 选择大客户
         * @author fubaosheng 2015-07-30
         */
        $form.find('input[value=ROLE_MARKET]').on('change', function(){
            var rolesObj = $form.find(".c-role-result .c-role-con");
            rolesObj.find("[type=market]").parent().remove();
            if($(this).prop('checked') === true){
                rolesObj.html(rolesObj.html()+"<span><i type='market' onclick='delRole(this)'>×</i>大客户</span>");
            }
        });

        /**
         * 选择超级管理员
         * @author fubaosheng 2015-04-29
         */
        $form.find('input[value=ROLE_SUPER_ADMIN]').on('change', function(){
            var rolesObj = $form.find(".c-role-result .c-role-con");
            rolesObj.find("[type=super_admin]").parent().remove();
            if($(this).prop('checked') === true){
                rolesObj.html(rolesObj.html()+"<span><i type='super_admin' onclick='delRole(this)'>×</i>超级管理员</span>");
            }
        });
        
        /**
         * 选择教师
         * @author fubaosheng 2015-04-29
         */
         $form.find('input[value=ROLE_TEACHER]').on('change', function(){
             var rolesObj = $form.find(".c-role-result .c-role-con");
             rolesObj.find("[type=teacher]").parent().remove();
             if($(this).prop('checked') === true ){
                 var categoryId = $('#course_categoryId').val();
                 if(categoryId == "0"){
                    Notify.info('请选择教师归属院/系');
                 }else{
                    rolesObj.html(rolesObj.html()+"<span><i type='teacher' onclick='delRole(this)'>×</i>教师</span>");
                 }
             }else{
                 $('#course_categoryId').val("0");
             }
         });
        $("#course_categoryId").on("change",function(){
            var categoryId  = $(this).val();
            var teacherObj = $form.find('input[value=ROLE_TEACHER]');
            var rolesObj = $form.find(".c-role-result .c-role-con");
            rolesObj.find("[type=teacher]").parent().remove();
            if(categoryId == "0"){
                if(teacherObj.is(":checked") === true){
                    Notify.info('请选择教师归属院/系');
                }
            }else{
                if(teacherObj.is(":checked") === false){
                     teacherObj.prop('checked', true);
                }
                rolesObj.html(rolesObj.html()+"<span><i type='teacher' onclick='delRole(this)'>×</i>教师</span>");
            }
         });
         
        /**
         * 选择管理员
         * @author fubaosheng 2015-04-29
         */
        $form.find('input[value=ROLE_ADMIN]').on('change', function(){
            var rolesObj = $form.find(".c-role-result .c-role-con");
            rolesObj.find("[type=admin]").parent().remove();
            if($(this).prop('checked') === true ){
                if($(".select2-choices .select2-search-choice").size()<1){
                    $('#admin-category').select2("open");
                    Notify.info('请选择分类');
                }else{
                    rolesObj.html(rolesObj.html()+"<span><i type='admin' onclick='delRole(this)'>×</i>管理员</span>");
                }
            }else{
                $('#admin-category').val("");
                $('#admin-category').data("val","");
                $(".select2-choices .select2-search-choice").remove();
            }
        });
         
        /**
         * 选择自定义角色
         * @author fubaosheng 2015-04-29
         */
         $form.find('.defineRole').on("change",function(){
             var rolesObj = $form.find(".c-role-result .c-role-con");
             var type = $(this).data("type");
             rolesObj.find("[type='"+type+"']").parent().remove();
             var pid = $(this).data("pid");
             if($(this).prop('checked') === true ){
                var str = "";
                str+= $form.find("[name=define-category] [value="+pid+"]").text();
                str+= "/";
                str+= $(this).parent().text();
                rolesObj.html(rolesObj.html()+"<span><i type='"+type+"' onclick='delRole(this)'>×</i>"+str+"</span>");
             }
         });
         
        /**
         * 设置用户角色
         * @author fubaosheng 2015-04-29
         */
        $form.on('submit', function() {
            var roles = [];
            var defineRoles = {};
            
            var $modal = $('#modal');
            
            var userObj = $form.find('input[value=ROLE_USER]');
            if( userObj.prop('checked') === false ){
                var user_name = $('#change-user-roles-btn').data('user') ;
                Notify.danger('用户必须拥有'+user_name+'角色');
                return false;
            }
            
            var teacherObj = $form.find('input[value=ROLE_TEACHER]');
            if( teacherObj.prop('checked') === true && $("#course_categoryId").val() == "0"){
                Notify.danger('请选择教师归属院/系');
                return false;
            }
            if( teacherObj.prop('checked') === false && $("#course_categoryId").val() != "0"){
                teacherObj.prop('checked',true);
            }
            
            var adminObj = $form.find('input[value=ROLE_ADMIN]');
            if( adminObj.prop('checked') === true && $(".select2-choices .select2-search-choice").size()<1){
                $('#admin-category').select2("open");
                Notify.danger('请选择分类');
                return false;
            }
            if( adminObj.prop('checked') === false && $(".select2-choices .select2-search-choice").size()>0){
                adminObj.prop('checked',true);
            }
            
            $form.find('input[name="roles[]"]:checked').each(function(){
                roles.push($(this).val());
            });
            $form.find('input[name="defineRoles[]"]:checked').each(function(){
                var pid = parseInt($(this).data("pid"));
                var id = parseInt($(this).val());
                if(!$.isArray(defineRoles[pid]))  defineRoles[pid] = [];
                defineRoles[pid].push(id);
            });
            
            var defineRolesNum = 0;
            for(var i in defineRoles){
                defineRolesNum++;
            }
            
            var data = {
                roles : roles,
            };
            if( teacherObj.prop('checked') === true ){
                data.teacherCategoryId = parseInt($("#course_categoryId").val());
            }
            if( adminObj.prop('checked') === true ){
                var adminCategoryIds = $("#admin-category").data('val');
                if(adminCategoryIds)
                    data.adminCategoryIds = adminCategoryIds;
            }
            if( defineRolesNum>0 )
                data.defineRoles = defineRoles;
            
            $form.find('input[value=ROLE_SUPER_ADMIN]').removeAttr('disabled');
            $('#change-user-roles-btn').button('submiting').addClass('disabled');
            $.post($form.attr('action'), data, function(html) {

                $modal.modal('hide');
                Notify.success('角色保存成功');
                var $tr = $(html);
                $('#' + $tr.attr('id')).replaceWith($tr);
            }).error(function(){
                Notify.danger('操作失败');
            });

            return false;
        });

	};

});