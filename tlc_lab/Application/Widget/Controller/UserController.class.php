<?php
/*
 * 用户组件
 * @author     wanglei@redcloud.com
 * @created_at    16/3/30 下午6:00
 */

namespace Widget\Controller;
use Home\Controller\BaseController;

class UserController extends BaseController
{

    private $userDao;


    public function __construct()
    {
        parent::__construct();
        $this->userDao = createService('User.UserModel');
    }

    public function searchAction($keyword)
    {
        $condition = array();
        $res = array();
        if(!empty($keyword)){
            $condition['nickname'] = array('LIKE','%'.$keyword.'%');
            $res = $this->userDao->where($condition)->field('id,nickname,verifiedMobile,email')->limit(40)->select();
        }

        $res = is_array($res) ? $res : array();
        $this->ajaxReturn(array('more'=>false,'results'=>$res));
    }


}

?>
