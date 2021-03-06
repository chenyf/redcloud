define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    require('common/validator-rules').inject(Validator);
    var Notify = require('common/bootstrap-notify');
    require('ckeditor');
    require('jquery.select2-css');
    require('jquery.select2');
    exports.run = function() {
        var $form = $("#daily-create-form");
        var $modal = $('#daily-create-form').parents('.modal');
        var editor = CKEDITOR.replace('content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#content').data('imageUploadUrl')
        });
        var validator = new Validator({
            element: '#daily-create-form',
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return false;
                }
                $('#daily-create-btn').button('submiting').addClass('disabled');
                $form.find('#content').val(editor.getData());
                $.post($form.attr('action'), $form.serialize(), function(info) {
                  //  console.info(typeof info);
                      if(info["status"] ==1000){
                            $modal.modal('hide');
                           Notify.success(info["msg"]);
                           window.location.reload();
                      }else{
                            $('#daily-create-btn').button('submiting').removeClass('disabled');
                             $('#daily-create-btn').button('submiting').text('提交');
                             Notify.info(info["msg"]);
                             return false;
                      }
                }).error(function() {
                    Notify.danger('添加失败');
                },'json');

            }
        });
//        validator.addItem({
//            element: '[name="title"]',
//            required: true,
//            rule: 'byte_minlength{min:5} byte_maxlength{max:100}'
//        });
        /**
         * 管理员选择分类
         * @author fubaosheng 2015-04-29
         */
        $('#ccpeople').select2({
            ajax: {
                url: '/Wclass/Group/getCCPeopleAction',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page_limit: 10,
                        msg_type :$("[name='msgtype']:checked").val(),
                        classId :$(":input[name='groupId']").val(),
                    };
                },
                results: function(data) {
                    var results = [];
                    $.each(data, function(index, item) {
                        results.push({
                            id: item.id,
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
                $(element.val().split(",")).each(function() {
                    data.push({
                        id: this,
                        name: this
                    });
                });
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
            placeholder: "请填写抄送人",
            width: 'off',
                    multiple: true,
                    createSearchChoice: function() {
                return null;
            },
            maximumSelectionSize: 20
        });
        $form.find(".selectRecRange").on("click", function() {

            if ($form.find(".chooseTree").hasClass("hide")) {
                $form.find(".chooseTree").removeClass("hide");
                $(this).text("收起");
            } else {
                $form.find(".chooseTree").addClass("hide");
                $(this).text("选择");
            }
        });
        $form.find(".cateMenu").click("click", function() {
            // $("input:checkbox[class='classList']").attr("checked",false);
            var subCateMenuObj = $(this).parent("span").parent("li").find(".subCateMenu");
            subCateMenuObj.toggleClass("hide");
            $form.find(".cateMenu").parent("span").removeClass("backgroundColor");
            $(this).parent("span").addClass("backgroundColor");
            var parentId = $(this).data("parentid");
            var perm = $(this).data("perm");
            var id = $(this).data("id");
            var show = $(this).data("show");
            var arr = new Array();
            $(".classList").parent("label").parent("li").addClass("hide");
            if (parentId == 0) {
                $(".teacherList").parent("label").parent("li").addClass("hide");
            }
            if (parentId == 0 && perm == 1) {
                $(".cateMenu[data-parentid='" + id + "']").each(function(i, item) {
                    var id = $(this).data("id");
                    arr.push(id);
                });
                arr.push(id);
                for (var i in arr) {
                    $(".classList[data-categoryid='" + arr[i] + "']").parent("label").parent("li").removeClass("hide");
                }
                $(".teacherList[data-topcateid='" + id + "']").parent("label").parent("li").removeClass("hide");

            } else if (parentId != 0 && perm == 1) {
                $(".classList[data-categoryid='" + id + "']").parent("label").parent("li").removeClass("hide");
            }
        })
        $form.find("[name='selectSendType']").on("change",function(){
            var value = $(this).val();
            if($(this).prop('checked') === true){
                if(value == 2){
                    $(".roleListGroup").removeClass("hide");
                }else{
                    $(".roleListGroup").addClass("hide");
                }
            }
        })
        $form.find(":radio[name='msgtype']").on("click",function(){
            $(".select2-search-choice-close").trigger("click");
             var msgtype = $(this).val();
            if(msgtype == 1){
                $(".titleFormGroup,.contentFormGroup").addClass("hide");
                $(".msgFormGroup").removeClass("hide");
                $(".selectSendTypeGroup").addClass("hide");
                $(".roleListGroup").removeClass("hide");
            }else if(msgtype ==3){
                $(".titleFormGroup").removeClass("hide");
                $(".contentFormGroup").addClass("hide");
                $(".msgFormGroup").removeClass("hide");
                $(".selectSendTypeGroup").removeClass("hide");
                var selectSendTypeObj = $form.find("[name='selectSendType']:checked");
                if(selectSendTypeObj.val() == 1){
                     $(".roleListGroup").addClass("hide");
                }else{
                     $(".roleListGroup").removeClass("hide");
                }
                
            }else{
                 $(".titleFormGroup,.contentFormGroup").removeClass("hide");
                $(".msgFormGroup").addClass("hide");
                $(".selectSendTypeGroup").addClass("hide");
                $(".roleListGroup").removeClass("hide");
            }
        })
        
        /* 点击删除已选择的班级
         * @author zhaozuowu 2015-05-05
         */
        window.delClass = function(obj) {
            var type = $(obj).attr("type");
            var rolesObj = $form.find(".student-input_res");
            var liobj = $(obj).parent("li");
            var classId = liobj.data("classid");
            $form.find(".classList[value='" + classId + "']").prop('checked', false);
            liobj.remove();
            jisuanClassNum();
          

        };
        /**
         * 计算班级人数
         * @returns {undefined}
         */
         window.jisuanClassNum =function(){
              var studentnum = 0;
            var checkedObj = $form.find(".classList:checked");
            var classnum = checkedObj.length;
            checkedObj.each(function(i, item) {
                studentnum += $(item).data("studentnum");
            });
            $(".classNumSpan em").text(classnum);
            $(".studentNumSpan em").text(studentnum);
        }
        /**
         * 计算老师人数
         * @returns {undefined}
         */
         window.jisuanTeacherNum = function(){
            var checkedObj = $form.find(".teacherList:checked");
            var teachernum = checkedObj.length;
            $(".teacherNumSpan em").text(teachernum);
         }
        /**
         *点击删除已选择的老师
         * @param {type} obj
         * @returns {undefined}
         */
        window.delTeacher = function(obj) {
            var rolesObj = $form.find(".student-input_res");
            var liobj = $(obj).parent("li");
            var teacherid = liobj.data("teacherid");
            $form.find(".teacherList[value='" + teacherid + "']").prop('checked', false);
            liobj.remove();
            jisuanTeacherNum();
        };
        /**
         * 选择班级
         * @author fubaosheng 2015-04-29
         */
        $form.find('.classList').on("change", function() {
            var rolesObj = $form.find(".student-input_res");
            var classId = $(this).val();
            rolesObj.find("li[data-classid='" + classId + "']").remove();
            var pid = $(this).data("categoryid");
            var classId = $(this).val();
            if ($(this).prop('checked') === true) {
                var str = "";
                var idStr = "";
                var obj = $form.find("[class='cateMenu'][data-id=" + pid + "]");
                    if (obj.data("parentid") == 0) {
                    str += obj.text();
                } else {
                    var ppid = obj.data("parentid");
                    var pObj = $form.find("[class='cateMenu'] [data-id=" + ppid + "]");
                    str += pObj.text();
                    str += ":";
                    str += obj.text();

                }
                str += ":";
                //  str+= ":"; <li><i>×</i><em>信息学院：计算机系：网络工程1班</em></li>
                str += $(this).parent().text();
                rolesObj.html(rolesObj.html() + "<li data-classId='" + classId + "'><i onclick='delClass(this)'>×</i>" + str + "</li>");
            }
           jisuanClassNum();
        });
        $form.find('.teacherList').on("change", function() {
            var teacherInputObj = $form.find(".teacher-input_res");
            var teacherId = $(this).val();
            teacherInputObj.find("li[data-teacherid='" + teacherId + "']").remove();
            var pid = $(this).data("topcateid");
            var str = "";
            if ($(this).prop('checked') === true) {
                str += $(this).parent().text();
                teacherInputObj.html(teacherInputObj.html() + "<li data-teacherid='" + teacherId + "'><i onclick='delTeacher(this)'>×</i>" + str + "</li>");
            }
           jisuanTeacherNum();
           
        });
        $form.find(".reception-rest").on("click",function(){
            $(".classList").attr("checked",false);
            $(".teacherList").attr("checked",false);
            jisuanTeacherNum();
            jisuanClassNum();
           var studentInputObj = $form.find(".student-input_res");
           var teacherInputObj = $form.find(".teacher-input_res");
            studentInputObj.find("li").remove();
            teacherInputObj.find("li").remove();
            $form.find(".chooseTree").addClass("hide");
           $form.find(".selectRecRange").text("选择");
        })
        $form.find(".reception-save").on("click",function(){
             $form.find(".chooseTree").addClass("hide");
             $form.find(".selectRecRange").text("选择");
            var classnum = $(".classNumSpan em").text();
            var studentnum =  $(".studentNumSpan em").text();
            var teachernum =  $(".teacherNumSpan em").text();
            Notify.success("一共选择了"+classnum+"个班级"+","+studentnum+"名学员,"+teachernum+",位教师");
        })

    };

});