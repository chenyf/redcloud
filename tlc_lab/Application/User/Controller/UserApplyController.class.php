<?php
namespace User\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\SimpleValidator;

class UserApplyController extends \Home\Controller\BaseController
{

    public function indexAction(Request $request){
        $uid = $request->get('uid') ? : 0;
        $uid = intval($uid);
        if(!$uid)
            return $this->createMessageResponse('info', 'UID不为0');
        $applyRecord = $this->getUserApplyService()->getUserLastApply($uid);
        if(!empty($applyRecord)){
            if( in_array( $applyRecord["status"],array(2,3) ) )
                $applyRecord = array();
        }
        $topCategory = $this->getCateService()->getTopCategory();
        $ban = 0;
        if( in_array( $applyRecord["status"],array(0,1) ) && $applyRecord )
            $ban = 1;
        return $this->render('User:apply-modal',array(
            "topCategory" => $topCategory,
            "applyRecord" => $applyRecord,
            "ban"         => $ban,
            "uid"         => $uid  
        ));
    }
    
    public function checkAction(Request $request){
        if ($request->getMethod() == 'POST') {
            $data = $request->request->all();
            $uid = isset($data["uid"]) ? intval($data["uid"]) : 0; 
            $applyName = isset($data["applyName"]) ? $data["applyName"] : "";
            $applyMobile = isset($data["applyMobile"]) ? $data["applyMobile"] : "";
            $applyEmail = isset($data["applyEmail"]) ? $data["applyEmail"] : "";
            $applyCateid = isset($data["applyCateid"]) ? intval($data["applyCateid"]) : 0;
            
             if( empty($uid) ){
                $this->error("UID不为0");
            }
            if( !SimpleValidator::nickname($applyName) ){
                $this->error("姓名格式不正确");
            }
            if( !isValidMobile( $applyMobile ) ){
                $this->error("手机号格式不正确");
            }
            if( !isValidEmail( $applyEmail ) ){
                $this->error("邮箱格式不正确");
            }
            if($applyCateid == 0){
                $this->error("请选择所属院/系");
            }
            $topCategory = $this->getCateService()->getTopCategory();
            if(empty($topCategory)){
                $this->error("暂无所属院/系列表");
            }else{
                $cateIdArr = array();
                foreach ($topCategory as $key => $value) {
                    if($value["isDelete"] == 0){
                        $cateIdArr[$key] = $value["id"];
                    }
                }
                if(!in_array($applyCateid, $cateIdArr)){
                    $this->error("您所选的院/系不在提供的院/系列表中");
                }
            }
            
            $applyRecord = $this->getUserApplyService()->getUserLastApply($uid);
            if(!empty($applyRecord)){
                if($applyRecord["status"] == 0){
                    $this->error("您的申请还未处理，不可以再次申请");
                }
                if($applyRecord["status"] == 1){
                    $this->error("您的申请已通过，不可以再次申请");
                }
            }

            $result = $this->getUserApplyService()->addUserApply($data);
            if($result){
                $this->success("申请成功");
            }else{
                $this->error("申请失败");
            }
        }
        $this->error("错误的提交方式");
    }
    
    /**
     * 取消申请
     * @author fubaosheng 2015-12-16
     */
    public function removeAction(Request $request){
        $id = $request->get('applyId') ? intval($request->get('applyId')) : 0;
        $applyRecord = $this->getUserApplyService()->getApply($id);
        if(empty($applyRecord))
            $this->error("申请记录不存在");
        if(intval($applyRecord["status"]) != 0)
            $this->error("申请状态已经改变，不可以取消");
        $r = $this->getUserApplyService()->removeApply($id);
        if($r)
             $this->success("取消申请成功");
        else
           $this->error("取消申请失败");
    }
    
    private function getUserApplyService(){
        return createService("User.UserApplyService");
    }
    
    private function getCateService() {
        return createService('Taxonomy.CategoryService');
    }

}