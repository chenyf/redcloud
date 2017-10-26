<?php
/*
 * Rbac配置
 * @author     wanglei@redcloud.com
 * @created_at    16/3/29 下午1:48
 */
namespace AccessControl\Service;

class RbacConfig{

    //配置
    private static $config = [

        'USER_AUTH_ON' => true, //是否需要认证
        'USER_AUTH_TYPE' => 2,  //认证类型(1,登录认证 2，实时认证)
        'USER_AUTH_KEY' => 'id', //认证识别号
        'USER_AUTH_GATEWAY' => '/User/Signin/indexAction',//未登录跳转
        'ADMIN_AUTH_KEY'      => 'administrator',     //超级管理员认证识别号

        //'REQUIRE_AUTH_MODULE' => [], //需要认证的模块
        //'NOT_AUTH_MODULE' => [],  //不需要认证的模块
    ];
    
    
    public static function getConfig($key){
        return self::$config[$key];
    }
    

}