<?php
namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
class SignController extends BaseController
{

    public function _initialize(){
        if(!C('if_open_sign')){
            return $this->_404("未开启签到功能");
        }
    }

    public function indexAction(Request $request)
    {
        return $this->render('Sign:index');
    }


}