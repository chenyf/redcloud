<?php
namespace Course\Controller;

use Symfony\Component\HttpFoundation\Request;
use Common\Lib\ArrayToolkit;

class LessonLessonPluginController extends \Home\Controller\BaseController
{

    public function listAction (Request $request)
    {
        $user = $this->getCurrentUser();
        list($course, $member) = $this->getCourseService()->tryTakeCourse($request->query->get('courseId'));

        $items = $this->getCourseService()->getCourseItems($course['id'],"published");
         #判断章节是否有子元素
        $data = array('lesson'=>array(),'chapter'=>array());
        $firstItem = array('id'=>0,'type'=>'');
        $firstItemKey = array_keys($items)[0];
        $showItemIds = array();
        foreach ($items as $dataKey => $dataVal) {
            if($dataKey == $firstItemKey){
                $firstItem["id"] = $dataVal["id"];
                $firstItem["type"] = $dataVal["itemType"];
            }
            if($dataVal["itemType"] == "lesson"){
                if($dataVal['chapterId'])
                    $data['lesson'][$dataVal['id']] = $dataVal['chapterId'];
            }else{
                if($dataVal['parentId'])
                    $data['chapter'][$dataVal['id']] = $dataVal['parentId'];
            }
        }
        #取出seq为1的子元素
        if($firstItem["type"] == "chapter"){
            $showlessonIds = implode(",", array_keys(array_intersect($data["lesson"], array($firstItem["id"]))) );
            $showchapterIds = implode(",", array_keys(array_intersect($data["chapter"], array($firstItem["id"]))) );
            $showItemIds = explode(",", trim($showlessonIds.",".$showchapterIds,",")) ? : array();
        }
        foreach ($items as $showItem) {
            if($showItem["itemType"] == "chapter"){
                if(in_array($showItem["parentId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }else{
                if(in_array($showItem["chapterId"], $showItemIds))
                    $showItemIds[] = $showItem["id"];
            }
        }
        #seq为1的子元素和pid/chapterId等于0显示，其余不显示
        foreach($items as $itemKey => $itemVal){
            $items[$itemKey]["child"] = array();
            if($itemVal["itemType"] == "chapter"){
                foreach ($data['lesson'] as $itemLessonId => $itemLessonPid) {
                    if($itemLessonPid == $itemVal["id"]){
                        $items[$itemKey]["child"][] = $itemLessonId;
                    }
                }
                foreach ($data['chapter'] as $itemChapterId => $itemChapterPid) {
                    if($itemChapterPid == $itemVal["id"]){
                        $items[$itemKey]["child"][] = $itemChapterId;
                    }
                }
                if( !$itemVal["parentId"] || in_array($itemVal["id"],$showItemIds) )
                    $items[$itemKey]["show"] = 1;
                else
                    $items[$itemKey]["show"] = 0;
            }
            if($itemVal["itemType"] == "lesson"){
                if( !$itemVal["chapterId"] || in_array($itemVal["id"],$showItemIds) )
                    $items[$itemKey]["show"] = 1;
                else
                    $items[$itemKey]["show"] = 0;
            }
        }
        
        
        $learnStatuses = $this->getCourseService()->getUserLearnLessonStatuses($user['id'], $course['id']);

        $homeworkPlugin = $this->getAppService()->findInstallApp('Homework');
        $homeworkLessonIds =array();
        $exercisesLessonIds =array();

        if($homeworkPlugin) {
            $lessons = $this->getCourseService()->getCourseLessons($course['id']);
            $lessonIds = ArrayToolkit::column($lessons, 'id');
            $homeworks = $this->getHomeworkService()->findHomeworksByCourseIdAndLessonIds($course['id'], $lessonIds);
            $exercises = $this->getExerciseService()->findExercisesByLessonIds($lessonIds);
            $homeworkLessonIds = ArrayToolkit::column($homeworks,'lessonId');
            $exercisesLessonIds = ArrayToolkit::column($exercises,'lessonId');
        }


        return $this->render('LessonLessonPlugin:list', array(
            'course' => $course,
            'items' => $items,
            'learnStatuses' => $learnStatuses,
            'currentTime' => time(),
            'weeks' => array("日","一","二","三","四","五","六"),
            'homeworkLessonIds' => $homeworkLessonIds,
            'exercisesLessonIds' => $exercisesLessonIds,
            'firstItemKey'      => $firstItemKey ,
        ));
    }

    private function getCourseService()
    {
        return createService('Course.CourseService');
    }

    protected function getAppService()
    {
        return createService('CloudPlatform.AppService');
    }

    private function getHomeworkService()
    {
            return createService('Homework.HomeworkService');
    } 

    private function getExerciseService()
    {
            return createService('Homework.ExerciseService');
    }

}