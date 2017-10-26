<?php
/**
 * 用户跟微信关系绑定
 * @author LiangFuJian 2015-11-03
 */
namespace Common\Model\Activity;
use Common\Model\Common\BaseModel;
class TeacherTaskRewardServiceModel extends BaseModel{

    
    private function getTeacherTaskRewardDao(){
        return $this->createService('Activity.TeacherTaskRewardModel');
    }
    
    /**
     * 获取用户当月是否领取奖励
     * @param int $uid
     * @return array
     */
    public function isGetMonthReward($uid){
        
        $beginThismonth = mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $map['uid'] = intval($uid);
        $map['createdTime'] = array(array('egt', $beginThismonth),array('elt', $endThismonth), 'and');
        return $this->getTeacherTaskRewardDao()->getTaskRewardRecord($map);
        
    }
    
    /**
     * 获取当月领取奖励的人数
     * @return int
     */
    public function getMonthRewardUserNum(){
        
        $beginThismonth = mktime(0,0,0,date('m'),1,date('Y'));
        $endThismonth = mktime(23,59,59,date('m'),date('t'),date('Y'));
        $map['createdTime'] = array(array('egt', $beginThismonth),array('elt', $endThismonth), 'and');
        $data = $this->getTeacherTaskRewardDao()->getTaskRewardRecord($map);
        return count($data);
    }

    
    /**
     * 添加奖励记录
     * @param int $uid
     * @param int $reward
     * @return int|boolean
     */
    public function addTeacherTaskReward($uid, $reward = 50){
        
        $data['uid'] = intval($uid);
        $data['reward'] = intval($reward);
        $data['createdTime'] = time();
        return $this->getTeacherTaskRewardDao()->addTeacherTaskReward($data);
        
    }

}
?>
