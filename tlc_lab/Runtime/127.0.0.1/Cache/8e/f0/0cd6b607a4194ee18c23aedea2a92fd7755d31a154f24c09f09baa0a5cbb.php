<?php

/* @Course/Course/course-detail-header.html.twig */
class __TwigTemplate_8ef00cd6b607a4194ee18c23aedea2a92fd7755d31a154f24c09f09baa0a5cbb extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<div class=\"course-cover-heading\">
    <div class=\"col-sm-8 picture\">
        <img src=\"";
        // line 3
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == "")) {
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "largePicture", array()), "large"), "html", null, true);
        } else {
            echo " ";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), "html", null, true);
        }
        echo "\" onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  loaderrimg=\"1\" class=\"img-responsive course-picture\"/>
        ";
        // line 4
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "serializeMode", array()) != "none")) {
            // line 5
            echo "            <span class=\"tag-serial\"></span>
        ";
        }
        // line 7
        echo "    </div>
    <div class=\"col-sm-4 info\">
        ";
        // line 9
        if ((isset($context["canManage"]) ? $context["canManage"] : null)) {
            // line 10
            echo "            <div class=\"btn-group pull-right\">
                <a class=\"btn btn-default btn-sm\" type=\"button\" href=\"";
            // line 11
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\" title=\"课程管理\">
                    <i class=\"esicon esicon-setting\"></i>
                </a>
            </div>
        ";
        }
        // line 16
        echo "        <div class=\"c-info-txt c-course-txt\">
            <span class=\"text-muted\">课程名称：</span>
            <span class=\"c-course-name c-info-lesson-value\">";
        // line 18
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo "</span>
        </div>
        <div class=\"c-info-txt c-teacher-txt\">
            <span class=\"text-muted\">授课老师：</span>
            <span class=\"c-teacher-name c-info-lesson-value\">";
        // line 22
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "teacherName", array()), "html", null, true);
        echo "</span>
        </div>
        <div class=\"c-info-txt c-teacher-txt\">
            <span class=\"text-muted\">课程编号：</span>
            <span class=\"c-course-number c-info-lesson-value\">";
        // line 26
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "number", array()), "html", null, true);
        echo "</span>
        </div>
        <div class=\"c-info-txt c-teacher-txt\">
            <span class=\"text-muted\">课&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;时：</span>
            <span class=\"c-lesson-num c-info-lesson-value\">";
        // line 30
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "lessonNum", array()), "html", null, true);
        echo "课时</span>
        </div>
        <div class=\"c-course-btn pbm\">
            ";
        // line 33
        if (((isset($context["firstLessonId"]) ? $context["firstLessonId"] : null) == 0)) {
            // line 34
            echo "                ";
            $context["lessonUrl"] = $this->env->getExtension('routing')->getPath("course_learn", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array())));
            // line 35
            echo "                <a class=\"btn btn-warning\" disabled=\"disabled\" href=\"javascript:;\">暂无课程内容</a>
            ";
        } else {
            // line 37
            echo "                ";
            $context["lessonUrl"] = $this->env->getExtension('routing')->getPath("course_learn", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "#lesson" => (isset($context["firstLessonId"]) ? $context["firstLessonId"] : null)));
            // line 38
            echo "                <a class=\"btn btn-primary buy-add-btn\" id=\"course-buy-btn\" href=\"";
            echo twig_escape_filter($this->env, (isset($context["lessonUrl"]) ? $context["lessonUrl"] : null), "html", null, true);
            echo "\">开始学习</a>
            ";
        }
        // line 40
        echo "        </div>
        ";
        // line 41
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "resourcePacketId", array()) > 0)) {
            // line 42
            echo "        <div class=\"c-course-btn pbm\">
            <a class=\"btn btn-wireframe buy-download-btn\" id=\"course-resource-download-btn\" target=\"_blank\" href=\"";
            // line 43
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("/Course/CourseResourcePacket/downloadCourseResourcePacketAction", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
            echo "\">下载课程资源包</a>
        </div>
        ";
        }
        // line 46
        echo "        <div class=\"c-share-box mtm\">
            <div class=\"c-collect-txt mtm\">
                <a  id=\"favorite-btn\" href=\"javascript:\" data-url=\"";
        // line 48
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_favorite", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\"  ";
        if ((isset($context["hasFavorited"]) ? $context["hasFavorited"] : null)) {
            echo "style=\"display:none;\"";
        }
        echo "><i class=\"glyphicon glyphicon-star-empty mrs\"></i>收藏课程</a>
                <a  id=\"unfavorite-btn\" href=\"javascript:\" data-url=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_unfavorite", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\" ";
        if ((!(isset($context["hasFavorited"]) ? $context["hasFavorited"] : null))) {
            echo "style=\"display:none;\"";
        }
        echo "><i class=\"glyphicon glyphicon-star mrs\"></i>已收藏</a>
            </div>
            
        </div>
    </div>
</div>";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail-header.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  130 => 49,  122 => 48,  118 => 46,  112 => 43,  109 => 42,  107 => 41,  104 => 40,  98 => 38,  95 => 37,  91 => 35,  88 => 34,  86 => 33,  80 => 30,  73 => 26,  66 => 22,  59 => 18,  55 => 16,  47 => 11,  44 => 10,  42 => 9,  38 => 7,  34 => 5,  32 => 4,  23 => 3,  19 => 1,);
    }
}
