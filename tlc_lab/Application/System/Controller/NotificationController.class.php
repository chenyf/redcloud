<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class NotificationController extends \Home\Controller\BaseController
{


    public function indexAction (Request $request)
    {
        $user = $this->getCurrentUser();
        if (!$user->isLogin()) {
            throw $this->createAccessDeniedException();
        }
        
        $paginator = new Paginator(
            $request,
            $this->getNotificationService()->getUserNotificationCount($user->id),
            20
        );

        $notifications = $this->getNotificationService()->findUserNotifications(
            $user->id,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $this->getNotificationService()->clearUserNewNotificationCounter($user->id);

        return $this->render('Notification:index', array(
            'notifications' => $notifications,
            'paginator' => $paginator
        ));
    }
    
    public function checkJoinGroupAction(Request $request){
        if($request->getMethod() == 'GET')
            return $this->createJsonResponse('GET method');
        $data = $request->request->all();
        $user = $this->getCurrentUser();
        if(empty($user['id']))
            $this->ajaxReturn(array('status'=>'error','info'=>'您尚未登录'));
        $apply = $this->getGroupApplyService()->findById($data['id']);
        if(empty($apply))
            $this->ajaxReturn(array('status'=>'error','info'=>"记录(#{$data['id']})不存在"));
        if(!in_array($data['status'], array('1','2')))
            $this->ajaxReturn(array('status'=>'error','info'=>'类型错误'));
        if(!$this->checkManagePermission($apply['groupId']))
            $this->ajaxReturn(array('status'=>'error','info'=>'没有权限操作'));

        $result = $this->getGroupApplyService()->checkApply(array('id'=>$data['id'],'status'=>$data['status']),$user['id']);
        if($result){
            $notify = createService('User.NotificationService');
            $group = $this->getGroupService()->getGroup($apply['groupId']);
            if($data['status'] == '1'){
                $notify->notify($apply['uid'],'','您申请加入班级:【'.$group['title'].'】审核通过');
                $member = getUserBaseInfo($apply['uid']);
                $this->getGroupService()->joinGroup($member,$apply['groupId'],$user['id']);
                $this->ajaxReturn(array('status'=>'passSuccess'));
            }
            if($data['status'] == '2'){
                $notify->notify($apply['uid'],'','您申请加入班级:【'.$group['title'].'】审核未通过');
                $this->ajaxReturn(array('status'=>'noPassSuccess'));
            }
        }else{
            $this->ajaxReturn(array('status'=>'error','info'=>'操作失败'));
        }
    }
    
    /**
    * 检查权限
    * @param $id
    * @return bool
    */
    private function checkManagePermission($id){
        $user = $this->getCurrentUser();
        if(isGranted('ROLE_ADMIN')==true) return true;
        if($this->getGroupService()->isOwner($id, $user['id'])) return true;
        if($this->getGroupService()->isAdmin($id, $user['id'])) return true;
        if($this->getGroupService()->isHeader($id, $user['id'])) return true;
        return false;
    }
    
    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getUserService()
    {
        return createService('User.UserService');
    }

    protected function getNotificationService()
    {
        return createService('User.NotificationService');
    }
    
    private function getGroupApplyService() {
        return createService('Group.GroupApplyService');
    }

    private function getGroupService() {
        return createService('Group.GroupService');
    }
}