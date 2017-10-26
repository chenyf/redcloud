<?php

namespace Common\Twig\DataTag;



class HotGroupDataTag
{
    /**
     * 获取最热班级
     * 
     * 可传入的参数：
     *
     *   count 必需 必需 班级数量，取值不能超过100
     * 
     * @param  array $arguments 参数
     * @return array 最热班级
     */
    
    public function getData(array $arguments)
    {
        if (empty($arguments['count'])) {
            throw new \InvalidArgumentException("count参数缺失");
        } else {
            $hotGroups = $this->getGroupService()->searchGroups(array('status'=>'open',),  array('memberNum', 'DESC'),0, $arguments['count']);
        }

    	return $hotGroups;
    }

    private function getGroupService() 
    {
        return createService('Group.GroupService');
    }
}
