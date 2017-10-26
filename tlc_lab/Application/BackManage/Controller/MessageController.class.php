<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;

class MessageController extends BaseController
{
    public function indexAction(Request $request)
    {   
        $fields = $request->query->all();
        $conditions = array(
            'content'=>'',
            'nickname'=>'',
            'startDate'=>0,
            'endDate'=>time()
        );
        if(!empty($fields)){
            $conditions = $this->convertConditions($fields);
        }
        
        $paginator = new Paginator(
            $request,
            $this->getMessageService()->searchMessagesCount($conditions),
            20
        );

        $messages = $this->getMessageService()->searchMessages(
            $conditions,
            null,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        ); 

        $usersFromId = $this->getUserService()->findUsersByIds(ArrayToolkit::column($messages, 'fromId'));
        $usersToId = $this->getUserService()->findUsersByIds(ArrayToolkit::column($messages, 'toId'));
        $users = ArrayToolkit::index(array_merge($usersFromId, $usersToId), 'id');
        return $this->render('Message:index',array(
            'users'=>$users,
            'messages' => $messages,
            'paginator' => $paginator));
    }

    public function deleteChoosedMessagesAction(Request $request)
    {  
        $ids = $request->request->get('ids');
        $result = $this->getMessageService()->deleteMessagesByIds($ids);
        if($result){
           return $this->createJsonResponse(array("status" =>"failed")); 
       } else {
           return $this->createJsonResponse(array("status" =>"success")); 
       }
    
    }

    private function convertConditions($conditions)
    {
        if (!empty($conditions['nickname'])) {
            $user = $this->getUserService()->getUserByNickname($conditions['nickname']);
            if (empty($user)) {
                return $this->createMessageResponse('error',sprintf("姓名为%s的用户不存在", $conditions['nickname']));
            }
            $conditions['fromId'] = $user['id'];
        }
        
        unset($conditions['nickname']);

        if (empty($conditions['content'])) {
            unset($conditions['content']);
        }

        if(empty($conditions['startDate'])){
             unset($conditions['startDate']);
        }

        if(empty($conditions['endDate'])){
             unset($conditions['endDate']);
        }

        if(isset($conditions['startDate'])){
            $conditions['startDate'] = strtotime($conditions['startDate']);
        }

        if(isset($conditions['endDate'])){
            $conditions['endDate'] = strtotime($conditions['endDate']);
        }

        return $conditions;
    }

    public function deleteAction(Request $request, $id)
    {
        $this->getFileService()->deleteFile($id);
        return $this->createNewJsonResponse(true);
    }

    public function uploadAction(Request $request)
    {
        return $this->render('File:upload-modal');
    }

    protected function getMessageService()
    {
        return createService('User.MessageService');
    }

    protected function getUserService()
    {
        return createService('User.UserService');
    }

    protected function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getSettingService()
    {
        return createService('System.SettingService');
    }
}