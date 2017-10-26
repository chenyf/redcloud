<?php
namespace Common\Model\AccessControl;


use Common\Model\Common\BaseModel;
use Common\Traits\DaoModelTrait;

class NodeModel extends BaseModel
{

    use DaoModelTrait;

    protected $tableName = 'admin_node';


    protected $connection = 'DB_CENTER';

    //自动验证
    protected $_validate = array(
        array('name', 'require', '请填写节点编码'),
        array('title', 'require', '请填写节点名称'),

        array(array('title', 'pid'), 'isTitleExists', '节点名称已存在', 1, 'callback', self::MODEL_INSERT),
        array(array('name', 'pid'), 'isNameExists', '节点编码已存在', 1, 'callback', self::MODEL_INSERT),
    );

    //自动完成
    protected $_auto = array(
        array('name', 'strtoupper', self::MODEL_BOTH, 'function'),
    );

    /**
     * 节点名称是否已存在
     * @param $params
     * @return bool
     */
    public function isTitleExists($params)
    {
        $count = $this->where(['title' => $params['title'], 'pid' => $params['pid']])->count();
        return !boolval($count);
    }

    /**
     * 节点编码是否已存在
     * @param $params
     * @return bool
     */
    public function isNameExists($params)
    {
        $count = $this->where(['name' => strtoupper($params['name']), 'pid' => $params['pid']])->count();
        return !boolval($count);
    }

}