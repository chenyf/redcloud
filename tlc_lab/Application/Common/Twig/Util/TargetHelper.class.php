<?php

namespace Common\Twig\Util;

use Common\Common\ServiceKernel;

class TargetHelper
{

    protected $container;

    public function __construct ($container)
    {
        $this->container = $container;
    }

    public function getTargets(array $targets)
    {

        $targets = $this->parseTargets($targets);

        $datas = $this->loadTargetDatas($targets);

        foreach ($targets as $key => $target) {
            if (empty($datas[$target['type']]) or empty($datas[$target['type']][$target['id']])) {
                $targets[$key] = null;
            } else {
                $targets[$key] = $datas[$target['type']][$target['id']];
            }
        }

        return $targets;
    }

    private function loadTargetDatas($targets)
    {
        $groupedTargets = array();
        foreach ($targets as $target) {
            if ($target['type'] == 'unknow') {
                continue;
            }
            if (empty($groupedTargets[$target['type']])) {
                $groupedTargets[$target['type']] = array();
            }
            $groupedTargets[$target['type']][] = $target['id'];
        }

        $datas = array();
        foreach ($groupedTargets as $type => $ids) {
            $finderClass = __NAMESPACE__ . '\\' . ucfirst($type) . 'TargetFinder';
            if(class_exists($finderClass)) {
                $finder = new $finderClass($this->container);
                $datas[$type] = $finder->find($ids);
            }
        }

        return $datas;
    }

    private function parseTargets($targets)
    {
        $parsedTargets = array();

        foreach ($targets as $target) {
            $explodedTarget = explode('/', $target);
            $lastTarget = end($explodedTarget);

            if (strpos($lastTarget, '-') === false) {
                $parsedTargets[$target] = array('type' => 'unknow', 'id' => 0);
            } else {
                list($type, $id) = explode('-', $lastTarget);
                $parsedTargets[$target] =  array('type' => $type, 'id' => $id);
            }
        }

        return $parsedTargets;
    }

}

abstract class AbstractTargetFinder
{
    protected $container;

    public function __construct ($container)
    {
        $this->container = $container;
    }

    abstract public function find(array $ids);
}

class CourseTargetFinder extends AbstractTargetFinder
{
    public function find(array $ids)
    {
        $courses = createService('Course.CourseService')->findCoursesByIds($ids);
        $targets = array();
        foreach ($courses as $id => $course) {
            $targets[$id] = array(
                'type' => 'course',
                'id' => $id,
                'simple_name' => $course['title'],
                'name' => $course['title'],
                'full_name' => $course['title'],
                'url' => generateUrl('course_show', array('id' => $id))
            );
        }

        return $targets;
    }
}

class LessonTargetFinder extends AbstractTargetFinder
{
    public function find(array $ids)
    {
        $lessons = createService('Course.CourseService')->findLessonsByIds($ids);

        $targets = array();
        foreach ($lessons as $id => $lesson) {
            $targets[$id] = array(
                'type' => 'lesson',
                'id' => $id,
                'simple_name' => "课程内容{$lesson['number']}",
                'name' => $lesson['title'],
                'full_name' => "课程内容{$lesson['number']}：{$lesson['title']}",
                'url' => generateUrl('course_learn', array('id' => $lesson['courseId'])) . '#lesson/' . $id,
            );
        }
        return $targets;
    }
}

class TestpaperTargetFinder extends AbstractTargetFinder
{
    public function find(array $ids)
    {
        return null;
    }
}