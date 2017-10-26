<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class TokenServiceModel extends BaseModel{
    
    protected function getTokenDao(){
        return $this->createDao("User.Token");
    }
    
    /**
     * 生成一个一次性的Token
     * 
     * @param  string   $type   Token类型
     * @param  array    $args   生成Token的一些限制规则
     * 
     * @return array 生成的Token
     */
    public function makeToken($type, array $args = array()){
        $token = array();
        $token['type'] = $type;
        $token['token'] = $this->_makeTokenValue(32);
        $token['data'] = !isset($args['data']) ? null : $args['data'];
        $token['times'] = empty($args['times']) ? 0 : intval($args['times']);
        $token['remainedTimes'] = $token['times'];
        $token['userId'] = empty($args['userId']) ? 0 : $args['userId'];
        $token['expiredTime'] = empty($args['duration']) ? 0 : time() + $args['duration'];
        $token['createdTime'] = time();
        return $this->getTokenDao()->addToken($token);
    }
    
    private function _makeTokenValue($length){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $value = '';
        for ( $i = 0; $i < $length; $i++ ) {
            $value .= $chars[ mt_rand(0, strlen($chars) - 1) ];
        }
        return $value;
    }
    
    /**
     * 生成一个假的Token
     */
    public function makeFakeTokenString($length = 32){
        return $this->_makeTokenValue($length);
    }

    /**
     * 校验Token
     *
     * @param   string  $type Token类型
     * @param   string  $key  Token的值
     *
     * @return boolean 该Token值是否OK
     */
    public function verifyToken($type, $value){
        $token = $this->getTokenDao()->getTokenByToken($value);
        if (empty($token)) {
            return false;
        }
        if ($token['type'] != $type) {
            return false;
        }
        if (($token['expiredTime'] > 0) && ($token['expiredTime'] < time()) ) {
            return false;
        }
        if ($token['remainedTimes'] > 1) {
            $this->getTokenDao()->waveRemainedTimes($token['id'], -1);
        }
        $this->_gcToken($token);
        return $token;
    }

    private function _gcToken($token){
        if (($token['times'] > 0) && ($token['remainedTimes'] <= 1)) {
            $this->getTokenDao()->deleteToken($token['id']);
            return ;
        }
        if (($token['expiredTime'] > 0) && ($token['expiredTime'] < time()) ) {
            $this->getTokenDao()->deleteToken($token['id']);
            return ;
        }
        return ;
    }
    
    /**
     * 作废一个Token
     * 
     * @param  [type] $value 要摧毁的Token的值
     */
    public function destoryToken($value){
        $token = $this->getTokenDao()->getTokenByToken($token);
        if (empty($token)) {
            return ;
        }
        $this->getTokenDao()->deleteToken($token['id']);
    }
    
}
?>
