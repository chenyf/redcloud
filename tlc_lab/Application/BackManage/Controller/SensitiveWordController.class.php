<?php

namespace BackManage\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\Paginator;
class SensitiveWordController extends BaseController
{
    public function _initialize(){
        $user = $this->getCurrentUser();
        if(!$user->isLogin())
            $this->redirect('User/Signin/index');
        if(!$user->isAdmin()){
            $this->createMessageResponse('info','对不起，只有管理员才有权限操作！','权限受限!');
        }
    }

    //首页
    public function indexAction(Request $request)
    {
        $wordList = $this->getSensitiveWordService()->getWordRecordList();
        return $this->render('SensitiveWord:index', array(
            'wordList' => $wordList,
        ));
    }

    //单词列表
    public function listAction(Request $request){
        $wordList = $this->getSensitiveWordService()->getWordRecordList();

        return $this->render('SensitiveWord:word_list', array(
            'wordList' => $wordList,
        ));
    }

    //添加敏感词
    public function addAction(Request $request){
        if($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $words = isset($data['words']) ? $data['words'] : "";
            $words = trim(trim(str_replace("，",",",$words),","));
            $words = array_slice(array_filter(array_unique(explode(",",$words))),0,100);

            if(empty($words)){
                return $this->createJsonResponse(array('status' => false,'message' => '请添加有效的敏感词再进行操作！'));
            }elseif ($this->getSensitiveWordService()->addWordList($words)){
                return $this->createJsonResponse(array('status' => true,'message' => '添加敏感词成功!'));
            }else{
                return $this->createJsonResponse(array('status' => false,'message' => '添加敏感词失败！'));
            }
        }

        return $this->createJsonResponse(array('status' => false,'message' => '方法不正确'));
    }

    //删除敏感词
    public function deleteAction(Request $request){
        if($request->getMethod() == 'POST'){
            $data = $request->request->all();
            $wordId = isset($data['id']) ? intval($data['id']) : 0;
            $wordListId = isset($data['word_ids']) ? trim($data['word_ids'],'|') : "";
            if(!empty($wordId)){
                if($this->getSensitiveWordService()->deleteWord($wordId)){
                    return $this->createJsonResponse(array('status' => true,'message' => '删除成功！'));
                }else{
                    return $this->createJsonResponse(array('status' => false,'message' => '删除失败！'));
                }
            }else if(!empty($wordListId)){
                $wordIds = explode('|',$wordListId);
                if($this->getSensitiveWordService()->deleteWordList($wordIds)){
                    return $this->createJsonResponse(array('status' => true,'message' => '删除成功！'));
                }else{
                    return $this->createJsonResponse(array('status' => false,'message' => '删除失败！'));
                }
            }else{
                return $this->createJsonResponse(array('status' => false,'message' => '参数不能为空'));
            }
        }

        return $this->createJsonResponse(array('status' => false,'message' => '方法不正确'));
    }

    private function getSensitiveWordService(){
        return createService('System.SensitiveWordService');
    }
}