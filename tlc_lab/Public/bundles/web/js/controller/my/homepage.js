define(function(require, exports, module) {

    require('jquery');
    require('jquery.ajaxupload').run();

    var Notify = require('common/bootstrap-notify');

    Vue.config.delimiters = ['[[', ']]'];
    exports.run = function() {
        var App = new Vue({
            el: '#edit_hompage_div',
            components:{
                contact: Contact,
                intro: Intro,
                teach: Teach,
                research: Research,
                publication: Publication,
                select_tpl:SelectTpl
            },
            ready: function () {
                var that = this;
                $.ajax({
                    type: "GET",
                    url: $('#edit_hompage_div').data('initurl'),
                    dataType: "json",
                    success: function(data){
                        if(data == null || data == undefined){
                            return false;
                        }
                        if (data.contacts != undefined && data.contacts.length != 0){
                            that.contacts = JSON.parse(data.contacts);
                        }
                        if (data.intros != undefined && data.intros.length != 0){
                            that.intros = JSON.parse(data.intros);
                        }
                        if (data.teaches != undefined && data.teaches.length != 0){
                            that.teaches = JSON.parse(data.teaches);
                        }
                        if (data.researches != undefined && data.researches.length != 0){
                            that.researches = JSON.parse(data.researches);
                        }
                        if (data.publications != undefined && data.publications.length != 0){
                            that.publications = JSON.parse(data.publications);
                        }
                        //模板是否是三个中的一个
                        if ([0, 1, 2].indexOf(parseInt(data.tpl)) != -1){
                            that.tpl = parseInt(data.tpl);
                        }
                        // that.clearNew();
                        return false;
                    }
                });
            },
            data:{
                current_view: 1,
                ph_key_map:{
                    '本科教学'   :  "例: C++程序设计(大一)",
                    '研究生教学' :  "例: 机器学习系统设计(研一)",
                    '科研项目'   :  "例: 国家A级项目",
                    '奖项'       :  "例: 优秀教师奖",
                    'paper'      :  "例: 基于XX的XX",
                    '书籍'       :  "例: C++程序设计",
                },
                contacts: [
                    { key: '邮箱' , value: ''},
                    { key: '办公室', value: ''},
                    { key: '办公时间', value: ''},
                    { key: '电话', value: ''}
                ],
                intros: [
                    { key: '研究领域', value: ''},
                    { key: '实验室', value: ''}
                ],
                teaches: [
                    {key: '本科教学', value: [{val:"", href:""}], _new: '', _placeholder: "例: C++程序设计(大一)"},
                    {key: '研究生教学', value: [{val:"", href:""}], _new: '', _placeholder: "例: 机器学习系统设计(研一)"}
                ],
                researches: [
                    {key: '科研项目', value: [{val:"", href:""}], _new: '', _placeholder: "例: 国家A级项目"},
                    {key: '奖项', value: [{val:"", href:""}], _new: '', _placeholder: "例: 优秀教师奖"}
                ],
                publications: [
                    {key: 'paper', updateable: 1, value: [{val:"", href:""}], _new: '', _placeholder: "例: 基于XX的XX"},
                    {key: '书籍', updateable: 0, value: [{val:"", href:""}], _new: '', _placeholder: "例: C++程序设计"}
                ],
                tpl: 0
            },
            methods:{
                nextStep: function () {
                    if (this.current_view == 6){
                        return;
                    }
                    this.current_view += 1;
                    window.scrollTo(0, 0);
                },
                lastStep: function () {
                    if (this.current_view == 1){
                        return;
                    }
                    this.current_view -= 1;
                    window.scrollTo(0, 0);
                },
                saveHomePage: function () {

                    $('#save_btn').button('submiting').addClass('disabled');
                    $.ajax({
                        type: "POST",
                        url: $('#edit_hompage_div').data('saveurl'),
                        data: {
                            contacts: JSON.stringify(App.contacts),
                            intros: JSON.stringify(App.intros),
                            teaches: JSON.stringify(App.teaches),
                            researches: JSON.stringify(App.researches),
                            publications: JSON.stringify(App.publications),
                            tpl: App.tpl
                        },
                        dataType: "json",
                        success: function(data){
                            if (data.code == 0){
                                Notify.success("保存成功!",4);
                            }else {
                                Notify.danger("保存失败!",4);
                            }
                            $('#save_btn').button('reset').removeClass('disabled');
                            return false;
                        },
                        error:function(){
                            $('#save_btn').button('reset').removeClass('disabled');
                            Notify.danger("发生错误，稍候重试！",2);
                        }
                    });
                },
                previewHomePage: function () {
                    $.ajax({
                        type: "POST",
                        url: $('#edit_hompage_div').data('previewurl'),
                        data: {
                            contacts: JSON.stringify(App.contacts),
                            intros: JSON.stringify(App.intros),
                            teaches: JSON.stringify(App.teaches),
                            researches: JSON.stringify(App.researches),
                            publications: JSON.stringify(App.publications),
                            tpl: App.tpl
                        },
                        dataType: "json",
                        success: function(data){
                            if(data.error){
                                Notify.danger(data.msg,2);
                                return false;
                            }
                            // window.open(data.url);
                            document.getElementById('preview_achor').click();
                            // $('#preview_achor').trigger('click');
                            
                            return false;
                        },
                        error:function(){
                            Notify.danger("发生错误，稍候重试！",2);
                        }
                    });
                },
                clearNew:function(){
                    // this.clearNewAttr(this.teaches);
                    // this.clearNewAttr(this.researches);
                    // this.clearNewAttr(this.publications);
                },
                clearNewAttr:function(arr){
                    for(var i in arr){
                        arr[i]._new = '';

                        if(arr[i].key != undefined && this.ph_key_map[arr[i].key] != undefined){
                            arr[i]._placeholder = this.ph_key_map[arr[i].key];
                        }else{
                            arr[i]._placeholder = "";
                        }

                        if( !$.isArray(arr[i].value) ){
                            arr[i].value = [];
                        }
                    }
                }
            }
        });

        function isArray(obj) {
            return Object.prototype.toString.call(obj) === '[object Array]';
        }

    };

});
