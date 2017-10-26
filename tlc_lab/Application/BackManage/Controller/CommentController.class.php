<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Common\ServiceException;

class CommentController extends BaseController
{

    public function indexAction (Request $request)
    {   
        $comments = array();
        $total = $this->getCommentService()->getCommentsCountByType('course');
        $paginator = new Paginator($this->get('request'), $total, 30);
        $comments = $this->getCommentService()->getCommentsByType('course', $paginator->getOffsetCount(), $paginator->getPerPageCount());
        $userIds = ArrayToolkit::column($comments, 'userId');
        return $this->render('Comment:index',array(
            'comments' => $comments,
            'userList' => $this->getUserService()->findUsersByIds($userIds),
            'paginator' => $paginator));
    }

    public function deleteAction (Request $request, $id)
    {
        $comment = $this->getCommentService()->getComment($id);
        if (empty($comment)) {
            return $this->createJsonResponse(array('status' => 'error', array('message' => 'Not Exsit!')));
        }
        try {
            $this->getCommentService()->deleteComment($id);
            return $this->createJsonResponse(array('status' => 'ok'));
        } catch(ServiceException $e) {
            return $this->createJsonResponse(array('status' => 'ok', 'error' => array($e->getMessage())));
        }
    }

    private function getCommentService()
    {
        return createService('Content.CommentService');
    }

}