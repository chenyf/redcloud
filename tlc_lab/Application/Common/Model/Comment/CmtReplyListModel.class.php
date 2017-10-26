<?php
namespace Common\Model\Comment;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class CmtReplyListModel extends BaseModel{
    
    protected $tableName = 'cmt_reply_list';

    public function getCmtReply($id){
        $where['id'] = $id;
        $where['isDel'] = 0;
        return $this->where($where)->find() ? : array();
    }
    
    public function getReplyUid($id){
        $where['id'] = $id;
        $where['isDel'] = 0;
        return $this->where($where)->field("replyName ,replyUid")->find() ? : array();
    }
    
    public function getReplyListByCmtIdCount($cmtId){
        $where['cmtId'] = $cmtId;
        $where['isDel'] = 0;
        return $this->where($where)->count() ? : 0;
    }
    
    public function getReplyNumByCmtStatId($cmtStatId){
        $where['$cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        return $this->where($where)->count() ? : 0;
    }
    
    public function getReplyListByCmtId($cmtId,$order ="",$start, $limit){
        $where['cmtId'] = $cmtId;
        if(!$order)
        $order = array('ctm' =>'desc');
        $where['isDel'] = 0;
        return $this->where($where)->order($order)->limit($start, $limit)->select() ? : array();
    }
    
    public function getReplyByCmtId($cmtId,$order ="",$start, $limit){
        $where['cmtId'] = $cmtId;
        if(!$order)
        $order = array('ctm' =>'desc');
        $where['isDel'] = 0;
        return $this->where($where)->order($order)->limit($start, $limit)->select() ? : array();
    }
    
    public function getAnonyDayReplyCnt($data){
        $where['IP'] = $data['IP'];
        $where['ctm'][] = array('gt',$data['start_time']);
        $where['ctm'][] = array('lt',$data['end_time']);
        $where['replyUid'] = 0;
        $where['isDel'] = 0;
        return $this->where($where)->count() ? : 0;
    }
    
    public function getUserDayReplyCnt($data){
        $where['replyUid'] = $data['uid'];
        
        $where['ctm'][] = array('lt',$data['end_time']);
        $where['ctm'][] = array('gt',$data['start_time']);
        $where['isDel'] = 0;
        
        return $this->where($where)->count() ? : 0;
    }
    
    public function addCmtReply($fields){
        $fields          = ArrayToolkit::filter($fields, array(
                        'cmtStatId'       => 0,
			'cmtId'           => 0,
                        'pid'             => 0,
                        'pidSta'          => 0,
			'reply'           => '',
			'replyUid'        => '',
			'replyName'       => '' ,
                        'IP'              => '',
		));
        
        $fields['ctm'] = time();
        
        $r = $this->add($fields);
        return  $this->getCmtReply($r) ? : array();
    }
    
    /**
     * 根据pid得到pidSta
     * @author tanhaitao 2015-11-21
     */
    public function getPidSta($pid){
        $where['id'] = $pid ;
        $where['isDel'] = 0;
        return $this->where($where)->field('pid,pidSta')->find() ? : array();
    }
    
    
    /**
     * 根据id得到回复数
     * @author tanhaitao 2015-11-21
     */
    public function getReplyNumByCmtId($cmtId){
        $where['cmtId'] = $cmtId ;
        $where['isDel'] = 0;
        return $this->where($where)->count() ? : 0 ;
    }
    
    public function getIdByReplyPid($id){
        $where['pid'] = $id;
        $where['isDel'] = 0;
        $r = $this->where($where)->field('id')->select();
        return $r  ? $r : array() ;
    }
    
    public function delCmtReply($id){
        $where['id'] = $id;
        $where['pid'] = $id;
        $where['pidSta'] = $id;
        $where['_logic'] = 'OR';
        $map['_complex'] = $where;
        $map['isDel'] = 0;
        $data['isDel'] = 1;
        $r = $this->where($map)->save($data);
        return $r ;
    }
    
    public function delCmt($id){
        $map['cmtId'] = $id;
        $map['isDel'] = 0;
        $data['isDel'] = 1;
        $r = $this->where($map)->save($data);
        return $r ;
    }
    
    public function getCountByIp($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['IP'] = $fields['IP'];
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    public function getReplyNumberByIp($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['IP'] = $fields['IP'];
        $where['replyUid'] = 0;
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    public function getCountByUid($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['replyUid'] = $fields['uid'];
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    public function getReplyNumByIp($cmtStatId){
        $where['replyUid'] = 0;
        $where['cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        $r = $this->group('IP')->where($where)->field('IP')->select() ;
        return $r ? $r : array();
    }
    
    public function getReplyNumByUid($cmtStatId){
        $where['replyUid'] = array('gt',0);
        $where['cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        $r = $this->group('replyUid')->where($where)->field('replyUid')->select() ;
        return $r ? $r : array();
    }
    
    
}
?>
