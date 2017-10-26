<?php
namespace Common\Model\Content;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
class NavigationServiceModel extends BaseModel
{
    public function getNavigation($id)
    {
        return $this->getNavigationDao()->getNavigation($id);
    }

    public function findNavigations($start, $limit)
    {
        return $this->getNavigationDao()->findNavigations($start, $limit);
    }

    public function getNavigationsCount()
    {
        return $this->getNavigationDao()->getNavigationsCount();
    }

    public function getNavigationsCountByType($type)
    {
        return $this->getNavigationDao()->getNavigationsCountByType($type);
    }
    
    /**
     * 获得院校新闻
     * @author 谈海涛 2016-03-04
     */
    public function getNewsNavigations($news)
    {
        return $this->getNavigationDao()->getNewsNavigations($news);
    }
    
    public function findNavigationsByType($type, $start, $limit)
    {
        return $this->getNavigationDao()->findNavigationsByType($type, $start, $limit);
//        createDao('Content.NavigationModel')->findNavigationsByType($type, $start, $limit);
    }

    public function getNavigationsTreeByType($type)
    {
        $count = $this->getNavigationsCountByType($type);
        $navigations = $this->findNavigationsByType($type, 0, $count);
        
        $navigations = ArrayToolkit::index($navigations, 'id');
        foreach ($navigations as $index => $nav) {
            if ($nav['parentId'] == 0) {
                continue;
            }

            if (empty($navigations[$nav['parentId']]['children'])) {
                $navigations[$nav['parentId']]['children'] = array();
            }

            if ($nav['isOpen']) {
            $navigations[$nav['parentId']]['children'][] = $nav;
            unset($navigations[$index]);
            }
        }

        return $navigations;
    }

    public function getNavigationsListByType($type)
    {
        $count = $this->getNavigationsCountByType($type);
        $navigations = $this->findNavigationsByType($type, 0, $count);

        $prepare = function($navigations) {
            $prepared = array();
            foreach ($navigations as $nav) {
                if (!isset($prepared[$nav['parentId']])) {
                    $prepared[$nav['parentId']] = array();
                }
                $prepared[$nav['parentId']][] = $nav;
            }
            return $prepared;
        };

        $navigations = $prepare($navigations);

        $tree = array();
        $this->makeNavigationTreeList($tree, $navigations, 0);

        return $tree;

    }

    private function makeNavigationTreeList(&$tree, &$navigations, $parentId)
    {
        static $depth = 0;
        static $leaf = false;
        if (isset($navigations[$parentId]) && is_array($navigations[$parentId])) {
            foreach ($navigations[$parentId] as $nav) {
                $depth++;
                $nav['depth'] = $depth;
                $tree[] = $nav;
                $this->makeNavigationTreeList($tree, $navigations, $nav['id']);
                $depth--;
            }
        }
        return $tree;
    }

    public function createNavigation($fields)
    {
        $keysArray = array('name', 'url', 'isOpen', 'isNewWin', 'type', 'sequence', 'parentId');
        $keysOfFields = array_keys($fields);
        foreach ($keysOfFields as $key => $keyOfFields) {
            if(!in_array($keyOfFields, $keysArray)){
                E("添加的{$keyOfFields}字段有问题！");
            }
        }

        $fields['createdTime'] = $fields['updateTime'] = time();
        $result = $this->getNavigationDao()->addNavigation($fields);

        $this->getLogService()->info('info', 'navigation_create', "创建导航{$fields['name']}");

        return $result;
    }

    public function updateNavigation($id, $fields)
    {
        if(isset($fields['id'])){
            unset($fields['id']);
        }
        $fields['updateTime'] = time();

        $this->getLogService()->info('info', 'navigation_update', "编辑导航#{$id}", $fields);

        return $this->getNavigationDao()->updateNavigation($id, $fields);
    }

    public function deleteNavigation($id)
    {
        return ($this->getNavigationDao()->deleteNavigation($id)) + ($this->getNavigationDao()->deleteNavigationByParentId($id));
    }

    private function getNavigationDao()
    {
        return $this->createDao('Content.NavigationModel');
    }

    protected function getLogService()
    {
        return $this->createService('System.LogServiceModel');        
    }
    
}
