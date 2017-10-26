<?php

namespace User\Controller;
use Symfony\Component\HttpFoundation\Request;


class AuthController extends \Home\Controller\BaseController
{

    public function test(){
        echo getServerIp();
    }

    public function auth(Request $request){

        if ($request->getMethod() == 'POST'){
            $data = json_decode(file_get_contents('php://input'));
            $result = array();

            if(empty($data)){
                $result['code'] = 110;
                $result['msg'] = '发生错误!';
            }else if(empty($data->userid)){
                $result['code'] = 111;
                $result['msg'] = '用户名不能为空!';
            }else if(empty($data->userpwd)){
                $result['code'] = 112;
                $result['msg'] = '密码不能为空!';
            }else{
                $authResult = $this->getUserService()->authLogin($data->userid,$data->userpwd);
                $resultCode = $authResult->code;
                if( $resultCode == 13 ){
                    $result['code'] = 113;
                    $result['msg'] = '密码错误!';
                }else if($resultCode == 20){
                    $result['code'] = 200;
                    $result['msg'] = '验证成功!';
                }else{
                    $result['code'] = 110;
                    $result['msg'] = '发生错误!';
                }
            }

            echo json_encode($result);
        }else{
            return $this->_404("nothing nothing nothing");
        }

    }

    protected function getUserService(){
        return createService('User.UserService');
    }

}