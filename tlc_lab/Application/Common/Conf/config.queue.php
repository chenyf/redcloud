<?php
/**
 * 队列服务注册配置
 */
return array(
	'QUEUE' => array(
		#jobName           => array('queueName' => '队列名', 'func'=>'任务方法', 'jobName'=>'任务名')
		'test'                    => array('queueName' => 'default', 'func' => 'test', 'title' => '测试'),
		'noticeByNote'            => array('queueName' => 'MsgTask', 'func' => 'noticeByNote', 'title' => '通知短信'),
		'noticeByEmail'           => array('queueName' => 'MsgTask', 'func' => 'noticeByEmail', 'title' => '通知邮件'),
		'noticeByPush'            => array('queueName' => 'MsgTask', 'func' => 'noticeByPush', 'title' => '通知推送'),
		'noticeByPost'            => array('queueName' => 'MsgTask', 'func' => 'noticeByPost', 'title' => "通知站内信"),
		'pushMsg'                 => array('queueName' => 'MsgTask', 'func' => 'pushMsg', 'title' => "消息推送"),
		'startLivePushMsg'        => array('queueName' => 'MsgTask', 'func' => 'startLivePushMsg', 'title' => "开始直播课推送"),
		'resetUserPass'           => array('queueName' => 'user', 'func' => 'resetUserPass', 'title' => "导入用户"),
		'resetPassTask'           => array('queueName' => 'user', 'func' => 'resetPassTask', 'title' => "重置密码任务"),
		'courseClassImportMember' => array('queueName' => 'user', 'func' => 'courseClassImportMember', 'title' => '授课班导入成员'),
		'documentConvert'         => array('queueName' => 'ConvertFile', 'func' => 'documentConvert', 'title' => "convertFile"),
		'videoConvert'         	  => array('queueName' => 'ConvertVideo', 'func' => 'videoConvert', 'title' => "视频格式转换"),
		'importClass'             => array('queueName' => 'default', 'func' => 'importClass', 'title' => '导入班级'),
		'CourseCopy'              => array('queueName' => 'CourseCopy', 'func' => 'run', 'title' => '课程复制'),
		'PublicToPrivate'         => array('queueName' => 'default', 'func' => 'publicToPrivate', 'title' => '公共课转私有课'),
		'groupMailTask'           => array('queueName' => 'default', 'func' => 'sendMail', 'title' => '群发邮件'),
		'cloudSync'               => array('queueName' => 'default', 'func' => 'cloudSync', 'title' => '云课程同步'),
		'importGroup'             => array('queueName' => 'BatTask', 'func' => 'importGroup', 'title' => '自然班添加学员'),
		'execSubTask'             => array('queueName' => 'BatTask', 'func' => 'execSubTask', 'title' => '执行多任务队列'),
        'queuename_chuzhaoqian'   => array('queueName' => 'default', 'func' => 'funcname_chuzhaoqian', 'title' => '褚兆前测试'),
		'pushUser'                => array('queueName' => 'MsgTask', 'func' => 'pushUser', 'title' => "批量单用户推送消息"),
		'syncFileToCloud'		  => array('queueName' => 'SyncFile', 'func' => 'syncFile', 'title' => "同步文件到云盘"),
    )
);