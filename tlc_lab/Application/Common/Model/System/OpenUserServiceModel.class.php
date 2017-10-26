<?php

namespace Common\Model\System;    
use \Common\Model\Common\BaseModel;
        /**
         * 高校云互联
         * @author @褚兆前 2015-04-06
         */
    class OpenUserServiceModel extends BaseModel {
        
        protected $tableName = 'open_user';
        
        public function getUserOpenUserList($userId,$stateInList = ''){
            $where = array();
            if($userId){
                $where['userId'] = $userId;
            }
            if(!empty($stateInList)){
                $where['state'] = array('IN', $stateInList);
            }
            $resouce = $this->where($where)->select();
            return !empty($resouce) ? $resouce : '';
        }
        
        public function getUserOpenUserCount($userId,$stateInList = '',$like='',$webCode=""){
                $where = array();
                if($userId){
                    $where['userId'] = $userId;
                }
                
                if(!empty($webCode)){
                $where['webCode'] = $webCode;
                }
                
                if(!empty($like)){
                    foreach($like as $key => $value){
                       $where[$key] =array('LIKE','%'.$value.'%');
                    }
                }
                
                if(!empty($stateInList)){
                    $where['state'] = array('IN', $stateInList);
                }
                $count = $this->where($where)->count();
                return $count;
        }
        
        public function getUserOpenUserPage($userId,$stateInList = '',$start,$limit,$like='',$webCode=""){
            $start = (int) $start;
            $limit = (int) $limit;
            $where = array();

            if($userId){
                $where['userId'] = $userId;
            }
            if(!empty($webCode)){
                $where['webCode'] = $webCode;
            }
            if(!empty($like)){
                foreach($like as $key => $value){
                   $where[$key] =array('LIKE','%'.$value.'%');
                }
            }
            
            if(!empty($stateInList)){
                $where['state'] = array('IN', $stateInList);
            }
            
            $resource = $this->where($where)
                             ->order(array('mTime'=>'desc','id'=>'desc'))
                             ->limit($start, $limit)
                             ->select();
            
            if(!$resource){ return '';}
            $resource = $this->decorateOpenUser($resource);
            
            return $resource;
        }
        
        public function getOpenUserById($userId,$id){
            $where = array();
            if($userId){
                $where['userId'] = $userId;
            }
            $where['id'] = $id;
            
            $resource = $this->where($where)
                             ->find();
            
            if(!$resource){ return '';}
            $resource = $this->decorateOpenUser($resource,1);
            return $resource;
        }
        
        public function getOpenUserByAppKey($appKey,$DB=''){
            $where['appKey'] = $appKey;
            $webCode = $DB ? '' : C('WEBSITE_CODE');
            $resource = $this->where($where)
                             ->find();
            if(!empty($resource)){    
                $resource['url'] = htmlspecialchars_decode($resource['url']);
                $resource['backUrl'] = htmlspecialchars_decode($resource['backUrl']);
            }
            return $resource;
        }
        
        public function decorateOpenUser($data,$depth=''){
            
            if($depth){
                $state = $data['state'];
                if( $state == 'success') $data['state'] = 'success';
                elseif( $state == 'stop') $data['state'] = 'stop';
                elseif( $state == 'delete') $data['state'] = 'delete';
                else $data['state'] = 'danger';
                $data['url'] = htmlspecialchars_decode($data['url']);
                $data['backUrl'] = htmlspecialchars_decode($data['backUrl']);
                return $data;
            }
            
            foreach($data as $key=>$value){
                $state = $value['state'];
                if( $state == 'success') $data[$key]['state'] = 'success';
                elseif( $state == 'stop') $data[$key]['state'] = 'stop';
                elseif( $state == 'delete') $data[$key]['state'] = 'delete';
                else $data[$key]['state'] = 'danger';
                
                $data[$key]['cUser'] = $this->getUserService()->getUser($value['userId']);
                $data[$key]['opUser'] = $this->getUserService()->getUser($value['opUserId']);
                $data[$key]['url'] = htmlspecialchars_decode($value['url']);
                $data[$key]['backUrl'] = htmlspecialchars_decode($value['backUrl']);
            }
            
            return $data;
        }
        
        public function getOpenUserState($state){
            
            if($state == 'all') return '';
            if($state == 'danger') return 'new,modify,danger';
            if(in_array($state,array('success','stop','delete','new','modify','danger'))) return $state;
            
            return 'new,modify,stop,success,danger';
        }
        
        public function setMetaIdSession($userId){
            if(session('metaId')) return session('metaId');
            session('metaId',$userId.mktime(),C('CLOUD_OAUTH_SESSION_EXPIRE'));
            return session('metaId');
        }
        
        public function addOpenUser($post,$userId,$metaId){
            $data = array();
            $data['url'] = trim($post['url']);
            $data['name'] = trim($post['name']);
            $data['des'] = $post['des'];
            $data['provider'] = $post['provider'];
            $data['backUrl'] = trim($post['backUrl']);
            $data['userId'] = $userId;
            $data['metaId'] = $metaId;
            $data['appId'] = mktime().$userId;
            $data['appKey'] = md5($userId.'-'.mktime().'-'.str_pad(rand(1,9999),4,'0'));
            $data['state'] = 'new';
            $data['cTime'] = mktime();
            
            $add = $this->add($data);
            
            return $add;
        }
        
        public function editOpenUser($post,$userId,$type='',$opType='',$opUserId){
            $where = array();
            $data = array();
            
            $where['id'] = $post['id'];
            if($userId){
                $where['userId'] = $userId;
            }
            
            $data['url'] = trim($post['url']);
            $data['name'] = trim($post['name']);
            $data['des'] = $post['des'];
            $data['provider'] = $post['provider'];
            $data['backUrl'] = trim($post['backUrl']);
            
            $data['state'] = empty($type) ? 'modify' : $type;
            if( $type == 'default') unset($data['state']);
                
            if($opUserId){
                $data['opUserId'] = $opUserId;
                $data['opTime'] = mktime();
            }
            
            $data['mTime'] = mktime();
            $save = $this->where($where)->save($data);
            return $save;
        }
        
        public function editOpenUserState($post,$userId,$type='',$opUserId=''){
            $where = array();
            $data = array();
            
            $where['id'] = $post['id'];
            if($userId){
                $where['userId'] = $userId;
            }
            
            $data['state'] = empty($type) ? 'modify' : $type;
            if($opUserId){
                $data['opTime'] = mktime();
                $data['opUserId'] = $opUserId;
            }
            $data['mTime'] = mktime();
            $save = $this->where($where)->save($data);
            return $save;
        }
        
        public function openUserIsOk(){
            return C('CLOUD_OAUTH_STATUS');
        }
        
        protected function getUserService() {
            return createService('User.UserServiceModel');
        }
}