<?php

namespace BackManage\Controller;
use Common\Lib\WebCode;
use Common\Lib\Paginator;

class OpenUserController extends BaseController
{
    public function indexAction($state='')
    {       
        
        $currentUser = $this->getCurrentUser();
        $openUserIsOk = $this->getOpenUserService()->openUserIsOk();
        if(!$openUserIsOk) E('未开启');
        if(empty($currentUser->id))E('没有登录');

        $userId = $currentUser->id;

        if(empty(goBackEnd())){
            throw $this->createAccessDeniedException();
        }

        $isCenter = webCode::isLocalcenterWeb();
        if(!$isCenter){
            E('权限错误');
        }
        $admin = true;

        $state = !empty(I('get.selectType')) ? I('get.selectType') : '';
        $state = empty($state) ? 'all' : $state;

        $siteSelect = empty(I('get.siteSelect')) ? '' : I('get.siteSelect');
        
        $name =empty(I('get.name')) ? '' : array('name'=>I('get.name'));
        $like = $name;
        $stateInList = $this->getOpenUserService()->getOpenUserstate($state);

        $paginator = new Paginator(
        $this->get('request'),
        $this->getOpenUserService()->getUserOpenUserCount('',$stateInList,$like,$siteSelect),
        12
        );

        $openUserList = $this->getOpenUserService()->getUserOpenUserPage(
            '',
            $stateInList,
            $paginator->getOffsetCount(),
            $paginator->getPerPageCount(),
            $like,
            $siteSelect 
        );
            
        return $this->render('OpenUser:index',array(
                'state'=>$state,
                'paginator' => $paginator,
                'resouces'=> $openUserList,
                'side_nav'=>'openUser',
                'webSiteCode'=>C('WEBSITE_CODE'),
                'menu' => 'openUserManagerIndex',
            ));
    }
    
    public function getOpenUserNavAction(){
        if($this->getOpenUserService()->openUserIsOk()){
                return $this->render('OpenUser:open-user-nav#Admin');
        }
        return '';
    }
    private function getOpenUserService(){
        return createService('System.OpenUserService');
    }

}
