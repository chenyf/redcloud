<?php
return array(
    'pcCodeTimeOut' => 30, #移动端验证码超时时间
    'PC_CLIENT_TYPE' => array(1=>'windows', 2=>'Mac'),
    'pcTestHost' => array(
        'www.cloud.com',
        'test.gkk.cn',
        'wl.cloud.com',
        'fbs.cloud.com',
        'tht.cloud.com',
        'ck.cloud.com',
        'qzw.cloud.com',
        'demo.cloud.com',
        'hbsi.gkk.cn',
        'tht-live.cloud.com',
        'livechat.cloud.com',
    ),
    'PC_DEBUG_ALLOW_USER'=>array(
        '326728721@qq.com'
    ),



    'pcTestConfig' => array(
        'getVersionInfo' => array(
            'name' => '获取最新客户端版本【谈海涛OK】',
            'param' => array(
                'type' => array('text' => '客户端类型', 'default' => "1", 'memo' => "1=>'windows', 2=>'Mac'", 'must' => 1),
            )
        ),
        'intoLive' => array(
            'name'  => '进入直播页接口【谈海涛】完成',
            'param' => array(
                'liveId'     => array('text'=>'直播课id', 'default'=>'', 'memo'=>'', 'must'=>1 ),
                'deviceId'     => array('text'=>'设备Id', 'default'=>'', 'memo'=>'', 'must'=>1 ),
            ),
            'showLogin' => 1,
        ),
        'liveReport' => array(
            'name'  => '直播页心跳接口【谈海涛】完成',
            'param' => array(
                'liveId'     => array('text'=>'直播课id', 'default'=>'', 'memo'=>'', 'must'=>1 ),
                'deviceId'     => array('text'=>'设备Id', 'default'=>'', 'memo'=>'', 'must'=>1 ),
            ),
            'showLogin' => 1,
        ),
        'finishLive' => array(
            'name'  => '结束直播【谈海涛OK】',
            'param' => array(
                'liveId'     => array('text'=>'直播id', 'default'=>'', 'memo'=>'', 'must'=>1),
            ),
            'showLogin' => 1,
        ),
        'startLive' => array(
            'name'  => '开始直播【谈海涛OK】',
            'param' => array(
                'liveId'     => array('text'=>'直播id', 'default'=>'', 'memo'=>'', 'must'=>1),
                'channelId'  => array('text'=>'直播频道id', 'default'=>'', 'memo'=>'', 'must'=>1),
                'liveUrl'    => array('text'=>'直播流', 'default'=>'', 'memo'=>'', 'must'=>1)
            ),
            'showLogin' => 1,
        ),
         'getLiveList' => array(
            'name'  => '获得课程直播列表【谈海涛】完成',
            'param' => array(
                
            ),
            'showLogin' => 1,
        ),
        'login' => array(
            'name'  => '用户登录【谈海涛OK】',
            'param' => array(
                'account'     => array('text'=>'邮箱/手机号', 'default'=>'1565995637@qq.com', 'memo'=>'', 'must'=>1),
                'password'  => array('text'=>'密码', 'default'=>'', 'memo'=>'', 'must'=>1, 'pwd'=>1)
            )
        ),
        'logout' => array(
            'name'  => '退出【谈海涛】完成',
            'param' => array(
            ),
            'showLogin' => 1,
        ),
    ),
);
