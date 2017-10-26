<?php
/**
 * 默认队列
 * @author 钱志伟 2015-05-22
 */
namespace Cli\Queue;
use Common\Lib\MailBat;
class DefaultQueue {
    /**
     * 示例任务
     */
    public static function test($param=array()){
        echo PHP_EOL.'begin test:';
        sleep(1);
        echo '-------------';
        print_r($param);
        echo '!!!!!!!!!!'.C('WEBSITE_CODE');
        $a = M('user')->find();
        print_r($a);
        echo M()->getLastSql().PHP_EOL;
        echo 'end test'.PHP_EOL;
    }
    
    public static function funcname_chuzhaoqian($param=array()){
        echo PHP_EOL.'begin test:';
        sleep(1);
        echo '-------------';
        print_r($param);
        echo '!!!!!!!!!!'.C('WEBSITE_CODE');
        $a = M('user')->find();
        print_r($a);
        echo M()->getLastSql().PHP_EOL;
        echo 'end test'.PHP_EOL;
    }
    /**
     * 发送邮件
     * @author 
     */
    public function sendMail($param = array()){
        $data = $this->args;
        $mailBat = MailBat::getInstance();
        $emailArr = $data['array'];
        //var_dump($emailArr[0]);die;
        foreach ($emailArr[0] as $k =>$email){
            
            $emailInfo = array(
             'to' => $email,
             'subject' => $emailArr['subject'],
             'html'  => $emailArr['content']
             );
            $result = $mailBat->sendMailBySohu($emailInfo);
            sleep(0.5);
            //更新数据库
            $arr = array(
                'id'      => $emailArr['id'],
                'last_update_time'=>time(),
                'sent'    => $k+1,
                );
            //print_r($arr);
            createService('Group.GroupServiceModel')->switchCenterDB()->updateGroupMailTask($arr);
        }
        
        
        
    }
    /**
     * 导入班级
     * @author fubaosheng  2015-06-30
     */
    public function importClass($param = array()){
        $options = array(
            'department'  => '',
            'faculty'     => '',
            'major'       => '',
            'class'       => '',
            'time'        => 0,
            'uid'         => 0,
            'admin'       => 0,
            'school'      => ''
        );
        $options = array_merge($options, $param);
        extract($options);
        
        $pinyin = new \Pinyin();
        $cateModel = createService("Taxonomy.CategoryModel");
        $groupModel = createService('Group.GroupModel');
        $groupMemberModel = createService('Group.GroupMemberModel');

        $failStr = "";
        #添加院
        $departmentArr = array();
        $dr = 0;
        if(!empty($department)){
            $departmentArr['code'] = $pinyin->switchChinese($department).date("Ymd")."_".rand(100, 999);
            $departmentArr['name'] = $department;
            $departmentArr['groupId'] = 1;
            $departmentArr['isSupper'] = $admin;
            $departmentArr['createUid'] =  $uid;
            $departmentArr['ctm'] = time();
            $departmentCate = $cateModel->getCateByName(array('name'=>$department,'parentId'=>0,'level'=>1));
            if(!empty($departmentCate)){
                $dr = $departmentCate;
            }else{
                $dr = $cateModel->addCategoryByExcel($departmentArr);
                if(!$dr) $failStr.= M()->getDbError()." : ".M()->getLastSql()."；<br/>\r\n";
            }
        }
        
        #添加系
        $facultyArr = array();
        $fr = 0;
     if(!empty($faculty)){
        $facultyArr['code'] = $pinyin->switchChinese($faculty).date("Ymd")."_".rand(100, 999);
        $facultyArr['name'] = $faculty;
        $facultyArr['groupId'] = 1;
        $facultyArr['isSupper'] = $admin;
        $facultyArr['createUid'] = $uid;
        $facultyArr['ctm'] = time();
        $facultyArr['parentId'] = !empty($dr) ? $dr : 0;
        $facultyArr['level'] = !empty($dr) ? 2 : 1;
        $facultyCate = $cateModel->getCateByName(array('name'=>$faculty,'parentId'=>$facultyArr['parentId'],'level'=>$facultyArr['level']));
        if(!empty($facultyCate)){
            $fr = $facultyCate;
        }else{
            $fr = $cateModel->addCategoryByExcel($facultyArr);
            if(!$fr) $failStr.= M()->getDbError()." : ".M()->getLastSql()."；<br/>\r\n";
        }
        
     }
        
        #添加专业
        $majorArr = array();
        $mr = 0;
        if(!empty($major)){
            $majorArr['code'] = $pinyin->switchChinese($major).date("Ymd")."_".rand(100, 999);
            $majorArr['name'] = $major;
            $majorArr['groupId'] = 1;
            $majorArr['isSupper'] = $admin;
            $majorArr['createUid'] = $uid;
            $majorArr['ctm'] = time();
            $majorArr['isLeafNode'] = 1;
            $majorArr['parentId'] = !empty($fr) ? $fr : $dr;
            $majorArr['level'] = !empty($fr)&&!empty($dr) ? 3 : 2;
            $majorCate = $cateModel->getCateByName(array('name'=>$major,'parentId'=>$majorArr['parentId'],'level'=>$majorArr['level']));
            if(!empty($majorCate)){
                $mr = $majorCate;
            }else{
                $mr = $cateModel->addCategoryByExcel($majorArr);
                if(!$mr) $failStr.= M()->getDbError()." : ".M()->getLastSql()."；<br/>\r\n";
            }
        }
        
        #添加班级
        $classArr = array();
        $classId = $groupModel->getClassIdByNameCateId($class,$mr);
        if(empty($classId)){
            $classArr['title'] = $class;
            $classArr['year'] = $time;
            $classArr['categoryId'] = $mr ? $mr : 0;
            $classArr['memberNum'] = 1;
            $classArr['ownerId'] = $uid;
            $classArr['createdTime'] = time();
            $classId = $groupModel->addGroupByExcel($classArr);
            if(!$classId) $failStr.= M()->getDbError()." : ".M()->getLastSql()."；<br/>\r\n";
        }
        
        #添加班级成员
//        $classMemberArr = array();
//        $classOwner = $groupMemberModel->getClassOwner($classId);
//        if(empty($classOwner)){
//            $classMemberArr['groupId'] = $classId ? $classId : 0;
//            $classMemberArr['userId'] = $uid;
//            $classMemberArr['role'] = "owner";
//            $classMemberArr['createdTime'] = time();
//            if(!empty($classId)){
//                $gr = $groupMemberModel->addMemberByExcel($classMemberArr);
//                if(!$gr) $failStr.= M()->getDbError()." : ".M()->getLastSql()."；<br/>\r\n";
//            }
//        }
        
        #添加失败，发邮件
         if(!empty($failStr)) {
            $mailBat = MailBat::getInstance();
            $manager = C("SYSTEM_MANAGER");
            $manager = implode(";", $manager);
            $emailArr = array(
                'to' => $manager,
                'subject' => "{$school}-班级导入",
                'html'  => $failStr
            );
            $mailBat->sendMailBySohu($emailArr);
        }
        
        unset($dr);
        unset($fr);
        unset($mr);
        unset($classId);
        unset($gr);
    }
    
    /**
     * 同步云课程
     * @author fubaosheng 2015-11-25
     */
     public function cloudSync($param = array()){
        $options = array(
            'courseId' => 0,
            'treeId'   => 0,
            'type'     => ''
        );
        $options = array_merge($options,$param);
        extract($options);
        
        if($type == "add")
            createService("Wyzc.WyzcService")->cloudChapterSync($courseId,$treeId);
        if($type == "update")
            createService("Wyzc.WyzcService")->cloudSyncUpdate($treeId,$courseId);
        
        createService("Wyzc.WyzcService")->cloudLessonNumSync($courseId);
     }

	public function publicToPrivate($param = array()) {
		self::getPubCourseCopyToPriService()->copyAll($param);

	}

	private function getPubCourseCopyToPriService() {
		return createService('Center\Course.PubCourseCopyToPriService');
	}
}