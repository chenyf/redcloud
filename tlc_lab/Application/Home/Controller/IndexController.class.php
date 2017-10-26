<?php
namespace Home\Controller;

class IndexController extends BaseController {
    public function indexAction(){
		#$this->redirect(U('Home/Default/index'));
        $this->redirect(U('Home/Test/index'));
    }
    
    public function testAction(){
        return $this->render('Index/index', array());
    }
    
    
    public function test1(){
        C('TMPL_L_DELIM', '{%');
        C('TMPL_R_DELIM', '%}');
        return $this->fetch('Index/test1');
    }
    
    public function test2(){
        C('TMPL_L_DELIM', '{%');
        C('TMPL_R_DELIM', '%}');
        return $this->fetch('Index/test2');
    }
}
