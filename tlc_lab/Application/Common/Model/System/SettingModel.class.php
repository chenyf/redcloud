<?php
/**
 * 系统设置model
 *
 * @author 钱志伟 2015-03-24
 */

namespace Common\Model\System;
use \Common\Model\Common\BaseModel;

class SettingModel extends BaseModel {
    protected $tableName = 'setting';
    
    public function initDb($siteSelect) {
//        #中心站需要修改各个分站库 qzw 2015-9-18
//        if($siteSelect == "local") $code = C("WEBSITE_CODE");
//        else $code = $siteSelect;
//        if(\Common\Lib\WebCode::isCenterWeb($code)){
//            $this->switchDB("center");
//        }else{
//            $this->switchDB("");
//        }
//        $this->setCommCenterWebUseWebCodeRule();
    }
    
    public function getSetting($id,$siteSelect="local")
    {
//        $this->initDb($siteSelect);
        $table = $this;
        if($siteSelect != "local"){
            $table = processSqlObj(array('sqlObj'=>$table,'siteSelect'=>$siteSelect));
        }
        return $table->where("id={$id}")->find();
    }

	public function getSettingByName($name)
	{
		return $this->where(['name'=>$name])->find();
	}


    public function getAllSettingByName($name , $siteSelect='local'){
//        $this->initDb($siteSelect);
        $table = $this;
        if($siteSelect!="local"){
            $table = processSqlObj(array('sqlObj'=>$table,'siteSelect'=>$siteSelect));
        }
        $where['name'] = array("eq" , $name);
        $res = $table->where($where)->select();
        return $res;
    }
    
    public function getAllSettingByCodes($codes = array() ,$name='site', $siteSelect = 'loacl'){
//        $this->initDb($siteSelect);
        $table = $this;
        $table = processSqlObj(array('sqlObj'=>$table,'siteSelect'=>$siteSelect));
        $where['name'] = array("eq" , $name);
        if(!empty($codes)){
            $where['webCode'] = array("in" , $codes);
        }
        $res = $table->where($where)->select();
        return $res;
    }

    public function addSetting($setting)
    {
//        $this->initDb($setting['webCode']);
        $r = $this->add($setting);
        if(!$r) E("Insert setting error.");
//         $info =  $this->getSetting($r);
        return $r;
        
        
//       $affected = $this->getConnection()->insert($this->tableName, $setting);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert setting error.');
//        }
//        return $this->getSetting($this->getConnection()->lastInsertId());
    }
    /**
     * 编辑配置
     * @author 钱志伟 2015-08-31
     */
    public function editSetting($id, $setting){
//        return $this->setWebCode()->where(array('id'=>$id))->save($setting);
    }

    public function findAllSettings($siteSelect="local",$cache=true)
    {        
//        $this->initDb($siteSelect);
        #cache by qzw 2015-09-08
        static $cacheArr = array();
        $cacheKey = md5($this->currentDb . unserialize(func_get_args()).$siteSelect);
        if(isset($cacheArr[$cacheKey]) && $cache) return $cacheArr[$cacheKey];

        $table = $this;
        $cacheArr[$cacheKey] = $rs = $table->select();
        return $rs;
    }

    public function deleteSettingByName($name,$siteSelect="local")
    {
//        $this->initDb($siteSelect);
        $table = $this;
        if($siteSelect) $table = processSqlObj(array('sqlObj'=>$table,'siteSelect'=>$siteSelect));
        $where['name'] = $name;
        return $table->where($where)->delete();
//        return $this->getConnection()->delete($this->tableName, array('name' => $name));
    }
    
    
}
