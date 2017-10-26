define(function(require, exports, module) {

    var Widget = require('widget');
    var Handlebars = require('handlebars');
    var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
    require('common/validator-rules').inject(Validator);
    require('webuploader');
    require('ckeditor');

    window.editor = window.editor || {};

    var QuestionCreator = Widget.extend({
        attrs: {
            validator: null,
            form: null,
            stemEditorName: 'Image'
        },
        events: {
            'click [data-role=submit]': 'onSubmit'
        },
        setup: function() {
            this.set('enableAudioUpload', $('#question-stem-audio-uploader').length > 0);
            this._initForm();
            this._initStemField();
            this._initAnalysisField();
        },
        onSubmit: function(e) {
            var submitType = $(e.currentTarget).data('submission');
            this.get('form').find('[name=submission]').val(submitType);
        },
        _initAnalysisField: function() {
            window.editor['analysis'] = CKEDITOR.replace('question-analysis-field', {
                toolbar: 'Minimal',
                height: 120
            });

            this.get('validator').on('formValidate', function(elemetn, event) {
                window.editor['analysis'].updateElement();
            });

            var uploader = WebUploader.create({
                swf: require.resolve("webuploader").match(/[^?#]*\//)[0] + "Uploader.swf",
                server: this.element.data('uploadUrl'),
                pick: '#question-analysis-uploader',
                formData: {'_csrf_token': $('meta[name=csrf-token]').attr('content')},
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,png',
                    mimeTypes: 'image/*'
                }

            });

            uploader.on('fileQueued', function(file) {
                Notify.info('正在上传，请稍等！', 0);
                uploader.upload();
            });

            uploader.on('uploadSuccess', function(file, response) {
                Notify.success('上传成功！', 1);
                var result = '[image]' + response.hashId + '[/image]';
                window.editor['analysis'].insertHtml(result);
            });

            uploader.on('uploadError', function(file, response) {
                Notify.danger('上传失败，请重试！');
            });

            uploader.disable();

            this.$('#advanced-collapse').on('shown.bs.collapse', function() {
                uploader.enable();
            });

            this.$('#advanced-collapse').on('hidden.bs.collapse', function() {
                uploader.disable();
            });

        },
        _initStemField: function() {
            var self = this;
            var height = $('#question-stem-field').height();

            // group: 'default'
            window.editor['stem'] = CKEDITOR.replace('question-stem-field', {
                toolbar: this.get('stemEditorName'),
                filebrowserImageUploadUrl: $('#question-stem-field').data('imageUploadUrl'),
                height: height
            });

            this.get('validator').on('formValidate', function(elemetn, event) {
                window.editor['stem'].updateElement();
            });

            var uploader = WebUploader.create({
                swf: require.resolve("webuploader").match(/[^?#]*\//)[0] + "Uploader.swf",
                server: this.element.data('uploadUrl'),
                pick: '#question-stem-uploader',
                formData: {'_csrf_token': $('meta[name=csrf-token]').attr('content')},
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,png',
                    mimeTypes: 'image/*'
                }
            });

            uploader.on('fileQueued', function(file) {
                Notify.info('正在上传，请稍等！', 0);
                uploader.upload();
            });

            uploader.on('uploadSuccess', function(file, response) {
                Notify.success('上传成功！', 1);
                var result = '[image]' + response.hashId + '[/image]';
                $('#tmpImgURL').val(result);
                //editor.insertHtml(result);
                $('#imgdiv').css('display', 'block');
                $('.stemImg').attr('src', '/' + window.app.globalData.DATA_FETCH_URL_PREFIX + '/' + response.hashId);
            });

            uploader.on('uploadError', function(file, response) {
                Notify.danger('上传失败，请重试！');
            });

            if ($('#question-stem-audio-uploader').length) {
                /**
                 * 音频上传
                 */
                var audioUploader = WebUploader.create({
                    swf: require.resolve("webuploader").match(/[^?#]*\//)[0] + "Uploader.swf",
                    pick: '#question-stem-audio-uploader',
                    formData: {'_csrf_token': $('meta[name=csrf-token]').attr('content')},
                    accept: {
                        title: 'Audio',
                        extensions: 'mp3,wav',
                        mimeTypes: 'audio/*'
                    }
                });

                audioUploader.on('fileQueued', function(file, a, b) {
                    Notify.info('正在上传，请稍等！', 0);

                    $.ajax({
                        url: self.element.data('mediaUploadParamsUrl'),
                        async: false,
                        dataType: 'json',
                        data: {convertor: 'audio'},
                        cache: false,
                        success: function(response, status, jqXHR) {
                            audioUploader.option('server', response.url)
                            audioUploader.option('formData', response.postParams);
                        },
                        error: function(jqXHR, status, error) {
                            Notify.danger('请求上传授权码失败！');
                        }
                    });

                    audioUploader.upload();
                });

                audioUploader.on('uploadSuccess', function(file, response) {
                    Notify.success('上传成功！', 1);

                    $.post(self.element.data('mediaUploadCallbackUrl'), response, function(response) {
                        var name = response.filename.match(/[^\.]*\./)[0].slice(0, -1);
                        var result = '[audio id="' + response.id + '"]' + name + '[/audio]';
                        window.editor['stem'].insertHtml(result);
                    });

                });

                audioUploader.on('uploadError', function(file, response) {
                    Notify.danger('上传失败，请重试！');
                });
            }

        },
        _initForm: function() {
            var $form = this.$('[data-role=question-form]');
            this.set('form', $form);
            this.set('validator', this._createValidator($form));
        },
        _createValidator: function($form) {
            var self = this;

            Validator.addRule('score', /^(\d){1,10}$/i, '请输入正确的分值');

            validator = new Validator({
                element: $form,
                autoSubmit: false
            });

            validator.addItem({
                element: '#question-stem-field',
                required: true
            });

            validator.addItem({
                element: '#question-score-field',
                required: false,
                rule: 'number'
            });
            validator.on('formValidated', function(error, msg, $form) {
                if (error) {
                    return false;
                }

                self.get('validator').set('autoSubmit', false);
                if (!$('.submit-btn').hasClass('disabled')) {
                    $.ajax({
                        url: $form.attr('action'),
                        type: 'post',
                        data: $form.serialize(),
                        success: function(response) {
                            switch (response.tag)
                            {
                                case 0:// 在题库中添加试题后操作
                                    Notify.success(response.success);
                                    window.location.href = response.url;
                                    break;

                                case 1://在试题列表中编辑试题后操作
                                    if (typeof(response.preId) != 'undefined') {
                                        delete window.questions[response.preId];
                                        window.questions[response.id] = {score: response.score, type: response.type};
                                    }
                                    if ($('.pagination li').size() > 0) {
                                        $(".pagination li").each(function() {
                                            if ($(this).hasClass('active')) {
                                                $(this).find('a').click();
                                            }
                                        });
                                    } else {
                                        $.post($('input[name=refreshUrl]').val(), {}, function(response) {
                                            $('#group_list').html(response.list);
                                            $('#page').html(response.page);
                                            $("input[name=groups]").each(function() {
                                                self._deassignScore($(this).val());
                                            });
                                        });
                                    }
                                    $('#selectedStatistic').load($('input[name=refreshUrl]').data('url'));
                                    Notify.success(response.success);
                                    $('#modal').modal('hide');
                                    break;

                                case 2: //在modal弹层中添加后操作
                                    Notify.success(response.success);
                                    window.questions[response.id] = {score: response.score, type: response.type};
                                    var notids = self._objToArr(window.questions);
                                    $.post($('input[name=refreshUrl]').val(), {notids: notids}, function(response) {
                                        $('#group_list').html(response.list);
                                        $('#page').html(response.page);
                                        $("input[name=groups]").each(function() {
                                            self._deassignScore($(this).val());
                                        });
                                    });
                                    $('#selectedStatistic').load($('input[name=refreshUrl]').data('url'));
                                    $('#modal').modal('hide');
                                    break;
                            }
                        }
                    });
                }
                $('.submit-btn').button('submiting').addClass('disabled');
            });
            return validator;
        },
        _objToArr: function(obj) {//对象转数组
            var arr = [];
            for (var item in obj) {
                arr.push(item);
            }
            return arr;
        },
        _deassignScore: function(group_id) {//重新赋值分数到input框
            var $groupDom = $("#group_" + group_id);
            var qid = $groupDom.data('id');
            for (wqid in window.questions) {
                if (qid == wqid) {
                    $groupDom.find('input[name=score]').val(questions[wqid].score);
                }
            }
        }
    });

    module.exports = QuestionCreator;
});