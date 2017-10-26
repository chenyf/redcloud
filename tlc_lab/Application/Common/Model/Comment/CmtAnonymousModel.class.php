<?php
namespace Common\Model\Comment;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class CmtAnonymousModel extends BaseModel{
    
    protected $tableName = 'cmt_anonymous';

    public function getCmtAnonymous($ip){
        $where['IP'] = $ip;
        $where['isDel'] = 0;
        return $this->where($where)->find() ? : array();
    }
    
    public function getCmtAnonymousById($id){
        $where['id'] = $id;
        $where['isDel'] = 0;
        return $this->where($where)->find() ? : array();
    }
    
    public function addCmtAnonymous($fields){
        $fields          = ArrayToolkit::filter($fields, array(
                        'cmtStatId'       => 0,
			'nickName'        => '',
                        'IP'              => '',
		));
        
        $fields['ctm'] = time();
        
        $r = $this->add($fields);
        return  $this->getCmtAnonymousById($r) ? : array();
    }
    
    
    public function delCmtAnonymous($ip){
        $map['IP'] = $ip;
        $map['isDel'] = 0;
        $data['isDel'] = 1;
        $r = $this->where($map)->save($data);
        return $r ;
    }
    
    
}
?>
