<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;
class LessonViewModel extends BaseModel{
    
    protected $tableName = 'course_lesson_view';

    public function getLessonView($id){
        return $this->where(" id = {$id}")->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function addLessonView($lessonView){
        $r = $this->add($lessonView);
        if(!$r) E("Insert LessonView error.");
        return $this->getLessonView($r);
//        $affected = $this->getConnection()->insert($this->tableName, $lessonView);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert LessonView error.');
//        }
//        return $this->getLessonView($this->getConnection()->lastInsertId());
    }

    public function searchLessonViewCount($conditions){
        $where = $this->_createSearchQueryBuilder($conditions);
        $builder = $this;
        if(isset($conditions['siteSelect'])) $builder = processSqlObj(array('sqlObj'=>$builder, 'siteSelect'=>$conditions['siteSelect']));
        return $builder->where($where)->count();
        
        
//        $builder = $this->_createSearchQueryBuilder($conditions);
//        if(isset($conditions['siteSelect'])) $builder = processSqlObj(array('sqlObj'=>$builder, 'siteSelect'=>$conditions['siteSelect']));
//        $builder = $this->_createSearchQueryBuilder($conditions)->select('COUNT(id)');
//        return $builder->execute()->fetchColumn(0);
    }

    public function getAnalysisLessonMinTime($type){
        $condition = $this->_filterTypeCondition($type);
        return $this->where($condition)->order("createdTime asc")->field('createdTime')->find() ? : null;
//        $sql = "SELECT `createdTime` FROM {$this->tableName} {$condition} ORDER BY `createdTime` ASC LIMIT 1;";
//        return $this->getConnection()->fetchAssoc($sql) ? : null;
    }

    public function searchLessonView($conditions, $orderBy, $start, $limit){
        $this->filterStartLimit($start, $limit);
        $where =  $this->_createSearchQueryBuilder($conditions);
        $info =  $this->where($where)->order("{$orderBy[0]} {$orderBy[1]}")->limit($start, $limit)->select()? : array();
        return $info;
//        $builder = $this->_createSearchQueryBuilder($conditions)
//            ->select('*')
//            ->orderBy($orderBy[0], $orderBy[1])
//            ->setFirstResult($start)
//            ->setMaxResults($limit);
//  
//        return $builder->execute()->fetchAll() ? : array(); 
    }

    public function searchLessonViewGroupByTime($startTime,$endTime,$conditions){
        $conditions = $this->_filterConditions($conditions);
        return $this->field("count(id) as count,from_unixtime(createdTime,'%Y-%m-%d') as date")->where("`createdTime`>={$startTime} and `createdTime`<={$endTime} {$conditions}")->group("from_unixtime(`createdTime`,'%Y-%m-%d')")->order("date ASC")->select();
//        $sql="SELECT count(`id`) as count, from_unixtime(createdTime,'%Y-%m-%d') as date FROM `{$this->tableName}` WHERE  `createdTime`>={$startTime} and `createdTime`<={$endTime} {$conditions} group by date_format(from_unixtime(`createdTime`),'%Y-%m-%d') order by date ASC ";
//        return $this->getConnection()->fetchAll($sql);
    }

    private function _createSearchQueryBuilder($conditions){
        $where = " 1=1";
        extract($conditions);
        if(isset($fileType)){
            $where.=" AND  fileType='".$fileType."'";
        }
           if(isset($fileStorage)){
            $where.=" AND  fileStorage='".$fileStorage."'";
        }
        if(isset($startTime)){
            $where.=" AND  createdTime>='".$startTime."'";
        }
          if(isset($endTime)){
            $where.=" AND  createdTime<='".$endTime."'";
        }
        return $where;
        /*
        $builder = $this->getConnection()->createQueryBuilder($conditions)
            ->from($this->tableName, 'course_lesson_view')
            ->andWhere('fileType ="'.$conditions["fileType"].'"')
            ->andWhere('fileStorage ="'.$conditions["fileStorage"].'"')
            ->andWhere('createdTime >= "'.$conditions["startTime"].'"')
            ->andWhere('createdTime <="'.$conditions["endTime"].'"')
            ->select("*");
        */
        
       ///   $o= $this->connection->createQueryBuilder(array())->from('course')->select('*');
       
    }

    private function _filterTypeCondition($type){
        if (in_array($type, array('net','local','cloud'))) return "`fileType` = '{$type}'";
        return "";
    }

    private function _filterConditions($conditions){
        $conditionStr = "";
        if (array_key_exists("fileType", $conditions)) {
            $conditionStr .= " and `fileType` = '".$conditions['fileType']."'";
        }
        if (array_key_exists("fileStorage", $conditions)) {
            $conditionStr .= " and `fileStorage` = '".$conditions['fileStorage']."'";
        }
        return $conditionStr;
    }
}
?>
