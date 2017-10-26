<?php
namespace AccessControl\Controller;

use Home\Controller\BaseController as WebBaseController;

class BaseController extends WebBaseController
{
    public function __construct()
    {
        parent::__construct();
        if($this->user->isLogin() && $this->user->isAdmin()){

        }else{
            $this->redirect('User/Signin/index');
            exit;
        }
    }
}