<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class NotificationServiceModel extends BaseModel{
    
    public function getNotificationDao(){
        return $this->createService('User.NotificationModel');
    }
    
    private function getUserService(){
        return $this->createService('User.UserServiceModel');
    }
    
    /**
     * 给用户发送通知
     * 
     * @param  integer 		$userId  通知接收方用户ID
     * @param  string 	$type    通知类型
     * @param  mixed 	$content 通知内容，可以为string，array。
     * @param  array 	$ext 通知内容扩展
     */
    public function notify($userId, $type, $content,$ext=array()){
        
        $notification = array();
        $notification['userId'] = $userId;
        $notification['type'] = empty($type) ? 'default' : (string) $type;
        $content = is_array($content) ? $content : array('message' => $content);
        if(!empty($ext) && $notification['type'] == "groupJoinAction")  $content = array_merge ($content,$ext);
        $notification['content'] = $content;
        $notification['createdTime'] = time();
        $notification['isRead'] = 0;
        $this->getNotificationDao()->addNotification($this->notificationSerializeserialize($notification));
        $this->getUserService()->waveUserCounter($userId, 'newNotificationNum', 1);
        return true;
    }

    /**
     * 获得用户最新的通知
     * 
     * @param  integer 	$userId 用户ID
     * @param  integer 	$start  取通知记录的开始行数
     * @param  integer 	$limit  取通知记录的行数
     * 
     * @return array 用户最新的通知
     */
    public function findUserNotifications($userId, $start, $limit,$type=array()){
        return NotificationSerialize::unserializes(
            $this->getNotificationDao()->findNotificationsByUserId($userId, $start, $limit,$type)
        );
    }

    public function getUserNotificationCount($userId,$type=array()){
        return $this->getNotificationDao()->getNotificationCountByUserId($userId,$type);
    }

    public function clearUserNewNotificationCounter($userId){
        return $this->getUserService()->clearUserCounter($userId, 'newNotificationNum');
    }

    public  function notificationSerializeSerialize(array $notification){
        $notification['content'] = json_encode($notification['content']);
        return $notification;
    }

    public  function unserialize(array $notification = null){
        if (empty($notification)) {
            return null;
        }
        $notification['content'] = json_decode($notification['content'], true);
        return $notification;
    }

    public  function unserializes(array $notifications){
    	return array_map(function($notification) {
    		return $this->notificationSerializeUnserialize($notification);
    	}, $notifications);
    }
    
}

class NotificationSerialize
{
    public static function serialize(array $notification)
    {
        $notification['content'] = json_encode($notification['content']);
        return $notification;
    }

    public static function unserialize(array $notification = null)
    {
        if (empty($notification)) {
            return null;
        }
        $notification['content'] = json_decode($notification['content'], true);
        return $notification;
    }

    public static function unserializes(array $notifications)
    {
    	return array_map(function($notification) {
    		return NotificationSerialize::unserialize($notification);
    	}, $notifications);
    }
}
?>
