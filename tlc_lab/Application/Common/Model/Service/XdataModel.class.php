<?php
namespace Common\Model\Service;
use Think\Model;

class XdataModel extends \Common\Model\Common\BaseModel{
    
    protected   $tableName	=	'system_data';	// 数据库表名
    protected	$list_name	=	'global';	// 默认列表名
    protected	$fields		=	array (0 => 'id',1 => 'uid',2 => 'list',3 => 'key',4 => 'value',5 => 'mtime','_autoinc' => true,'_pk' => 'id');
    
    
    /**
     * 读取参数列表
     * @param string $listName 参数列表list
     * @return array
     */
    public function lget($list_name='') {
        $list_name = $this->_strip_key($list_name);
        
        static $_res = array();
        if (!isset($_res[$list_name])){
            $data = array();
            $result	= $this->where("list = '{$list_name}'")->order('id ASC')->select();
            if ($result){
                foreach($result as $v){
                    $data[$v['key']] = unserialize($v['value']);
                }
            }
            $_res[$list_name] = $data;
        }
        
        return $_res[$list_name];
    }
    
    /**
     * 过滤key
     * @param string  $key 只允许格式 数字字母下划线，list:key 不允许出现html代码 和这些符号 ' " & * % ^ $ ? ->
     * @return string
     */
    protected function _strip_key($key = ''){
        if($key == ''){
            return $this->list_name;
        }else{
            $key    =   strip_tags($key);
            $key    =   str_replace(array('\'','"','&','*','%','^','$','?','->'),'',$key);
            return $key;
        }
    }
    
}
?>
