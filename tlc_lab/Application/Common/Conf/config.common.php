<?php

return array(
    'PHP_BIN_PATH' => '/usr/local/php/bin/php',
//    'MULTI_WEB_MODE' => 1, #多站共存模式 0=>关闭 1=>开启
    'PUSH_CFG_FROM_DB' => 1, #推送配置使用数据库 qzw 2015-07-24
    'GROUP_DEPTH' => 9, //组织机构设置等级
//    'SUPPORT_CENTER_DB' => 1, #支持中心DB qzw 2015-07-29
//    'USE_CENTER_DB' => 0, #使用中心DB 动态设置, 不使用=>C('USE_CENTER_DB', 0) 使用=>C('USE_CENTER_DB', 1)
    'USER_RECORD_CLASS_NUM' => 4,
    'DEFAULT_THEME_CFG' => array(
        'THEME_FRONT_COLOR' => '#c10000', #默认主题前景色
        'THEME_BACK_COLOR' => '#8c1515', #默认主题背景色
        'THEME_NAV_BACK_COLOR' => '#8c1515', #默认主题导航条颜色
        "THEME_ICON_COLOR" => '33ffdd', #主题图标颜色
    ),
    'UNOCONV_CMD' => '/usr/bin/unoconv',
    'FFMPEG_CMD' => '/usr/bin/ffmpeg',
    'ASYNC_CMD' =>  '/usr/local/sbin/async_file',
    'CONVERT_CMD' => '/usr/local/bin/convert',
    'SCHEMASYNC_CMD' => '/usr/bin/schemasync',
    'MYSQL_CMD' => '/usr/bin/mysql',
    'CRYPT_COURSE_CMD' => 'D:/dev/phpEnv/crypt.exe',
    'QA_URL' => 'http://qa.cstarcloud.com',
    'VPS_URL' => 'http://localhost:8081/cloudssd/index.php/user/login/2',
    /* 邮件接收者 */
    'SYSTEM_MANAGER' => array(
        '任思可' => '326728721@qq.com',
//        '付宝生' => 'fubaosheng@redcloud.com',
//        '钱志伟' => 'qianzhiwei@redcloud.com',
//        '姚向飞' => 'yaoxiangfei@redcloud.com',
//        '谈海涛' => 'tanhaitao@redcloud.com'
    ),
    'SYSTEM_NOTE_MANAGER' => array(
        '钱志伟' => '15810815921',
        '周小龙' => '18610094936',
    ),
    /* redis 库使用分配，使用时用C('REDIS_DIST.xxxx') */
    'REDIS_DIST' => array(
        //'业务代号'  => 库代码,
        'COMMON'            => 10,#公共，放临散数据 by qzw 2016-3-10
        'PHP_RESQUEUE'      => 4, #其他业务不能共用 by qzw 2015-06-10
        'USER_PUSH_INFO'    => 1, #用户推送信息（app上最后查看时间）
        'USER_RECORD_CLASS' => 2, #批量重置密码
        'CLASS_SIGN_IN'     => 3, #班级签到 qzw 2015-08-18
        'PLAY_REPORT'       => 5, #播放上报
        'APPRAISE'          => 6, #点赞 by tht 2015-11-27 
        'APPLY_PAY'         => 7, #苹果充值票据 by gjq 2015-11-30 
        'BAT_TASK'          => 8, #批任务处理
        'HEART_BEAT'        => 9, #直播心跳 by tht 2016-01-20
        'REDCLOUD_IMPORT'       => 11,
    ),
    /* 公共课申请失败类型 */
    'COUSE_APPLY_FAIL_TYPE' => array(
        1 => '课程基础信息填写错误',
        2 => '课程内容不合格',
        3 => '课程分类选择错误',
        4 => '其它'
    ),
    /* 缓存key前缀登记 */
    'CACHE_KEY_PFX' => array(
        'CACHE_UID_UINFO' => 'cache_uid_uinfo_151026', #用户信息
    ),
    /* 移动端信息配置 */
    'APP_INFO_CFG' => array(
        #键值 => array('backEndEdit'=>1:后台编辑,0: 动态设置, 'mean'=>含义)
        'TIP_LOGIN_NO_ACCOUNT' => array('backEndEdit' => 1, 'mean' => '登录时，帐号不存在提示', 'value' => ''),
        'TIP_LOGIN_ERR_PWD' => array('backEndEdit' => 1, 'mean' => '登录时，错误密码提示', 'value' => ''),
        'CREATE_COURSE_EXPECT_TIP' => array('backEndEdit' => 1, 'mean' => '创建课程敬请期待页面，文字引导PC操作流程', 'value' => ''),
        'PROCESS_WORK_EXPECT_TIP' => array('backEndEdit' => 1, 'mean' => '批改作业敬请期待页面，文字引导PC操作流程', 'value' => ''),
        'LOOK_SCORE_EXPECT_TIP' => array('backEndEdit' => 1, 'mean' => '查看成绩敬请期待页面，文字引导PC操作流程', 'value' => ''),
        'ADD_WORK_TIP' => array('backEndEdit' => 1, 'mean' => '添加作业页面，引导用户在PC上创建试题的文字描述', 'value' => ''),
        'ADD_EXAM_TIP' => array('backEndEdit' => 1, 'mean' => '添加考试页面，引导用户在PC上创建试题的文字描述', 'value' => ''),
        'LOGIN_TIP' => array('backEndEdit' => 1, 'mean' => '登录注意事项文字提示', 'value' => ''),
        'ADMIN_MOBILE' => array('backEndEdit' => 0, 'mean' => '管理员手机号', 'value' => ''),
        'COURSE_CLASS_IMPORT_MEMBER_TIP' => array('backEndEdit' => 1, 'mean' => '授课班增加成员方法二的文字提示（excel导入方法）', 'value' => ''),
        'COURSE_GOLAL_FREE' => array('backEndEdit' => 1, 'mean' => '课程全部免费', 'value' => '1'),
        'TASK_ACTIVITY_PAGE' => array('backEndEdit' => 0, 'mean' => '任务活动页', 'value' => '/index.php'),
        'COURSE_MUST_MODULE_NAME' => array('backEndEdit' => 1, 'mean' => '我的必修课和选修课名称', 'value' => '我的必修课和选修课'),
        'COURSE_SELECT_MODULE_NAME' => array('backEndEdit' => 1, 'mean' => '我的自修课名称', 'value' => '我的自修课'),
    ),
    'COMMENT_SYSTEM_G' => 'open', #'close'/'open'
    'COMMENT_ITEM' => array(
        # '类别' => array('status'=>状态(open: 开启, open-read: 只允许展现，close: 关闭) , 'forceLogin'=>是否强制登录 0=>不强制 1=>强制, 'anonyNoLogin'=>未登录时是否允许匿名 0=>不允许 1=>允许， 'userDayCmtMaxCnt'=>每用户日评论上限, 'userDayReplyMaxCnt'=>每用户日回复上限，‘allowDel’=>是否允许删除，‘noLoginDayMaxCnt'=>未登录每日评论上限,‘noLoginDayReplyMaxCnt'=>未登录每日回复上限),
        'Poster' => array('status' => 'open', 'forceLogin' => 1, 'anonyNoLogin' => 0, 'userDayCmtMaxCnt' => 50, 'userDayReplyMaxCnt' => 50, 'allowDel' => 1, 'noLoginDayMaxCnt' => 50, 'noLoginDayReplyMaxCnt' => 50),
        'Blog' => array('status' => 'open', 'forceLogin' => 1, 'anonyNoLogin' => 0, 'userDayCmtMaxCnt' => 10, 'userDayReplyMaxCnt' => 5, 'allowDel' => 1, 'noLoginDayMaxCnt' => 10, 'noLoginDayReplyMaxCnt' => 10),
    ),
    'BAT_TASK' => array(
            #类型  => array('name'=>名称, 'autoBegin'=>'是否自动开启',  'processTaskMethod'=>创建任务回调函数 | 空串表示无, 'taskDetailCallBack'=>子任务数据装饰回调函数 | 空串表示无，'itemTask'=>子项需要显示的数据，'task'=>父项需要显示的数据)
            'classImportUser' => array('name'=>'班级学生导入', 'autoBegin'=>1, "processTaskMethod"=>"Wclass\Controller\ClassMemberController::classImportUserCallBack", "taskDetailCallBack"=>"","itemTask"=>array("itemData"=>"用户名","status"=>"状态","processTm"=>"操作时间","remark"=>"备注")),
            'importUser'      => array('name'=>'导入用户',      'autoBegin'=>1, "processTaskMethod"=>"Admin\Controller\UserController::importUserCallBack", "taskDetailCallBack"=>"","itemTask"=>array("itemData"=>"数据","status"=>"状态","processTm"=>"操作时间","remark"=>"备注"),"task"=>array("uid"=>"操作者","createTm"=>"创建时间","lastModifyTm"=>"最后修改时间","status"=>"状态","count"=>"总数","successNum"=>"成功次数","errorNum"=>"失败次数")),
    ),
    /* 评价配置 */
    'APPRAISE' => array(
        #类型 = {允许取消: 1/0,点赞用户头像显示最大数}
        'poster' => array('allowCancel' => 1, 'listUserMaxNum' => 100),
        'cmt'    => array('allowCancel' => 1, 'listUserMaxNum' => 100),
        'cmtReply' => array('allowCancel' => 1, 'listUserMaxNum' => 100),
        'thread' => array('allowCancel' => 1, 'listUserMaxNum' => 100),
        'photo'  => array('allowCancel'=> 1, 'listUserMaxNum' => 100),
        'course_thread'  => array('allowCancel'=> 1, 'listUserMaxNum' => 100),
        'thread_post' => array('allowCancel'=> 1, 'listUserMaxNum' => 100),
    ),
    'TEST_ACCOUNT_CFG' => array(
        'NICKNAME' => '管理员',
        'DFT_PWD' => 'redcloud#!$$',
        'DFT_ROLE' => '|ROLE_USER|ROLE_SUPER_ADMIN|ROLE_TEACHER|',
    ),
    /* 引导配置 */
    'GUIDE_CFG' => array(
        'GUIDE_SHOW_MOBILE_SCAN_ICON' => array('interval' => 24 * 3600, 'expire' => '2015-10-14 10:22:22', 'name' => '首页扫码图标')
    ),
    'LIVE_PWD_TM' => 30, #获取动态密码间隔时间(秒)
    'CONVERT_FILE_FAIL' => 1, #文档转换失败是否发送邮件
    'CONVERT_VIDEO_FAIL' => 1, #视频转换失败是否发送邮件
    'SYNC_FILE_FAIL' => 1, #文件同步到云盘失败是否发送邮件
    'COURSE_DEFAULT_PIC' => array(
        '1' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default1.jpg',
        '2' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default2.jpg',
        '3' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default3.jpg',
        '4' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default4.jpg',
        '5' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default5.jpg',
        '6' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default6.jpg',
        '7' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default7.jpg',
        '8' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default8.jpg',
        '9' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default9.jpg',
        '10' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default10.jpg',
        '11' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default11.jpg',
        '12' => '/' . DATA_FETCH_URL_PREFIX . '/course/course-default12.jpg',
    ),
    'COURSE_DEFAULT_PIC_IDX' => array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12),
    'LOGIN_DEFAULT_PIC' => array(
        '1' => '/Public/static/img/login-icon/1.jpg',
        '2' => '/Public/static/img/login-icon/2.jpg',
        '3' => '/Public/static/img/login-icon/3.jpg',
        '4' => '/Public/static/img/login-icon/5.jpg',
        '5' => '/Public/static/img/login-icon/bg-pic.jpg',
    ),
    'REGISTER_SUCCESS_DEFAULT_PIC' => array(
        '0' => '/Public/static/img/zhuce/0.jpg',
        '1' => '/Public/static/img/zhuce/1.jpg',
        '2' => '/Public/static/img/zhuce/2.jpg',
        '3' => '/Public/static/img/zhuce/3.jpg',
        '4' => '/Public/static/img/zhuce/4.png',
        '5' => '/Public/static/img/zhuce/5.jpg',
    ),
    'REGISTER_POSTER_DEFAULT_PIC' => array(
        '0' => '/Public/static/img/zhuce/0.jpg',
        '1' => '/Public/static/img/register-icon/1.jpg',
        '2' => '/Public/static/img/register-icon/2.jpg',
        '3' => '/Public/static/img/register-icon/3.jpg',
    ),
    'USER_DEFAULT_PIC' => array(
        'TEACHER_MALE' => 'teacherAvatarMale.png', #/Public/assets/img/default/img/teacherAvatarMale.png
        'TEACHER_FEMALE' => 'teacherAvatarFemale.png',
        'STUDENT_MALE' => 'studentAvatarMale.png',
        'STUDENT_FEMALE' => 'studentAvatarFemale.png',
    ),
    'APP_BANNER_RULE' => array(
        'blank' => array('value' => '', 'memo' => '默认(无触发动作)', 'isConcat' => 0, 'tip' => ''),
        'gotoexampage' => array('value' => 0, 'memo' => '跳转特定的考试/作业', 'isConcat' => 1, 'tip' => '请输入考试或作业id'),
        'webpage' => array('value' => '', 'memo' => 'Web页面', 'isConcat' => 1, 'tip' => '请输入合法的url'),
        'gotorewardpage' => array('value' => '', 'memo' => '跳转任务奖励页面', 'isConcat' => 0, 'tip' => ''),
        'gotocoursedetail' => array('value' => '', 'memo' => '跳转课程播放页', 'isConcat' => 1, 'tip' => '请输入课程id'),
        'gotoclassdetail' => array('value' => '', 'memo' => '跳转班级详情', 'isConcat' => 1, 'tip' => '请输入班级id'),
        'gotomyquestionlist' => array('value' => 0, 'memo' => '跳转我的问答', 'isConcat' => 0, 'tip' => ''),
        'gotomyclasslist' => array('value' => 0, 'memo' => '跳转我的课程列表', 'isConcat' => 0, 'tip' => ''),
        'gotomycollection' => array('value' => 0, 'memo' => '跳转我的收藏', 'isConcat' => 0, 'tip' => ''),
        'gotomyexam' => array('value' => 0, 'memo' => '跳转我的考试', 'isConcat' => 0, 'tip' => ''),
        'gotomyhomework' => array('value' => 0, 'memo' => '跳转我的作业', 'isConcat' => 0, 'tip' => ''),
        'gototopicdetail' => array('value' => '', 'memo' => '跳转特定的话题详情', 'isConcat' => 1, 'tip' => '请输入话题id'),
        'gototeachingcenter' => array('value' => 0, 'memo' => '跳转教学中心-老师', 'isConcat' => 0, 'tip' => ''),
        'gotostudycenter' => array('value' => 0, 'memo' => '跳转学习中心-学生', 'isConcat' => 0, 'tip' => ''),
    ),
    'NO_SHOW_LOGO_PFX' => array(), #不展现logo后缀 （.高校云）
    /* 站点各类别结构名称 */
    'SITE_STRUC_NAME' => array(
        'CENTER' => '全部分类',
        'SCHOOL' => '院系结构',
        'COMPANY'=> '课程分类',
    ),
    /* 特殊结构名，默认“院系结构” */
    'SPECIAL_STRUC_NAME' => array(
        'peoplearn' => '课程分类', #众学网
        'thinkcathr'=> '课程分类', #北京弈博明道教育科技有限公司
        'bttzy'     => '共享性专业教学资源库', #包头铁道职业技术学院,
    ),
    /* 学校签约类别 by qzw 2015-11-25 */
    'SCHOOL_SIGNIN_TYPE' => array(
        0 => '未签',
        1 => '试用+授权',
        2 => '只试用',
        3 => '只授权',
        4 => '未签测试',
        5 => '正式',
        6 => '过期'
    ),
    /* 密钥配置 */
    'SECRET' => array(
        'APPRAISE' => 'q3!$-za#', #评价
        'COMMENT' => 'redcloud!#$$', #评论
    ),
    /* 问题反馈类型 */
    'PROBLEM_TYPE' => array(
        '0' => '系统建议',
        '1' => '系统问题',
        '2' => '课程建议',
        '3' => '课程纠错',
        '4' => '其他',
    ),
    /* 公共课程统一的名字 */
    'PUBLIC_COURSE_NAME' => "资源库课程",
    'NAVBAR_PUBLIC_COURSE_NAME' => "课程资源库",
    /* 本校课程程统一的名字 */
    'PRIVATE_COURSE_NAME' => "本校课程",
    /* 课程名的最大长度(60) */
    'COURSE_NAME_MAX_LENGTH' => 60,
     /* 签到最大时间 */
    'COURSE_SIGNIN_EXPIRE' => 60*60 ,
    /* 直播系统配置 */
    'COURSE_LIVE_FLOW' => 'rtmp',    #可选择 flv/rtmp/hls
    'COURSE_LIVE_RULE' => array(
        'min_tm_length' => 60,              #直播最小时长 单位秒
        'max_tm_length' => 12 * 3600,       #直播最大时长 单位秒
        'max_channel_num' => 5,             #直播频道最大数
        'before_enter_tm' => 5 * 60,        #学生提前进入直播间 单位秒
        'giveup_tm' => 15 * 60,             #预开播时间到达，老师Ｘ时间内未开始，则视为已结束 单位秒
        'request_stream_interval' => 5,     #学生端开播前每隔Ｘ秒请求流地址 单位秒
        'heartbeat_ck_interval' => 3 * 60,  #学生端掉线认定 单位秒
        'report_active_interval' => 3000,  #(直播端/web/app)心跳上报间隔 单位毫秒
        'report_fail_try_num' => 3,         #(直播端/web/app)上报失败尝试次数
        'stat_online_num' => 1 * 60,        #在线人数统计间隔
    ),
    /* 课程问答配置 */
    'COURSE_THREAD' => array(
        'is_allow_post' => 1,   # 是否允许非本课程学员使用问答功能
        'is_need_post'  => 0,   #是否要求答疑老师回答分本课程学员提问
        'is_grab_mode'  => 0,   #是否开始抢答模式
        'max_number'    => 3,   #允许最多占有数量
        'max_time'      => 30*60,   #每道问题占题时长
    ),
    /* sina 第三方登录的秘钥 */
    'SINA_AKEY' => 3347235018,
    'SINA_SKEY' => '6df93c00c08b555b8f23ec26cf137b1f',
   /* 腾讯云Cos文件上传配置 */
   /* 项目名*/
    'QCOS_BUCKETNAME' => "cloud",
    /* 项目id */
    'QCOS_APPID' => "10011123",
    /* 密钥SecretID */
    'QCOS_SECRETID' => "AKIDaZysHzwQBoe8sWGKCaD4UPuIGqyn536x",
    /* 密钥SecretKey */
    'QCOS_SECRETKEY' => "vnrUxcznD6JxpH2tC0H9dUhgEIenPfsh",
    
);
