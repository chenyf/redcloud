<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;

class UserProfileModel extends BaseModel {
 
    protected $tableName = 'user_profile';

    public function getProfile($id){
        $where['id'] = $id;
        return $this->where($where)->find() ? : null;
//        $sql = "SELECT * FROM {$this->tableName} WHERE id = ? LIMIT 1";
//        return $this->getConnection()->fetchAssoc($sql, array($id)) ? : null;
    }

    public function getFieldList($field,$where){
        if(!empty($where) && is_array($where)){
            return $this->field("DISTINCT {$field}")->where($where)->select() ? : array();
        }else{
            return $this->field("DISTINCT {$field}")->select() ? : array();
        }
    }
    
    public function getAll(){
        return $this->field("id")->select() ? : null;
    }

    public function selectProfile($condition){
        return $this->where($condition)->select() ? : array();
    }
    
    public function getCollegeList($role='teacher'){
        if(!in_array($role,['student','teacher'])){
            return array();
        }
        return $this->field("DISTINCT college")->where(array('role' => $role))->select() ? : array();
    }

    /**
     *
     * @edit fubaosheng 2015-08-11
     */
    public function addProfile($profile){
        $r = $this->add($profile);
        $id = $profile['id'];
        if(!$r) E("Insert profile error.");
        return  $this->getProfile($id);
//        $affected = $this->getConnection()->insert($this->tableName, $profile);
//        if ($affected <= 0) {
//            throw $this->createDaoException('Insert profile error.');
//        }
//        return $this->getProfile($this->getConnection()->lastInsertId());
    }
    
    /**
     * excel导入用户 添加用户资料 先中心库 后私有库
     * @edit tanhaitao 2015-09-10
     */
    public function addExcelProfile($profile){
        $options = array(
            "id"=>0,"truename"=>'',"idcard"=>'',"gender"=>'',"iam"=>'',"birthday"=>NULL, "city"=>'', "mobile"=>'', "qq"=>'',"signature"=>NULL,"about"=>NULL,"company"=>'',"job"=>'',"school"=>'',"class"=>'',"weibo"=>'',"weixin"=>'',"site"=>'',"intField1"=>NULL,"intField2"=>NULL,"intField3"=>NULL,"intField4"=>NULL,"intField5"=>NULL,"dateField1"=>NULL,"dateField2"=>NULL,"dateField3"=>NULL,"dateField4"=>NULL,"dateField5"=>NULL, "floatField1"=>NULL, "floatField2"=>NULL,"floatField3"=>NULL, "floatField4"=>NULL, "floatField5"=>NULL, "varcharField1"=>'', "varcharField2"=>'', "varcharField3"=>'', "varcharField4"=>'',"varcharField5"=>'', "varcharField6"=>'',"varcharField7"=>'',"varcharField8"=>'', "varcharField9"=>'', "varcharField10"=>'',"textField1"=>'',"textField2"=>'', "textField3"=>'', "textField4"=>'', "textField5"=>'',"textField6"=>'',"textField7"=>'',"textField8"=>'',"textField9"=>'',"textField10"=>'',
        );
        $profile = array_merge($options, $profile);
        $profile['webCode'] = (isset($profile['webCode']) && !empty($profile['webCode'])) ? $profile['webCode'] : C('WEBSITE_CODE');
        $r = $this->add($profile);
        $id = $profile['id'];
        if(!$r) return false;
        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
        return true;
    }
    
    public function addUserProfile($param){
        $options = array(
            "id"=>0,"truename"=>'',"idcard"=>'',"gender"=>'',"iam"=>'',"birthday"=>NULL, "city"=>'', "mobile"=>'', "qq"=>'',"signature"=>NULL,"about"=>NULL,"company"=>'',"job"=>'',"school"=>'',"class"=>'',"weibo"=>'',"weixin"=>'',"site"=>'',"intField1"=>NULL,"intField2"=>NULL,"intField3"=>NULL,"intField4"=>NULL,"intField5"=>NULL,"dateField1"=>NULL,"dateField2"=>NULL,"dateField3"=>NULL,"dateField4"=>NULL,"dateField5"=>NULL, "floatField1"=>NULL, "floatField2"=>NULL,"floatField3"=>NULL, "floatField4"=>NULL, "floatField5"=>NULL, "varcharField1"=>'', "varcharField2"=>'', "varcharField3"=>'', "varcharField4"=>'',"varcharField5"=>'', "varcharField6"=>'',"varcharField7"=>'',"varcharField8"=>'', "varcharField9"=>'', "varcharField10"=>'',"textField1"=>'',"textField2"=>'', "textField3"=>'', "textField4"=>'', "textField5"=>'',"textField6"=>'',"textField7"=>'',"textField8"=>'',"textField9"=>'',"textField10"=>'',
        );
        $fields = array_merge($options, $param);
//        $r = $this->add($fields);
        $r = $this->add($fields);
        if(!$r) return false;
        return  true;
    }
    
//(16, '', '', 'female', '', NULL, '', '', '', NULL, NULL, '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '')

    public function updateProfile($id, $profile){
        $where['id'] = $id;
        $r = $this->where($where)->save($profile);
        return $this->getProfile($id);
    }

    public function findProfilesByIds(array $ids){
        if(empty($ids)){ return array(); }
        $str = implode(',',$ids);
        $where['id'] = array('in',$str);
        return $this->where($where)->select() ? : array();
//        $marks = str_repeat('?,', count($ids) - 1) . '?';
//        $sql ="SELECT * FROM {$this->tableName} WHERE id IN ({$marks});";
//        return $this->getConnection()->fetchAll($sql, $ids);
    }

    public function dropFieldData($fieldName){ 
        return  $this->save(array($fieldName=>null));
//        $sql="UPDATE {$this->tableName} set {$fieldName} =null ";
//        return $this->getConnection()->exec($sql);
    }
    
}
?>
