define(function(require, exports, module) {
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('jquery.sortable');

    exports.run = function() {

        var sortList = function($list) {
            var data = $list.sortable("serialize").get();
            $.post($list.data('sortUrl'), {ids: data}, function(response) {
                var lessonNum = chapterNum = unitNum = 0;

                for (var key in response.data) {
                    var newPid = response.data[key];
                    $("li#" + key).removeAttr("data-pid").attr("data-pid", newPid);
                }

                $list.find('.item-lesson, .item-chapter').each(function() {
                    var $item = $(this);
                    if ($item.hasClass('item-lesson')) {
                        lessonNum++;
                        $item.find('.number').text(lessonNum);
                    } else if ($item.hasClass('item-chapter-unit')) {
                        unitNum++;
                        $item.find('.number').text(unitNum);
                        if (response.info)
                            window.deploy($item.attr("id"));
                    } else if ($item.hasClass('item-chapter')) {
                        chapterNum++;
                        unitNum = 0;
                        $item.find('.number').text(chapterNum);
                        if (response.info)
                            window.deploy($item.attr("id"));
                    }
                });
            }, 'json');
        };

        var validator = new Validator({
            element: '#course-chapter-form',
            autoSubmit: false
        });

        validator.addItem({
            element: '#chapter-title-field',
            required: true
        });

        validator.on('formValidated', function(error, msg, $form) {
            if (error) {
                return;
            }
            $('#course-chapter-btn').button('submiting').addClass('disabled');
            var $list = $("#course-item-list");
            if($('#course-chapter-btn').data('lock') == 1) return false;
            $('#course-chapter-btn').data('lock',1);
            $.post($form.attr('action'), $form.serialize(), function(html) {

                var id = '#' + $(html).attr('id'),
                        $item = $(id);
                var $parent = $('#' + $form.data('parentid'));
                var $panel = $('.reload-container');
                $panel.find('.empty').remove();
                if ($item.length) {
                    var deployObj = $item.find("span.deploy");
                    var deployHTML = "";
                    if (deployObj.size()) {
                        deployObj = deployObj.get(0);
                        deployHTML = deployObj.outerHTML;
                    }
                    $item.replaceWith(html);
                    if (deployHTML)
                        $(id).find(".item-content i").before(deployHTML);
                    Notify.success('信息已保存');
                    sortList($list);
                } else {
                    if ($parent.length > 0) {
                        var add = 0;
                        $parent.nextAll().each(function() {
                            if ($(this).hasClass('item-chapter-chapter')) {
                                $(this).before(html);
                                add = 1;
                                return false;
                            }
                        });

                        if (add != 1) {
                            $("#course-item-list").append(html);
                        }
                    } else {
                        $("#course-item-list").append(html);
                        $(".lesson-manage-panel").find('.empty').remove();
                    }

                    Notify.success('添加成功');
                    sortList($list);
                    var $emDeploy = $(document).find("[data-id=" + window.tempChapterId + "]").find("em.deploy");
                    if (typeof(window.tempChapterId) != 'undefined' && $emDeploy.hasClass("fa-plus")) {
                        window.toggleDeploy($emDeploy);
                    }
                }
                $(id).find('.btn-link').tooltip();
                $form.parents('.modal').modal('hide');
                $('#course-chapter-btn').data('lock',0);
                
                $('.chapter-create-class').on('click', function() {
                    window.tempChapterId = $(this).parents('.item-chapter').data('id');
                });
            });

        });

    };



});