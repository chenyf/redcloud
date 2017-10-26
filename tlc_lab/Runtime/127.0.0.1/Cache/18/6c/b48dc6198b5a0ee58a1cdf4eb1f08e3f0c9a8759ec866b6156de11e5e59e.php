<?php

/* @Course/CourseManage/courseLayout.html.twig */
class __TwigTemplate_186cb48dc6198b5a0ee58a1cdf4eb1f08e3f0c9a8759ec866b6156de11e5e59e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@My/My/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'main' => array($this, 'block_main'),
            'side' => array($this, 'block_side'),
            'courseContent' => array($this, 'block_courseContent'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@My/My/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["side_nav"] = "my-teaching-courses";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "在教课程 - 基本信息 - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 4
    public function block_main($context, array $blocks = array())
    {
        echo "   
       
        ";
        // line 6
        $this->env->loadTemplate("@Course/CourseManage/course-manage-header.html.twig")->display($context);
        // line 7
        echo "        ";
        // line 8
        echo "            <div class=\"reload-container\">
                <div class=\"panel\" id=\"panel-body\">
                    <span class=\"controller-js hidden\" data-name=\"";
        // line 10
        echo twig_escape_filter($this->env, (isset($context["script_controller"]) ? $context["script_controller"] : null), "html", null, true);
        echo "\"></span>
                    <div class=\"course-list-tit t-course-set-tit\">
                            <div class=\"tabs-screen\">
                                <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\"#nav-content\" aria-expanded=\"false\" aria-controls=\"nav-content\">
                                    <span class=\"icon-bar\"></span>
                                    <span class=\"icon-bar\"></span>
                                    <span class=\"icon-bar\"></span>
                                </button>
                                <ul class=\"nav nav-tabs collapse in\" id=\"nav-content\">
                                    <li ";
        // line 19
        if (((isset($context["menu"]) ? $context["menu"] : null) == "lesson")) {
            echo "class=\"active\"";
        }
        echo "><a class=\"ajaxLoad\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseLessonManage/index", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">课程内容</a></li>
                                    <li ";
        // line 20
        if (((isset($context["menu"]) ? $context["menu"] : null) == "question")) {
            echo "class=\"active\"";
        }
        echo "><a class=\"ajaxLoad\"  href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseQuestionManage/index", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">题库管理</a></li>
                                    <li ";
        // line 21
        if (((isset($context["menu"]) ? $context["menu"] : null) == "base")) {
            echo "class=\"active\"";
        }
        echo "><a class=\"ajaxLoad\"  href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseManage/base", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">课程信息</a></li>
                                    <li ";
        // line 22
        if (((isset($context["menu"]) ? $context["menu"] : null) == "courseResource")) {
            echo "class=\"active\"";
        }
        echo "><a class=\"ajaxLoad\"  href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseResource/index", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">课程资料</a></li>
                                    <li ";
        // line 23
        if (((isset($context["menu"]) ? $context["menu"] : null) == "resourcePacket")) {
            echo "class=\"active\"";
        }
        echo "><a class=\"ajaxLoad\"  href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseResourcePacket/index", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">课程资源包</a></li>
                                </ul>
                            </div>
                            
                    </div>
                    <div class=\"panel-body\" id=\"panel-main-box\">
                        ";
        // line 29
        $this->displayBlock('side', $context, $blocks);
        // line 30
        echo "                        ";
        $this->displayBlock('courseContent', $context, $blocks);
        // line 31
        echo "                    </div>
                </div>
            </div>
            ";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->loadScript("course-manage/course-layout"), "html", null, true);
        echo "  

";
    }

    // line 29
    public function block_side($context, array $blocks = array())
    {
        echo " ";
    }

    // line 30
    public function block_courseContent($context, array $blocks = array())
    {
        echo "  ";
    }

    public function getTemplateName()
    {
        return "@Course/CourseManage/courseLayout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  134 => 30,  128 => 29,  121 => 34,  116 => 31,  113 => 30,  111 => 29,  98 => 23,  90 => 22,  82 => 21,  74 => 20,  66 => 19,  54 => 10,  50 => 8,  48 => 7,  46 => 6,  40 => 4,  33 => 3,  28 => 1,);
    }
}
