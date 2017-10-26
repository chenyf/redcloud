<?php
namespace Common\Model\User;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
use Common\Common\ServiceKernel;
use Common\Model\User\CurrentUserModel;

class UserProviderModel extends BaseModel{
       
    public function __construct ($container){
        $this->container = $container;
    }
    
    protected function getUserService(){
        return $this->createService('User.UserServiceModel');
    }

    public function loadUserByUsername ($username) {
        if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
            $user = $this->getUserService()->getUserByEmail($username);
        } else {
            $user = $this->getUserService()->getUserByNickname($username);
        }

        if (empty($user)) {
            E(sprintf('User "%s" not found.', $username));
        }
        $user['currentIp'] = $this->container->get('request')->getClientIp();
        $currentUser = new CurrentUser();
        $currentUser->fromArray($user);

        ServiceKernel::instance()->setCurrentUser($currentUser);
        return $currentUser;
    }

    public function refreshUser (UserInterface $user) {
        if (! $user instanceof CurrentUser) {
            E(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
        return $this->loadUserByUsername($user->getUsername());
    }

    public function supportsClass ($class) {
        return $class === 'Topxia\Service\User\CurrentUser';
    }

}
?>
