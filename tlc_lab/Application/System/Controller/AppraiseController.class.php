<?php
namespace System\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Common\Lib\Paginator;
use Common\Lib\FileToolkit;
use Common\Services\CommentService;

class AppraiseController extends \Home\Controller\BaseController
{
    /**
     * 点赞、取消赞
     * @param  string $type , string $strId
     */
    public function goodAction(Request $request){
        if($request->getMethod() == 'POST'){
            $all  = $request->request->all();
            $type   = trim($all['type'])    ? strtolower(trim($all['type']))  : '';
            $strId  = trim($all['appraiseid'])  ? : 0;
            $vcode  = trim($all['vcode'])   ? : '';
            
            $uid = $this->getCurrentUser()->id; 
            if(!$uid) $this->jsonResponse(403, 'Unlogin');
            $check = CommentService::checkVcode($type , $strId , $vcode);
            //var_dump($uid ,$type,$strId ,$vcode ,$check);die;
            if(!$type || !$strId || !$vcode || !$check )
                return  $this->createJsonResponse(array('success' => false , 'message' =>'参数错误'));
            $is_good = $this->getAppraiseService()->isGoodByUid($type , $strId ,$uid);
            
            if($is_good == 1){
                $rule = C('APPRAISE');
                $ruleType = $rule[$type];
                if($ruleType['allowCancel'] != 1) 
                    return  $this->createJsonResponse(array('success' => false , 'message' =>'取消赞功能已关闭'));
                $r = $this->getAppraiseService()->removeGood($type , $strId ,$uid);
                if(!$r) return  $this->createJsonResponse(array('success' => false ,'type' => 'remove' , 'message' =>'取消赞失败'));
                return  $this->createJsonResponse(array('success' => true ,'type' => 'remove' , 'message' =>'取消赞成功','goodInfo' =>$r));
            }elseif($is_good == 2){
                $r = $this->getAppraiseService()->addGood($type , $strId ,$uid);
                if(!$r) return  $this->createJsonResponse(array('success' => false ,'type' => 'add' , 'message' =>'点赞失败'));
                //点赞成功推送日志
                $this->getAppraiseService()->pushAppraiseLog($type ,$strId);
                return  $this->createJsonResponse(array('success' => true ,'type' => 'add' , 'message' =>'点赞成功','goodInfo' =>$r));
            }
        }
        
    }
  
    public function eachAppraiseAction(Request $request){
        if($request->getMethod() == 'POST'){
            $all  = $request->request->all();
            $type   = trim($all['type'])    ? : '';
            $strId  = trim($all['appraiseid'])  ? : 0;
            $vcode  = trim($all['vcode'])   ? : '';
            $check = CommentService::checkVcode($type , $strId , $vcode);
            if(!$check) return false;
            $uid = $this->getCurrentUser()->id; 
            
            $r = $this->getAppraiseService()->getAppraiseInfo($type , $strId);
            if(!$r) return  $this->createJsonResponse(array('success' => false , 'appraise' =>''));
            if($uid){
              $is_good = $this->getAppraiseService()->isGoodByUid($type , $strId ,$uid);
              $r['isGood'] = $is_good == 1 ? 1 : 0 ;  
            }else{
              $r['isGood'] = 0 ; 
            }
            
            return  $this->createJsonResponse(array('success' => true ,'appraise' =>$r));
        }
        
    }
    
    
    
    private function getAppraiseService()
    {
        return createService('System.AppraiseService');
    }
    
    
    
}