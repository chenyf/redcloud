<?php
return array(
    #推送日志文件
    'PUSH_LOG_FILE' => LOG_PATH . 'push-%s-'.date('y_m_d').'.log',
    'QZW' => LOG_PATH . 'qzw-'.date('y_m_d').'.log',
    #班级组tag前缀
    'PUSH_CLASS_TAG_KEY_PFX_DEV'      => 'class_', #推送tag键名格式:例如class_3、class_31
    'PUSH_CLASS_TAG_KEY_PFX_PRO'      => 'class_product_', #推送tag键名格式:例如class_3、class_31
    #推送消息主题, 可以据此查询消息记录
    'PUSH_TOPIC_CLASS' => 'classTopic', #消息的topic，长度限制为 1-128个长度的「字母和数字」组合
    #推送业务统一配置 qzw 2016-4-5
    'PUSH_GLOBAL_STATUS' => 1,  #平台推送开关，不影响加入推送队列，在执行推送任务时检测  0=>关闭 1=>开启
    #推送描述最大长度，超出进行截取: 文本前{PUSH_MAX_LEN-3}个+...
    'PUSH_MAX_LEN' => array(
        'ANDROID' => 300,
        'IOS'     => 60
    ),
    #推送记录判断是否已读起始时间戳
    'PUSH_READ_BEGIN_TIME' => '1460634727',
    #推送过期时间
    'PUSH_EXPIRED_TIME' => '',
    #全局推送配置
    'PUSH_WORK_CFG' => array(
        'NOTICECLASS' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array( #该模块下的所有需要推送操作的业务
                ## 话题 开始 #######################
                'stickTopic' => array(
                    'TITLE'  => '置顶了你的话题',
                    'ARGS'   => 'publicCourse,image,topicId,topicTitle'
                ),
                'eliteTopic' => array(
                    'TITLE'  => '将你的话题设为了精华',
                    'ARGS'   => 'publicCourse,image,topicId,topicTitle'
                ),
                'appriseTopic' => array(
                    'TITLE'  => '赞了你的话题',
                    'ARGS'   => 'publicCourse,image,topicId,topicTitle'
                ),
                'commentTopic' => array(
                    'TITLE'  => '评论了你的话题',
                    'ARGS'   => 'publicCourse,image,topicId,topicTitle'
                ),
                'replyTopic' => array(
                    'TITLE'  => '回复了你的话题',
                    'ARGS'   => 'publicCourse,image,replyId,topicId,topicTitle'
                ),
                'replyTopicComment' => array(
                    'TITLE'  => '回复了你的评论',
                    'ARGS'   => 'publicCourse,image,replyId,topicId,replyTitle'
                ),
                ## 话题 结束 #######################
                
                ## 相册 开始 #######################
                'appriseImage' => array(
                    'TITLE'  => '赞了你的照片',
                    'ARGS'   => 'publicCourse,image,imageName'
                ),
                'deleteImage' => array(
                    'TITLE'  => '删除了你的照片',
                    'ARGS'   => 'publicCourse,image,imageName'
                ),
                ## 相册 结束 #######################
                
                ## 文件 开始 #######################
                'editFile' => array(
                    'TITLE'  => '更改了你共享的文件名称',
                    'ARGS'   => 'publicCourse,image,oldFileName,newFileName'
                ),
                'deleteFile' => array(
                    'TITLE'  => '删除了你共享的文件',
                    'ARGS'   => 'publicCourse,image,fileName'
                ),
                ## 文件 结束 #######################
                
                ## 成员 开始 #######################
                'addMember' => array(
                    'TITLE'  => '已将你加入班级',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                'removeMember' => array(
                    'TITLE'  => '已将你移出班级',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                'applyClass' => array(
                    'TITLE'  => '申请加入班级',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                'applyClassOk' => array(
                    'TITLE'  => '审核了你加入班级的申请',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                'applyClassNo' => array(
                    'TITLE'  => '审核了你加入班级的申请',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                'applyStudentNum' => array(
                    'TITLE'  => '申请学号',
                    'ARGS'   => 'publicCourse,image,studentNum'
                ),
                'applyStudentNumOk' => array(
                    'TITLE'  => '审核了你的学号',
                    'ARGS'   => 'publicCourse,image,studentNum'
                ),
                'applyStudentNumNo' => array(
                    'TITLE'  => '审核了你的学号',
                    'ARGS'   => 'publicCourse,image,studentNum,reason'
                ),
                'leaveClass' => array(
                    'TITLE'  => '退出了班级',
                    'ARGS'   => 'publicCourse,image,classId,className'
                ),
                ## 成员 结束 #######################
                
                ## 班级身份 开始 ###################
                'beCST' => array(
                    'TITLE'  => '已将你设置成为「班委」',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                'revokeCST' => array(
                    'TITLE'  => '撤销了你的「班委」职位',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                'beMaster' => array(
                    'TITLE'  => '已将你设置成为「班主任」',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                'revokeMaster' => array(
                    'TITLE'  => '撤销了你的「班主任」职位',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                'beCreater' => array(
                    'TITLE'  => '已将你设置成为「创建人」',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                'revokeCreater' => array(
                    'TITLE'  => '撤销了你的「创建人」身份',
                    'ARGS'   => 'publicCourse,image,className'
                ),
                ## 班级身份 结束 ###################
            )
        ),
        'POSTER' => array(
            'STATUS' => 0, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                'apprisePoster' => array(
                    'TITLE'  => '赞了你的海报',
                    'ARGS'   => 'publicCourse,image,posterId,posterTitle'
                ),
                'commentPoster' => array(
                    'TITLE'  => '评论了你的海报',
                    'ARGS'   => 'publicCourse,image,posterId,posterTitle'
                ),
                'replyPoster' => array(
                    'TITLE'  => '回复了你的海报',
                    'ARGS'   => 'publicCourse,image,posterId,replyId,posterTitle'
                ),
                'replyPosterComment' => array(
                    'TITLE'  => '回复了你的评论',
                    'ARGS'   => 'publicCourse,image,posterId,replyId,replyTitle'
                )
            )
        ),
        'COURSE' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                ## 作业 开始 #######################
                'beginHomework' => array(
                    'TITLE'  => '你的课程作业已开始',
                    'ARGS'   => 'publicCourse,image,courseId,homeworkId,beginTime,homeworkName'
                ),
                'submitHomework' => array(
                    'TITLE'  => '完成了你布置的作业',
                    'ARGS'   => 'publicCourse,image,courseId,homeworkId,homeworkName'
                ),
                'markHomework' => array(
                    'TITLE'  => '批阅了你的作业',
                    'ARGS'   => 'publicCourse,image,courseId,homeworkId,homeworkName'
                ),
                ## 作业 结束 #######################
                
                ## 考试 开始 #######################
                'beginTest' => array(
                    'TITLE'  => '你的课程考试已开始',
                    'ARGS'   => 'publicCourse,image,courseId,testId,beginTime,testName'
                ),
                'submitTest' => array(
                    'TITLE'  => '完成了你布置的考试',
                    'ARGS'   => 'publicCourse,image,courseId,testId,testName'
                ),
                'markTest' => array(
                    'TITLE'  => '批阅了你的考试',
                    'ARGS'   => 'publicCourse,image,courseId,testId,testName'
                ),
                ## 考试 结束 #######################
                
                ## 问答 开始 #######################
                'submitThread' => array(
                    'TITLE'  => '提出了问题',
                    'ARGS'   => 'publicCourse,image,courseId,threadId,threadTitle,threadUid'
                ),
                'answerThread' => array(
                    'TITLE'  => '回答了你的问题',
                    'ARGS'   => 'publicCourse,image,courseId,answerId,threadId,threadTitle,threadUid'
                ),
                'appendAnswer' => array(
                    'TITLE'  => '追问了你的回答',
                    'ARGS'   => 'publicCourse,image,courseId,answerId,threadId,threadTitle,threadUid'
                ),
                'answerAppend' => array(
                    'TITLE'  => '回答了你的追问',
                    'ARGS'   => 'publicCourse,image,courseId,answerId,threadId,threadTitle,threadUid'
                ),
                'eliteThread' => array(
                    'TITLE'  => '已将你的问题设为了精华',
                    'ARGS'   => 'publicCourse,image,courseId,threadId,threadTitle,threadUid'
                ),
                'adoptedThread' => array(
                    'TITLE'  => '采纳了你的回答',
                    'ARGS'   => 'publicCourse,image,courseId,threadId,threadTitle,threadUid'
                ),
                ## 问答 结束 #######################
                
                ## 关闭课程 开始 #######################
                'closeCourse' => array(
                    'TITLE'  => '关闭了你的课程',
                    'ARGS'   => 'publicCourse,image,courseId,courseTitle'
                ),
                ## 关闭课程 结束 #######################
                
                ## 私转公 开始 #######################
                'privateToPublicOk' => array(
                    'TITLE'  => '你申请加入资源库的课程已审核完成',
                    'ARGS'   => 'publicCourse,image,courseId,courseTitle'
                ),
                'privateToPublicNo' => array(
                    'TITLE'  => '你申请加入资源库的课程已审核完成',
                    'ARGS'   => 'publicCourse,image,courseId,courseTitle'
                ),
                ## 私转公 结束 #######################
                
                ## 公转私 开始 #######################
                'publicToPrivateOk' => array(
                    'TITLE'  => '你申请的在教课程已提交完成',
                    'ARGS'   => 'publicCourse,image,courseId,courseTitle'
                ),
                'publicToPrivateNo' => array(
                    'TITLE'  => '你申请的在教课程已提交完成',
                    'ARGS'   => 'publicCourse,image,courseId,courseTitle'
                ),
                ## 公转私 结束 #######################
            )
        ),
        'IDENTITY' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                'edit' => array(
                    'TITLE'  => '你当前的账户身份已变更为',
                    'ARGS'   => 'publicCourse,image,role'
                )
            )
        ),
        'SIGN' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                'onLineSign' => array(
                    'TITLE'  => '已发起点名签到',
                    'ARGS'   => 'publicCourse,image,signId,courseId,courseClassId,courseTitle'
                ),
                'offLineSign' => array(
                    'TITLE'  => '你已离线签到成功',
                    'ARGS'   => 'publicCourse,image,signId,courseId,courseClassId,courseTitle'
                ),
                'revertSignStatus' => array(
                    'TITLE'  => '将你的「已签到」改为「未签到」',
                    'ARGS'   => 'publicCourse,image,signId,courseId,courseClassId,courseTitle'
                )
            )
        ),
        'LIVE' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                'liveJust2Begin' => array(
                    'TITLE'  => '你的课程直播即将开始',
                    'ARGS'   => 'publicCourse,image,courseLiveId,createUid,beginTime,courseTitle'
                )
            )
        ),
        'CHAT' => array(
            'STATUS' => 1, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array(
                'chat' => array(
                    'TITLE'  => 'App会话',
                    'ARGS'   => 'publicCourse,image,chatId'
                )
            )
        ),
        'NIGHT' => array(
            'STATUS' => 0, #服务端是否启用  0=>不启用  1=>启用
            'OPERATIONS' => array()
        ),
        
    )
);
