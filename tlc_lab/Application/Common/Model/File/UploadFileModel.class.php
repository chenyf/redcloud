<?php

namespace Common\Model\File;

use Common\Model\Common\BaseModel;

//use Topxia\Service\Common\BaseDao;
//use Common\Model\File\UploadFile;

class UploadFileModel extends BaseModel {
	protected $tableName = 'upload_files';

//	public function getConnection() {
//		return $this;
//	}
        
        /**
         * 
         * @param int $id
         * @return array
         * @author ZhaoZuoWu 2015-03-26
         */
	public function getFile($id) {
		//$sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
		return $this->where(array('id'=>$id))->find() ?: null;
	}

	public function selectFilesByCourseId($courseId){
		return $this->where(array('targetId'=>$courseId))->select() ?: array();
	}

	public function getFileByHashId($hash) {
		//$sql = "SELECT * FROM {$this->tableName} WHERE hashId = ?";
		return $this->where(array('hashId'=>$hash))->select() ?: null;
	}

	public function getFileByConvertHash($hash) {
		//$sql = "SELECT * FROM {$this->tableName} WHERE convertHash = ?";
		return $this->where(array('convertHash'=>$hash))->select() ?: null;
	}

	public function getFileExtById($id){
		return $this->where(array('id'=>$id))->field('ext')->find() ?: "";
	}

	public function findFilesByIds($ids) {
		if (empty($ids)) {
			return array();
		}
//		$marks = str_repeat('?,', count($ids) - 1) . '?';
		$marks = implode(',', $ids);
		//$sql   = "SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
		return $this->where(array('id'=>array('in',$marks)))->select();
	}

	public function findFilesCountByEtag($etag) {
		if (empty($etag)) {
			return 0;
		}

		//$sql = "SELECT COUNT(*) FROM {$this->tableName} WHERE etag = ? ";
		return $this->where(array('etag'=>$etag))->count();
	}

	/**
         *
         * @param array $conditions
         * @param array $orderBy
         * @param int $start
         * @param int $limit
         * @return array
         * @author ZhaoZuoWu 2015-03-26
         */
	public function searchFiles($conditions, $orderBy, $start, $limit) {
		$this->filterStartLimit($start, $limit);
        $data = $this
				->field("*")
				->where($conditions)
				->order("{$orderBy[0]} {$orderBy[1]} ")
				->limit("{$start},{$limit}")
				->select();
		return  $data ? : array();
	}


	public function searchFileCount($conditions) {
//		$builder = $this->createSearchQueryBuilder($conditions)
//			->select('COUNT(id)');
//		return $builder->execute()->fetchColumn(0);
		$this->where($conditions)->count('id');
	}

	public function deleteFiles($conditions){
		return $this->where($conditions)->delete();
	}

	public function deleteFile($id) {
		return $this->where(array('id'=>$id))->delete();
	}

	public function addFile(array $file) {
		$file['createdTime'] = time();
		$affected            = $this->add($file);
		if ($affected <= 0) {
			E('Insert Course File disk file error.');
		}
		return $this->getFile($affected);
	}

	public function updateFile($id, array $fields) {
		$fields['updatedTime'] = time();
		$this->where(array('id'=>$id))->save($fields);
		return $this->getFile($id);
	}


	public function updateFileUsedCount($fileIds, $offset) {
		$fileIdsCollection = "'" . implode("', '", $fileIds) . "'";
		$sql               = "UPDATE {$this->tableName} SET usedCount = usedCount + {$offset} where id in ({$fileIdsCollection})";

		$info = $this->execute($sql);
                return $info;
	}

	public function getFileByTargetType($targetType) {
		//$sql = "SELECT * FROM {$this->tableName} WHERE targetType = ? LIMIT 1";
		return $this->where(array('targetType'=>$targetType))->find() ? :array();
	}


	private function createSearchQueryBuilder($conditions) {
//		$conditions = array_filter($conditions);
//
//		if (isset($conditions['filename'])) {
//			$conditions['filenameLike'] = "%{$conditions['filename']}%";
//			unset($conditions['filename']);
//		}
//
//		$builder = $this->createDynamicQueryBuilder($conditions)
//			->from($this->tableName, $this->tableName)
//			->andWhere('targetType = :targetType')
//			->andWhere('targetId = :targetId')
//			->andWhere('type = :type')
//			->andWhere('storage = :storage')
//			->andWhere('filename LIKE :filenameLike');
//
//		if (isset ($conditions ['createdUserIds'])) {
//			$builder->andStaticWhere('createdUserId in (' . $conditions ['createdUserIds'] . ')');
//		}
//
//		return $builder;
	}

}