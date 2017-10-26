<?php
/**
 * 安全扩展
 * @author 钱志伟 2015-03-13
 */

namespace Common\Twig\Web;

/**
 * SecurityExtension exposes security context features.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SecurityExtension extends \Twig_Extension
{
    public function __construct(){}

    public function isGranted($role)
    {
        return isGranted($role);
    }
    
    public function isClassManager($uid){
       $groupServeiceModel= createService('Group.GroupServiceModel');
       $ownerClassList = $groupServeiceModel->searchGroups(array("ownerId" =>$uid), array("id", "DESC"), 0, 1);
       $adminMemeberList = $groupServeiceModel->searchMembers(array('role' => "admin", "userId" =>$uid), array('createdTime', 'DESC'), 0, 1);
       $headerList = $groupServeiceModel->searchMembers(array('role' => "header", "userId" =>$uid), array('createdTime', 'DESC'), 0, 1);
        if(!empty($ownerClassList) || !empty($adminMemeberList) || !empty($headerList)){
            return 1;
        }else{
            return 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('is_granted', array($this, 'isGranted')),
            new \Twig_SimpleFunction('is_userRole', array($this, 'isUserRole')),
            new \Twig_SimpleFunction('is_class_manager', array($this, 'isClassManager')),
            new \Twig_SimpleFunction('is_thread_teacher', array($this, 'isThreadTeacher')),
        );
    }

    /**
     * Returns the name of the extension.
     *
     * @return string The extension name
     */
    public function getName()
    {
        return 'security';
    }
    
    /**
     * 判断用户是否拥有权限进后台操作
     * @author fubaosheng 2015-05-14
     */
    public function isUserRole(){
        return createService("User.UserService")->isUserRole();
    }
    
    /*
     * 是否是答疑老师
     */
    public function isThreadTeacher(){
        $user = createService("User.UserService")->getCurrentUser();
        return createService('Thread.ThreadTeacherModel')->getTeacher($user['id']) ? TRUE : FALSE;
    }
}
