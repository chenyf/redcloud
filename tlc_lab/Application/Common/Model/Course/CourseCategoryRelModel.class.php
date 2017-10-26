<?php
/*
 * 数据层
 * @package
 * @version    $Id$
 */
namespace  Common\Model\Course;

use Common\Model\Common\BaseModel;

class CourseCategoryRelModel extends BaseModel
{

	protected $tableName = 'course_category_rel';

        /**
         * 根据分类id获取课程id
         * @param int $cid
         * @return array
         * @date 2015-08-12
         * @author LiangFuJian <liangfujian@redcloud.com>
         */
        public function getCourseIdsByCid($cid = 0){
            
            if (is_array($cid))
                $map['categoryId'] = array('in',$cid);
            else if ($cid != 0)
                $map['categoryId'] = intval($cid);
            else
                $map['categoryId'] = array('gt', 0);
            return $this->field('courseId')->where($map)->select();
            
        }

	

}