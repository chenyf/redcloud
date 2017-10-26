<?php
namespace My\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
use Common\Lib\ArrayToolkit;
use Common\Twig\Util\AvatarAlert;

class MyController extends \Home\Controller\BaseController
{

    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin()) {
            $this->redirect('User/Signin/index');
        }
    }

    public function avatarAlertAction()
    {
        $user = $this->getCurrentUser();
        return $this->render('My:avatar-alert#My', array(
            'avatarAlert' => AvatarAlert::alertInMyCenter($user)
        ));
    }

    public function changePasswordAction(Request $request){
        $user = $this->getCurrentUser();
        if ($request->getMethod() == 'POST') {
            $password = $request->request->get('pwd');
            if(empty($password["current"])){
                return $this->createJsonResponse(array('status'=>false,'message'=>'当前密码不能为空'));
            }
            if(empty($password["new_pwd"])){
                return $this->createJsonResponse(array('status'=>false,'message'=>'新密码不能为空'));
            }
            if($password["new_pwd"] !== $password["new_pwd1"]){
                return $this->createJsonResponse(array('status'=>false,'message'=>'两次密码不一致'));
            }
            if(strlen($password["new_pwd"]) < 4 || strlen($password["new_pwd"]) > 20){
                return $this->createJsonResponse(array('status'=>false,'message'=>'密码长度在4-20个字符之间'));
            }

            $user = $this->getCurrentUser();

            if(C('if_iface')) {
                $changeResult = $this->getUserService()->changeUserPasswordIface($user['userNum'], $password["current"], $password["new_pwd"]);
            }else{
                $changeResult = $this->getUserService()->changeUserPasswordLocal($password["current"], $password["new_pwd"]);
            }

            if($changeResult->code != 20){
                return $this->createJsonResponse(array('status'=>false,'message'=>$changeResult->msg));
            }else{
                return $this->createJsonResponse(array('status'=>true,'message'=>'修改密码成功'));
            }
        }

        return $this->render("My:change_pwd");
    }

    protected function getUserService()
    {
        return createService('User.UserServiceModel');
    }

}