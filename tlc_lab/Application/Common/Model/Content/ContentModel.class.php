<?php
namespace Common\Model\Content;
use Common\Model\Common\BaseModel;

class ContentModel extends BaseModel
{
	protected $tableName = 'content';

	public function getContent($id)
	{
            return $this-> where("id = {$id}")-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
	}

	public function getContentByAlias($alias)
	{
            $map['alias'] = $alias;
            return $this-> where($map)-> find()? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE alias = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($alias)) ? : null;
	}

	public function searchContents($conditions, $orderBy, $start, $limit)
	{
//            $this->filterStartLimit($start, $limit);
//            return $this-> where($conditions)-> order("$orderBy[0] $orderBy[1]")-> limit($start, $limit)? : array();
		$builder = $this->_createSearchQueryBuilder($conditions)
			->select('*')
			->addOrderBy($orderBy[0], $orderBy[1])
			->setFirstResult($start)
			->setMaxResults($limit);
		return $builder->execute()->fetchAll() ? : array();
	}

	public function searchContentCount($conditions)
	{
//            return $this-> where($conditions)-> count("id");
            $builder = $this->_createSearchQueryBuilder($conditions)
                ->select('COUNT(id)');
            return $builder->execute()->fetchColumn(0);
	}

	public function addContent($content)
	{
            return $this-> add($content);
//        $affected = $this->getConnection()->insert($this->tableName, $content);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert content error.');
//        }
//        return $this->getConnection()->lastInsertId();
	}

	public function updateContent($id, $content)
	{
            return $this->where("id = {$id}")-> save($content);
//        return $this->getConnection()->update($this->tableName, $content, array('id' => $id));
	}

	public function deleteContent($id)
	{
            return $this-> where("id = {$id}")-> delete();
//		return $this->getConnection()->delete($this->tableName, array('id' => $id));
	}

	private function _createSearchQueryBuilder($conditions)
	{
		if (isset($conditions['keywords'])) {
			$conditions['keywords'] = "%{$conditions['keywords']}%";
		}

		$builder = $this->createDynamicQueryBuilder($conditions)
			->from($this->tableName, 'content')
			->andWhere('type = :type')
			->andWhere('status = :status')
			->andWhere('title LIKE :keywords');

		if (isset($conditions['categoryIds'])) {
			$categoryIds = array();
			foreach ($conditions['categoryIds'] as $categoryId) {
				if (ctype_digit($categoryId)) {
					$categoryIds[] = $categoryId;
				}
			}
			if ($categoryIds) {
				$categoryIds = join(',', $categoryIds);
				$builder->andStaticWhere("categoryId IN ($categoryIds)");
			}
		}

		return $builder;
	}
}