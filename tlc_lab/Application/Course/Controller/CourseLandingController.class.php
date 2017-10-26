<?php

namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;
use Common\Lib\Paginator;

/*
 * To change this template, choose Tools | Templates
 * @time2016/1/20
 */

class CourseLandingController extends \Home\Controller\BaseController {

    public function indexAction(Request $request) {
        header("content-type:text/html;charset=utf8");
        $id = $_GET['cmsId'];
        $status = $this->getCmsListService()->getCms($id);
        $user = $this->getCurrentUser();
        $admin = $user->isAdmin();
        
          if (!isset($id)) {
            return $this->createMessageResponse('error', '非法操作');
        }
        if ($status['agstatus'] == 1) {
            return $this->createMessageResponse('error', '页面已删除');
        }


        if ($status['status'] == 0) {
            if ($admin == false) {
                if ($user->isLogin() == false) {
                    redirect("/User/Signin/indexAction");
                } else {
                    return $this->createMessageResponse('error', '未发布');
                    die;
                }
            }
        }

        $res = $this->getCmsListService()->getDataConsulting($id);

        $arr = $this->Cms_ModularServer()->getModularData($id);
       
      
        $cmsIdArr = implode(ArrayToolkit::column($arr, 'id'), ",");

        $v = $this->Cms_ModularServer()->getConfig($cmsIdArr); 
          if ($v == "") {
            return $this->createMessageResponse('error', '对不起 ！暂无数据');
        }
        return $this->render('CourseLanding:index', array("cmsId" => $id, "list" => $arr, "Consulting" => $res, "data" => $v));
    }

    public function videoAction() {
        $id = $_GET['id'];

        $lession = $this->Cms_ModularServer()->selpicPath($id);
        return $this->render('CourseLanding:video', array("order" => $lession));
    }

    private function getCmsListService() {
        return createService('Cms.CmsListServiceModel');
    }

    protected function Cms_ModularServer() {
        return createService('Cms.CmsModularServer');
    }

}

?>
