<?php
/*
 * 监听应用标签位(钩子)
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
$LISTEN = array(

    //审核资源课程库申请
    'Course_checkCourseApply' => array(
        'Common\\Behaviors\\Course\\checkCourseApplyBehavior',
    ),

    //创建资源课程库申请
    'Course_createCourseApply' => array(
        'Common\\Behaviors\\Course\\createCourseApplyBehavior',
    ),

    //资源课程库复制完成
    'Course_CommonCourseCopyDone' => array(
        'Common\\Behaviors\\Course\\CommonCourseCopyDoneBehavior',
    ),

    //创建课程之后
    'created_course' => array(
        'Common\\Behaviors\\Course\\createdCourseBehavior',
    ),

    'generalize_buy' => array(
        'Common\\Behaviors\\Generalize\\GeneralizeBuyBehavior',
    ),
    'registered' => array(
        'Common\\Behaviors\\GeneralizeBehavior',
    ),
    'bindMobileGeneralize' => array(
        'Common\\Behaviors\\BindMobileGeneralizeBehavior',
    ),
    //用户登录
    'user_login' => array(
        'Common\\Behaviors\\User\\UserLoginBehavior',
    ),
    //班级话题
    'class_thread' => array(
        'Common\\Behaviors\\Wclass\\ClassThreadBehavior',
    ),
    //班级话题回复
    'class_thread_post' => array(
        'Common\\Behaviors\\Wclass\\ClassThreadPostBehavior',
    ),
    //班级相册
    'class_photo' => array(
        'Common\\Behaviors\\Wclass\\ClassPhotoBehavior',
    ),
    //班级文件
    'class_file' => array(
        'Common\\Behaviors\\Wclass\\ClassFileBehavior',
    ),
    //班级成员
    'class_member' => array(
        'Common\\Behaviors\\Wclass\\ClassMemberBehavior',
    ),
    //班级加入申请
    'class_apply' => array(
        'Common\\Behaviors\\Wclass\\ClassApplyBehavior',
    ),
    //学号申请与审核
    'check_studNum' => array(
        'Common\\Behaviors\\Wclass\\CheckStudNumBehavior',
    ),
    //公转私
    'pubCourseToPri'=>array(
        'Common\\Behaviors\\Course\\PubCourseToPriBehavior',
    )

);


return compact('LISTEN');