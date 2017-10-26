<?php
/*
 * 问答音频
 * @author     yangjinlong
 */
namespace Common\Model\Service;

use Think\Model;

class AudioServiceModel extends \Common\Model\Common\BaseModel {
        protected $currentDb = CENTER;
	protected $tableName = 'question_audio';
        
        

	/**
	 * 获取音频
	 * @param      $id
	 * @return array
	 */
	public function getAudio($id, $fields=array()){
            if(empty($fields)){
                return  $this-> where("id = {$id}")-> find()? : null;
            }else{
               return  $this-> where("id = {$id}")->field($fields)-> find()? : null; 
            }
	}
        
        /**
	 *添加音频
	 */
        public function addAudio($data,$dbType=""){
            $affected = $this->add($data);
            if($affected <= 0){
                E("Insert audio error");
            }
            return $affected;
	}
        /**
	 *删除音频
	 */
        public function deleteAudio($id){
            return  $this-> where("id = {$id}")-> delete();
	}

        /**
	 *更新音频
	 */
        public function updateAudio($id, $data){
            return $this->where("id = {$id}")-> save($data);
	}

}