<?php

/*
 * 客户端版本管理
 * @package
 * @author     tanhaitao  2016-01-29
 * @version    $Id$
 */

namespace Common\Model\Pc;

use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class PcVersionModel extends BaseModel {

    protected $tableName = 'pc_version';

    public function getWebCodeCount($conditions = array()) {
        $appWebCode = $conditions['siteSelect'];
        $keyword = $conditions['keyword'];
        $keyKind = $conditions['keyword_kind'];
        $where = "1=1";
        if ($appWebCode)
            $where.= " and appWebCode = '{$appWebCode}' ";
        if ($conditions['keyword_type'] && $keyword)
            $where.= " and appWebCode like '%{$keyword}%' ";
        if (!empty($keyKind) && $keyKind == "B")
            $where.= " and type in (1,2) ";
        if (!empty($keyKind) && $keyKind == "A")
            $where.= " and type in (3,4) ";
        return $this->where($where)->count();
    }

    public function getVersionFind($id) {
        $where['id'] = $id;
        return $this->where($where)->find();
    }

    public function getVersionByType($type) {
        $where['type'] = $type;
        $where['appWebCode'] = C('WEBSITE_CODE');
        $where['isCurrent'] = 1;
        return $this->where($where)->field('isForceUpdate,name,url,version,mtm publicTm,function')->order('mtm desc')->find();
    }

    public function getVersionList($start = 0, $end = 15, $conditions = array(), $switch = false) {
        $isLocalCenter = \Common\Lib\WebCode::isLocalCenterWeb();
        $siteSelect = !empty($siteSelect) ? $siteSelect : '';
        if ($isLocalCenter && $switch) {
            $appWebCode = $conditions['siteSelect'];
            $keyword = $conditions['keyword'];
            $keyKind = $conditions['keyword_kind'];
            $where = "1=1";
            if ($appWebCode)
                $where.= " and appWebCode = '{$appWebCode}' ";
            if ($conditions['keyword_type'] && $keyword)
                $where.= " and appWebCode like '%{$keyword}%' ";
            if (!empty($keyKind) && $keyKind == "B")
                $where.= ' and type = 2';
            if (!empty($keyKind) && $keyKind == "A")
                $where.= ' and type = 1';
            $list = $this->where($where)->limit($start, $end)->order('id asc')->select();
        }else {
            $list = $this->limit($start, $end)->order('id asc')->select();
        }
        return $list;
    }

    public function addVersion($fields) {
        $fields = ArrayToolkit::filter($fields, array(
                    'name' => '',
                    'url' => '',
                    'version' => '',
                    'function' => '',
                    'type' => 0,
                    'isForceUpdate' => 0,
                    'isCurrent' => 0,
                    'ctm' => 0,
                    'mtm' => 0,
                    'appWebCode' => ''
        ));
        $fields['ctm'] = time();
        $fields['mtm'] = time();
        $id = $this->add($fields);

        if ($fields['isCurrent'] == 1) {
            $this->where(array("appWebCode" => $fields['appWebCode'], 'type' => $fields['type'], 'id' => array('neq', $id)))->setField('isCurrent', 0);
        }
        return $id;
    }

    public function editVersion($fields) {
        $fields['mtm'] = time();
        $this->save($fields);

        if ($fields['isCurrent'] == 1) {
            $this->where(array("appWebCode" => $fields['appWebCode'], 'type' => $fields['type'], 'id' => array('neq', $fields['id'])))->setField('isCurrent', 0);
        }

        return true;
    }

    /**
     * 返回当前客户端app下载链接
     * 1:windows 
     * 2:Mac
     * @author tanhaitao 2016-02-17
     */
    public function getDownloadByType($device) {
        $device = strtolower($device);
        if ($device == "windows")
            $type = 1;
        if ($device == "macintosh")
            $type = 2;

        $field = "url";

        $url = $this->setWebCode('')
                ->where(array('type' => $type, 'isCurrent' => 1, 'appWebCode' => C('WEBSITE_CODE')))
                ->order('mtm desc')
                ->getField($field);
        return $url ? $url : '';
    }

}