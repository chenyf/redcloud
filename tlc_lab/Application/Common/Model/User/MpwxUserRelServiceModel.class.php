<?php
/**
 * 用户跟微信关系绑定
 * @author LiangFuJian 2015-11-03
 */
namespace Common\Model\User;
use Common\Model\Common\BaseModel;
class MpwxUserRelServiceModel extends BaseModel{

    
    private function getMpwxUserRelDao(){
        return $this->createService('User.MpwxUserRelModel');
    }
    
    /**
     * 根据用户id获取对应关系
     * @param int $uid
     * @return array
     */
    public function getRelByUid($uid){
        
        return $this->getMpwxUserRelDao()->getRelByUid(intval($uid));
        
    }
    
    /**
     * 根据openid获取对应关系
     * @param string $openid
     * @return array
     */
    public function getRelByOpenid($openid){
        
        return $this->getMpwxUserRelDao()->getRelByOpenid($openid);
        
    }
    
    
    /**
     * 添加对应关系
     * @param int $uid
     * @param string $openid
     * @return int|boolean
     */
    public function addRel($uid, $openid){
        
        return $this->getMpwxUserRelDao()->addRel(intval($uid), $openid);
        
    }
    
    
    public function delRel($id){
        return $this->getMpwxUserRelDao()->delRel($id);
    }

}
?>
