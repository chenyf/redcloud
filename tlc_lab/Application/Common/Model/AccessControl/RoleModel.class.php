<?php
namespace Common\Model\AccessControl;


use Common\Model\Common\BaseModel;
use Common\Traits\DaoModelTrait;

class RoleModel extends BaseModel {

    use DaoModelTrait;

    protected $tableName = 'admin_role';

    protected $order = 'updated_time desc';


    //自动验证
    protected $_validate = array(
        array('name', 'require', '请填写角色名称'),
        array('code', 'require', '请填写角色编码'),

        array(array('name'), 'isTitleExists', '角色名称已存在', 1, 'callback', self::MODEL_INSERT),
        array(array('code'), 'isNameExists', '角色编码已存在', 1, 'callback', self::MODEL_INSERT),
    );

    //自动完成
    protected $_auto = array(
        array('updated_time', 'time', 3, 'function'),
        array('code', 'strtoupper', self::MODEL_INSERT, 'function'),
        array('operator', 'getCurrentUid', 3, 'callback'),
    );

    protected $_link = [

        //关联用户表
        'user'  => array(
            'mapping_type'   => self::BELONGS_TO,
            'mapping_name'   => 'user',
            'foreign_key'    => 'operator',
            'mapping_fields' => 'nickname',
            'as_fields'      => 'nickname:operator'
        ),

    ];

    /**
     * 角色名称是否已存在
     * @param $params
     * @return bool
     */
    public function isTitleExists($params)
    {
        $count = $this->where(['name' => $params['name']])->count();
        return !boolval($count);
    }

    /**
     * 角色编码是否已存在
     * @param $params
     * @return bool
     */
    public function isNameExists($params)
    {
        $count = $this->where(['code' => strtoupper($params['code'])])->count();
        return !boolval($count);
    }

}