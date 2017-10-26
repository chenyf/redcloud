<?php

return array(
    'isCourseContentAsResource' => true, //是否将课程内容作为下载的资料
//    'cloudPanFilePathPrefix' => 'owncloud/data',
    'sync_cloud_passwd'        =>  'tlc',
    'php_bin_path'  =>  '/usr/local/php/bin/php',

    'owncloud/path/prefix' => REDISK_DATA_PATH,
    'sensitive/dict/filter' => "%kernel.root_dir%/Data/sensitive_dict_filter",
    'user/teacher/homepage_dir'  => STATIC_HTML_PATH . "user/teacher_personal_page",
//    'course/poster' => "%kernel.root_dir%/Public/dataFolder/course/poster",
    'course/poster' => DATA_PATH . "/course/poster",
    'course_packet_path' => DATA_PATH . "/coursepacket",    #课程资源包路径
    'sync/cloud/cli_script' =>  "%kernel.root_dir%/redisk/upload_cli.php",
//    'teacher/homepage/paper_file' =>  "%kernel.root_dir%/Public/dataFolder/paper",
    'teacher/homepage/paper_file' =>  DATA_PATH . "/data/paper",

    #tp下“.”有其他用途，命名中不能用它
    'kernel/root_dir'               => realpath(__ROOT__),
    'kernel/cache_dir'              => RUNTIME_PATH,
    'redcloud/disk/local_directory'   => "%kernel.root_dir%/Data/udisk",
    'category_system_icon'          => "%kernel.root_dir%/Data/udisk/categoryicon", #系统分类图标存放 qzw 2015-05-06
    'category_system_icon_urlpath'      => "/Data/udisk/categoryicon", #系统分类图标存放 qzw 2015-05-06
    'redcloud/disk/cloud_video_fop'   => "avthumb/mp4/r/30/vb/512k/vcodec/libx264/ar/22050/ab/64k/acodec/libfaac",
    'redcloud/disk/upgrade_dir'       => "%kernel.root_dir%/Data/upgrade",
    'redcloud/disk/update_dir'        => "%kernel.root_dir%/Data/upgrade",
    'redcloud/disk/backup_dir'        => "%kernel.root_dir%/Data/backup",
//    'redcloud/upload/public_directory'=> "%kernel.root_dir%/Public/dataFolder",
    'redcloud/upload/public_directory'=> DATA_PATH,
//    'redcloud/upload/public_url_path' => "/Public/dataFolder",
    'redcloud/upload/public_url_path' => "/" . DATA_FETCH_URL_PREFIX,
    'redcloud/upload/backup_url_path' => "/Data/backup",
    'redcloud/upload/private_url_path' => "/Data/private_files",
    'redcloud/upload/private_url_path_file' => "/Data/private_files/Upload",  ## 获取下载的路径Upload 12-22 zhuxu
    'redcloud/upload/private_directory'=> "%kernel.root_dir%/Data/private_files",
    'cloud_convertor'  => Array (
            'HLSVideo' => Array(
                    'video' => Array(
                            'low' => Array(0 => '240k', 1 => '440k', 2 => '640k' ),
                            'normal' => Array(0 => '440k', 1 => '640k', 2 => '1000k'),
                            'high' => Array(0 => '640k', 1 => '1000k', 2 => '1500k')
                        ),
                    'audio' => Array(
                            'low' => Array( 0 => '32k', 1 => '48k', 2 => '64k'),
                            'normal' => Array( 0 => '48k', 1 => '64k', 2 => '96k'),
                            'high' => Array( 0 => '64k', 1 => '96k', 2 => '128k')
                        ),
                    'segtime' => 10
                ),
            'HLSAudio' => Array( 'low' => '64k', 'normal' => '96k', 'high' => '128k' ),
            'audio' => Array( 'shd' => 'mp3',),
            'ppt' => Array( 'density' => 150, 'quality' => 80, 'resize' => 1200 )
        ),
    //头像缩略图设置
    'face_thumb_config'=>Array(
        'largeAvatar'=>array("width"=>200,'height'=>200),
        'mediumAvatar'=>array("width"=>120,'height'=>120),
        'smallAvatar'=>array("width"=>48,'height'=>48),
    ),
    'msg_error_log_file' =>array(
       '1'=>array('filePath'=> LOG_PATH . 'msg-error-note-'.date('y_m_d').'.log'),
       '2'=>array('filePath'=> LOG_PATH . 'msg-error-email-'.date('y_m_d').'.log'),
       '3'=>array('filePath'=> LOG_PATH . 'msg-error-push-'.date('y_m_d').'.log'),
       '4'=>array("filePath"=> LOG_PATH . 'msg-error-notice-'.date('y_m_d').'.log'),  
    ),
    'msg_str_len_config' =>Array(
        '1' =>array("titlelen"=>0,"contentlen"=>100,'msg'=>'短信','require'=>false),
        '2' =>array("titlelen"=>30,"contentlen"=>400,'msg'=>'邮件','require'=>true),
        '3' =>array("titlelen"=>30,"contentlen"=>100,'msg'=>'客户端推送','require'=>true),
        '4' =>array("titlelen"=>30,"contentlen"=>100,'msg'=>'站内信','require'=>true),
    ) ,
    
    'max_class_member_num'  =>  300,
    'max_course_class_num'  =>  1,

    'test_log_path' => LOG_PATH . "Test/test.log",
    'if_open_sign'  =>  0,  //是否开启签到功能
    'if_iface'  =>  0,  //是否采用接口登录、获取信息
);