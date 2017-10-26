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
        window.sendTeacher = 'true';
        window.sendStudent = 'true';
        var editor = CKEDITOR.replace('content', {
            toolbar: 'Simple',
            filebrowserImageUploadUrl: $('#content').data('imageUploadUrl')
        });
        $(".modal-dialog").addClass("w-popupbox");
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
                    if (info["status"] == 1000) {
                        $modal.modal('hide');
                        Notify.success(info["msg"]);
                        window.location.reload();
                    } else {
                        $('#daily-create-btn').button('submiting').removeClass('disabled');
                        $('#daily-create-btn').button('submiting').text('提交');
                        Notify.info(info["msg"]);
                        return false;
                    }
                }).error(function() {
                    Notify.danger('添加失败');
                }, 'json');

            }
        });
//        validator.addItem({
//            element: '[name="title"]',
//            required: true,
//            rule: 'byte_minlength{min:5} byte_maxlength{max:100}'
//        });

         window.rangeType = function(){
            var type = 0;
            var msgtype = $("[name=msgtype]:checked").val();
            var selectSendType = $("[name=selectSendType]:checked").val();
            if($('#school-all').is(':checked')) type = 0;
            if($('#teacher').is(':checked')) type = 1;
            if($('#student').is(':checked')) type = 2;
            if(msgtype == "3" && selectSendType == '1') type = -1;
            $("[name=rangeType]").val(type);
        }
        rangeType();

        /**
         * 管理员选择分类
         * @author fubaosheng 2015-04-29
         */
        $('#ccpeople').select2({
            ajax: {
                url: '/Admin/MyStudents/getCCPeopleAction',
                dataType: 'json',
                quietMillis: 100,
                data: function(term, page) {
                    return {
                        q: term,
                        page_limit: 10,
                        msg_type: $("[name='msgtype']:checked").val(),
                        classId: $(":input[name='groupId']").val(),
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
        
        window.changeType = function(){
            $form.find(".check-no").unbind('click');
            $form.find(".check-no").on("click", function() {
                var emCheckbox = $(this);
                var nextObj = emCheckbox.next("a");
                var parentId = nextObj.data("parentid");
                var perm = nextObj.data("perm");
                var id = nextObj.data("id");
                //var show = nextObj.data("show");
                var arr = new Array();
                if (emCheckbox.hasClass("check-all")) { /*click all select*/
                    emCheckbox.removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    if(parentId == 0 ){
                        $(this).parent("span").next().find("em").removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    }
                    $(".cateMenu[data-parentid='" + id + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    if (parentId != 0) {
                        $(".cateMenu[data-id='" + parentId + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-portion");
                        var totalLength = $(".cateMenu[data-parentid='" + parentId + "']").length;
                        var checkedLength = $(".cateMenu[data-parentid='" + parentId + "']").prev("em[class*='check-all']").length;
                        if (checkedLength == 0)
                            $(".cateMenu[data-id='" + parentId + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    }
                } else if (emCheckbox.hasClass("check-portion")) { /*click part select*/
                    emCheckbox.removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    if(parentId == 0 ){
                        $(this).parent("span").next().find("em").removeClass("check-all").removeClass("check-portion").addClass("check-no");
                    }
                    $(".cateMenu[data-parentid='" + id + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-no");
                } else {/*click empty select*/
                    emCheckbox.removeClass("check-all").removeClass("check-portion").addClass("check-all");
                    if(parentId == 0 ){
                        $(this).parent("span").next().find("em").removeClass("check-all").removeClass("check-portion").addClass("check-all");
                    }
                    $(".cateMenu[data-parentid='" + id + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-all");
                    if (parentId != 0) {
                        $(".cateMenu[data-id='" + parentId + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-portion");
                        var totalLength = $(".cateMenu[data-parentid='" + parentId + "']").length;
                        var checkedLength = $(".cateMenu[data-parentid='" + parentId + "']").prev("em[class*='check-all']").length;
                        if (checkedLength == totalLength)
                            $(".cateMenu[data-id='" + parentId + "']").prev("em").removeClass("check-all").removeClass("check-portion").addClass("check-all");
                    }
                }
                
                $(".classList").parent("label").parent("li").addClass("hide");
                if (parentId == 0) {
                    $(".cateMenu[data-parentid='" + id + "']").each(function(i, item) {
                        var id = $(this).data("id");
                        arr.push(id);
                    });
                    for (var i in arr){
                        $(".cateMenu[data-parentid='" + arr[i] + "']").each(function(i, item) {
                        var id = $(this).data("id");
                            arr.push(id);
                        });
                    }
                    arr.push(id);
                    var bigChecked = emCheckbox.hasClass("check-all");
                    for (var i in arr) {
                        $(".classList[data-categoryid='" + arr[i] + "']").prop("checked", false);
                        $(".classList[data-categoryid='" + arr[i] + "']").parent("label").parent("li").removeClass("hide");
                        if (bigChecked && window.sendStudent == 'true')
                            $(".classList[data-categoryid='" + arr[i] + "']").prop("checked", true);
                    }
                    
                   $(".teacherList[data-topcateid='" + id + "']").prop("checked", false);
                   if (bigChecked && window.sendTeacher == 'true')
                   $(".teacherList[data-topcateid='" + id + "']").prop("checked", true);
                   $(".teacherList").parent("label").parent("li").addClass("hide");
                   $(".teacherList[data-topcateid='" + id + "']").parent("label").parent("li").removeClass("hide");
                }else{
                    if($(this).hasClass("check-all")){
                        var   aArr  = []; 
                        var   childId = [];
                        $(".classList[data-categoryid='" +id + "']").prop("checked", true);
                        var aArr = $(this).parent().next().find("a");
                        aArr.each(function(i, item){
                            childId.push($(item).data("id"));
                        })
                        for (var i = 0; i< childId.length; i++){
                            $(".classList[data-categoryid='" +childId[i] + "']").prop("checked", true);
                            $(".classList[data-categoryid='" + childId[i] + "']").parent("label").parent("li").removeClass("hide");
                        }
                      $(".teacherList[data-topcateid='" + id + "']").prop("checked", true);  
                  }else{
                      $(".classList[data-categoryid='" +id+ "']").prop("checked", false);
                      var dataId = nextObj.data("id");
                      var arr = emCheckbox.parent().next().find("a[data-parentid="+ dataId +"]");
                      var childId  = [];
                      arr.each(function(i, item){
                          childId.push($(item).data("id"));
                      })
                      for(var i = 0 ; i <  childId.length ; i ++ ){
                          $(".classList[data-categoryid='" +childId[i]+ "']").prop("checked", false);
                          $(".classList[data-categoryid='" + childId[i] + "']").parent("label").parent("li").removeClass("hide");
                      }
                     $(".teacherList[data-topcateid='" + id + "']").prop("checked", false);
                  }
                $(".classList[data-categoryid='" + id + "']").parent("label").parent("li").removeClass("hide");
                $(".classList[data-categoryid='" +parentId + "']").prop("checked", false);
                if( $(".cateMenu[data-id='" +parentId+ "']").prev("em").hasClass("check-all")){
                    $(".classList[data-categoryid='" +parentId + "']").prop("checked", true);
                }

    //                 $(".cateMenu[data-parentid='" +parentId+ "']").each(function(i, item) {
    //                   
    //                    var obj  = $(this);
    //                    var id = $(this).data("id");
    //                    
    //                    $(".classList[data-categoryid='" +id + "']").prop("checked", false);
    //                    if($(item).prev("em").hasClass("check-all")){
    //                          var   aArr  = []; 
    //                          var   childId = [];
    //                          $(".classList[data-categoryid='" +id + "']").prop("checked", true);
    //                          var aArr = obj.parent().next().find("a");
    //                          aArr.each(function(i, item){
    //                              childId.push($(item).data("id"));
    //                          })
    //                          for (var i = 0; i< childId.length; i++){
    //                              $(".classList[data-categoryid='" +childId[i] + "']").prop("checked", true);
    //                          }
    //                    }else{
    //                        var dataId = nextObj.data("id");
    //                        var arr = emCheckbox.parent().next().find("a[data-parentid="+ dataId +"]");
    //                        var childId  = [];
    //                        arr.each(function(i, item){
    //                            childId.push($(item).data("id"));
    //                        })
    //                        for(var i = 0 ; i <  childId.length ; i ++ ){
    //                            $(".classList[data-categoryid='" +childId[i]+ "']").prop("checked", false);
    //                        }
    //                    }
    //                   $(".classList[data-categoryid='" +parentId + "']").prop("checked", false);
    //                   if( $(".cateMenu[data-id='" +parentId+ "']").prev("em").hasClass("check-all")){
    //                        $(".classList[data-categoryid='" +parentId + "']").prop("checked", true);
    //                   }
    //                });
                }
                $(".classList").each(function(i, item) {
                    $(item).trigger("change");
                });
                $(".teacherList").each(function(i, item) {
                    $(item).trigger("change");
                });
            });
            $form.find(".cateMenu").unbind('click');
            $form.find('.classList').unbind('change');
            if(window.sendStudent == 'true'){
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
                        for (var i in arr){
                            $(".cateMenu[data-parentid='" + arr[i] + "']").each(function(i, item) {
                            var id = $(this).data("id");
                                arr.push(id);
                            });
                        }
                        arr.push(id);
                        for (var i in arr) {
                            $(".classList[data-categoryid='" + arr[i] + "']").parent("label").parent("li").removeClass("hide");
                        }
                        $(".teacherList[data-topcateid='" + id + "']").parent("label").parent("li").removeClass("hide");
                    } else if (parentId != 0 && perm == 1) {
                        $(".classList[data-categoryid='" + id + "']").parent("label").parent("li").removeClass("hide");
                        var childId=[];
                        var aArr = $(".cateMenu[data-parentid='" + id + "']");
                            aArr.each(function(i, item){
                                childId.push($(item).data("id"));
                            })
                            for (var i = 0; i< childId.length; i++){
                                $(".classList[data-categoryid='" + childId[i] + "']").parent("label").parent("li").removeClass("hide");
                            }
                    }
                });
                $form.find('.classList').on("change", function() {
                var rolesObj = $form.find(".student-input_res");
                var classId = $(this).val();
                rolesObj.find("li[data-classid='" + classId + "']").remove();
                var pid = $(this).data("categoryid");
                if ($(this).prop('checked') === true) {
                    var str = "";
                    var idStr = "";
                    var obj = $form.find("[class='cateMenu'][data-id=" + pid + "]");
                    if (obj.data("parentid") == 0) {
                        str += obj.text();
                        if (str != "")
                            str += ":";
                    } else {
                        var ppid = obj.data("parentid");
                        var pObj = $form.find("[class='cateMenu'][data-id=" + ppid + "]");
                        str += pObj.text();
                        if (str != "")
                            str += ":";
                        str += obj.text();
                        if (str != "")
                            str += ":";

                    }
                    //  str+= ":"; <li><i>×</i><em>信息学院：计算机系：网络工程1班</em></li>
                    str += $(this).parent().text();
                    rolesObj.html(rolesObj.html() + "<li data-classId='" + classId + "'><i onclick='delClass(this)'>×</i>" + str + "</li>");
                }
                jisuanClassNum();
            });
            }
            
            $form.find('.teacherList').unbind('change');
            if(window.sendTeacher == 'true'){
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
            }
        }
        
        changeType();
        
        $form.find("[name='selectSendType']").on("change", function() {
            var value = $(this).val();
            if ($(this).prop('checked') === true) {
                if (value == 2) {
                    $(".roleListGroup").removeClass("hide");
                    $("#copied").css("display","block");
                } else {
                    $(".roleListGroup").addClass("hide");
                    $("#copied").css("display","none");
                }
                rangeType();
            }
        })
        $form.find(":radio[name='msgtype']").on("click", function() {
            var msgtype = $(this).val();
            var createType = $('#msgtype-radios input[name="createType"]').val();
            
            $(".select2-search-choice-close").trigger("click");
            if (msgtype == 1) {
                $(".titleFormGroup,.contentFormGroup").addClass("hide");
                $(".msgFormGroup").removeClass("hide");
                $(".selectSendTypeGroup").addClass("hide");
                $(".roleListGroup").removeClass("hide");
            } else if (msgtype == 3) {
                $(".titleFormGroup").removeClass("hide");
                $(".contentFormGroup").addClass("hide");
                $(".msgFormGroup").removeClass("hide");
                $(".selectSendTypeGroup").removeClass("hide");
                var selectSendTypeObj = $form.find("[name='selectSendType']:checked");
                if (selectSendTypeObj.val() == 1) {
                    $(".roleListGroup").addClass("hide");
                } else {
                    $(".roleListGroup").removeClass("hide");
                }

            } else {
                $(".titleFormGroup,.contentFormGroup").removeClass("hide");
                $(".msgFormGroup").addClass("hide");
                $(".selectSendTypeGroup").addClass("hide");
                $(".roleListGroup").removeClass("hide");
            }
            if(createType != 'manager') {
                $.post("/Admin/MyStudents/teacherListAction", {msgtype:msgtype}, function(info) {
                        $('#teacher-all ul').html('');
                        $('#teacher-all ul').html(info.data);
                        rangeType();
                        if($('#school-all').is(':checked')) $form.find('#school-all').trigger('click');
                        if($('#teacher').is(':checked')) $form.find('#teacher').trigger('click');
                        if($('#student').is(':checked')) $form.find('#student').trigger('click');
                    }, 'json');
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
        
        /* 选择不同方式之前初始化
         * @author tanhaitao 2015-10-30
         */
         window.sendChangeInit = function(){
            $form.find('.check-no').removeClass("check-all").removeClass("check-portion");
            $form.find(".classList").prop('checked', false);
            $form.find(".teacherList").prop('checked', false);
            $form.find(".student-input_res").html('');
            $form.find(".teacher-input_res").html('');
            $(".classNumSpan em").text('0');
            $("[name=classNum]").val('0');
            $(".studentNumSpan em").text('0');
            $("[name=studentNum]").val('0');
            $(".teacherNumSpan em").text('0');
            $("[name=teacherNum]").val('0'); 
        }
        
        
        /* 点击只发送老师
         * @author tanhaitao 2015-10-30
         */
        $form.find('#teacher').on("click", function() {
            $form.find('.subCateMenu').addClass('hide');
            $form.find('.reception-class').addClass('hide');
            $form.find('.student-id').addClass('hide');
            
            $form.find('.reception-teacher').removeClass('hide');
            $form.find(".teacher-id").removeClass('hide');
            
            sendChangeInit();
            window.sendTeacher = 'true';
            window.sendStudent = 'false';
            changeType();
        });
        /* 点击只发送老师
         * @author tanhaitao 2015-10-30
         */
        $form.find('#student').on("click", function() {
            $form.find('.reception-teacher').addClass('hide');
            $form.find(".teacher-id").addClass('hide');
            $form.find('.reception-class').removeClass('hide');
            $form.find('.student-id').removeClass('hide');
            
            sendChangeInit();
            window.sendStudent = 'true';
            window.sendTeacher = 'false';
            changeType();
        });
        /* 点击发送全部
         * @author tanhaitao 2015-10-30
         */
        $form.find('#school-all').on("click", function() {
           $form.find('.reception-teacher').removeClass('hide');
           $form.find('.reception-class').removeClass('hide');
           $form.find('.student-id').removeClass('hide');
           $form.find(".teacher-id").removeClass('hide');
            
           sendChangeInit();
           window.sendStudent = 'true';
           window.sendTeacher = 'true';
           changeType(); 
        });
        
        /**
         * 计算班级人数
         * @returns {undefined}
         */
        window.jisuanClassNum = function() {
            if(window.sendStudent == 'false')
                return false;
            var studentnum = 0;
            var checkedObj = $form.find(".classList:checked");
            var classnum = checkedObj.length;
            checkedObj.each(function(i, item) {
                studentnum += $(item).data("studentnum");
            });
            $(".classNumSpan em").text(classnum);
            $("[name=classNum]").val(classnum);
            $(".studentNumSpan em").text(studentnum);
            $("[name=studentNum]").val(studentnum);
        }
        /**
         * 计算老师人数
         * @returns {undefined}
         */
        window.jisuanTeacherNum = function() {
            if(window.sendTeacher == 'false')
                return false;
            var checkedObj = $form.find(".teacherList:checked");
            var teachernum = checkedObj.length;
            $(".teacherNumSpan em").text(teachernum);
            $("[name=teacherNum]").val(teachernum);
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
  
        $form.find(".reception-rest").on("click", function() {
            $(".classList").attr("checked", false);
            $(".teacherList").attr("checked", false);
            jisuanTeacherNum();
            jisuanClassNum();
            var studentInputObj = $form.find(".student-input_res");
            var teacherInputObj = $form.find(".teacher-input_res");
            studentInputObj.find("li").remove();
            teacherInputObj.find("li").remove();
            $form.find(".chooseTree").addClass("hide");
            $form.find(".selectRecRange").text("选择");
        })
        $form.find(".reception-save").on("click", function() {
            $form.find(".chooseTree").addClass("hide");
            $form.find(".selectRecRange").text("选择");
            var classnum = $(".classNumSpan em").text();
            var studentnum = $(".studentNumSpan em").text();
            var teachernum = $(".teacherNumSpan em").text();
            Notify.success("一共选择了" + classnum + "个班级" + "," + studentnum + "名学员," + teachernum + "位教师");
        })

    };

});