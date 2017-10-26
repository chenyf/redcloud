<?php
namespace Common\Model\Comment;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class CmtStatModel extends BaseModel{
    
    protected $tableName = 'cmt_stat';

    public function getCmtStat($id){
        return $this->where("id = {$id}")->find() ? : array();
    }
    
    public function getCommentNum($cmtIdStr){
        $where['cmtIdStr'] = $cmtIdStr;
        return $this->where($where)->getField('cmtCnt')? : 0;
    }

    public function addCmtStat($fields){
        $fields          = ArrayToolkit::filter($fields, array(
			'cmtType'         => '',
			'cmtIdStr'        => '',
                        'createUid'       => '',
		));
        $fields['cmtCnt']  = 1;
        $fields['userCnt'] = 1;
        $fields['ctm'] = time();
        $r = $this->add($fields);
        return  $this->getCmtStat($r) ? : array();
    }
    
    public function updateCmtStat($fields){
        $where['id']        =   $fields['id'];
        if($fields['cmtCnt']){
           $data['cmtCnt']     =   $fields['cmtCnt']; 
        }
        $data['userCnt']    =   $fields['userCnt'];
        $r = $this->where($where)->save($data);
        return  $this->getCmtStat($where['id']) ? : array();
    }
    
    public function changeCmtstat($fields){
        $where['id']        =   $fields['id'];
        $data['cmtCnt']     =   $fields['cmtCnt'];
        $data['userCnt']    =  $fields['userCnt'];
        //var_dump($data);die("ds");
        return $this->where($where)->save($data);
    }
    
    public function getCmtStatByTypeAndIdStr($fields){
        $data['cmtType']  = $fields['cmtType'];
        $data['cmtIdStr'] = $fields['cmtIdStr'];
        
        return  $this->where($data)->find() ? : false;
    }
    
    
    
}
?>
