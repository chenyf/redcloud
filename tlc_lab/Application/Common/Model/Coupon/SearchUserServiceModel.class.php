<?php
namespace Common\Model\Coupon;
use Think\Model;

class SearchUserServiceModel extends \Common\Model\Common\BaseModel
{
    public function searUserAction($map)
    {
        $data = M('user')->where($map)->field('nickname,email,verifiedMobile,createdTime,id')->select();
        return $data;
    }

}


?>
