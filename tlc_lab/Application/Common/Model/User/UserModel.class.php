<?php
namespace Common\Model\User;
use Common\Lib\ArrayToolkit;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\Paginator;
class UserModel extends BaseModel {

    protected $tableName = 'user';

    protected $_link = [

            //关联角色表
            'role' => array(
                    'mapping_type'         => self::MANY_TO_MANY,
                    'class_name'           => 'role',
                    'mapping_name'         => 'role',
                    'foreign_key'          => 'user_id',
                    'relation_foreign_key' => 'role_id',
                    'relation_table'       => 'role_user' //此处应显式定义中间表名称，且不能使用C函数读取表前缀
            ),

            //关联用户设备表
            'device' => array(
                    'mapping_type'  => self::HAS_MANY,
                    'class_name'    => 'user_device',
                    'mapping_name'  => 'device',
                    'foreign_key'   => 'uid',
                    'mapping_limit' => 2
            )


    ];

    public function getUser($id, $lock = false){
        $where['id'] = $id;
        return $this->where($where)->find() ? : null;
    }
    
    /*
     * @author 褚兆前
     * 根据用户webCode 获取用户相应的信息
     */
    public function getUserLocal($id,$webCode ,$lock = false){
        $where['id'] = $id;
        return $this->where($where)->find() ? : null;
    }
    
    /**
     * 根据学号获取用户中心库
     * @edit tanhaitao 2015-09-07
     */
    public function getUsers($ids, $lock = false){
        $str = implode(',',$ids);
        $where['id'] = array('in',$str);
        return $this->where($where)->select() ? : null;
    }

    public function getTestAccountId($webcode){
        $where['testAccount'] = 1;
        $where['webCode'] = $webcode ? : C('WEBSITE_CODE');
        $uid = $this->where($where)->getField("id") ? : 0;
        return $uid;
    }

    public function getUserByField($field){
          return $this->field($field)->select();
    }

    public function getUserByFieldExcel($field){
          return $this->where("'$field'!= ''")->field($field)->select();
    }

    //根据学号或教师工号获取用户
    public function findUserByUserNum($unumer){
        $where['userNum'] = $unumer;
        return $this->where($where)->find() ? : null;
    }

    public function findUserByEmail($email){
        $where['email'] = $email;
        return $this->where($where)->find() ? : null;
    }
    
    /**
     * 根据邮箱获取用户 中心库
     * @edit tanhaitao 2015-09-07
     */
    public function findUserByEmails($emails){
        $str = implode(',',$emails);
        $where['email'] = array('in',$str);
        return $this->where($where)->select() ? : null;
    }
    
    /**
     * 获取用户的webCode
     * @author guojunqiang
     */
    public function getFirstWebCode($parm){
         return $this->where($parm)->order('createdTime asc')->field('webCode')->find() ? : null;
    }
    
    /**
     * 依据苹果设备id查找用户
     * @author 钱志伟 2015-11-30
     */
    public function findUserByAppleDeviceId($appleDeviceId=''){
        $where = array('appleBuyDeviceId'=>$appleDeviceId);
        return $this->where($where)->find() ? : array();
    }

    public function findUserByNickname($nickname){
        $where['nickname'] = $nickname;
        return $this->where($where)->find() ? : null;
    }
    
    public function getUserByName($nickname,$webCode){
        $where['nickname'] = $nickname;
        $where['webCode'] = $webCode ? : C('WEBSITE_CODE');
        return $this->where($where)->find() ? : null;
    }
    
    public function findTeacherByNickname($nickname){
        $where['nickname'] = $nickname;
        $where['roles']   = array("like", "%ROLE_TEACHER%");
        return $this->where($where)->find() ? : null;
    }
    
    public function getTeacherByNickname($nickname){
        $where['nickname'] = array("like", "%".$nickname."%");
        $where['roles']   = array("like", "%ROLE_TEACHER%");
        return $this->field('id')->where($where)->select() ? : null;
    }
    
    public function getIdsByLikeName($nickname){
        if($nickname == "") return array();
        $where['nickname'] = array("like", "%".$nickname."%");
        $userArr = $this->field('id')->where($where)->select();
        $idArr = array();
        if(!empty($userArr)){
            foreach ($userArr as $key => $uid) {
                $idArr[$key] = $uid["id"];
            }
        }
        return array_values($idArr);
    }

     public function findUserByStudNum($studNum){
        $where['userNum'] = $studNum;
        return $this->where($where)->find() ? : array();
    }

    public function findUserByVerifiedMobile($verifiedMobile){
        $where['verifiedMobile'] = $verifiedMobile;
        return $this->where($where)->find() ? : array();
    }
    /**
     * 根据手机号获取用户 中心库
     * @edit tanhaitao 2015-09-07
     */
    public function findUserByVerifiedMobiles($verifiedMobiles){
        $str = implode(',',$verifiedMobiles);
        $where['verifiedMobile'] = array('in',$str);
        return $this->where($where)->select() ? : null;
    }

    public function findUsersByNicknames(array $nicknames){
        if(empty($nicknames)) { return array(); }
        $str = implode(',',$nicknames);
        $where['nickname'] = array('in',$str);
        return $this->where($where)->select();
    }

    public function findUsersByIds(array $ids){
        if(empty($ids)){ return array(); }
        $ids = array_values(array_unique($ids));
        $str = implode(',',$ids);
        $where['id'] = array('in',$str);
        return $this->where($where)->select();
    }
    
    /**
     * 多个老师id获取老师姓名
     * @author 谈海涛 2015-11-6
     */
    public function findUsersByTeacherids($teacherids){
         return  $this->where(array('id' => array('in', $teacherids)))->field('id, nickname teacherName')->select() ? : array();
    }
    

    public function searchUsers($conditions, $start, $limit,$orderBy = null){
        if(empty($orderBy)){
            $orderBy = array('userNum','ASC');
        }
        $builder = $this->createUserQueryBuilder($conditions);

        $builder = $builder->select('*')
            ->orderBy($orderBy[0], $orderBy[1])
            ->setFirstResult($start)
            ->setMaxResults($limit);
        return $builder->execute()->fetchAll() ? : array();
    }

    public function searchUserCount($conditions){
        $builder = $this->createUserQueryBuilder($conditions);
        $builder = $builder->select('COUNT(id)');
        return $builder->execute()->fetchColumn(0);
    }

    public function searchAllUserId($conditions){
        $builder = $this->createUserQueryBuilder($conditions);
        $builder = $builder->select('id');
        return $builder->execute()->fetchAll() ? : array();
    }

    public function getGoldAdminId(){
       return  $this->field("id")->where(" roles like '%ROLE_GOLD_ADMIN%' ")->select() ? : array();
    }
    
    /**
     * 获取所有角色为大客户的用户
     * @authro ZhaozuowuWu 2015-05=07
     */
    public function getUserMarket(){
       return  $this->field("id,nickname")->where(" roles like '%ROLE_MARKET%' ")->select() ? : array();
    }

    private function createUserQueryBuilder($conditions){
        $conditions = array_filter(
            $conditions,
            function($key) use ($conditions){
                $v = $conditions[$key];
                if($key == "uids" or $v === 0){
                    return true;
                }

                if(empty($v)){
                    return false;
                }
                return true;
            },
            ARRAY_FILTER_USE_KEY
        );

//	    $conditions = array_filter($conditions,function($v){
//		    if($v === 0){
//			    return true;
//		    }
//
//		    if(empty($v)){
//			    return false;
//		    }
//		    return true;
//	    });

	    if (isset($conditions['roles'])) {
		    $conditions['roles'] = "%{$conditions['roles']}%";
	    }

	    if (isset($conditions['role'])) {
		    $conditions['role'] = "|{$conditions['role']}|";
	    }

	    if(isset($conditions['keywordType']) && isset($conditions['keyword'])) {
		    $conditions[$conditions['keywordType']]=$conditions['keyword'];
		    unset($conditions['keywordType']);
		    unset($conditions['keyword']);
	    }

        if(isset($conditions['keys'])){
            $keys = $conditions['keys'];
            unset($conditions['keys']);
        }

	    if (isset($conditions['nickname'])) {
		    $conditions['nickname'] = "%{$conditions['nickname']}%";
	    }
//            $this->setCommCenterWebUseWebCodeRule();
        $builder = $this->createDynamicQueryBuilder($conditions);

	    $userDynamicQueryBuilder = $builder
		    ->from($this->tableName, 'user')
            ->andWhere('id = :uid')
		    ->andWhere('promoted = :promoted')
		    ->andWhere('roles LIKE :roles')
		    ->andWhere('roles = :role')
		    ->andWhere('nickname LIKE :nickname')
		    ->andWhere('loginIp = :loginIp')
		    ->andWhere('createdIp = :createdIp')
		    ->andWhere('approvalStatus = :approvalStatus')
		    ->andWhere('email = :email')
		    ->andWhere('level = :level')
		    ->andWhere('createdTime >= :startTime')
		    ->andWhere('createdTime <= :endTime')
		    ->andWhere('lock= :lock')
		    ->andWhere('level >= :greatLevel')
            ->andWhere('userNum = :userNum')
		    ->andWhere('verifiedMobile = :verifiedMobile');

        if(isset($keys)){
            $keys_sql = array();
            foreach ($keys as $key) {
                $sql = "userNum LIKE '%{$key}%' or nickname LIKE '%{$key}%'";
                array_push($keys_sql,$sql);
            }
            $userDynamicQueryBuilder->andStaticWhere(implode(" or ",$keys_sql));
        }

        $profileSearch = array();

        if(isset($conditions['college']) && !empty($conditions['college'])){
            $profileSearch['college'] = $conditions['college'];
        }

        if(isset($conditions['major']) && !empty($conditions['major'])){
            $profileSearch['major'] = $conditions['major'];
        }

        if(!empty($profileSearch)){
            $ulist = $this->getProfileDao()->selectProfile($profileSearch);
            $uidlist = ArrayToolkit::column($ulist,'id');
            $conditions['uids'] = isset($conditions['uids']) ? array_merge($conditions['uids'],$uidlist) : $uidlist;
        }

        if(isset($conditions['bindMobile'])){
            if($conditions['bindMobile'] == "1")
                $userDynamicQueryBuilder->andStaticWhere("verifiedMobile != ''");
             if($conditions['bindMobile'] == "2")
                $userDynamicQueryBuilder->andStaticWhere("verifiedMobile = ''");
        }

        if(isset($conditions['uids'])){
            $ids = join(",",$conditions['uids']);
            if(count($conditions['uids']) == 0){
                $ids = 0;
            }
            $userDynamicQueryBuilder->andStaticWhere(" id in ({$ids})");
        }

        if(isset($conditions['no_uids'])){
            $ids = join(",",$conditions['no_uids']);
            $userDynamicQueryBuilder->andStaticWhere(" id not in ({$ids})");
        }

        return  $userDynamicQueryBuilder;
    }

    /**
     * 注册添加用户 先中心库 后私有库
     * @edit fubaosheng 2015-08-11
     */
    public function addUser($user){
        $user['webCode'] = (isset($user['webCode']) && !empty($user['webCode'])) ? $user['webCode'] : C('WEBSITE_CODE');
        $id = $this->add($user);
        if(!$id) E("Insert user error.");
//        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
        return  $this->getUser($id);
    }

    public function addExcelUser($user){
       $user['webCode'] = (isset($user['webCode']) && !empty($user['webCode'])) ? $user['webCode'] : C('WEBSITE_CODE');
        $id = $this->add($user);
        if(!$id) return false;
//        else synchroData(array('db'=>'private','sql'=>$this->getLastSql(),'pk'=> $this->getAutoIncField(),'id'=>$id));
        return  $this->getUser($id);
    }

    public function updateUser($id, $fields){
        $where['id'] = $id;
        $r = $this->where($where)->save($fields);
//        if($r) synchroData(array('db'=>'private','sql'=>$this->getLastSql()));
        return $this->getUser($id);
    }

    public function waveCounterById($id, $name, $number){
        $names = array('newMessageNum', 'newNotificationNum');
        if (!in_array($name, $names)) {
            E('counter name error');
        }
//        $this->db(C('DB_CENTER')['DB_NUM'],C('DB_CENTER'));
        $this->where("id = {$id}")->save(array($name=>$name+$number));
    }

    public function clearCounterById($id, $name){
        $names = array('newMessageNum', 'newNotificationNum');
        if (!in_array($name, $names)) {
            E('counter name error');
        }
//        $this->db(C('DB_CENTER')['DB_NUM'],C('DB_CENTER'));
        $this->where("id = {$id}")->save(array($name=>0));
    }

    public function analysisRegisterDataByTime($startTime,$endTime, $conditions=array()){
        $model = $this;
        #钱志伟 2015-07-10

        return $model->field(" count(id) as count,from_unixtime(createdTime,'%Y-%m-%d') as date")->where("1 and `createdTime`>={$startTime} and `createdTime`<={$endTime}")->group("from_unixtime(`createdTime`,'%Y-%m-%d')")->order("date ASC")->select();
    }

    public function analysisUserSumByTime($endTime){
        if(empty($endTime)){
            return array();
        }
        $sql = $this->table("{$this->tableName} as o")->field("from_unixtime(o.createdTime,'%Y-%m-%d') as date")->where("createdTime<={$endTime}")->select(false);
        $info =  $this->table($sql." as d")->field("date,count(*) as count")->group("date")->order("date desc")->select();
        return $info;
    }

    //根据角色查找用户
    public function selectUserByRole($role){
        $res = $this->where("roles like '%".$role."%'")->select() ?: array();
        return $res;
    }

    public function findUsersCountByLessThanCreatedTime($endTime){
        $builder = $this;
        return  $builder->where("createdTime <= {$endTime}")->count("id");
    }

     public function getEmailByLikeName($name){
        $where['email'] = array('like',$name);
        $info = $this->where("email like '%".$name."%' and email !=''")->field("email as name ,id,nickname")->select();
        return $info;
    }

     public function getMobileByLikeName($name){
        $info = $this
                ->where("verifiedMobile like '%".$name."%' and  verifiedMobile !=''")->field("verifiedMobile as name ,id,nickname")->select();
        return $info;
    }

    public function getNickByLikeName($name){
        $info = $this->where("nickname like '%".$name."%' and  nickname !=''")->field("nickname as name ,id,nickname")->select();
        return $info;
    }
    
    public function getUserInfoByRoles($where){
        $info =  $this
                 ->table($this->tableName." as a")
                 ->join("user_profile as b on a.id=b.id")
                ->where("a.roles like '%".$where."%'")
                ->field("a.id,a.email,a.verifiedMobile,a.nickname,a.roles,a.teacherCategoryId,b.truename")
                ->select();
        return $info;
    }

    /**
     * 根据自定义习惯获取用户信息
     * @authro ZhaozuowuWu 2015-05=07
     */
    public function  getUserInfoByCustom($param){
        $options = array(
            'field'=>"",//查询保留的字段
            'where' =>"", //查询的条件
            'limit' => 1 // 查询多条还是一条 1=>代表一条，否则代表多条
        );
        $options = array_merge($options,$param);
        extract($options);
        if(empty($field)) $field = "*";
        #by qzw 2015-06-15
        if($limit ==1){
            $userinfo =$this
                ->field($field)
                ->table("user as a")
               ->join("user_profile as b on a.id=b.id", 'LEFT')
               ->where($where)->find();
            $userinfo = $this->decorateUserInfo($userinfo);
        }else{
              $userinfo =$this
                ->field($field)
                ->table("user as a")
               ->join("user_profile as b on a.id=b.id", 'LEFT')
               ->where($where)->select();
              $userinfo = array_map(function($user){
                  $user = $this->decorateUserInfo($user);
                  return $user;
              }, $userinfo);
        }

        // lvwulong 2016-3-28
        if($userinfo && isset($userinfo['gold']))
            unset($userinfo['gold']);
        
        return $userinfo;

    }
    
    /**
     * 装饰用户信息
     * @author 钱志伟 2015-06-16
     */
    private function decorateUserInfo($userinfo){
        if($userinfo){
            if(isset($userinfo['gender'])) $userinfo['gender'] = (string)$userinfo['gender'];
            if(isset($userinfo['sex'])) $userinfo['sex'] = (string)$userinfo['sex'];
            if(isset($userinfo['signature'])) $userinfo['signature'] = (string)$userinfo['signature'];
            if(isset($userinfo['qq'])) $userinfo['qq'] = (string)$userinfo['qq'];
        }
        return $userinfo;
    }

    /**
     * 根据用户id或邮箱或者手机来获取用户id
     * @param array $account
     * @return array
     * @author LiangFuJian
     * @date 2015-08-20
     */
    public function getUserIdsByAccount($account){

        $map['id'] = array('in',$account);
        $map['email'] = array('in',$account);
        $map['verifiedMobile'] = array('in',$account);
        $map['_logic'] = 'or';
        $data = $this
                ->field('id,email,verifiedMobile')
                ->where($map)
                ->select();

        $arrIds = array();
        foreach($data as $v) {
            $arrIds[] = $v['id'];
            foreach($account as $k => $vo) {
                if ($vo == $v['id'] || $vo == $v['email'] || $vo == $v['verifiedMobile'])
                    unset($account[$k]);
            }
        }

        $return['ids'] = $arrIds;
        $return['noAccount'] = $account;
        return $return;
    }
    
    /**
     * 根据账号获取用户信息
     * @param string $account
     * @return array
     * @author LiangFuJian <liangfujian@redcloud.com>
     * @date 2015-10-25
     */
    public function getUserByAccount($account){
        
        $map['id'] = intval($account);
        $map['email'] = $account;
        $map['verifiedMobile'] = $account;
        $map['_logic'] = 'or';
        return $this
                ->field('id,email,verifiedMobile')
                ->where($map)
                ->find();
    }
    /**
     * 根据用户名获取用户的id
     * @param type $condition
     * @author 朱旭
     */
    public function getUserGId($nickname){
        $where['nickname'] = array('like', "%{$nickname}%");
       return   $this->where($where)->field('id')->select();
    }

    public function getUsersByCondition($condition){
        return $this->where($condition)->select();
    }

    private function getProfileDao(){
        return $this->createDao("User.UserProfile");
    }

}