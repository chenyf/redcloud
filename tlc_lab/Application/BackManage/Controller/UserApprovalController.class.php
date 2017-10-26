<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Lib\ImgConverToData;

class UserApprovalController extends BaseController
{

    public function approvingAction(Request $request)
    {

        $fields = $request->query->all();

        $conditions = array(
            'roles'=>'',
            'keywordType'=>'',
            'keyword'=>'',
            'approvalStatus' => 'approving'
        );

        if(empty($fields)){
            $fields = array();
        }

        $conditions = array_merge($conditions, $fields);
        $conditions['switch'] = true;    # 不影响其他的查询操作 传入一个开关

        $paginator = new Paginator(
            $this->get('request'),
            $this->getUserService()->searchUserCount($conditions),
            20
        );

        $users = $this->getUserService()->searchUsers(
            $conditions,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $approvals = $this->getUserService()->findUserApprovalsByUserIds(ArrayToolkit::column($users, 'id'));
        $approvals = ArrayToolkit::index($approvals, 'userId');

        return $this->render('User:approving', array(
            'users' => $users,
            'paginator' => $paginator,
            'approvals' => $approvals
        ));
    }
    
    public function approvedAction(Request $request)
    {
        $fields = $request->query->all();

        $conditions = array(
            'roles'=>'',
            'keywordType'=>'',
            'keyword'=>'',
            'approvalStatus' => 'approved'
        );

        if(empty($fields)){
            $fields = array();
        }

        $conditions = array_merge($conditions, $fields);

        $paginator = new Paginator(
            $this->get('request'),
            $this->getUserService()->searchUserCount($conditions),
            20
        );

        $users = $this->getUserService()->searchUsers(
            $conditions,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );

        $userProfiles = $this->getUserService()->findUserProfilesByIds(ArrayToolkit::column($users, 'id'));
        $userProfiles = ArrayToolkit::index($userProfiles, 'id');
        return $this->render('User:approved', array(
            'users' => $users,
            'paginator' => $paginator,
            'userProfiles' => $userProfiles
        ));
    }

    public function approveAction(Request $request, $id)
    {
        list($user, $userApprovalInfo) = $this->getApprovalInfo($request, $id);
        if ($request->getMethod() == 'POST') {
            
            $data = $request->request->all();
            if($data['form_status'] == 'success'){
                $this->getUserService()->passApproval($id, $data['note']);
            } else if ($data['form_status'] == 'fail') {
                $this->getUserService()->rejectApproval($id, $data['note']);
            }

            return $this->createJsonResponse(array('status' => 'ok'));
        }

        return $this->render("User:user-approve-modal",
            array(
                'user' => $user,
                'userApprovalInfo' => $userApprovalInfo,
            )
        );
    }

    public function viewApprovalInfoAction(Request $request, $id){
        list($user, $userApprovalInfo) = $this->getApprovalInfo($request, $id);

        return $this->render("User:user-approve-info-modal",
            array(
                'user' => $user,
                'userApprovalInfo' => $userApprovalInfo,
            )
        );
    }

    protected function getApprovalInfo(Request $request, $id){
        $user = $this->getUserService()->getUser($id);

        $userApprovalInfo = $this->getUserService()->getLastestApprovalByUserIdAndStatus($user['id'], 'approving');
        return array($user, $userApprovalInfo);
    }

    public function showIdcardAction($userId, $type)
    {
        $user = $this->getUserService()->getUser($userId);
        $currentUser = $this->getCurrentUser();

        if (empty($currentUser)) {
            throw $this->createAccessDeniedException();
        }

        $userApprovalInfo = $this->getUserService()->getLastestApprovalByUserIdAndStatus($user['id'], 'approving');

        $idcardPath = $type === 'back' ? $userApprovalInfo['backImg'] : $userApprovalInfo['faceImg'];
        $imgConverToData = new ImgConverToData;
        $imgConverToData -> getImgDir($idcardPath);
        $imgConverToData -> img2Data();
        $imgData = $imgConverToData -> data2Img();
        echo $imgData;
        exit;
    }


    public function cancelAction(Request $request, $id)
    {
        $this->getUserService()->rejectApproval($id, '管理员撤销');
        return $this->createJsonResponse(true);
    }

}
