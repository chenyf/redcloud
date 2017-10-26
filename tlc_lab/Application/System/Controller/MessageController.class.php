<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Resources\translations;
use Common\Lib\Paginator;
use Common\Form\MessageType;
use Common\Form\MessageReplyType;
use Common\Lib\ArrayToolkit;

class MessageController extends \Home\Controller\BaseController
{
    /**
     * 判断登录
     * @author fubaosheng 2015-06-02
     */
    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            $this->jsonResponse(403, 'Unlogin');
    }
        
    public function indexAction (Request $request)
    {
        $user = $this->getCurrentUser();
        
        $paginator = new Paginator(
            $request,
            $this->getMessageService()->getUserConversationCount($user->id),
            10
        );
        $conversations = $this->getMessageService()->findUserConversations(
            $user->id,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($conversations, 'fromId'));

        $this->getMessageService()->clearUserNewMessageCounter($user['id']);

        return $this->render('Message:index', array(
            'conversations' => $conversations,
            'users' => $users,
            'paginator' => $paginator
        ));
    }
    public function checkReceiverAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $nickname = $request->query->get('value');
        $result = $this->getUserService()->isNicknameAvaliable($nickname);
        if ($result) {
            $response = array('success' => false, 'message' => '该收件人不存在');
        } else if ($currentUser['nickname'] == $nickname){
            $response = array('success' => false, 'message' => '不能给自己发私信哦！');
        } else {
            $response = array('success' => true, 'message' => '');
        }
        return $this->createJsonResponse($response);
    }

    public function showConversationAction(Request $request, $conversationId)
    {
        $user = $this->getCurrentUser();
        $conversation = $this->getMessageService()->getConversation($conversationId);
        if (empty($conversation) or $conversation['toId'] != $user['id']) {
            return $this->createMessageResponse('error','私信会话不存在！');
        }
        $paginator = new Paginator(
            $request,
            $this->getMessageService()->getConversationMessageCount($conversationId),
            10
        );

        $this->getMessageService()->markConversationRead($conversationId);

        $messages = $this->getMessageService()->findConversationMessages(
            $conversation['id'], 
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
        $form = $this->createForm(new MessageReplyType());
        if($request->getMethod() == 'POST'){
            $form->bind($request);
//            if($form->isValid()){
            if(1){
                $message = $form->getData();
                $message = $this->getMessageService()->sendMessage($user['id'], $conversation['fromId'], $message['content']);
                $html = $this->renderView('Message/item', array('message' => $message, 'conversation'=>$conversation));
                return  $this->createJsonResponse_a(array('status' => 'ok', 'html' => $html));
            }
        }
        return $this->render('Message:conversation-show', array(
            'conversation'=>$conversation, 
            'messages'=>$messages,
            'receiver'=>$this->getUserService()->getUser($conversation['fromId']),
            'form' => $form->createView(),
            'res' => $res,
            'paginator' => $paginator
        ));
    }

    public function createAction(Request $request, $toId)
    {
        $userSerObj = $this->getUserService();
        $user = $this->getCurrentUser();
        if(!$user->id) $this->jsonResponse(403, 'Unlogin');
        
        $receiver = $userSerObj->getUser($toId);
        $nick_name = $receiver['nickname'];
        $form = $this->createForm(new MessageType(), array('receiver'=>$receiver['nickname']));
        if($request->getMethod() == 'POST'){
            if (isCloseUserWrite()) {
                return $this->createMessageResponse('error',"抱歉，发送私信功能已关闭!");
            }
            $form->bind($request);
            //by yangjinlong            if($form->isValid()){
            if(1){
                $message = $form->getData();
                $nickname = $message['receiver'];
                $receiver = $userSerObj->getUserByNickname($nickname);
                if(empty($receiver)) {
                    return $this->createMessageResponse('error',"抱歉，该收信人尚未注册!");
                }
                $this->getMessageService()->sendMessage($user['id'], $receiver['id'], $message['content']);
                return $this->redirect($this->generateUrl('message'));
            }
        }
        return $this->render('Message:send-message-modal', array(
//                'form' => $form->createView(),
                'nickname'=>$nick_name,
                'userId'=>$toId));
    }

    //联系管理员
    public function contactManagerAction(Request $request){
        $userSerObj = $this->getUserService();
        $user = $this->getCurrentUser();
        if(!$user->id){
            return $this->jsonResponse(403, 'Unlogin');
        }
        if(!$user->isTeacher()) {
            return $this->createMessageResponse("info","只有老师才有权操作");
        }

        if($request->getMethod() == 'POST'){
            $adminList = $userSerObj->getAdminList();
            $message = isset($_POST['message']) ? trim($_POST['message']) : "";
            if(empty($message)){
                return $this->createJsonResponse(array('status' => false,'message'=>'内容不能为空'));
            }
            foreach ($adminList as $admin){
                $this->getMessageService()->sendMessage($user['id'], $admin['id'], $message['content']);
            }

            return $this->createJsonResponse(array('status' => true,'message'=>'私信成功！'));
        }

        return $this->render('Message:contact-admin-modal');
    }

    public function sendAction(Request $request) 
    {
        $user = $this->getCurrentUser();
        if(!$user->id) {
            Header("Location: /User/Signin/indexAction");
        };
        $receiver = array();
        $form = $this->createForm(new MessageType());
        if($request->getMethod() == 'POST'){
            if(isCloseUserWrite()){
                return $this->createMessageResponse('error',"抱歉，发送私信功能已关闭!");
            }
            $form->bind($request);
            //by yangjinlong            if($form->isValid()){
            if(1){
                $message = $form->getData();
                $nickname = $message['receiver'];
                $receiver = $this->getUserService()->getUserByNickname($nickname); 
                if(empty($receiver)){
                    return $this->createMessageResponse('error',"抱歉，该收信人尚未注册!");
                }
                $this->getMessageService()->sendMessage($user['id'], $receiver['id'], $message['content']);
            }
            return $this->redirect($this->generateUrl('message'));
        }
        return $this->render('Message:create', array(
//                'form' => $form->createView())
                ));
    }

    public function sendToAction(Request $request, $receiverId)
    {
        $receiver = $this->getUserService()->getUser($receiverId);
        $user = $this->getCurrentUser();
        $form = $this->createForm(new MessageType(), array('receiver'=>$receiver['nickname']));
        if($request->getMethod() == 'POST'){
            $form->bind($request);
            if($form->isValid()){
                $message = $form->getData();
                $nickname = $message['receiver'];
                $receiver = $this->getUserService()->getUserByNickname($nickname); 
                if(empty($receiver)){
                    return $this->createMessageResponse('error',"抱歉，该收信人尚未注册!");
                }
                $this->getMessageService()->sendMessage($user['id'], $receiver['id'], $message['content']);
            }
            return $this->redirect($this->generateUrl('message'));
        }
        return $this->render('Message:create', array(
                'form' => $form->createView()));
    }


    public function deleteConversationAction(Request $request, $conversationId)
    {
        $user = $this->getCurrentUser();
        $conversation = $this->getMessageService()->getConversation($conversationId);
        if (empty($conversation) or $conversation['toId'] != $user['id']) {
            throw $this->createAccessDeniedException('您无权删除此私信！');
        }

        $this->getMessageService()->deleteConversation($conversationId);
        return $this->redirect($this->generateUrl('message'));
    }

    public function deleteConversationMessageAction(Request $request, $conversationId, $messageId)
    {
        $user = $this->getCurrentUser();
        $conversation = $this->getMessageService()->getConversation($conversationId);
        if (empty($conversation) or $conversation['toId'] != $user['id']) {
            throw $this->createAccessDeniedException('您无权删除此私信！');
        }
        
        $this->getMessageService()->deleteConversationMessage($conversationId, $messageId);
        $messagesCount = $this->getMessageService()->getConversationMessageCount($conversationId);
        if($messagesCount > 0){
            return $this->redirect($this->generateUrl('message_conversation_show',array('conversationId' => $conversationId)));
        }else {
           return $this->redirect($this->generateUrl('message'));
        }
    }

    public function matchAction(Request $request)
    {
        $currentUser = $this->getCurrentUser();
        $data = array();
        $queryString = $request->query->get('q');
        $callback = $request->query->get('callback');
        $findedUsersByNickname = $this->getUserService()->searchUsers(
            array('nickname'=>$queryString),
            0,
            10);
        $findedFollowingIds = $this->getUserService()->filterFollowingIds($currentUser['id'], 
            ArrayToolkit::column($findedUsersByNickname, 'id'));

        $filterFollowingUsers = $this->getUserService()->findUsersByIds($findedFollowingIds);

        foreach ($filterFollowingUsers as $filterFollowingUser) {
            $data[] = array(
                'id' => $filterFollowingUser['id'], 
                'nickname' => $filterFollowingUser['nickname']
            );
        }

        return new JsonResponse($data);
    }

    private function getWebExtension()
    {
        return $this->container->get('redcloud.twig.web_extension');
    }

    protected function getUserService(){
        return createService('User.UserService');
    }

    protected function getMessageService(){
        return createService('User.MessageService');
    }
}