<?php
namespace Common\Model\Comment;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class CmtListModel extends BaseModel{
    
    protected $tableName = 'cmt_list';

    public function getCmtList($id){
        $data['id'] = $id;
        $data['isDel'] = 0;
        return $this->where($data)->find() ? : array();
    }
    
    public function getCmtSendName($id){
        $data['id'] = $id;
        $data['isDel'] = 0;
        return $this->where($data)->field("sendName,sendUid")->find() ? : array();
    }
    
    public function getCmtNumByCmtStatId($cmtStatId){
        $data['cmtStatId'] = $cmtStatId;
        $data['isDel'] = 0;
        return $this->where($data)->count() ? : 0;
    }
    
    public function getAnonyDayCmtCnt($data){
        $where['IP'] = $data['IP'];
        $where['ctm'][] = array('gt',$data['start_time']);
        $where['ctm'][] = array('lt',$data['end_time']);
        $where['sendUid'] = 0;
        $where['isDel'] = 0;
        return $this->where($where)->count() ? : 0;
    }
    
    public function getUserDayCmtCnt($data){
        $where['sendUid'] = $data['uid'];
        
        $where['ctm'][] = array('lt',$data['end_time']);
        $where['ctm'][] = array('gt',$data['start_time']);
        $where['isDel'] = 0;
        
        return $this->where($where)->count() ? : 0;
    }
    
    
    
    
    
    
    public function getAllCmtList($cmtStatId, $orderBy ="ctm", $start, $limit){
        $data['a.cmtStatId'] = $cmtStatId;
        $data['a.isDel'] = 0;
        $order = array('a.ctm' =>'desc','r.ctm' =>'asc');
        if($orderBy) 
        $order = array('a.'.$orderBy =>'desc');
        $r = $this->table('cmt_list as a')->join("LEFT JOIN cmt_reply_list as r on a.id = r.cmtId and a.isDel = r.isDel")->field('a.*,r.id as rid,r.cmtId,r.pid,r.pidSta,r.reply,r.replyUid,r.replyName,r.ctm as rctm')->where($data)->order($order)->limit($start, $limit)->select();
        //echo $this->getLastSql();die;
        return $r ? : array();
    }
    
    public function getAllCmtCnt($cmtStatId){
        $where['cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        
        return $this->where($where)->count() ? : 0;
    }
    
    public function getCmtNumByIp($cmtStatId){
        $where['sendUid'] = 0;
        $where['cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        $r = $this->group('IP')->where($where)->field('IP')->select() ;
        return $r ? $r : array();
    }
    
    public function getCmtNumByUid($cmtStatId){
        $where['sendUid'] = array('gt',0);
        $where['cmtStatId'] = $cmtStatId;
        $where['isDel'] = 0;
        $r = $this->group('sendUid')->where($where)->field('sendUid')->select() ;
        return $r ? $r : array();
    }
    
    public function getAllCommentList($cmtStatId,$order ="",$start, $limit){
        $where['cmtStatId'] = $cmtStatId;
        if(!$order)
        $order = array('ctm' =>'desc');
        $where['isDel'] = 0;
        return $this->where($where)->order($order)->limit($start, $limit)->select() ? : array();
    }

    public function addCmtList($fields){
        $fields          = ArrayToolkit::filter($fields, array(
			'cmtStatId'       => '',
			'comment'         => '',
			'sendUid'         => 0 ,
			'sendName'        => '',
                        'replyCnt'        => 0 ,
                        'IP'              =>'',
		));
        
        $fields['ctm'] = time();
        $r = $this->add($fields);
        return  $this->getCmtList($r) ? : array();
    }
    
    
    public function getCountByIp($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['IP'] = $fields['IP'];
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    public function getCountByUid($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['sendUid'] = $fields['uid'];
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    public function getNumberByIp($fields){
        $where['cmtStatId'] = $fields['cmtStatId'];
        $where['IP'] = $fields['IP'];
        $where['sendUid'] = 0;
        $where['isDel'] = 0;
        $r = $this->where($where)->count();
        return $r ? $r : 0 ;
    }
    
    
    public function delCmtList($id){
        $where['id'] = $id;
        $where['isDel'] = 0;
        $data['isDel'] = 1;
        $r = $this->where($where)->save($data);
        return $r ;
    }
    
    public function setReplyCntById($param){
        $data['replyCnt'] = $param['replyCnt'] ;
        $where['id'] = $param['id'] ;
        $r = $this->where($where)->save($data);
        return $r ;
    }
    
    
    
}
?>
