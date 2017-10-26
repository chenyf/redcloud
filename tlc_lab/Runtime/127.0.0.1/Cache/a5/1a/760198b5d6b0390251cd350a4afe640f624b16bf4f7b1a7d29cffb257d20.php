<?php

/* CourseChapterManage/list-item.html.twig */
class __TwigTemplate_a51a760198b5d6b0390251cd350a4afe640f624b16bf4f7b1a7d29cffb257d20 extends Twig_Template
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
        echo "<li class=\"item-chapter ";
        if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
            echo "item-chapter-unit";
        } else {
            echo "item-chapter-chapter";
        }
        echo " clearfix ";
        if ((($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "show", array()) == 0) && ((isset($context["deploy"]) ? $context["deploy"] : null) == false))) {
            echo "hide";
        }
        echo " c-item-chapter\" id=\"chapter-";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "html", null, true);
        echo "\" data-id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "html", null, true);
        echo "\" data-pid=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "parentId", array()), "html", null, true);
        echo "\">
    ";
        // line 2
        if (((($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "parentId", array()) == 0) && $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "child", array())) && ((isset($context["deploy"]) ? $context["deploy"] : null) == false))) {
            // line 3
            echo "        <span class=\"pull-right lesson-hour\" style=\"color: #666;padding-right: 10px;\"></span>
        <em class=\"deploy pull-right fa ";
            // line 4
            if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "seq", array()) == 1)) {
                echo "fa-minus ";
            } else {
                echo "fa-plus ";
            }
            echo " \"></em>
    ";
        } elseif (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "parentId", array()) == 0)) {
            // line 6
            echo "        <span class=\"pull-right lesson-hour\" style=\"color: #666;padding-right: 10px;\">0</span>
    ";
        }
        // line 8
        echo "    ";
        if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
            echo "<div class=\"item-line item-line-chapter\"></div>";
        }
        // line 9
        echo "    <div class=\"item-content pull-left\">
        ";
        // line 10
        if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
            echo "<i class=\"fa fa-file-text-o pull-left\"></i> ";
        } else {
            echo " <i class=\"fa fa-list-ul deploy pull-left\"></i>";
        }
        echo "第 <span class=\"number\">";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "number", array()), "html", null, true);
        echo "</span>";
        if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name"), "节"), "html", null, true);
            } else {
                echo "节";
            }
        } else {
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name"), "章"), "html", null, true);
            } else {
                echo "章";
            }
        }
        echo "： ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "title", array()), "html", null, true);
        echo "
    </div>
    <div class=\"item-actions prs\">
        <div class=\"btn-group\">
            <button class=\"btn btn-link dropdown-toggle\"   data-toggle=\"dropdown\"><i class=\"glyphicon glyphicon-plus-sign\"></i>添加</button>
            ";
        // line 15
        if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
            // line 16
            echo "                <ul class=\"dropdown-menu\" role=\"menu\" style=\"top:29px\">
                    <li>
                        <a class=\"chapter-create-class\" href=\"#\" id=\"chapter-create-btn\" data-toggle=\"modal\" data-target=\"#modal\" data-backdrop=\"static\" data-keyboard=\"false\" data-url=\"";
            // line 18
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "type", array()) == "normal")) {
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            } else {
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("live_course_manage_lesson_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            }
            echo "\"><i class=\"glyphicon glyphicon-plus\"></i> 添加 课程内容 </a>
                    </li>
                    ";
            // line 20
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "type", array()) == "normal")) {
                // line 21
                echo "                    <li>
                        <a class=\"chapter-create-class\" id=\"chapter-create-btn\"  data-toggle=\"modal\" data-target=\"#modal\" data-url=\"";
                // line 22
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("/Course/CourseTestpaperManage/createCourseLessonAction", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "type" => 2, "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\" ><i class=\"glyphicon glyphicon-plus\"></i> 练习</a>
                    </li>
                    ";
            }
            // line 25
            echo "                </ul>
             ";
        } else {
            // line 27
            echo "                <ul class=\"dropdown-menu\" role=\"menu\" style=\"top:29px\">
                    <li>
                        <a class=\"chapter-create-class\" href=\"#\" id=\"chapter-create-btn\" data-id=\"";
            // line 29
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "html", null, true);
            echo "\" data-toggle=\"modal\" data-target=\"#modal\" data-backdrop=\"static\" data-keyboard=\"false\" data-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "type" => "unit", "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\"><i class=\"glyphicon glyphicon-plus\"></i> 添加 ";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name"), "节"), "html", null, true);
            } else {
                echo "节";
            }
            echo " </a>
                    </li>
                    <li>
                        <a class=\"chapter-create-class\" href=\"#\" id=\"chapter-create-btn\" data-toggle=\"modal\" data-target=\"#modal\" data-backdrop=\"static\" data-keyboard=\"false\" data-url=\"";
            // line 32
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "type", array()) == "normal")) {
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            } else {
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("live_course_manage_lesson_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            }
            echo "\"><i class=\"glyphicon glyphicon-plus\"></i> ";
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "type", array()) == "normal")) {
                echo " 添加 课程内容 ";
            } else {
                echo " 添加 直播课程内容 ";
            }
            echo " </a>
                     </li>
                    ";
            // line 34
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "type", array()) == "normal")) {
                // line 35
                echo "                    <li>
                        <a class=\"chapter-create-class\" id=\"chapter-create-btn\" data-toggle=\"modal\" data-target=\"#modal\" data-url=\"";
                // line 36
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("/Course/CourseTestpaperManage/createCourseLessonAction", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "type" => 2, "parentId" => ("chapter-" . $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array())), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\" ><i class=\"glyphicon glyphicon-plus\"></i> 练习</a>
                    </li>
                    ";
            }
            // line 39
            echo "                </ul>
            ";
        }
        // line 40
        echo "\t
        </div>
        <button class=\"btn btn-link\"  data-toggle=\"modal\" data-target=\"#modal\" data-keyboard=\"false\" data-url=\"";
        // line 42
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_edit", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "chapterId" => $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\"><i class=\"glyphicon glyphicon-pencil\"></i>编辑</button>
        <button class=\"btn btn-link delete-chapter-btn\"  data-url=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_delete", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "chapterId" => $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\" data-chapter=\"";
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "chapter_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "chapter_name", array()), "章")) : ("章")), "html", null, true);
        echo "\" data-part=\"";
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "part_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "part_name", array()), "节")) : ("节")), "html", null, true);
        // line 44
        echo "\"><i class=\"glyphicon glyphicon-trash\"></i>删除</button>
    </div>
</li>";
    }

    public function getTemplateName()
    {
        return "CourseChapterManage/list-item.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  186 => 44,  180 => 43,  176 => 42,  172 => 40,  168 => 39,  162 => 36,  159 => 35,  157 => 34,  142 => 32,  128 => 29,  124 => 27,  120 => 25,  114 => 22,  111 => 21,  109 => 20,  100 => 18,  96 => 16,  94 => 15,  64 => 10,  61 => 9,  56 => 8,  52 => 6,  43 => 4,  40 => 3,  38 => 2,  19 => 1,);
    }
}
