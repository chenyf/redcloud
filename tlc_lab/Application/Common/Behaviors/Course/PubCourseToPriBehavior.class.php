<?php
/*
 * 公转私完成后
 * @package
 * @author     wanglei@redcloud.com
 * @version    $Id$
 */
namespace Common\Behaviors\Course;

use Think\Behavior;

class PubCourseToPriBehavior extends Behavior
{

    public function run(&$param)
    {

		$userId = $param['userId'];
        $public_course = $param['public_course'];
        $private_course = $param['public_course'];

        //发送通知
        $redirect_url = U('Course/CourseManage/base', array('id' => $private_course['id']));
        $redirect_url = ltrim($redirect_url, '.');
        $this->getNotifyService()->notify(
            $userId,
            '',
            "资源库课程《".$public_course['title']."》已成为您的在教课程！<a href=".$redirect_url.">查看详情</a>"
        );
        $this->getPushService()->pushUser(
            $userId,
            "COURSE",
            'publicToPrivateOk',
            array('courseId'=>$private_course['id'],'courseTitle'=>$private_course['title'])
        );
    }

    private function getNotifyService()
    {
        return createService('User.NotificationService');
    }

    private function getPushService(){
        return createService("Group.GroupPushServiceModel");
    }


}