<?php
namespace Common\Model\User;

class UserSerializeModel
{
	public static function serialize(array $user)
	{
		$user['roles'] = empty($user['roles']) ? '' :  '|' . implode('|', $user['roles']) . '|';
		return $user;
	}

	public static function unserialize(array $user = null)
	{
		if (empty($user)) {
			return null;
		}
		$user['roles'] = empty($user['roles']) ? array() : explode('|', trim($user['roles'], '|')) ;
		if(in_array('ROLE_TEACHER',$user['roles'])){
			$user['courseNum'] = createService('Course.CourseService')->findUserTeachCourseCount($user['id']);
		}

		$user['profile'] = createService('User.UserProfileModel')->getProfile($user['id']);

		if($user['profile']['gender'] == 'male'){
			$user['xingbie'] = '男';
		}else if($user['profile']['gender'] == 'female'){
			$user['xingbie'] = '女';
		}else{
			$user['xingbie'] = '-';
		}

		$user['roleName'] = in_array('ROLE_SUPER_ADMIN',$user['roles']) ? "管理员" : (in_array('ROLE_TEACHER',$user['roles']) ? "教师" : "学生");

		return $user;
	}

	public static function unserializes(array $users)
	{
		return array_map(function($user) {
			return UserSerializeModel::unserialize($user);
		}, $users);
	}
}