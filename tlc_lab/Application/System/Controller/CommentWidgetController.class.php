<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;

use Common\Lib\ArrayToolkit;
use Common\Form\CommentType;

class CommentWidgetController extends \Home\Controller\BaseController
{
 
    public function initAction(Request $request)
    {
        $objectType = $request->query->get('objectType');
        $objectId = $request->query->get('objectId');

        $form = $this->createForm(new CommentType(), array(
            'objectType' => $objectType,
            'objectId' => $objectId,
        ));

        $comments = $this->getCommentService()->findComments($objectType, $objectId, 0, 1000);
        $users = $this->getUserService()->findUsersByIds(ArrayToolkit::column($comments, 'userId'));
        return $this->render('CommentWidget:init', array(
            'form' => $form->createView(),
            'comments' => $comments,
            'users' => $users,
        ));
    }

    public function createAction(Request $request)
    {
        $form = $this->createForm(new CommentType());
        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $comment = $form->getData();
                $comment = $this->getCommentService()->createComment($comment);
                return $this->render('CommentWidget:item', array(
                    'comment' => $comment,
                    'user' => $this->getCurrentUser(),
                ));
            }
        }
    }

    public function deleteAction(Request $request)
    {
        $id = $request->query->get('id');
        $this->getCommentService()->deleteComment($id);
        return $this->createJsonResponse(true);
    }

    private function getCommentService()
    {
        return createService('Content.CommentService');
    }

}