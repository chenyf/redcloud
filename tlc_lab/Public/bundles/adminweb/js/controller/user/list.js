define(function(require, exports, module) {

	var Notify = require('common/bootstrap-notify');
	exports.run = function() {

		var $table = $('#user-table');
                
                /**
                 * 解绑手机号
                 * @author fubaosheng 2015-04-28
                 */
                $table.find(".unbind-mobile").on('click',function(){
                    var $trigger = $(this);
                    if (!confirm('真的要' + $trigger.attr('title') + '吗？')) {
                        return ;
                    }
                    $.post($(this).data('url'), function(html){
                        Notify.success($trigger.attr('title') + '成功！');
                        var $tr = $(html);
                        $('#' + $tr.attr('id')).replaceWith($tr);
                    }).error(function(){
                        Notify.danger($trigger.attr('title') + '失败');
                    });
                });


		$table.on('click', '.lock-user, .unlock-user', function() {
			var $trigger = $(this);

			if (!confirm('真的要' + $trigger.attr('title') + '吗？')) {
				return ;
			}

                        $.post($(this).data('url'), function(html){
                            Notify.success($trigger.attr('title') + '成功！');
                             var $tr = $(html);
                            $('#' + $tr.attr('id')).replaceWith($tr);
                        }).error(function(){
                            Notify.danger($trigger.attr('title') + '失败');
                        });
		});

		$table.on('click', '.send-passwordreset-email', function(){
            Notify.info('正在发送密码重置验证邮件，请稍等。', 60);
            $.post($(this).data('url'),function(response){
                Notify.success('密码重置验证邮件，发送成功！');
            }).error(function(){
                Notify.danger('密码重置验证邮件，发送失败');
            });
		});

            $table.on('click', '.set-emailverify-email', function(){
                    if(confirm('确认激活该邮箱吗？')){
                        $.post($(this).data('url'),function(response){
                            if(response=="noSuperAdmin"){
                                Notify.danger('您不是管理员，不能操作');
                            }else if(response=="true"){
                                Notify.success('设置成功');
                                window.location.reload();
                            }else{
                                Notify.danger('设置失败');
                            }
                        });
                    }else{
                        return false;
                    }
                
            });
            
            $table.on('click', '.send-emailverify-email', function(){
                Notify.info('正在发送Email验证邮件，请稍等。', 60);
                $.post($(this).data('url'),function(response){
                    Notify.success('Email验证邮件，发送成功！');
                }).error(function(){
                    Notify.danger('Email验证邮件，发送失败');
                });
            });

                var $userSearchForm = $('#user-search-form');
                var $roles = $userSearchForm.find('[name=roles]').val(); 
                var $keywordType = $userSearchForm.find('[name=keywordType]').val();
                var $keyword = $userSearchForm.find('[name=keyword]').val();

                $('#user-export').on('click', function() {
                   var self = $(this);
                   self.attr('data-url', self.attr('data-url')+"?roles="+$roles+"&keywordType="+$keywordType+"&keyword="+$keyword);
                });

	};

});