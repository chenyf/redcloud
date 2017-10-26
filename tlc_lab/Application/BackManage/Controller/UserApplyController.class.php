<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;

class UserApplyController extends BaseController {

    public function indexAction (Request $request)
    {
        $data = $request->query->all();
        $status = isset($data["status"]) ? intval($data["status"]) : -1;
        $applyName = isset($data["applyName"]) ? $data["applyName"] : "";
        
        $conditions = array("status"=>$status,"applyName"=>$applyName);
        $paginator = new Paginator(
            $this->get('request'),
            $this->getUserApplyService()->searchApplyCount($conditions),
            15
        );

        $applys = $this->getUserApplyService()->searchApplys(
            $conditions,
            array('applyTm', 'DESC'),
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount()
        );
       
        return $this->render('UserApply:index', array(
            'applys' => $applys,
            'paginator' => $paginator,
            'status' => $status,
            'applyName' => $applyName
        ));
    }
    
    public function applyRemarkAction(Request $request){
        $id = $request->query->get('id');

        $apply = $this->getUserApplyService()->getApply($id);

        return $this->render('UserApply:apply-remark', [
            'remark'=>$apply['applyRemark']
        ]);
    }
    
    public function checkApplyAction(Request $request) {
        $id = $request->query->get('id');

        $apply = $this->getUserApplyService()->getApply($id);
        
        if ($request->getMethod() == 'POST') {
            $data = I('post.');
            $id = $data["id"] ? : 0;
            $apply = $this->getUserApplyService()->getApply($id);

            if(empty($apply))
                $this->error('申请记录不存在！');

            if(intval($apply["status"]) != 0)
                $this->error('申请状态已经改变！');

            $r = $this->getUserApplyService()->checkApply($data);
            if($r){
                if(intval($data["status"]) == 1){
                    $applyUser = $this->getUserService()->getUser($apply['applyUid']);
                    $roles = "|".implode("|",$applyUser['roles'])."|ROLE_TEACHER|";
                    $this->getUserService()->updateUser($apply['applyUid'],array("roles"=>$roles,"teacherCategoryId"=>$apply['applyCateid']));
                    $this->getNotificationService()->notify($apply['applyUid'], 'default', '您申请老师角色审核通过了');
                }else{
                    $this->getNotificationService()->notify($apply['applyUid'], 'default', '您申请老师角色审核未通过');
                }
                $this->success('审核操作成功！');
            }else{
                $this->error('审核操作失败！');
            }
        }

        return $this->render('UserApply:check-apply', compact('apply'));
    }

    public function applyRecordsAction(Request $request) {
        $id = $request->query->get('id');

        $apply = $this->getUserApplyService()->getApply($id);

        return $this->render('UserApply:apply-records', compact('apply'));
    }

    private function getUserApplyService(){
        return createService("User.UserApplyService");
    }
    
    private function getNotificationService(){
        return createService('User.NotificationService');
    }
    
}