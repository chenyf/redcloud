<?php

/* Course/courses-block-grid.html.twig */
class __TwigTemplate_1698ebe356492aede3ce35cb81bf8d77b43469025cdc4b0fc4220d72447dd59e extends Twig_Template
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
        echo "<style>
.classroomPicture{

  width: 24px;
  height: 24px;
}
</style>
";
        // line 8
        $context["mode"] = ((array_key_exists("mode", $context)) ? (_twig_default_filter((isset($context["mode"]) ? $context["mode"] : null), "default")) : ("default"));
        // line 9
        echo "<ul class=\"course-grids clearfix\">
  ";
        // line 10
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["courses"]) ? $context["courses"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["course"]) {
            // line 11
            echo "    <li class=\"course-grid col-md-4 col-sm-6 clearfix\">
        <div class=\"course-con\">
            <div class=\"course-grid-img\">
                <a href=\"";
            // line 14
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
            echo "\" class=\"grid-body\">
                    <img src=\"";
            // line 15
            if (($this->getAttribute($context["course"], "selectPicture", array()) == "")) {
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute($context["course"], "largePicture", array()), "large"), "html", null, true);
            } else {
                echo " ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "selectPicture", array()), "html", null, true);
            }
            echo "\"  class=\"img-responsive thumb\">
                </a>
            </div>
          ";
            // line 18
            if (($this->getAttribute($context["course"], "status", array()) == "draft")) {
                // line 19
                echo "            <span class=\"label  label-warning course-status\">未发布</span>
          ";
            } elseif (($this->getAttribute($context["course"], "status", array()) == "closed")) {
                // line 21
                echo "            <span class=\"label label-danger course-status\">已关闭</span>
          ";
            }
            // line 23
            echo "
        <a href=\"";
            // line 24
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
            echo "\" class=\"title\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "title", array()), "html", null, true);
            echo "</a>
        ";
            // line 25
            if (twig_in_filter((isset($context["mode"]) ? $context["mode"] : null), array(0 => "default", 1 => "teach"))) {
                // line 26
                echo "
          ";
                // line 27
                if (($this->getAttribute($context["course"], "type", array()) == "live")) {
                    // line 28
                    echo "            ";
                    $context["lesson"] = (($this->getAttribute($context["course"], "lesson", array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute($context["course"], "lesson", array(), "array"), null)) : (null));
                    // line 29
                    echo "            ";
                    if ((isset($context["lesson"]) ? $context["lesson"] : null)) {
                        // line 30
                        echo "              <span class=\"live-course-lesson metas\">
                <span class=\"text-success mrm\">";
                        // line 31
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "startTime", array()), "n月j日 H:i"), "html", null, true);
                        echo " ~ ";
                        echo twig_escape_filter($this->env, twig_date_format_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "endTime", array()), "H:i"), "html", null, true);
                        echo "</span>
                <span class=\"text-muted mrm\">第";
                        // line 32
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "number", array()), "html", null, true);
                        echo "课程内容</span>
              </span>
            ";
                    }
                    // line 35
                    echo "          ";
                }
                // line 36
                echo "
          <span class=\"metas clearfix\">

            ";
                // line 39
                if (($this->env->getExtension('topxia_web_twig')->getSetting("course.show_student_num_enabled", "1") == 1)) {
                    // line 40
                    echo "            <span class=\"student-col\">
              <span class=\"meta-label\">";
                    // line 41
                    echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "lessonNum", array()), "html", null, true);
                    echo "课时</span>
              <span class=\"student-num\">";
                    // line 42
                    echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "studentCount", array()), "html", null, true);
                    echo "人在学</span>
            </span>
            ";
                }
                // line 45
                echo "
          </span>
        ";
            }
            // line 48
            echo "
        ";
            // line 49
            if (twig_in_filter((isset($context["mode"]) ? $context["mode"] : null), array(0 => "default"))) {
                // line 50
                echo "          ";
                $context["user"] = (($this->getAttribute((isset($context["users"]) ? $context["users"] : null), twig_first($this->env, $this->getAttribute($context["course"], "teacherIds", array())), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["users"]) ? $context["users"] : null), twig_first($this->env, $this->getAttribute($context["course"], "teacherIds", array())), array(), "array"), null)) : (null));
                // line 51
                echo "          ";
                if ((isset($context["user"]) ? $context["user"] : null)) {
                    // line 52
                    echo "            <span class=\"teacher clearfix\">
              <img src=\"";
                    // line 53
                    echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getUserDefaultPath($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()), "small"), "html", null, true);
                    echo "\" class=\"thumb\">
              <span class=\"nickname ellipsis\">";
                    // line 54
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "nickname", array()), "html", null, true);
                    echo "</span>
              ";
                    // line 56
                    echo "            </span>
          ";
                }
                // line 58
                echo "        ";
            }
            // line 59
            echo "
        ";
            // line 60
            if (twig_in_filter((isset($context["mode"]) ? $context["mode"] : null), array(0 => "learn"))) {
                // line 61
                echo "          <div class=\"learn-status\">

            ";
                // line 63
                if (($this->env->getExtension('topxia_web_twig')->isPluginInstaled("Classroom") && _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("classroom.enabled"), 0))) {
                    // line 64
                    echo "              <div class=\"mbm\">
              ";
                    // line 65
                    if (($this->getAttribute($context["course"], "classroomCount", array()) > 0)) {
                        // line 66
                        echo "                ";
                        $context["classroom"] = $this->getAttribute($context["course"], "classroom", array());
                        // line 67
                        echo "                <img class=\"classroomPicture\" src=\"";
                        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("classroomPicture", $this->getAttribute((isset($context["classroom"]) ? $context["classroom"] : null), "smallPicture", array()), ""), "html", null, true);
                        echo "\"> <span class=\"text-muted\">";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["classroom"]) ? $context["classroom"] : null), "title", array()), "html", null, true);
                        echo "
                ";
                        // line 68
                        if (($this->getAttribute($context["course"], "classroomCount", array()) > 1)) {
                            // line 69
                            echo "                等
                ";
                        }
                        // line 70
                        echo "</span>
              ";
                    }
                    // line 72
                    echo "              </div>
            ";
                }
                // line 74
                echo "

            ";
                // line 76
                if ($this->getAttribute($context["course"], "memberIsLearned", array())) {
                    // line 77
                    echo "              <div class=\"progress\">
                <div class=\"progress-bar progress-bar-success\" style=\"width: 100%;\"></div>
              </div>
              学习总时长:";
                    // line 80
                    echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "learnTime", array()), "html", null, true);
                    echo "
              <div class=\"action clearfix\"><span class=\"btn btn-default btn-sm pull-right\"><a href=\"";
                    // line 81
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
                    echo "\">查看课程</a></span></div>
            ";
                } else {
                    // line 83
                    echo "              <div class=\"progress\">
                <div class=\"progress-bar progress-bar-success\" style=\"width: ";
                    // line 84
                    echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->calculatePercent($this->getAttribute($context["course"], "memberLearnedNum", array()), $this->getAttribute($context["course"], "lessonNum", array())), "html", null, true);
                    echo "\"></div>
              </div>
              <div class=\"action\"><a href=\"";
                    // line 86
                    echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
                    echo "\" class=\"btn btn-primary btn-sm\">继续学习</a></div>
            ";
                }
                // line 88
                echo "          </div>
          ";
                // line 89
                if (($this->getAttribute($context["course"], "status", array()) == "draft")) {
                    // line 90
                    echo "            <span class=\"label  label-warning course-status\">未发布</span>
          ";
                } elseif (($this->getAttribute($context["course"], "status", array()) == "closed")) {
                    // line 92
                    echo "            <span class=\"label label-danger course-status\">已关闭</span>
          ";
                }
                // line 94
                echo "        ";
            }
            // line 95
            echo "
        ";
            // line 96
            if (((isset($context["mode"]) ? $context["mode"] : null) == "teach")) {
                // line 97
                echo "          ";
                if (($this->getAttribute($context["course"], "status", array()) == "published")) {
                    // line 98
                    echo "            <span class=\"label label-success course-status\">已发布</span>
          ";
                } elseif (($this->getAttribute($context["course"], "status", array()) == "draft")) {
                    // line 100
                    echo "            <span class=\"label  label-warning course-status\">未发布</span>
          ";
                } elseif (($this->getAttribute($context["course"], "status", array()) == "closed")) {
                    // line 102
                    echo "            <span class=\"label label-danger course-status\">已关闭</span>
          ";
                }
                // line 104
                echo "        ";
            }
            // line 105
            echo "
      
   </div>
    </li>
  ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['course'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 110
        echo "</ul>";
    }

    public function getTemplateName()
    {
        return "Course/courses-block-grid.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  283 => 110,  273 => 105,  270 => 104,  266 => 102,  262 => 100,  258 => 98,  255 => 97,  253 => 96,  250 => 95,  247 => 94,  243 => 92,  239 => 90,  237 => 89,  234 => 88,  229 => 86,  224 => 84,  221 => 83,  216 => 81,  212 => 80,  207 => 77,  205 => 76,  201 => 74,  197 => 72,  193 => 70,  189 => 69,  187 => 68,  180 => 67,  177 => 66,  175 => 65,  172 => 64,  170 => 63,  166 => 61,  164 => 60,  161 => 59,  158 => 58,  154 => 56,  150 => 54,  146 => 53,  143 => 52,  140 => 51,  137 => 50,  135 => 49,  132 => 48,  127 => 45,  121 => 42,  117 => 41,  114 => 40,  112 => 39,  107 => 36,  104 => 35,  98 => 32,  92 => 31,  89 => 30,  86 => 29,  83 => 28,  81 => 27,  78 => 26,  76 => 25,  70 => 24,  67 => 23,  63 => 21,  59 => 19,  57 => 18,  46 => 15,  42 => 14,  37 => 11,  33 => 10,  30 => 9,  28 => 8,  19 => 1,);
    }
}
