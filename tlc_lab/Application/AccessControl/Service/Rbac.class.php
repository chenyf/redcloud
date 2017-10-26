<?php
/*
 * 后台rbac权限管理
 * @author     wanglei@redcloud.com
 * @created_at    16/3/29 下午1:16
 */
namespace AccessControl\Service;

use Think\Db;
/**
 * 基于角色的数据库方式验证类
 */
// 配置文件增加设置
// USER_AUTH_ON 是否需要认证
// USER_AUTH_TYPE 认证类型
// USER_AUTH_KEY 认证识别号
// REQUIRE_AUTH_MODULE  需要认证模块
// NOT_AUTH_MODULE 无需认证模块
// USER_AUTH_GATEWAY 认证网关
// RBAC_DB_DSN  数据库连接DSN
// RBAC_ROLE_TABLE 角色表名称
// RBAC_USER_TABLE 用户表名称
// RBAC_ACCESS_TABLE 权限表名称
// RBAC_NODE_TABLE 节点表名称

class Rbac
{

    //用于检测用户权限的方法,并保存到Session中
    public static function saveAccessList($authId = null)
    {
        if (null === $authId) {
            $authId = $_SESSION[RbacConfig::getConfig('USER_AUTH_KEY')];
        }

        // 如果使用普通权限模式，保存当前用户的访问权限列表
        // 对管理员开放所有权限
        if (RbacConfig::getConfig('USER_AUTH_TYPE') != 2 && !$_SESSION[RbacConfig::getConfig('ADMIN_AUTH_KEY')]) {
            $_SESSION['_ACCESS_LIST'] = self::getAccessList($authId);
        }

        return;
    }

    //检查当前操作是否需要认证
    public static function checkAccess()
    {
        //如果项目要求认证，并且当前模块需要认证，则进行权限认证
        if (RbacConfig::getConfig('USER_AUTH_ON')) {

            $_module = array();
            $_action = array();
            if ("" != RbacConfig::getConfig('REQUIRE_AUTH_MODULE')) {
                //需要认证的模块
                $_module['yes'] = explode(',', strtoupper(RbacConfig::getConfig('REQUIRE_AUTH_MODULE')));
            } else {
                //无需认证的模块
                $_module['no'] = explode(',', strtoupper(RbacConfig::getConfig('NOT_AUTH_MODULE')));
            }
            //检查当前模块是否需要认证
            if ((!empty($_module['no']) && !in_array(strtoupper(CONTROLLER_NAME), $_module['no'])) || (!empty($_module['yes']) && in_array(strtoupper(CONTROLLER_NAME), $_module['yes']))) {
                if ("" != RbacConfig::getConfig('REQUIRE_AUTH_ACTION')) {
                    //需要认证的操作
                    $_action['yes'] = explode(',', strtoupper(RbacConfig::getConfig('REQUIRE_AUTH_ACTION')));
                } else {
                    //无需认证的操作
                    $_action['no'] = explode(',', strtoupper(RbacConfig::getConfig('NOT_AUTH_ACTION')));
                }
                //检查当前操作是否需要认证
                if ((!empty($_action['no']) && !in_array(strtoupper(ACTION_NAME), $_action['no'])) || (!empty($_action['yes']) && in_array(strtoupper(ACTION_NAME), $_action['yes']))) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }
        return false;
    }

    //登录检查
    public static function checkLogin()
    {
        //检查当前操作是否需要认证
        if (self::checkAccess()) {
            //检查认证识别号,如果未登录
            if (!$_SESSION[RbacConfig::getConfig('USER_AUTH_KEY')]) {
                // 禁止游客访问跳转到认证网关
                redirect(PHP_FILE . RbacConfig::getConfig('USER_AUTH_GATEWAY'));

            }
        }
        return true;
    }

    //权限认证的过滤器方法
    public static function AccessDecision($appName = MODULE_NAME)
    {
        //检查是否需要认证
        if (self::checkAccess()) {
            //存在认证识别号，则进行进一步的访问决策
            $accessGuid = md5($appName . CONTROLLER_NAME . ACTION_NAME);
            if (empty($_SESSION[RbacConfig::getConfig('ADMIN_AUTH_KEY')])) {
                if (RbacConfig::getConfig('USER_AUTH_TYPE') == 2) {
                    //加强验证和即时验证模式 更加安全 后台权限修改可以即时生效
                    //通过数据库进行访问检查
                    $accessList = self::getAccessList($_SESSION[RbacConfig::getConfig('USER_AUTH_KEY')]);
                } else {
                    // 如果是管理员或者当前操作已经认证过，无需再次认证
                    if ($_SESSION[$accessGuid]) {
                        return true;
                    }
                    //登录验证模式，比较登录后保存的权限访问列表
                    $accessList = $_SESSION['_ACCESS_LIST'];
                }

                //判断是否为组件化模式，如果是，验证其全模块名
                if (!isset($accessList[strtoupper($appName)][strtoupper(CONTROLLER_NAME)][strtoupper(ACTION_NAME)])) {
                    $_SESSION[$accessGuid] = false;
                    return false;
                } else {
                    $_SESSION[$accessGuid] = true;
                }
            } else {
                //管理员无需认证
                return true;
            }
        }
        return true;
    }

    /**
     * 取得当前认证号的所有权限列表
     * @param integer $authId 用户ID
     * @access public
     */
    public static function getAccessList($authId)
    {
        $access_list = self::getRoleService()->getAccessList($authId);
        $_apps = array_filter($access_list, function ($val) {
            return $val['level'] == 1;
        });
        $access = array();
        foreach ($_apps as $app) {
            foreach ($access_list as $module) {
                if ($module['level'] == 2 && $module['pid'] == $app['id']) {
                    $_action = [];
                    foreach ($access_list as $action) {
                        if ($action['level'] == 3 && $action['pid'] == $module['id']) {
                            $action_name = strtoupper($action['name']);
                            $action_name = rtrim($action_name,"ACTION");
                            $action_name = $action_name."ACTION";
                            $_action[$action_name] = $action['id'];
                        }
                    }
                    $access[strtoupper($app['name'])][strtoupper($module['name'])] = array_change_key_case($_action, CASE_UPPER);
                }
            }
        }
        return $access;
    }

    protected static function getRoleService()
    {
        return createService('AccessControl.RoleService');
    }


}
