<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class UserFieldServiceModel extends BaseModel{
    
    protected function getUserService(){
        return $this->createService('User.UserService');
    }

    protected function getUserFieldDao(){
	    return $this->createService('User.UserField');
    }
    
    public function getField($id){
        return $this->getUserFieldDao()->getField($id);
    }

    public function addUserField($field){
        if (empty($fields['field_title'])) E('字段名称不能为空！');
        if (empty($fields['field_seq'])) E('字段排序不能为空！');
        if (!intval($fields['field_seq'])) E('字段排序只能为数字！');
        $fieldName = $this->checkType($fields['field_type']);
        
        if($fieldName == false) E('字段类型是错误的！');
        $field['fieldName'] = $fieldName;
        $field['title'] = $fields['field_title'];
        $field['seq'] = $fields['field_seq'];
        $field['enabled'] = 0;
        if(isset($fields['field_enabled'])) $field['enabled']=1;
        $field['createdTime']=time();

        return $this->getUserFieldDao()->addField($field);
    }
    
    private function checkType($type){   
        $fieldName = "";
        if($type == "text"){
            for($i=1;$i<11;$i++){
                $field = $this->getUserFieldDao()->getFieldByFieldName("textField".$i);
                if(!$field){
                    $fieldName = "textField".$i;
                    break;
                }
            }
        }
        if($type == "int"){
           for($i=1;$i<6;$i++){
                $field = $this->getUserFieldDao()->getFieldByFieldName("intField".$i);
                if(!$field){
                    $fieldName = "intField".$i;
                    break;
                }
            }
        }
        if($type == "date"){
             for($i=1;$i<6;$i++){
                $field = $this->getUserFieldDao()->getFieldByFieldName("dateField".$i);
                if(!$field){
                    $fieldName = "dateField".$i;
                    break;
                }
            }
        }
        if($type == "float"){
            for($i=1;$i<6;$i++){
                $field = $this->getUserFieldDao()->getFieldByFieldName("floatField".$i);
                if(!$field){
                    $fieldName = "floatField".$i;
                    break;
                }
            }
        }
        if($type == "varchar"){
            for($i=1;$i<11;$i++){
                $field = $this->getUserFieldDao()->getFieldByFieldName("varcharField".$i);
                if(!$field){
                    $fieldName = "varcharField".$i;
                    break;
                }
            }
        }
        if($fieldName == "") return false;
        return $fieldName;
    }

    public function searchFieldCount($condition){
        return $this->getUserFieldDao()->searchFieldCount($condition);
    }

    public function getAllFieldsOrderBySeq(){
         return $this->getUserFieldDao()->getAllFieldsOrderBySeqAndEnabled();
    }

    public function getAllFieldsOrderBySeqAndEnabled(){
        return $this->getUserFieldDao()->getAllFieldsOrderBySeqAndEnabled();
    }

    public function updateField($id,$fields){
        $fields = ArrayToolkit::filter($fields, array(
            "title"=>"",
            "seq"=>"",
            "enabled"=>0,
        ));
        if (isset($fields['title']) && empty($fields['title'])) E('字段名称不能为空！');
        if (isset($fields['seq']) && empty($fields['seq'])) E('字段排序不能为空！');
        if (isset($fields['seq']) && !intval($fields['seq'])) E('字段排序只能为数字！');
        return $this->getUserFieldDao()->updateField($id, $fields);
    }

    public function dropField($id){
        $field=$this->getUserFieldDao()->getField($id);
        $this->getUserService()->dropFieldData($field['fieldName']);
        $this->getUserFieldDao()->deleteField($id);
    }
    
    
}
?>
