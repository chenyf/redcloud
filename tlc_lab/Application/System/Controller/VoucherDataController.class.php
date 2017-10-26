<?php
/**
 * Description of VoucherData
 *
 * @author Administrator
 */
namespace System\Controller;
use \Home\Controller\BaseController;
use System\Common\QcloudApi;
class VoucherDataController extends BaseController {
    public function indexAction(){
        $user = $this->getCurrentUser();
       
        if(!$user->isLogin()){
            //未登录
            $this->render("VoucherData:register");
        
    }else{
        //已登录
            $this->render("VoucherData:index");
    }
    }

//查询兑换码
public function queryAction(){
  $code=$_GET['code'];
  $config = array(
                'SecretId'       => 'AKIDd6yCJtlm24TJvvCfZzvLLp1UY0PzmEN0',
                'SecretKey'      => 'S5S1Q18r6OfXpgIr8zARoInlFI7zOAaq',
                'RequestMethod'  => 'GET',
                'DefaultRegion'  => 'gz');

   $umt= QcloudApi::load(QcloudApi::MODULE_MARKET, $config);

    $package = array(array('voucherCode' =>$code) );
   //查询兑换码
   $msg= $umt->__call("QueryVoucherData", $package);
   
        if($msg){
             if($msg['data']['status']==1)  {
                 //请求接口成功，兑换码未使用
                 //使用兑换码
                  $info=$umt->__call("UseVoucherData", $package);
              
                  if($info){
                      //code不为0使用失败
                      if($info['data']['msg']=='success'){
                        
                          die("兑换成功");
                      }else{
                          
                          die("兑换失败");
                      }
                  }else{
                    
                     die("兑换失败");
                  }
             }else{
                 //请求接口成功，兑换码已使用
               die("无效的兑换码");
             }
           }else{
               //查询兑换码失败
              die("兑换失败");
           }
    }
}
?>
