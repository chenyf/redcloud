<?php

namespace Common\Model\Theme;
use Common\Model\Common\BaseModel;
use PDO;

class ThemeConfigModel extends BaseModel
{
    protected $tableName = 'theme_config';

    private $serializeFields = array(
            'config' => 'json',
            'allConfig' => 'json',
            'confirmConfig' => 'json',
    );

    public function getThemeConfig($id)
    {
        $themeConfig = $this-> where("id = {$id}")-> find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        $themeConfig = $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
        return $themeConfig ? $this->createSerializer()->unserialize($themeConfig, $this->serializeFields) : null;
    }

    public function getThemeConfigByName($name)
    {
        $themeConfig = $this-> where("name = {$name}")-> find();
//        $sql = "SELECT * FROM {$this->tableName} WHERE name = ? LIMIT 1";
//        $themeConfig = $this->getConnection()->fetchAssoc($sql, array($name)) ? : null;

        return $themeConfig ? $this->createSerializer()->unserialize($themeConfig, $this->serializeFields) : null;
    }

    public function addThemeConfig($themeConfig)
    {
        $themeConfig = $this->createSerializer()->serialize($themeConfig, $this->serializeFields);
        $affected = $this-> add($themeConfig);
        if($affected <= 0){
            E("Insert themeConfig errore.");
        }
        return $affected;
//        $affected = $this->getConnection()->insert($this->tableName, $themeConfig);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert themeConfig error.');
//        }
//        return $this->getThemeConfig($this->getConnection()->lastInsertId());
    }

    public function updateThemeConfigByName($name, $fields)
    {
        $fields = $this->createSerializer()->serialize($fields, $this->serializeFields);
        $this->where("name = {$name}")-> save($fields);
//        $this->getConnection()->update($this->tableName, $fields, array('name' => $name));
        return $this->getThemeConfigByName($name);
    }

}