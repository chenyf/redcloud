<?php

namespace Topxia\DataTag;

use Topxia\DataTag\DataTag;

class CategoriesDataTag extends CourseBaseDataTag implements DataTag  
{
    /**
     * 获取所有分类
     * 
     * 可传入的参数：
     *
     *   group      分类组CODE
     *   parentId   分类的父Id
     * 
     * @param  array $arguments 参数
     * @return array 分类
     */
    
    public function getData(array $arguments)
    {

        $this->checkGroupId($arguments);

        if(array_key_exists("parentId", $arguments)){
            return $this->getCategoryService()->findCategoriesByParentId($arguments['parentId']);
        } else {
            return $this->getCategoryService()->findCategories();
        }

        return array();
    }

}
