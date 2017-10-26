<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;
use Common\Model\Common\BaseModel;

class ImportClassMemberDetailServiceModel extends BaseModel
{

        public function getMemberDetailCount($conditions)
        {
            return $this->getImportClassMemberDetailDao()
                    ->getMemberDetailCount($conditions);
        }
        
        public function getMemberDetailPageList($conditions,$orderBy,$start,$limit)
        {
            return $this->getImportClassMemberDetailDao()
                    ->getMemberDetailPageList($conditions,$orderBy,$start,$limit);
        }
        
        
        public function updateMemberDetail($taskId, $account, $status, $remark){
            
            $data['status'] = $status;
            $data['remark'] = $remark;
            $data['updatedTime'] = time();
            return $this->getImportClassMemberDetailDao()
                    ->updateMemberDetail($taskId, $account, $data);
        }
        
        
        public function addMemberDetail($data){
            return $this->getImportClassMemberDetailDao()->add($data);
        }

        public function addAllMemberDetail($dataList){
            return $this->getImportClassMemberDetailDao()->addAll($dataList);
        }
        
        /**
         * 获取任务状态数量
         * @param int $taskId
         * @return array
         */
        public function getTaskStatusNum($taskId){
            
            $importTaskObj = $this->getImportClassMemberDetailDao();
            #总数
            $map['taskId'] = intval($taskId);
            $totalNum = $importTaskObj->getMemberDetailCount($map);
            $data['totalNum'] = intval($totalNum);
            
            #未开始
            $map['status'] = 0;
            $waitNum = $importTaskObj->getMemberDetailCount($map);
            $data['waitNum'] = intval($waitNum);
            
            #成功数量
            $map['status'] = 2;
            $succNum = $importTaskObj->getMemberDetailCount($map);
            $data['succNum'] = intval($succNum);
            
            #已完成数量
            $data['completeNum'] = $data['totalNum'] - $data['waitNum'];
            
            #失败数量
            $data['failNum'] = $data['completeNum'] - $data['succNum'];
            
            return $data;
            
        }

        
        public function getImportClassMemberDetailDao(){

            return $this->createDao('Course.ImportClassMemberDetail');  

        }

}