<?php

/* @Course/CourseManage/course-manage-header.html.twig */
class __TwigTemplate_3f201a93c504cca1cc04aa565ffd5030ed5e184d47824a3c392977ee3c2aacce extends Twig_Template
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
        echo "<div class=\"panel panel-default panel-col lesson-manage-panel\" >
<div class=\"c-panel-heading\">
    <div id=\"t-toggle\" class=\"t-toggle-btn pull-right mlm\" data-toggle=\"collapse\" data-target=\"#t-collapseExample\" aria-expanded=\"false\" aria-controls=\"t-collapseExample\">
        <i class=\"fa fa-angle-double-down\"></i>
        <!-- <i class=\"fa fa-angle-double-down\"></i> -->
    </div>
    <a class=\"cc-cancel pull-right text-muted\" href=\"";
        // line 7
        echo $this->env->getExtension('routing')->getPath("my_teaching_courses");
        echo "\"><i class=\"fa fa-reply mrs\"></i>返回在教课程</a>
    <span>";
        // line 8
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo "</span>
</div>

<div class=\"t-course-edit-top collapse\" id=\"t-collapseExample\">
    ";
        // line 12
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()) && $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))) {
            // line 13
            echo "        ";
            $context["banShow"] = 1;
            // line 14
            echo "    ";
        }
        // line 15
        echo "        
        <div class=\"row t-edit-top-con\">
            <div class=\"col-md-6\">
                <div class=\"t-course-pic\">
                    <a href=\"";
        // line 19
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">
                        <img class=\"course-picture\" onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  loaderrimg=\"1\"  id=\"headerPicture\" ";
        // line 20
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == "")) {
            echo "src=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "largePicture", array()), "large"), "html", null, true);
            echo "\" ";
        } else {
            echo " src=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), "html", null, true);
            echo "\" ";
        }
        echo " alt=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo "\" width=\"100%\">
                    </a>
                    <span class=\"cc-mask\">
                        <a href=\"javascript:;\"  data-url=\"/Course/CourseManage/pictureAutoAction/id/";
        // line 23
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "/edit/1/tab/3/\" data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\">
                            <i class=\"glyphicon glyphicon-pencil prs\"></i>编辑
                        </a>
                    </span>
                </div>
            </div>
            <div class=\"col-md-6\">
                <div class=\"t-course-text t-course-name\">
                    <div class=\"text-muted col-md-3\">课程名称：</div>
                    <div class=\"col-md-9 c-text-name\">
                        ";
        // line 33
        if (((isset($context["banShow"]) ? $context["banShow"] : null) != 1)) {
            // line 34
            echo "                            <a href=\"javascript:;\" class=\"c-icon-edit pull-right\">
                                <i class=\"glyphicon glyphicon-pencil prs\"></i>编辑
                            </a>
                            <input class=\"form-control\" style=\"display:none;width:70%;\" type=\"text\" value=\"";
            // line 37
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
            echo "\" data-url=\"";
            echo $this->env->getExtension('routing')->getPath("course_manage_edit_title");
            echo "\" data-id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
            echo "\"/>
                        ";
        }
        // line 39
        echo "                        <span style=\"word-wrap:break-word;word-break:break-all;\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo "</span>
                    </div>
                </div>
                <div class=\"t-course-text t-course-specialty\">
                    <div class=\"text-muted col-md-3\">课程分类：</div>
                    <div class=\"col-md-9 c-text-specialty\">
                        ";
        // line 45
        if (((isset($context["banShow"]) ? $context["banShow"] : null) != 1)) {
            // line 46
            echo "                        <a data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\" data-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseManage/courseCategory", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
            echo "\" class=\"c-icon-edit pull-right\">
                            <i class=\"glyphicon glyphicon-pencil prs\"></i>编辑
                        </a>
                        ";
        }
        // line 50
        echo "                        <input type=\"hidden\" name=\"categoryIds\" value=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->courseCategoryIds($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array())), "html", null, true);
        echo "\" />
                        ";
        // line 52
        echo "                        ";
        $context["course_category"] = $this->env->getExtension('topxia_web_twig')->getCourseCategory($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "categoryId", array()));
        // line 53
        echo "                        <div id=\"course-category-name\" class=\"c-specialty-con\">
                            <em data-id=\"";
        // line 54
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "id", array()), "html", null, true);
        echo "\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "name", array()), "html", null, true);
        echo "</em>
                            <div class=\"more-specialty\">
                                <span>更多课程分类<i class=\"fa fa-arrow-circle-o-down mls\"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class=\"t-course-btn mtl\">
                    ";
        // line 62
        $context["publishurl"] = $this->env->getExtension('routing')->getPath("course_manage_publish", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array())));
        // line 63
        echo "                    ";
        $context["closeurl"] = $this->env->getExtension('routing')->getPath("course_manage_close", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array())));
        // line 64
        echo "                    <input type=\"hidden\" id=\"publishurl\" value=\"";
        echo twig_escape_filter($this->env, (isset($context["publishurl"]) ? $context["publishurl"] : null), "html", null, true);
        echo "\">
                    <input type=\"hidden\" id=\"closeurl\" value=\"";
        // line 65
        echo twig_escape_filter($this->env, (isset($context["closeurl"]) ? $context["closeurl"] : null), "html", null, true);
        echo "\">
                    <a class=\"btn btn-primary mrm c-btn-lg\" id=\"courseStatus\" href=\"javascript:void()\" data-publish=\"";
        // line 66
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "status", array()) != "published")) {
            echo "0";
        } else {
            echo "1";
        }
        echo "\">";
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "status", array()) != "published")) {
            echo "发布课程";
        } else {
            echo "关闭课程";
        }
        echo "</a>
                </div>
            </div>
        </div>
</div>
    </div>";
    }

    public function getTemplateName()
    {
        return "@Course/CourseManage/course-manage-header.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  159 => 66,  155 => 65,  150 => 64,  147 => 63,  145 => 62,  132 => 54,  129 => 53,  126 => 52,  121 => 50,  113 => 46,  111 => 45,  101 => 39,  92 => 37,  87 => 34,  85 => 33,  72 => 23,  56 => 20,  52 => 19,  46 => 15,  43 => 14,  40 => 13,  38 => 12,  31 => 8,  27 => 7,  19 => 1,);
    }
}
