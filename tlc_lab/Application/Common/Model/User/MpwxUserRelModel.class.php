<?php
/**
 * 用户跟微信关系绑定
 * @author LiangFuJian 2015-11-03
 */
namespace Common\Model\User;
use Common\Model\Common\BaseModel;

class MpwxUserRelModel extends BaseModel{
    
    protected $tableName = 'mpwx_user_rel';
    
    /**
     * 根据用户id获取对应关系
     * @param int $uid
     * @return array
     */
    public function getRelByUid($uid){
        $map['uid'] = $uid;
        return $this->switchCenterDB()->where($map)->find();
    }
    
    /**
     * 根据openid获取对应关系
     * @param string $openid
     * @return array
     */
    public function getRelByOpenid($openid){
        $map['openid'] = $openid;
        return $this->switchCenterDB()->where($map)->find();
    }
    
    
    /**
     * 添加对应关系
     * @param int $uid
     * @param string $openid
     * @return int|boolean
     */
    public function addRel($uid, $openid){
        
        $data['uid'] = $uid;
        $data['openid'] = $openid;
        $data['createdTime'] = time();
        return $this->switchCenterDB()->add($data);
        
    }
    
    public function delRel($id){
        $map['id'] = $id;
        $this->switchCenterDB()->where($map)->delete();
        echo $this->getLastSql();
    }

}
?>
