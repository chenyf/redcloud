<?php
namespace Common\Model\User;
use Common\Lib\ApiService;
use Common\Traits\ServiceTrait;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
use Common\Lib\SimpleValidator;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use Symfony\Component\HttpFoundation\File\File;
use Common\Component\OAuthClient\OAuthClientFactory;
use  Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Common\Model\User\UserSerializeModel as serialize;
use Common\Lib\Paginator;
use Common\Lib\WebCode;
use Common\Common\Url;
use Common\Model\User\LoginResultModel;
use Common\Model\User\IFaceResultModel;

class UserServiceModel extends BaseModel{

	use ServiceTrait;

	protected $page = 15;

    private static $curl;

    private function getUserApprovalDao(){
        return $this->createDao("User.UserApproval");
    }
    private function getFriendDao(){
        return $this->createDao("User.Friend");
    }

    private function getUserDao(){
        return $this->createDao('User.UserModel');
    }

	private function getDao(){
		return $this->createService('User.UserModel')->switchCenterDB();
	}

	private  function getRoleUserDao(){
		return $this->createService('Role.RoleUserModel');
	}

    private function getProfileDao(){
        return $this->createDao("User.UserProfile");
    }

    private function getUserSecureQuestionDao(){
        return $this->createDao("User.UserSecureQuestion");
    }

    private function getUserBindDao(){
        return $this->createDao("User.UserBind");
    }

    private function getUserTokenDao(){
        return $this->createDao("User.Token");
    }

    private function getUserFortuneLogDao(){
        return $this->createDao("User.UserFortuneLog");
    }

    private function getTmpStudInfoDao(){
        return $this->createDao("User.TmpStudInfo");
    }

    private function getTmpStudApplyDao(){
        return $this->createDao("User.TmpStudApply");
    }

    private function getFileService(){
        return $this->createService('Content.FileService');
    }

    private function getNotificationService(){
        return $this->createService('User.NotificationService');
    }
    
    public function getUserId($nickname){
        return $this->getUserDao()->getUserGId($nickname);
    }
    private function getSettingService(){
        return $this->createService('System.SettingService');
    }

    protected function getLogService(){
        return $this->createService('System.LogService');
    }

    protected function getCategoryService(){
        return $this->createService('Taxonomy.CategoryService');
    }

    private function getPasswordEncoder(){
        return new MessageDigestPasswordEncoder('sha256');
    }

    public function getTestAccountId($webcode=''){
        return $this->getUserDao()->getTestAccountId($webcode);
    }

    public function findUserSimple($id){
        return $this->getUserDao()->getUser($id);
    }

    public function getUser($id, $lock = false){
        $user = $this->getUserDao()->getUser($id, $lock);

        if(!$user){
            return null;
        } else {
            $user = serialize::unserialize($user);
            if($user['adminCategoryIds']){
                $categorys = $this->getCategoryService()->getNameByIds($user['adminCategoryIds']);
                $str = "";
                foreach ($categorys as $category) {
                    if($category['isDelete'] == 1) $category['name'] = "【已删除】".$category['name'];
                    $str.= $category['name'].",";
                }
                $user["adminCategoryNames"] = rtrim($str,",");
            }
            return $user;
        }
    }
    
    /*
     * @author 褚兆前
     * 根据用户webCode 获取用户相应的信息
     */
    public function getUserLocal($id,$webCode, $lock = false){
        $user = $this->getUserDao()->getUserLocal($id,$webCode, $lock);

        if(!$user){
            return null;
        } else {
            $user = serialize::unserialize($user);
            if($user['adminCategoryIds']){
                $categorys = $this->getCategoryService()->getNameByIds($user['adminCategoryIds']);
                $str = "";
                foreach ($categorys as $category) {
                    if($category['isDelete'] == 1) $category['name'] = "【已删除】".$category['name'];
                    $str.= $category['name'].",";
                }
                $user["adminCategoryNames"] = rtrim($str,",");
            }
            return $user;
        }
    }

	private function SearchCondition($condition) {

		$searchCondition = array();

		if ($condition['keywordType'] && $condition['keyword']) {
			$searchCondition[$condition['keywordType']] = array('LIKE', '%' . $condition['keyword'] . '%');
		}

		if ($condition['siteSelect']) {
			$searchCondition['webCode'] = $condition['siteSelect'];
		}

		if ($condition['role']) {
			$buildSql              = $this->getRoleUserDao()->field('user_id')->where(['role_id' => $condition['role']])->group('user_id')->buildSql();
			$searchCondition['id'] = array('IN', $buildSql);
		}

		return $searchCondition;
	}

    /**
     * 根据学号获取用户 中心库
     * @edit tanhaitao 2015-09-07
     */
    public function getUsers($ids, $lock = false){
        $user = $this->getUserDao()->getUsers($ids, $lock);

        if(!$user){
            return null;
        } else {
            return $user;
        }
    }

    /**
     * 根据学号获取用户 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function getUserByStudNum($nickname){
        $user = $this->getUserDao()->findUserByStudNum($nickname);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }

    /**
     * 获取用户根据账号 (stripos($user['owebPriv'], 'r')!==false)
     * @author fubaosheng 2015-08-11
     */
    public function getUserByAccount($account){
        if(!empty($account)){
            $user = $this->getUserByEmail($account);
            if(empty($user)) $user = $this->getUserByVerifiedMobile($account);
            if(empty($user)) $user = $this->getUserByStudNum($account);
            if(!empty($user)){
                if( $user['webCode'] == C('WEBSITE_CODE') ) return $user;
            }
            return array();
        }
        return array();
    }

    /**
     * 根据昵称获取用户 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function getUserByNickname($nickname){
        $user = $this->getUserDao()->findUserByNickname($nickname);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }
    /**
     * 根据昵称获取用户
     * @add 郭俊强 2015-11-25
     */
    public function getUserByName($nickname,$webCode){
        $user = $this->getUserDao()->getUserByName($nickname,$webCode);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }

	public function updateUser($userId,$data){
		return $this->getUserDao()->updateUser($userId, $data);
	}

     /**
     * 根据昵称获取用户 中心库
     * @edit tanhaitao 2015-09-11
     */
    public function getTeacherByNickname($nickname){
        return $this->getUserDao()->findTeacherByNickname($nickname);
    }

     /**
     * 根据昵称获取教师用户id 中心库
     * @edit tanhaitao 2015-09-11
     */
    public function getAllTeacherByName($nickname){
        return $this->getUserDao()->getTeacherByNickname($nickname);
    }

    /**
     * 根据手机号获取用户当前站 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function getUserByVerifiedMobile($mobile){
        $user = $this->getUserDao()->findUserByVerifiedMobile($mobile);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }

    /**
     * 根据手机号获取用户 中心库
     * @edit tanhaitao 2015-09-07
     */
    public function getUserByVerifiedMobiles($mobiles){
        return $this->getUserDao()->findUserByVerifiedMobiles($mobiles);

    }

    /**
     * 根据Email获取用户 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function getUserByEmail($email){
        
        if (empty($email)) {
            return null;
        }
        $user = $this->getUserDao()->findUserByEmail($email);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }

    /**
     * 获取管理员列表
     */
    public function getAdminList(){
        $this->getUserDao()->selectUserByRole('ROLE_SUPER_ADMIN');
    }

    /**
     * 根据学生学号或教师工号获取用户
     */
    public function getUserByUserNum($numer){
        if (empty($numer)) {
            return null;
        }
        $user = $this->getUserDao()->findUserByUserNum($numer);
        if(!$user){
            return null;
        } else {
            return serialize::unserialize($user);
        }
    }


    /**
     * 本地数据库验证登录
     */
    public function localLogin($userid,$password){
        //判断账号是否存在
        $user = $this->getUserByUserNum($userid);
        if(empty($user)) {
            $user = $this->getUserByEmail($userid);
            if (empty($user)) {
                $user = $this->getUserByVerifiedMobile($userid);
            }
        }

        if( !empty($user) && $user['salt'] ==""){
            $repassword = md5($password);
        }else{
            if( empty($user) ){
                $result = new LoginResultModel();
                $result->code = -1;
                $result->role = 1;
                return $result;
            }
            $repassword = $this->getPasswordEncoder()->encodePassword($password, $user["salt"]);
        }

        $repassword = strtolower($repassword);

        $result = new LoginResultModel();
        if($repassword === $user["password"]){
            $result->code = 20;
            $roles = explode('|',$user['roles']);
            $result->role = in_array("ROLE_SUPER_ADMIN",$roles) ? 3 : (in_array("ROLE_TEACHER",$roles) ? 2 : 1);
        }

        return $result;
    }

    /**
     * 调用AUTH接口进行登录
     */
    public function authLogin($userid,$password){
//        $postArr = array(
//            'appId'    =>   AUTH_APPID,
//            'skey'     =>   AUTH_SECRET_KEY,
//            'userid'   =>   $userid,
//            'password' =>   $password
//        );
//
//        if(empty($curl)){
//            $curl = curl_init();
//            curl_setopt($curl, CURLOPT_URL, AUTH_URL);
//            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
//            curl_setopt($curl, CURLOPT_RETURNTRANSFER,true);
//        }
//
//        $data_string = json_encode($postArr);
//        curl_setopt($curl, CURLOPT_POSTFIELDS,$data_string);
//        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//                'Content-Type: application/json',
//                'Content-Length: ' . strlen($data_string))
//        );
//
//        $result = curl_exec($curl);
        return ApiService::postData(C('api_url.auth'),array(
            'userid'   =>   $userid,
            'password' =>   $password
        ));
        
//
//        return json_decode($result);

//        $usermap = array(
//            '201010013174' => '070034',
//            '201010013442' => '211716',
//            '201210113055' => '043426',
//        );
//
//        if(strlen($userid) == 4 && strrev($userid) == $password){
//            $result = array(
//                'code'	=>	20,
//                'role'	=>	1,
//            );
//        }else if($usermap[$userid] == $password){
//            $result = array(
//                'code'	=>	20,
//                'role'	=>	2,
//            );
//        }else{
//            $result = array(
//                'code'	=>	12,
//                'role'	=>	0,
//            );
//        }
//
//        return $result;

    }

    //修改用户密码-通过接口
    public function changeUserPasswordIface($userNum,$oldPwd,$newPwd){
        return ApiService::postData(C('api_url.change_user_pwd'),array(
            'userid'    =>   $userNum,
            'oldpwd'    =>   $oldPwd,
            'newpwd'    =>   $newPwd
        ));
    }

    //修改用户密码-通过本地数据库
    public function changeUserPasswordLocal($oldPwd,$newPwd){
        $user = $this->getCurrentUser();
        if(empty($user) || !$user->isLogin()){
            return new IFaceResultModel(13,'请登录后再操作');
        }

        $checkpwd = empty($user['salt']) ? strtolower(md5($oldPwd)) : strtolower($this->getPasswordEncoder()->encodePassword($oldPwd, $user["salt"]));
        if($checkpwd !== $user['password']){
            return new IFaceResultModel(11,'原密码输入错误');
        }

        $repwd = empty($user['salt']) ? strtolower(md5($newPwd)) : strtolower($this->getPasswordEncoder()->encodePassword($newPwd, $user["salt"]));
        $newuser = $this->getUserDao()->updateUser($user['id'],array('password' => $repwd));

        if($newuser['password'] === $repwd){
            return new IFaceResultModel(20,'修改密码成功');
        }else{
            return new IFaceResultModel(13,'修改密码失败');
        }
    }

    //获取用户信息
    public function findUserInfo($userid){
        if(C('if_iface')){
            return $this->findUserInfoIface($userid);
        }else{
            return $this->findUserInfoLocal($userid);
        }
    }

    //获取用户信息-通过接口
    public function findUserInfoIface($userNum){
        return ApiService::postData(C('api_url.get_user_info'),array(
            'userid'    =>   $userNum
        ));
    }

    //获取用户信息-通过本地数据库
    public function findUserInfoLocal($userid){
        //判断用户是否存在
        $user = $this->getUserByUserNum($userid);
        if(empty($user)) {
            $user = $this->getUserByEmail($userid);
            if (empty($user)) {
                $user = $this->getUserByVerifiedMobile($userid);
            }
        }

        if(empty($user)){
            $result = array('code'=>10,'msg'=>'不存在该用户');
            return array2object($result);
        }

        $userProfile = $this->getUserProfileSimple($user['id']);
        $userProfile['name'] = $userProfile['truename'];
        $userProfile['sex'] = $userProfile['gender'] == 'male' ? '男' : '女';
        $userProfile['cellphone'] = $user['verifiedMobile'];
        $userProfile['email'] = $user['email'];

        unset($userProfile['id']);
        unset($userProfile['truename']);
        unset($userProfile['gender']);
        unset($userProfile['role']);

        $result = array('code'=>20,'msg'=>'');
        $result['info'] = $userProfile;
        return array2object($result);
    }

    /**
     * 根据Email获取用户 中心库
     */
    public function getUserByEmails($emails){
        return $this->getUserDao()->findUserByEmails($emails);
    }

    /**
     * 多个老师id获取老师姓名
     * @author 谈海涛 2015-11-6
     */
    public function findUsersByTeacherids($teacherids){
        $teacherArr = $this->getUserDao()->findUsersByTeacherids($teacherids);
        $urlModel = new Url();
        foreach($teacherArr as $key => $teacher){
            $teacherImage = C("site_url").$urlModel->getUserFaceUrl(array('type'=>'middle','uid'=>$teacher['id']));
            $teacherArr[$key]["teacherImage"] = $teacherImage;
        }
        return $teacherArr ? : array();
    }

    public function findUsersByIds(array $ids){
         $users = serialize::unserializes(
            $this->getUserDao()->findUsersByIds($ids)
        );
        return ArrayToolkit::index($users, 'id');
    }

    public function findUserProfilesByIds(array $ids){
        $userProfiles = $this->getProfileDao()->findProfilesByIds($ids);
        return  ArrayToolkit::index($userProfiles, 'id');
    }

    public function searchUsers(array $conditions, $start, $limit,array $orderBy){
        $users = $this->getUserDao()->searchUsers($conditions, $start, $limit,$orderBy);
        
        return serialize::unserializes($users);
    }

    public function searchUserCount(array $conditions){
        return $this->getUserDao()->searchUserCount($conditions);
    }

    private function getCourseService ()
    {
        return createService('Course.CourseService');
    }


    //获取教师所教课程数
    public function getTeachCourseNum(&$teacher){
        if(in_array('ROLE_TEACHER',$teacher['roles'])){
            $teacher['courseNum'] = $this->getCourseService()->findUserTeachCourseCount($teacher['id']);
        }
        return $teacher;
    }

    //获取所有的学院
    public function getCollegeList($role='teacher'){
        $collegeList =  $this->getProfileDao()->getCollegeList($role);
        $collegeList =  ArrayToolkit::column($collegeList,'college');
        return array_filter($collegeList);
    }

    public function searchAllUserId(array $conditions){
        $arr = $this->getUserDao()->searchAllUserId($conditions);
        $id = array();
        if(!empty($arr)){
            foreach($arr as $values){
                $id[]= $values['id'];
            }
        }
        return $id;
    }

    /**
     * 所有站的神管id
     * @author fubaosheng 2015-07-24
     */
    public function getGoldAdminId(){
        $arr = $this->getUserDao()->getGoldAdminId();
        $id = array();
        if(!empty($arr)){
            foreach($arr as $values){
                $id[]= $values['id'];
            }
        }
        return $id;
    }

    public function setEmailVerified($userId){
        $this->getUserDao()->updateUser($userId, array('emailVerified' => 1));
    }
  /**
     * 修改用户webCode及owebPriv
     * @edit tanhaitao 2015-09-07
     */
    public function updateWebCodePriv($userId,$fields){
        $this->getUserDao()->updateUser($userId, $fields);
        $this->getProfileDao()->updateProfile($userId, $fields);
    }

    public function changeNickname($userId, $nickname){
        $user = $this->getUser($userId);
        if (empty($user)) {
            E('用户不存在，设置帐号失败！');
        }
        if (!SimpleValidator::nickname($nickname)) {
           E('用户名只限汉字、字母、数字、下划线 2~20位!');
        }
        #edit fubaosheng 2015-08-13
//        $existUser = $this->getUserDao()->findUserByNickname($nickname);
//        if ($existUser && $existUser['id'] != $userId) {
//            E('昵称已存在！');
//        }
        $this->getLogService()->info('user', 'nickname_change', "用户id:{$userId}，修改用户名{$user['nickname']}为{$nickname}成功");
        $this->getUserDao()->updateUser($userId, array('nickname' => $nickname));
    }
    public function changeEmailVerified($userId){
        $user = $this->getUserDao()->getUser($userId);
        if (empty($user)) {
            E('该用户不存在');
        }
        $res = $this->getUserDao()->updateUser($userId, array('emailVerified' => 1));
        return $res['id'];
    }

    public function changeEmail($userId, $email){
        if (!SimpleValidator::email($email)) {
            E('Email格式不正确，变更Email失败。');
        }
        $user = $this->getUserDao()->findUserByEmail($email);
        if ($user && $user['id'] != $userId) {
            E('Email({$email})已经存在，Email变更失败。');
        }
        $this->getUserDao()->updateUser($userId, array('email' => $email));
    }

    public function changeStudNum($userId, $studNum){
        $user = $this->getUserDao()->getUser($userId);
        if (empty($user)) {
            E('该用户不存在');
        }
        if(strlen($studNum)){
            $avaliable = $this->isUseStudNumStudNumAvaliable($studNum , $userId);
            if(!$avaliable)
                E("学号({$studNum})已经存在，学号变更失败。");
        }
        $this->getUserDao()->updateUser($userId, array('studNum' => $studNum));
    }

    public function changeAvatar($userId, $filePath, array $options){
        $user = $this->getUser($userId);
        $width = isset($options["width"]) ? $options["width"]:0;
        $height = isset($options["height"]) ? $options["height"]:0;
        $x = isset($options["x"]) ? $options["x"]:0;
        $y = isset($options["y"]) ? $options["y"]:0;
        $pathinfo = pathinfo($filePath);
        $thumbConfig= C("face_thumb_config");
        $largeConfig = $thumbConfig["largeAvatar"];
        $mediumConfig = $thumbConfig["mediumAvatar"];
        $smallConfig = $thumbConfig["smallAvatar"];
        $imagine = new Imagine();
        $rawImage = $imagine->open($filePath);
        $largeImage = $rawImage->copy();
        $largeImage->crop(new Point($x, $y), new Box($width,$height));
        $largeImage->resize(new Box($largeConfig["width"],$largeConfig["height"]));
        $largeFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_large.{$pathinfo['extension']}";
        $largeImage->save($largeFilePath, array('quality' => 90));
        $largeFileRecord = $this->getFileService()->uploadFile('user', new File($largeFilePath));

        $largeImage->resize(new Box($mediumConfig["width"], $mediumConfig["height"]));
        $mediumFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_medium.{$pathinfo['extension']}";
        $largeImage->save($mediumFilePath, array('quality' => 90));
        $mediumFileRecord = $this->getFileService()->uploadFile('user', new File($mediumFilePath));

        $largeImage->resize(new Box($smallConfig["width"], $smallConfig["height"]));
        $smallFilePath = "{$pathinfo['dirname']}/{$pathinfo['filename']}_small.{$pathinfo['extension']}";
        $largeImage->save($smallFilePath, array('quality' => 90));
        $smallFileRecord = $this->getFileService()->uploadFile('user', new File($smallFilePath));
        @unlink($filePath);
        $oldAvatars = array(
          'smallAvatar' => $user['smallAvatar'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $user['smallAvatar']) : null,
          'mediumAvatar' => $user['mediumAvatar'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $user['mediumAvatar']) : null,
          'largeAvatar' => $user['largeAvatar'] ? getParameter('redcloud.upload.public_directory') . '/' . str_replace('public://', '', $user['largeAvatar']) : null
      );
        array_map(function($oldAvatar){
            if (!empty($oldAvatar)) {
                @unlink($oldAvatar);
            }
        }, $oldAvatars);

        return array($smallFileRecord['uri'],$mediumFileRecord['uri'],$largeFileRecord['uri']);

//        return $this->getUserDao()->updateUser($userId, array(
//            'smallAvatar' => $smallFileRecord['uri'],
//            'mediumAvatar' => $mediumFileRecord['uri'],
//            'largeAvatar' => $largeFileRecord['uri'],
//        ));
    }

    /**
     * 手机号码是否存在当前站 中心库
     * @author fubaosheng 2015-04-28
     * @edit fubaosheng 2015-08-10
     */
    public function isMobileAvaliable($mobile){
        if (empty($mobile)) {
            return false;
        }
        $user = $this->getUserDao()->findUserByVerifiedMobile($mobile);
        return empty($user) ? true : false;
    }

    /**
     * 判断学号是否存在（同一学校） 中心库
     * @author fubaosheng 2015-09-22
     */
    public function isStudNumAvaliable($studNum){
        if(empty($studNum)){
            return false;
        }
        $currentUser = $this->getCurrentUser();
        $user = $this->getUserDao()->findUserByStudNum($studNum);
        return empty($user) ? true : ( $user['id'] == $currentUser['id'] );
    }
    
    
     /**
     * 判断学号是否可用（同一学校） 中心库
     * @author tanhaitao 2016-03-28
     */
    public function isUseStudNumStudNumAvaliable($studNum , $uid){
        if(empty($studNum) || !$uid){
            return false;
        }
        $user = $this->getUserDao()->findUserByStudNum($studNum);
        return empty($user) ? true : ( $user['id'] == $uid );
    }

    /**
     * 判断昵称是否已存在当前站 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function isNicknameAvaliable($nickname){
        if (empty($nickname)) {
            return false;
        }
        $user = $this->getUserDao()->findUserByNickname($nickname);
        return empty($user) ? true : false;
    }

    /**
     * 判断邮箱是否已存在当前站 中心库
     * @edit fubaosheng 2015-08-10
     */
    public function isEmailAvaliable($email){
        if (empty($email)) {
            return false;
        }
        $user = $this->getUserDao()->findUserByEmail($email);
        return empty($user) ? true : false;
    }

    /**
     * 是否有管理员权限
     * @edit fubaosheng 2015-05-25
     */
    public function hasAdminRoles($userId){
        $user = $this->getUser($userId);
        if (count(array_intersect($user['roles'], array('ROLE_ADMIN', 'ROLE_SUPER_ADMIN'))) > 0) {
            return true;
        }
        return false;
    }

	/**
	 * 是否有指定角色
	 */
	public function hasRoles($userId,$role = array()){
		$user = $this->getUser($userId);
		if (count(array_intersect($user['roles'], $role)) > 0) {
			return true;
		}
		return false;
	}

    public function rememberLoginSessionId($id, $sessionId){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，检测关注状态失败！');
        }
        return $this->getUserDao()->updateUser($id, array(
            'loginSessionId' => $sessionId
        ));
    }

    public function changePayPassword($userId, $newPayPassword){
        $user = $this->getUser($userId);
        if (empty($user) or empty($newPayPassword)) {
            E('参数不正确，更改支付密码失败。');
        }
        $payPasswordSalt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $fields = array(
            'payPasswordSalt' => $payPasswordSalt,
            'payPassword' => $this->getPasswordEncoder()->encodePassword($newPayPassword, $payPasswordSalt)
        );
        $this->getUserDao()->updateUser($userId, $fields);
        $this->getLogService()->info('user', 'pay-password-changed', "用户{$user['email']}(ID:{$user['id']})重置支付密码成功");
        return true;
    }

    public function verifyPayPassword($id, $payPassword){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('参数不正确，校验密码失败。');
        }
        return $this->verifyInSaltOut($payPassword, $user['payPasswordSalt'], $user['payPassword']);
    }

    public function getUserSecureQuestionsByUserId($userId){
        return $this->getUserSecureQuestionDao()->getUserSecureQuestionsByUserId($userId);
    }

    public function addUserSecureQuestionsWithUnHashedAnswers($userId,$fieldsWithQuestionTypesAndUnHashedAnswers){
        $encoder = $this->getPasswordEncoder();
        $userSecureQuestionDao = $this->getUserSecureQuestionDao();
        for ($questionNum = 1;$questionNum <= (count($fieldsWithQuestionTypesAndUnHashedAnswers) / 2);$questionNum++){
                $fields = array('userId'=>$userId);
                $fields['securityQuestionCode'] = $fieldsWithQuestionTypesAndUnHashedAnswers['securityQuestion'.$questionNum];
                $fields['securityAnswerSalt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
                $fields['securityAnswer'] =
                $encoder->encodePassword($fieldsWithQuestionTypesAndUnHashedAnswers['securityAnswer'.$questionNum], $fields['securityAnswerSalt']);
                $fields['createdTime'] = time();
                $userSecureQuestionDao ->addOneUserSecureQuestion($fields);
        }
        return true;
    }

    public function verifyInSaltOut($in,$salt,$out){
        return $out == $this->getPasswordEncoder()->encodePassword($in,$salt);
    }

    public function isMobileUnique($mobile){
        $count = $this->searchUserCount(array('verifiedMobile' => $mobile));
        if ($count > 0) {
            return false;
        }
        return true;
    }

    /**
     * 更换手机号
     * @author fubaosheng 2015-04-28
     */
    public function changeMobile($id, $mobile){
        $user = $this->getUser($id);
        if (empty($user) or empty($mobile)) {
            E('参数不正确，更改失败。');
        }
        if(!isValidMobile($mobile))
            E('手机号格式不正确，更改失败。');
        $fields = array(
            'verifiedMobile' => $mobile
        );
        $this->getUserDao()->updateUser($id, $fields);
//        $this->updateUserProfile($id, array(
//            'mobile' => $mobile
//        )); 
        $this->getLogService()->info('user', 'verifiedMobile-changed', "用户{$user['email']}(ID:{$user['id']})重置mobile成功");
        return true;
    }

    /**
     * 变更密码
     *
     * @param  [integer]    $id       用户ID
     * @param  [string]     $password 新密码
     */
    public function changePassword($id, $password){
        $user = $this->getUser($id);
        if (empty($user) or empty($password)) {
            E('参数不正确，更改密码失败。');
        }
        $salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $fields = array(
            'salt' => $salt,
            'password' => $this->getPasswordEncoder()->encodePassword($password, $salt)
        );
        $this->getUserDao()->updateUser($id, $fields);
        $this->clearUserConsecutivePasswordErrorTimesAndLockDeadline($id);
        $this->getLogService()->info('user', 'password-changed', "用户{$user['email']}(ID:{$user['id']})重置密码成功");
        return true;
    }

    /**
     * 校验密码是否正确
     *
     * @param  [integer]    $id       用户ID
     * @param  [string]     $password 密码
     *
     * @return [boolean] 密码正确，返回true；错误，返回false。
     */
    public function verifyPassword($id, $password){
        $user = $this->getUser($id);
        if (empty($user)) {
           E('参数不正确，校验密码失败。');
        }
        return $this->verifyInSaltOut($password, $user['salt'], $user['password']);
    }

    /**
     * 自动创建测试账号
     * @author fubaosheng 2015-09-02
     */
    public function autoRegister($webCode = ''){
        $webCode = $webCode ? $webCode : C('WEBSITE_CODE');
        $user = array();
        $testAccountCfg = C("TEST_ACCOUNT_CFG");
        $user['email'] =  'redcloud_test_'.$webCode.'@redcloud.com';
        $user['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $user['password'] = $this->getPasswordEncoder()->encodePassword($testAccountCfg['DFT_PWD'], $user['salt']);
        $user['nickname'] = $testAccountCfg['NICKNAME'];
        $user['type'] = 'default';
        $user['emailVerified'] = 1;
        $user['roles'] = $testAccountCfg['DFT_ROLE'];
        $user['createdIp'] = get_client_ip();
        $user['createdTime'] = time();
        $user['testAccount'] = 1;
        $user['webCode'] = $webCode;
        $arr = $this->getUserDao()->addUser($user);
        $uid = $arr ? $arr['id'] : 0;
        if($uid){
            $profile = array('id'=>$uid,'webCode'=>$webCode);
            $this->getProfileDao()->addProfile($profile);
        }
        return $uid;
    }
    /**
     * 检查并创建用户（依据设备）
     * @author 钱志伟 2015-11-30
     */
    public function checkAndAddUserByDevice($param=array()){
        $options = array(
            'deviceId' => '',   #设备id
        );
        $options = array_merge($options, $param);
        extract($options);

        if(!$deviceId) return false;

        $user = $this->getUserDao()->findUserByAppleDeviceId($deviceId);
        if(!$user) {
            $user['roles'] = array('ROLE_USER');
            $user['type'] = 'default';
            $user['createdTime'] = time();
            $user['from'] = $registration['from'] ? : 0;

            $arr = $this->getUserDao()->addUser(array('appleBuyDeviceId'=>$deviceId));
            $uid = $arr ? $arr['id'] : 0;
            if($uid){
                $profile = array('id'=>$uid);
                $this->getProfileDao()->addProfile($profile);
            }
            $user = $this->getUserDao()->findUserByAppleDeviceId($deviceId);
        }
        return $user;
    }

    /**
     * 用户修改注册信息
     * @author fubaosheng 2015-10-26
     */
    public function modifyRegUser($registration,$uid){
        $user = array();
        if(!SimpleValidator::nickname($registration['nickname']))
            E('用户名只限汉字、字母、数字、下划线 2~20位');
        if(isset($registration['verifiedMobile']) && !empty($registration['verifiedMobile'])) {
            if(!$this->isMobileAvaliable($registration['verifiedMobile']))
                E('手机号已存在');
            if(!SimpleValidator::mobile($registration['verifiedMobile']))
                E('verifiedMobile error!');
            $user['verifiedMobile'] = $registration['verifiedMobile'];
        }
        $user['nickname'] =  $registration['nickname'];
        $user['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $user['password'] = $this->getPasswordEncoder()->encodePassword($registration['password'], $user['salt']);
        $user['setup'] = 1;
        $this->getUserDao()->updateUser($uid, $user);

        if(isset($registration['gender']) && !empty($registration['gender'])){
            $this->getProfileDao()->updateProfile($uid,array('gender'=>$registration['gender']));
        }
        return $this->getUser($uid);
    }


    /**
     * 用户注册
     *
     * 当type为default时，表示用户从自身网站注册。
     * 当type为weibo、qq、renren时，表示用户从第三方网站连接，允许注册信息没有密码。
     *
     * @param  [type] $registration 用户注册信息
     * @param  string $type         注册类型
     * @return array 用户信息
     */
    public function register($registration, $type = 'default'){
        if(empty($registration['regType']) || (!empty($registration['regType']) && $registration['regType'] == "email")){
            $checkEmail = 1;
        }
        if($registration['regType']=='mobile'){
            $checkEmail = 0;
        }
        if($checkEmail){
            if (!SimpleValidator::email($registration['email'])) {
                E('email error!');
            }
            if (!$this->isEmailAvaliable($registration['email'])) {
                E('Email已存在');
            }
        }
        if (!SimpleValidator::nickname($registration['nickname'])) {
            E('用户名只限汉字、字母、数字、下划线 2~20位');
        }

        $user = array();
        if(!empty($registration['regType']) && $registration['regType'] == "mobile")
            $registration['email'] = '';
        $user['email'] = $registration['email'];

        if (isset($registration['verifiedMobile']) && !empty($registration['verifiedMobile'])) {
            if (!$this->isMobileAvaliable($registration['verifiedMobile'])) {
                E('手机号已存在');
            }
            if (!SimpleValidator::mobile($registration['verifiedMobile'])) {
                E('verifiedMobile error!');
            }
            $user['verifiedMobile'] = $registration['verifiedMobile'];
        }else{
            $user['verifiedMobile'] = '';
        }
        $user['nickname'] = $registration['nickname'];
        $user['roles'] =  array('ROLE_USER');
        $user['type'] = $type;
        $user['createdIp'] = empty($registration['createdIp']) ? '' : $registration['createdIp'];
        $user['emailVerified'] = intval($registration['emailVerified']) ? 1 : 0;
        $user['createdTime'] = time();
        $user['from'] = $registration['from'] ? : 0;
        if(in_array($type, array('default', 'phpwind', 'discuz'))) {
            $user['salt'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
            $user['password'] = $this->getPasswordEncoder()->encodePassword($registration['password'], $user['salt']);
            $user['setup'] = 1;
        } else {
            $user['salt'] = '';
            $user['password'] = '';
            $user['setup'] = 0;
        }
        $user['sourceWebCode'] = $this->getFirstWebCode($checkEmail,$user);
        $user = serialize::unserialize(
            $this->getUserDao()->addUser(serialize::serialize($user))
        );

        if (isset($registration['mobile']) &&$registration['mobile']!=""&& !SimpleValidator::mobile($registration['mobile'])) {
            E('mobile error!');
        }
        if (isset($registration['idcard']) &&$registration['idcard']!=""&& !SimpleValidator::idcard($registration['idcard'])) {
            E('idcard error!');
        }
        if (isset($registration['truename']) &&$registration['truename']!=""&& !SimpleValidator::truename($registration['truename'])) {
           E('truename error!');
        }

        $profile = array();
        $profile['id'] = $user['id'];
        $profile['mobile'] = empty($registration['mobile']) ? '' : $registration['mobile'];
        $profile['idcard'] = empty($registration['idcard']) ? '' : $registration['idcard'];
        $profile['truename'] = empty($registration['truename']) ? '' : $registration['truename'];
        $profile['company'] = empty($registration['company']) ? '' : $registration['company'];
        $profile['job'] = empty($registration['job']) ? '' : $registration['job'];
        $profile['weixin'] = empty($registration['weixin']) ? '' : $registration['weixin'];
        $profile['weibo'] = empty($registration['weibo']) ? '' : $registration['weibo'];
        $profile['qq'] = empty($registration['qq']) ? '' : $registration['qq'];
        $profile['site'] = empty($registration['site']) ? '' : $registration['site'];
        $profile['gender'] = empty($registration['gender']) ? 'male' : $registration['gender'];
        for($i=1;$i<=5;$i++){
            $profile['intField'.$i] = empty($registration['intField'.$i]) ? null : $registration['intField'.$i];
            $profile['dateField'.$i] = empty($registration['dateField'.$i]) ? null : $registration['dateField'.$i];
            $profile['floatField'.$i] = empty($registration['floatField'.$i]) ? null : $registration['floatField'.$i];
        }
        for($i=1;$i<=10;$i++){
            $profile['varcharField'.$i] = empty($registration['varcharField'.$i]) ? "" : $registration['varcharField'.$i];
            $profile['textField'.$i] = empty($registration['textField'.$i]) ? "" : $registration['textField'.$i];
        }
        $this->getProfileDao()->addProfile($profile);
        if ($type != 'default') {
            $this->bindUser($type, $registration['token']['userId'], $user['id'], $registration['token']);
        }

//        $this->getDispatcher()->dispatch('user.service.registered', new ServiceEvent($user));
        return $user;
    }
    public function getFirstWebCode($checkEmail,$user){
        if($checkEmail){
            $parm['email']=$user['email'];
        }else{
            $parm['verifiedMobile']=$user['verifiedMobile'];
        }
        $data=$this->getUserDao()->getFirstWebCode($parm);
        if($data){
            return $data['webCode'];
        }else{
            return C('WEBSITE_CODE');
        }
    }
    public function setupAccount($userId){
        $user = $this->getUser($userId);
        if (empty($user)) {
            E('用户不存在，设置帐号失败！');
        }
        if ($user['setup']) {
            E('该帐号，已经设置过帐号信息，不能再设置！');
        }
        $this->getUserDao()->updateUser($userId, array('setup' => 1));
        return $this->getUser($userId);
    }

    public function markLoginInfo(){
        $user = $this->getCurrentUser();//
        if (empty($user)) {
            return ;
        }
        $this->getUserDao()->updateUser($user->id, array(
            'loginIp' => $user['currentIp'],
            'loginTime' => time(),
        ));
        $this->getLogService()->info('user', 'login_success', '登录成功');
    }

    //更新用户信息-远程接口
    public function updateUserProfile($id, $fields){
        if(C('if_iface')){
            return $this->updateUserProfileIface($id, $fields);
        }else{
            return $this->updateUserProfileLocal($id, $fields);
        }
    }

    //更新用户信息-本地数据库
    public function updateUserProfileLocal($id, $profileData){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，更新用户失败。');
        }
        
        if(array_key_exists('truename',$profileData) && empty(trim($profileData['truename']))){
            return new IFaceResultModel(14,'用户名不能为空');
        }
        if(array_key_exists('gender',$profileData) && !in_array($profileData['gender'],['male','female'])){
            return new IFaceResultModel(15,'性别值不正确');
        }

        if(!isValidEmail($profileData['email'])){
            return new IFaceResultModel(16,'邮箱格式不正确');
        }

        $userData['email'] = $profileData['email'];
        if(array_key_exists('truename',$profileData)) {
            $userData['nickname'] = $profileData['truename'];
        }
//        $userData['cellphone'] = $profileData['cellphone'];

        unset($profileData['email']);
        
        $this->getUserDao()->updateUser($id,$userData);

        $this->getProfileDao()->updateProfile($id,$profileData);

        return new IFaceResultModel(20,'更改信息成功');
    }

    //更新用户信息-远程接口
    public function updateUserProfileIface($id, $fields){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，更新用户失败。');
        }

        $result = ApiService::postData(C('api_url.update_user_info'),array(
            'userid'    =>   $user['userNum'],
            "data"  =>  $fields
        ));
        return $result;
    }

    public function getUserProfileSimple($id){
        $profile =  $this->getProfileDao()->getProfile($id);
        return $profile;
    }

    public function getUserProfile($id){
        $profile =  $this->getProfileDao()->getProfile($id);
        $user = $this->getUser($id);
        $userInfo = $this->findUserInfo($user['userNum']);

        $userInfo->info = is_object($userInfo->info) ? get_object_vars($userInfo->info) : $userInfo->info;
        if($userInfo->code == 20 and is_array($userInfo->info)){
            $profile = array_merge($profile,$userInfo->info);
        }

        return $profile;
    }

    /**
     * 改变老师的分类
     * @author fubaosheng 2015-04-29
     */
    public function changeUserTeacherCategoryId($id,$teacherCategoryId){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，设置用户角色失败。');
        }
        $teacherCategoryId = $teacherCategoryId ? : 0;
        $this->getUserDao()->updateUser($id, array('teacherCategoryId' => $teacherCategoryId));
    }

    /**
     * 改变管理员的分类
     * @author fubaosheng 2015-04-29
     */
    public function changeUserAdminCategoryIds($id,$adminCategoryIds){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，设置用户角色失败。');
        }
        $adminCategoryIds = trim($adminCategoryIds,",") ? : null;
        $this->getUserDao()->updateUser($id, array('adminCategoryIds' => $adminCategoryIds));
    }

    /**
     * 改变自定义
     * @author fubaosheng 2015-04-29
     */
    public function changeUserDefineRoles($id,$defineRoles){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，设置用户角色失败。');
        }
        $defineRoles = $defineRoles ? json_encode ($defineRoles) : null;
        $this->getUserDao()->updateUser($id, array('defineRoles' => $defineRoles));
    }

    public function changeUserRoles($id, array $roles){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，设置用户角色失败。');
        }
        if (empty($roles)) {
            E('用户角色不能为空');
        }
        if (!in_array('ROLE_USER', $roles)) {
            E('用户角色必须包含ROLE_USER');
        }

        $allowedRoles = array('ROLE_USER', 'ROLE_ADMIN', 'ROLE_SUPER_ADMIN','ROLE_TEACHER','ROLE_GOLD_ADMIN','ROLE_MARKET');
        $notAllowedRoles = array_diff($roles, $allowedRoles);
        if (!empty($notAllowedRoles)) {
            E('用户角色不正确，设置用户角色失败。');
        }
        $this->getUserDao()->updateUser($id, serialize::serialize(array('roles' => $roles)));
        $this->getLogService()->info('user', 'change_role', "设置用户{$user['nickname']}(#{$user['id']})的角色为：" . implode(',', $roles));
    }

    /**
     * @deprecated move to TokenService
     */
    public function makeToken($type, $userId = null, $expiredTime = null, $data = null,$siteSelect = "local"){
        $token = array();
        $token['type'] = $type;
        $token['userId'] = $userId ? (int)$userId : 0;
        $token['token'] = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
        $token['data'] = serialize($data);
        $token['expiredTime'] = $expiredTime ? (int) $expiredTime : 0;
        $token['createdTime'] = time();

        $token = $this->getUserTokenDao()->addToken($token,$siteSelect);
        return $token;
    }

    /**
     * @deprecated move to TokenService
     */
    public function getToken($type, $tokenId){
        $token = $this->getUserTokenDao()->getToken($tokenId);
        if (empty($token) || $token['type'] != $type) {
            return null;
        }
        if ($token['expiredTime'] > 0 && $token['expiredTime'] < time()) {
            return null;
        }
        $token['data'] = unserialize($token['data']);
        return $token;
    }

    //获取用户最后一个token
    public function getUserLastToken($type, $userId){
        $token = $this->getUserTokenDao()->getUserLastToken($userId);
        if (empty($token) || $token['type'] != $type) {
            return null;
        }
        if ($token['expiredTime'] > 0 && $token['expiredTime'] < time()) {
            return null;
        }
        $token['data'] = unserialize($token['data']);
        return $token;
    }

    /**
     * @deprecated move to TokenService
     */
    public function searchTokenCount($conditions){
        return $this->getUserTokenDao()->searchTokenCount($conditions);
    }

    /**
     * @deprecated move to TokenService
     */
    public function deleteToken($type, $token){
        $token = $this->getUserTokenDao()->findTokenByToken($token);
        if (empty($token) || $token['type'] != $type) {
            return false;
        }
        $this->getUserTokenDao()->deleteToken($token['id']);
        return true;
    }

    /**
     * 解绑手机号
     * @author fubaosheng 2015-04-28
     */
    public function unbindMobile($id){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，解绑手机号失败！');
        }
        if(empty($user['email'])){
            E('用户没有绑定邮箱，解绑手机号失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('verifiedMobile' => ''));
        $this->getLogService()->info('user', 'unbindMobile', "解绑用户{$user['nickname']}的手机号(#{$user['id']})");
        return true;
    }

    public function lockUser($id,$lockReason='other'){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，封禁失败！');
        }

        if(!in_array($lockReason,'other','delete','admin')){
            $lockReason = 'other';
        }
        $this->getUserDao()->updateUser($user['id'], array('lock' => 1,'lockTime'=>time(),'lockReasonType'=>$lockReason));
        $this->getLogService()->info('user', 'lock', "封禁用户{$user['nickname']}(#{$user['id']})");
        return true;
    }

    public function unlockUser($id){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，解禁失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('lock' => 0,'lockReasonType'=>'other'));
        $this->getLogService()->info('user', 'unlock', "解禁用户{$user['nickname']}(#{$user['id']})");
        return true;
    }

    public function promoteUser($id){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，推荐失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('promoted' => 1, 'promotedTime' => time()));
        $this->getLogService()->info('user', 'recommend', "推荐用户{$user['nickname']}(#{$user['id']})");
    }

    public function cancelPromoteUser($id){
        $user = $this->getUser($id);
        if (empty($user)) {
            E('用户不存在，取消推荐失败！');
        }
        $this->getUserDao()->updateUser($user['id'], array('promoted' => 0, 'promotedTime' => 0));
        $this->getLogService()->info('user', 'cancel_recommend', "取消推荐用户{$user['nickname']}(#{$user['id']})");
    }
    /**
     * 查找最新推荐教师
     * @param type $start
     * @param type $limit
     * @param type $param qzw add 2015-11-30
     * @return type
     */
    public function findLatestPromotedTeacher($start, $limit, $param=array()){
        $options = array(
            'siteSelect' => C('WEBSITE_CODE'),   #默认当前站点 qzw 2015-11-30
        );
        $options = array_merge($options, $param);
        extract($options);

        $condition = array('roles' => 'ROLE_TEACHER', 'promoted' => 1, 'siteSelect'=>$siteSelect);
        return $this->searchUsers($condition,  $start, $limit);
    }

    /**
     * 更新用户的计数器
     *
     * @param  integer $number 用户ID
     * @param  string $name   计数器名称
     * @param  integer $number 计数器增减的数量
     */
    public function waveUserCounter($userId, $name, $number){
        if (!ctype_digit((string) $number)) {
           E('计数器的数量，必须为数字');
        }
        $this->getUserDao()->waveCounterById($userId, $name, $number);
    }

    /**
     * 清零用户的计数器
     *
     * @param  integer $number 用户ID
     * @param  string $name   计数器名称
     */
    public function clearUserCounter($userId, $name){
        $this->getUserDao()->clearCounterById($userId, $name);
    }

    /**
     *
     * 绑定第三方登录的帐号到系统中的用户帐号
     *
     */
    public function bindUser($type, $fromId, $toId, $token){
        $user = $this->getUserDao()->getUser($toId);
        if (empty($user)) {
            E('用户不存在，第三方绑定失败');
        }

        $types = array_keys(OAuthClientFactory::clients());
        $types = array_merge($types, array('discuz', 'phpwind'));

        if(!in_array($type, $types)) {
            E("{$type}类型不正确，第三方绑定失败。");
        }
        return $this->getUserBindDao()->addBind(array(
            'type' => $type,
            'fromId' => $fromId,
            'toId'=>$toId,
            'token'=> empty($token['token']) ? '' : $token['token'],
            'createdTime'=>time(),
            'expiredTime'=>empty($token['expiredTime']) ? 0 : $token['expiredTime'],
        ));
    }

    public function getUserBindByTypeAndFromId($type, $fromId){
        return $this->getUserBindDao()->getBindByTypeAndFromId($type, $fromId);
    }

    public function getUserBindByTypeAndUserId($type, $toId){
        $user = $this->getUserDao()->getUser($toId);
        if(empty($user)) {
           E('获取用户绑定信息失败，该用户不存在');
        }
        $types = array_keys(OAuthClientFactory::clients());
        $types = array_merge($types, array('discuz', 'phpwind'));
        if(!in_array($type, $types)) {
            E("{$type}类型不正确，获取第三方登录信息失败。");
        }
        return $this->getUserBindDao()->getBindByToIdAndType($type, $toId);
    }

    public function findBindsByUserId($userId){
        $user = $this->getUserDao()->getUser($userId);
        if (empty($user)){
            E('获取用户绑定信息失败，当前用户不存在');
        }
        return $this->getUserBindDao()->findBindsByToId($userId);
    }

    public function unBindUserByTypeAndToId($type, $toId){
        $user = $this->getUserDao()->getUser($toId);
        if(empty($user)) {
            E('解除第三方绑定失败，该用户不存在');
        }

        $types = array_keys(OAuthClientFactory::clients());
        if(!in_array($type, $types)) {
            E("{$type}类型不正确，解除第三方绑定失败。");
        }
        $bind = $this->getUserBindByTypeAndUserId($type, $toId);
        if($bind){
            $bind = $this->getUserBindDao()->deleteBind($bind['id']);
        }
        return $bind;
    }

    /**
     * 判断用户是否在同一个学校
     * @return boolean
     * @author LiangFuJian <liangfujian@redcloud.com>
     * @date 2015-08-05
     */
    public function isInSameSchool($fromId, $toId){

        $userObj = $this->getUserDao();
        $fromUser = $userObj->getUser($fromId);
        $toUser = $userObj->getUser($toId);

        if (!$fromUser || !$toUser)
            return false;

        if ($fromUser['webCode'] != $toUser['webCode'])
            return false;

        return true;
    }

    /**
     * 用户之间相互关注
     */

    public function follow($fromId, $toId){
        $fromUser = $this->getUser($fromId);
        $toUser = $this->getUser($toId);
        if(empty($fromUser) || empty($toUser)) {
            E('用户不存在，关注失败！');
        }
        if($fromId == $toId) {
            E('不能关注自己！');
        }
        $friend = $this->getFriendDao()->getFriendByFromIdAndToId($fromId, $toId);
        if(!empty($friend)) {
            E('不允许重复关注!');
        }
        return $this->getFriendDao()->addFriend(array(
            "fromId"=>$fromId,
            "toId"=>$toId,
            "createdTime"=>time()));
    }

    public function unFollow($fromId, $toId){
        $fromUser = $this->getUser($fromId);
        $toUser = $this->getUser($toId);
        if(empty($fromUser) || empty($toUser)) {
            E('用户不存在，取消关注失败！');
        }
        $friend = $this->getFriendDao()->getFriendByFromIdAndToId($fromId, $toId);
        if(empty($friend)) {
            E('不存在此关注关系，取消关注失败！');
        }
        return $this->getFriendDao()->deleteFriend($friend['id']);
    }

    public function isFollowed($fromId, $toId){
        $fromUser = $this->getUser($fromId);
        $toUser = $this->getUser($toId);
        if(empty($fromUser)) {
            E('用户不存在，检测关注状态失败！');
        }
        if(empty($toUser)) {
            E('被关注者不存在，检测关注状态失败！');
        }

        $friend = $this->getFriendDao()->getFriendByFromIdAndToId($fromId, $toId);
        if(empty($friend)) {
            return false;
        } else {
            return true;
        }
    }

    public function findUserFollowing($userId, $start, $limit){
        $friends = $this->getFriendDao()->findFriendsByFromId($userId, $start, $limit);
        $ids = ArrayToolkit::column($friends, 'toId');
        return $this->findUsersByIds($ids);
    }

    public function findAllUserFollowing($userId){
        $friends =$this->getFriendDao()->findAllUserFollowingByFromId($userId);
        $ids = ArrayToolkit::column($friends, 'toId');
        return $this->findUsersByIds($ids);
    }

    public function findUserFollowingCount($userId){
        return $this->getFriendDao()->findFriendCountByFromId($userId);
    }

    public function findUserFollowers($userId, $start, $limit){
        $friends = $this->getFriendDao()->findFriendsByToId($userId, $start, $limit);
        $ids = ArrayToolkit::column($friends, 'fromId');
        return $this->findUsersByIds($ids);
    }

    public function findUserFollowerCount($userId){
        return $this->getFriendDao()->findFriendCountByToId($userId);
    }

    public function findAllUserFollower($userId){
        $friends =$this->getFriendDao()->findAllUserFollowerByToId($userId);
        $ids = ArrayToolkit::column($friends, 'fromId');
        return $this->findUsersByIds($ids);
    }

    /**
     * 过滤得到用户关注中的用户ID列表
     *
     * 此方法用于给出一批用户ID($followingIds)，找出哪些用户ID，是已经被用户($userId)关注了的。
     *
     * @param  integer $userId       关注者的用户ID
     * @param  array   $followingIds 被关注者的用户ID列表
     * @return array 用户关注中的用户ID列表。
     */
    public function filterFollowingIds($userId, array $followingIds){
        if (empty($followingIds)) {
            return array();
        }
        $friends = $this->getFriendDao()->getFriendsByFromIdAndToIds($userId, $followingIds);
        return ArrayToolkit::column($friends, 'toId');
    }

    public function getLastestApprovalByUserIdAndStatus($userId, $status){
         return $this->getUserApprovalDao()->getLastestApprovalByUserIdAndStatus($userId, $status);
    }

    public function applyUserApproval($userId, $approval, $faceImg, $backImg, $directory){
        $user = $this->getUser($userId);
        if (empty($user)) {
            E("用户#{$userId}不存在！");
        }

        $faceImgPath = 'userFaceImg'.$userId.time().'.'. $faceImg->getClientOriginalExtension();
        $backImgPath = 'userbackImg'.$userId.time().'.'. $backImg->getClientOriginalExtension();
        $faceImg = $faceImg->move($directory, $faceImgPath);
        $backImg = $backImg->move($directory, $backImgPath);

        $approval['userId'] = $user['id'];
        $approval['faceImg'] = $faceImg->getPathname();
        $approval['backImg'] = $backImg->getPathname();
        $approval['faceImg'] = substr($approval['faceImg'],(strripos($approval['faceImg'],'/' )-28));
        $approval['backImg'] = substr($approval['backImg'],(strripos($approval['backImg'],'/' )-28));
        $approval['status'] = 'approving';
        $approval['createdTime'] = time();

        $this->getUserDao()->updateUser($userId, array(
            'approvalStatus' => 'approving',
            'approvalTime' => time()
        ));
        $this->getUserApprovalDao()->addApproval($approval);
        return true;
    }

    public function findUserApprovalsByUserIds($userIds){
        return $this->getUserApprovalDao()->findApprovalsByUserIds($userIds);
    }

    public function passApproval($userId, $note = null){
        $user = $this->getUserDao()->getUser($userId);
        if (empty($user)) {
            E("用户#{$userId}不存在！");
        }

        $this->getUserDao()->updateUser($user['id'], array(
            'approvalStatus' => 'approved',
            'approvalTime' => time()
        ));
        $lastestApproval = $this->getUserApprovalDao()->getLastestApprovalByUserIdAndStatus($user['id'],'approving');
        $this->getProfileDao()->updateProfile($userId, array(
            'truename'=>$lastestApproval['truename'],
            'idcard'=> $lastestApproval['idcard'])
        );

        $currentUser = $this->getCurrentUser();//
        $this->getLogService()->info('user', 'approved', "用户{$user['nickname']}实名认证成功，操作人:{$currentUser->nickname} !" );
        $message = '您的个人实名认证，审核已经通过！' . ($note ? "({$note})" : '');
        $this->getNotificationService()->notify($user['id'], 'default', $message);
        return true;
    }

    /**
     * 判断用户是否拥有权限进后台
     * @author fubaosheng 2015-05-14
     */
    public function isUserRole(){
        if(goBackEnd()) return true;
        return false;
    }

    public function rejectApproval($userId, $note = null){
        $user = $this->getUserDao()->getUser($userId);
        if (empty($user)) {
            E("用户#{$userId}不存在！");
        }

        $this->getUserDao()->updateUser($user['id'], array(
            'approvalStatus' => 'approve_fail',
            'approvalTime' => time(),
        ));
        $currentUser = $this->getCurrentUser(); //
        $this->getUserApprovalDao()->addApproval(
            array(
            'userId'=> $user['id'],
            'note'=> $note,
            'status' => 'approve_fail',
            'operatorId' => $currentUser->id)
        );

        $this->getLogService()->info('user', 'approval_fail', "用户{$user['nickname']}实名认证失败，操作人:{$currentUser->nickname} !" );
        $message = '您的个人实名认证，审核未通过！' . ($note ? "({$note})" : '');
        $this->getNotificationService()->notify($user['id'], 'default', $message);
        return true;
    }

    public function analysisRegisterDataByTime($startTime,$endTime, $conditions=array()){
        return $this->getUserDao()->analysisRegisterDataByTime($startTime,$endTime, $conditions);
    }

	/**
	 * 为$list装饰用户信息
	 * ---------------
	 * 装饰前：
	 * array(
	 *   'title'=>'话题标题',
	 *   'created_time'=>'1429084389'
	 *   'userid'=>55
	 * )
	 * ---------------
	 * 装饰后：
	 *  array(
	 *   'title'=>'话题标题',
	 *   'created_time'=>'1429084389'
	 *   'userid'=>55
	 *   'username'=>'发布者用户名'
	 * )
	 * ----------------
	 * @param       $list
	 * @param array $options
	 */
	public function decorationOfUserByUid(&$list, $options = array()){
		//参数选项
		$default_options = array(
			//$list中的uid别名
			'uid'=>'uid',
			//需要获取的用户信息，value是数据库中的字段，key是需要返回的字段别名
			'filter_column' => array('uname'=>'nickname')
		);
		$options = array_merge($default_options,$options);
		//从$list中获取uid数组
		$user_ids = array();
		foreach($list as $v){
			$user_ids[] = $v[$options['uid']];
		}

		//获取用户信息
		if(!empty($user_ids)){
			$fields = array_values($options['filter_column']);
			array_push($fields,'id');
			if(in_array('gender',$fields)){
				$genders = $this->getProfileDao()->where(array('id'=>['IN',array_unique($user_ids)]))->field('id,gender')->select();
				$fields = array_filter($fields,function($val){ return $val!='gender';});
			}
			$users = $this->getDao()->where(array('id'=>['IN',array_unique($user_ids)]))->field($fields)->select();
			if(!empty($genders)){
				$ids = array_column($genders,'id');
				$genders = array_combine($ids,$genders);
				foreach($users as $k=>$v){
					$users[$k]['gender'] = $genders[$v['id']] ? $genders[$v['id']]['gender'] : "male";
				}
			}
		}
		//为list装饰用户信息
		if(!empty($users)){
			$id_keys = array_column($users,'id');
			$users = array_combine($id_keys,$users);
			foreach ($list as $k=>$v) {
				$userInfo = $users[$v[$options['uid']]];
				if(!empty($userInfo)){
					unset($userInfo['id']);
					$user_info = array();
					foreach($options['filter_column'] as $key=>$val){
						$user_info[$key] = $userInfo[$val];
					}
					$list[$k] = array_merge($user_info,$list[$k]);
				}
			}
		}
	}

    public function analysisUserSumByTime($endTime){
        $perDayUserAddCount = $this->getUserDao()->analysisUserSumByTime($endTime);
        $dayUserTotals = array();
        foreach ($perDayUserAddCount as $key => $value) {
            $dayUserTotals[$key] = array();
            $dayUserTotals[$key]["date"] = $value["date"];
            $dayUserTotals[$key]["count"] = 0;
            for ($i=$key; $i < count($perDayUserAddCount); $i++) {
                $dayUserTotals[$key]["count"] += $perDayUserAddCount[$i]["count"];
            }
        }
        return $dayUserTotals;
    }

    public function findUsersCountByLessThanCreatedTime($endTime){
         return $this->getUserDao()->findUsersCountByLessThanCreatedTime($endTime);
    }

    public function dropFieldData($fieldName){
        $this->getProfileDao()->dropFieldData($fieldName);
    }

    public function userLoginFail($user,$failAllowNum = 3, $temporaryMinutes = 20){
        $currentTime = time();
        if ($user['consecutivePasswordErrorTimes'] >= $failAllowNum-1){
            $this->getUserDao()->updateUser($user['id'], array('lockDeadline' => $currentTime+$temporaryMinutes*60));
        } else {
            $this->getUserDao()->updateUser($user['id'], array('consecutivePasswordErrorTimes' => $user['consecutivePasswordErrorTimes']+1));
        }
        $this->getUserDao()->updateUser($user['id'], array('lastPasswordFailTime' => $currentTime));
    }

    public function isUserTemporaryLockedOrLocked($user){
         return ( $user['locked'] == 1 )||( $user['lockDeadline'] > time() );
    }

    public function clearUserConsecutivePasswordErrorTimesAndLockDeadline($userId){
        $this->getUserDao()->updateUser($userId, array('lockDeadline' => 0, 'consecutivePasswordErrorTimes' => 0));
    }

    /**
     * 解析文本中@(提)到的用户
     */
    public function parseAts($text){
        preg_match_all('/@([\x{4e00}-\x{9fa5}\w]{2,16})/u', $text, $matches);
        $users = $this->getUserDao()->findUsersByNicknames(array_unique($matches[1]));
        if (empty($users)) {
            return array();
        }
        $ats = array();
        foreach ($users as $user) {
            $ats[$user['nickname']] = $user['id'];
        }
        return $ats;
    }

	/**
	 * 设置用户的超级管理员身份
	 * @param $userId
	 * @param $status
	 */
	public function setSuperAdmin($userId,$status) {
		return $this->updateUser($userId, ['super_admin' => $status]);

	}

    public function importUpdateEmail($users){
        $this->getUserDao()->getConnection()->beginTransaction();
        try{
            for($i=0;$i<count($users);$i++){
                $member = $this->getUserDao()->findUserByEmail($users[$i]["email"]);
                $member = serialize::unserialize($member);
                $this->changePassword($member["id"],$users[$i]["password"]);
                $this->updateUserProfile($member["id"],$users[$i]);
            }
            $this->getUserDao()->getConnection()->commit();
        }catch(\Exception $e){
            $this->getUserDao()->getConnection()->rollback();
            throw $e;
        }
    }


  /**
     * 用户学号导入临时表 中心库
     * @author tanhaitao 2015-09-29
     */
    public function addTmpStudInfo($field){
        $tmpStud['studNum'] = $field['studNum'];
        $tmpStud['studName'] = $field['studName'];

        $studInfo = $this->getTmpStudInfoDao()->existTmpStudInfo($tmpStud['studNum']);
        if(!empty($studInfo)){
           $tmpStud['id'] = $studInfo['id'];
           $this->getTmpStudInfoDao()->saveTmpStudInfo($tmpStud);
        }else{
           $this->getTmpStudInfoDao()->addTmpStudInfo($tmpStud);
        }
    }

    /**
     * 查询学号临时表
     * @author tanhaitao 2015-09-30
     */
    public function existTmpStudInfo($field){
        $tmpStud['studNum'] = $field['studNum'];
        $tmpStud['studName'] = $field['studName'];

        $r = $this->getTmpStudInfoDao()->existTmpStudInfo($tmpStud) ;
        return $r ? $r : null;
    }

    /**
     * 学号绑定不成功申请老师协助
     * @author tanhaitao 2015-09-30
     */
    public function addTmpStudAPPly($field){
        return $this->getTmpStudApplyDao()->addTmpStudApply($field);
    }

    /**
     * 学号申请表是否已申请
     * @author tanhaitao 2015-09-30
     */
    public function getApplyBystudUid($studUid){
        return $this->getTmpStudApplyDao()->getApplyBystudUid($studUid);
    }

    /**
     * 学号申请根据id查询
     * @author tanhaitao 2015-09-30
     */
    public function getTmpStudApply($id){
        return $this->getTmpStudApplyDao()->getTmpStudApply($id);
    }

    /**
     * 学号申请列表
     * @author tanhaitao 2015-09-30
     */
    public function getAllTmpStudApply($status, $orderBy, $start, $limit){
        return $this->getTmpStudApplyDao()->getAllTmpStudApply($status, $orderBy, $start, $limit);
    }

    /**
     * 学号申请表修改
     * @author tanhaitao 2015-09-30
     */
    public function updateTmpStudApply($id,$fields){
        return $this->getTmpStudApplyDao()->updateTmpStudApply($id,$fields);
    }
    
    /**
     * 根据用户 id 获取用户金币
     * @author Czq 2016-03-17
     */
    public function getUserGold($userId) {
        $userGold = $this->createDao('User.UserModel')->where(array('id' => $userId))->getField('gold');
        return $userGold;
    }
    /**
     * 学号申请删除
     * @author tanhaitao 2015-10-09
     */
    public function delTmpStudApply($uid){
        return $this->getTmpStudApplyDao()->delTmpStudApply($uid);
    }

    /**
     * 学号申请总数
     * @author tanhaitao 2015-10-09
     */
    public function searchApplyStatusCount($status){
        return $this->getTmpStudApplyDao()->searchApplyStatusCount($status);
    }


    public function findUserByStudNum($studNum){
        return $this->getUserDao()->findUserByStudNum($studNum);
    }
    
    public function findUserByVerifiedMobile($mobile){
        return $this->getUserDao()->findUserByVerifiedMobile($mobile);
    }
    public function findUserByEmail($email){
        return $this->getUserDao()->findUserByEmail($email);
    }
    public function getEmailByLikeName($name)
    {
        return $this->getUserDao()->getEmailByLikeName($name);
    }
     public function getMobileByLikeName($name)
    {
        return $this->getUserDao()->getMobileByLikeName($name);
    }
     public function getNickByLikeName($name)
    {
        return $this->getUserDao()->getNickByLikeName($name);
    }
    public function getUserInfoByRoles($where){
        return $this->getUserDao()->getUserInfoByRoles($where);
    }
    public function getUserInfoByCustom($param){
        return $this->getUserDao()->getUserInfoByCustom($param);
    }
     public function getUserMarket(){
        return $this->getUserDao()->getUserMarket();
    }


    public function getUsersByCondition($condition){
        return $this->getUserDao()->getUsersByCondition($condition);
    }


}


