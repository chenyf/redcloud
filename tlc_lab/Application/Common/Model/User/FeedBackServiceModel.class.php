<?php
/**
 * 用户意见反馈
 * @author fubaosheng 2015-05-08
 */
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class FeedBackServiceModel extends BaseModel{
    
    private function getFeedBackDao(){
        return $this->createDao("User.FeedBack");
    }
    
    private function getProblemFeedbackDao(){
        return $this->createDao("User.ProblemFeedback");
    }
    
    private function getCourseDao(){
        return $this->createDao('Course.Course');
    }
    
    private function getUserService(){
        return $this->createService("User.UserService");
    }
    
    public function addFeedBack($data){
        $typeArr = array( 0 => '咨询',1 => '建议',2 => '其他');
        $fromArr = array( 0 => 'web',1 => 'iphnoe',2 => 'android',3 => 'ipad',4 => 'apad' );
        
        if(!empty($data['uid'])){
            $user = $this->getUserService()->getUser($data['uid']);
            if(empty($user))
                E('用户不存在！');
        }
        if(!in_array($data['type'], array_keys($typeArr)))
            E('类型错误！');
        if(!empty($data['courseId'])){
            $course = $this->getCourseDao()->getCourse($data['courseId']);
            if(empty($course))
                E('课程不存在！');
        }
        if($data["content"] == "")
            E('内容不能为空！');
        if(!empty($data["email"]) && !isValidEmail($data["email"]))
            E('邮箱格式错误！');
        if(!in_array($data['from'], array_keys($fromArr)))
            E('来源错误！');
        $data['ctm'] = time();
        return $this->getFeedBackDao()->addFeedBack($data);
    }
    
    public function searchFeedBackCount(array $conditions){
        return $this->getFeedBackDao()->searchFeedBackCount($conditions);
    }
    
    public function searchFeedBacks(array $conditions, array $orderBy, $start, $limit){
        return $this->getFeedBackDao()->searchFeedBacks($conditions, $orderBy, $start, $limit);
    }
    
    public function addProblemFeedback($data){
        $user =$this->getCurrentUser();
        $data['uid'] = $user['id'];
        $arr = $user['roles'];
        $data['roles'] = 1;
        
        unset($arr[array_search("ROLE_USER",$arr)]);
        if(!empty($arr)){
          $data['roles'] = 2;  
        }
        
        $data['ctm'] = time();
        return $this->getProblemFeedbackDao()->addProblemFeedback($data);
    }
    
    public function getProblemFeedback($id){
        return $this->getProblemFeedbackDao()->getProblemFeedback($id);
    }
    
    public function searchProFeedbackCount(array $conditions){
        return $this->getProblemFeedbackDao()->searchProFeedbackCount($conditions);
    }
    
    public function searchProFeedbacks(array $conditions, array $orderBy, $start, $limit){
        return $this->getProblemFeedbackDao()->searchProFeedbacks($conditions, $orderBy, $start, $limit);
    }
}
?>
