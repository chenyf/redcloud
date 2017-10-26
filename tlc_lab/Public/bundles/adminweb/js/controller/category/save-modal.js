define(function(require, exports, module) {

	var Validator = require('bootstrap.validator');
    var Notify = require('common/bootstrap-notify');
	require('common/validator-rules').inject(Validator);
    require('webuploader');
    
	exports.run = function() {
        var $form = $('#category-form');
		var $modal = $form.parents('.modal');
        var $table = $('#category-table');

        var uploader = WebUploader.create({
            swf: require.resolve("webuploader").match(/[^?#]*\//)[0] + "Uploader.swf",
            server: $('#category-creater-widget').data('uploadUrl'),
            pick: '#category-icon-uploader',
            formData: {'_csrf_token': $('meta[name=csrf-token]').attr('content') },
            accept: {
                title: 'Images',
                extensions: 'gif,jpg,jpeg,png',
                mimeTypes: 'image/*'
            }

        });

        uploader.on( 'fileQueued', function( file ) {
            Notify.info('正在上传，请稍等！', 0);
            uploader.upload();
        });

        uploader.on( 'uploadSuccess', function( file, response ) {
            Notify.success('上传成功！', 1);
            $('#category-icon-field').html('<img src="' + response.hashId + '">');
            $('#category-icon-field').addClass('mbm');
//            $form.find('[name=icon]').val(response.hashId);
            $("#defineType").val(response.hashId).attr("name","icon");
            $("#systemType").attr("name","");
            $("#category-icon-delete").show();
        });

        uploader.on( 'uploadError', function( file, response ) {
            Notify.danger('上传失败，请重试！');
        });

        $("#category-icon-delete").on('click', function(){
            if (!confirm('确认要删除图标吗？')) return false;
            var $btn = $(this);
            $.post($btn.data('url'), function(){
                $("#category-icon-field").html('');
                $form.find('[name=icon]').val('');
                $btn.hide();
                $('#category-icon-field').removeClass('mbm');
                $(".iconType[value='system']").attr("checked","checked");
                $("option[value='default.png']").attr("selected","selected");
                $("#systemType").attr("name","icon");
                $("#systemType").val("default.png");
                $("#defineType").attr("name","");
                $("#category-icon-field").html('<img src="/Data/udisk/categoryicon/default.png">');
                Notify.success('删除分类图标成功！');
            }).error(function(){
                Notify.danger('删除分类图标失败！');
            });
        });
        
        $("#categoryIcon").on("change",function(){
            var selectedName = $(this).children('option:selected').val();   //获取图片名称
            var tabImgUrl = "/Data/udisk/categoryicon/"+selectedName;
            $("#category-icon-field").html('<img src="'+tabImgUrl+'">');
            $("#systemType").val(selectedName);
        })
        
        $(".iconType").on("click" ,function(){
            var thisVal = $(this).val();
            if(thisVal=='define'){
                $("#defineType").attr('name','icon');
                $("#systemType").attr('name','');
                var defineTypeVal = $("#defineType").val();
                var str = 'category'
                if(defineTypeVal.indexOf(str) != -1){
                    $("#category-icon-field").html('<img src="'+defineTypeVal+'">');
                }else{
                    $("#category-icon-field").html('');
                }
            }else if(thisVal=='system'){
                var obj = $("#systemType");
                obj.attr('name','icon');
                $("#defineType").attr('name','');
                var newVal = $("#categoryIcon").children('option:selected').val();
                obj.val(newVal);
                var newUrl = "/Data/udisk/categoryicon/"+newVal;
                $("#category-icon-field").html('<img src="'+newUrl+'">');
            }
        })

		var validator = new Validator({
            element: $form,
            autoSubmit: false,
            onFormValidated: function(error, results, $form) {
                if (error) {
                    return ;
                }
                
                var formId =  $form.data('id');
                if($('#category-form [name=isLeafNode]:checked').val() == "1" && $('#category-table tr[pid="'+formId+'"]').size()>0 && formId!=0){
                    if(confirm("选择为专业，将会删除其下所有子分类，确定吗?")){
                        var url = $modal.find('.delete-category').data('url');
                        url = url + "/deleSelf/0";
                        $.post(url, function(html) {});
                    }else{
                        $('#category-form [name=isLeafNode]').removeAttr("checked");
                        $('#category-form [name=isLeafNode][value=0]').trigger('click').attr('checked',true);
                    }
                }

                $('#category-create-btn').button('submiting').addClass('disabled');

                $.post($form.attr('action'), $form.serialize(), function(html){
                    $modal.modal('hide');
                    $table.find('tbody').replaceWith(html);
                    Notify.success('保存分类成功！');
                    window.location.reload();
				}).fail(function() {
                    Notify.danger("添加分类失败，请重试！");
                });

            }
        });

        validator.addItem({
            element: '#category-name-field',
            required: true,
            rule: 'maxlength{max:40}'
        });

        validator.addItem({
            element: '#category-code-field',
            required: true,
            rule: 'alphanumeric not_all_digital remote'
        });

        validator.addItem({
            element: '#category-weight-field',
            required: false,
            rule: 'integer'
        });

        $modal.find('.delete-category').on('click', function() {
            if (!confirm('真的要删除该分类及其子分类吗？')) {
                return ;
            }

            $.post($(this).data('url'), function(html) {
                $modal.modal('hide');
                $table.find('tbody').replaceWith(html);
                window.location.reload();
            });

        });
        
        /**
         * 恢复删除的分类
         * @author fuabosheng 2015-05-14
         */
        $modal.find('.recover-category').on('click', function() {
            if (!confirm('恢复该分类，将恢复此顶级下所有分类？')) {
                return ;
            }

            $.post($(this).data('url'), function(html) {
                $modal.modal('hide');
                $table.find('tbody').replaceWith(html);
                window.location.reload();
            });

        });

	};

});