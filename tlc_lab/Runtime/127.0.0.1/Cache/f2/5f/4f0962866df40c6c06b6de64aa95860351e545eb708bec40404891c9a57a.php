<?php

/* CourseLessonManage/list-item.html.twig */
class __TwigTemplate_f25f4f0962866df40c6c06b6de64aa95860351e545eb708bec40404891c9a57a extends Twig_Template
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
        echo "<li class=\"item-lesson clearfix ";
        if ((($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "show", array()) == 0) && ((isset($context["deploy"]) ? $context["deploy"] : null) == false))) {
            echo "hide";
        }
        echo "\" id=\"lesson-";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "html", null, true);
        echo "\" data-id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "html", null, true);
        echo "\" data-pid=\"";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "chapterId", array()), "html", null, true);
        echo "\" ";
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "lessonLevel", array()) == "unit")) {
            echo "style=\"margin-left: 40px;\"";
        }
        echo ">
\t<div class=\"item-line ";
        // line 2
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "lessonLevel", array()) == "unit")) {
            echo "item-line-unit";
        } else {
            echo "item-line-chapter";
        }
        echo "\"></div>
\t<div class=\"item-content pull-left\">
\t  ";
        // line 4
        $context["mediaStatus"] = (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "mediaStatus", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "mediaStatus", array()), null)) : (null));
        // line 5
        echo "
\t\t";
        // line 6
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "video")) {
            // line 7
            echo "                    ";
            if (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "waiting")) {
                // line 8
                echo "                        <i class=\"fa fa-play-circle-o pull-left text-warning\"></i>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "doing")) {
                // line 10
                echo "                        <i class=\"fa fa-play-circle-o pull-left text-info\"></i>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "error")) {
                // line 12
                echo "                        <i class=\"fa fa-play-circle-o pull-left text-danger\"></i>
                    ";
            } else {
                // line 14
                echo "                        <i class=\"fa fa-play-circle-o pull-left text-success\"></i>
                    ";
            }
            // line 16
            echo "\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "live")) {
            // line 17
            echo "                    ";
            if (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "waiting")) {
                // line 18
                echo "                        <i class=\"fa fa-video-camera text-warning\"></i>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "doing")) {
                // line 20
                echo "                        <i class=\"fa fa-video-camera text-info\"></i>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "error")) {
                // line 22
                echo "                        <i class=\"fa fa-video-camera text-danger\"></i>
                    ";
            } else {
                // line 24
                echo "                        <i class=\"fa fa-video-camera text-success\"></i>
                    ";
            }
            // line 26
            echo "\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "audio")) {
            // line 27
            echo "                    <i class=\"fa fa-file-audio-o text-success\"></i>
\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "testpaper")) {
            // line 29
            echo "                    <i class=\"fa fa-file-text-o text-success\"></i>
\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "text")) {
            // line 31
            echo "                    <i class=\"fa fa-picture-o pull-left text-success\"></i>
\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "document")) {
            // line 33
            echo "
            ";
            // line 34
            if (($this->getAttribute((isset($context["file"]) ? $context["file"] : null), "ext", array()) == "pdf")) {
                // line 35
                echo "                    <i class=\"fa fa-file-pdf-o pull-left text-success\"></i>
            ";
            } elseif ((($this->getAttribute((isset($context["file"]) ? $context["file"] : null), "ext", array()) == "ppt") || ($this->getAttribute((isset($context["file"]) ? $context["file"] : null), "ext", array()) == "pptx"))) {
                // line 37
                echo "                    <i class=\"fa fa-powerpoint-ppt-o pull-left text-success\"></i>
            ";
            } else {
                // line 39
                echo "                    <i class=\"fa fa-file-word-o pull-left text-success\"></i>
            ";
            }
            // line 41
            echo "\t\t";
        } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "flash")) {
            // line 42
            echo "                    <i class=\"fa fa-film text-success\"></i>
\t\t";
        } else {
            // line 44
            echo "                    <i class=\"fa fa-pencil-square-o pull-left text-success\"></i>
\t\t";
        }
        // line 46
        echo "\t\t";
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "free", array()) == 1)) {
            echo "<span class=\"label label-danger prs\">免费</span>";
        }
        // line 47
        echo "\t\t
\t\t";
        // line 48
        if (((((((($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "text") && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "live")) && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "testpaper")) && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "testtask")) && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "practice")) && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "mediaId", array()) != 0)) && (!((array_key_exists("file", $context)) ? (_twig_default_filter((isset($context["file"]) ? $context["file"] : null), false)) : (false))))) {
            // line 49
            echo "                    <span class=\"label label-danger fileDeletedLesson\" title=\"课程内容文件已删除\">无效课程内容</span>
\t\t";
        }
        // line 51
        echo "\t\t";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "title", array()), "html", null, true);
        echo "

\t\t";
        // line 53
        if (twig_in_filter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()), array(0 => "video", 1 => "audio"))) {
            // line 54
            echo "                    ";
            // line 55
            echo "\t\t";
        }
        // line 56
        echo "
\t\t";
        // line 57
        if (twig_in_filter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()), array(0 => "live"))) {
            // line 58
            echo "                    <span class=\"text-muted\">";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->dataformatFilter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "startTime", array())), "html", null, true);
            echo "</span>
\t\t";
        }
        // line 60
        echo "
\t\t";
        // line 61
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "giveCredit", array()) > 0)) {
            // line 62
            echo "                    <small class=\"text-muted\">(";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "giveCredit", array()), "html", null, true);
            echo "学分)</small>
\t\t";
        }
        // line 64
        echo "
                ";
        // line 65
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "requireCredit", array()) > 0)) {
            // line 66
            echo "                    <small class=\"text-muted\">(需";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "requireCredit", array()), "html", null, true);
            echo "学分)</small>
                ";
        }
        // line 68
        echo "
                ";
        // line 69
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) != "practice")) {
            // line 70
            echo "                    ";
            $context["mediaTypeName"] = ((($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "video")) ? ("视频") : (((($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "audio")) ? ("音频") : ("文件"))));
            // line 71
            echo "                    ";
            if (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "waiting")) {
                // line 72
                echo "                        <span class=\"text-warning\">(正在等待";
                echo twig_escape_filter($this->env, (isset($context["mediaTypeName"]) ? $context["mediaTypeName"] : null), "html", null, true);
                echo "格式转换)</span>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "doing")) {
                // line 74
                echo "                        <span class=\"text-info\">(正在转换";
                echo twig_escape_filter($this->env, (isset($context["mediaTypeName"]) ? $context["mediaTypeName"] : null), "html", null, true);
                echo "格式)</span>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "error")) {
                // line 76
                echo "                        <span class=\"text-danger\">(";
                echo twig_escape_filter($this->env, (isset($context["mediaTypeName"]) ? $context["mediaTypeName"] : null), "html", null, true);
                echo "格式转换失败";
                if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "document")) {
                    echo "<a href=\"javascript:;\" style=\"color:#a94442;text-decoration:none\"  class=\"document-new-convert\" data-url=\"";
                    echo $this->env->getExtension('routing')->getPath("document_convert");
                    echo "\" data-fileid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "mediaId", array()), "html", null, true);
                    echo "\">【重新转码】</a>";
                }
                echo ")</span>
                    ";
            } elseif (((isset($context["mediaStatus"]) ? $context["mediaStatus"] : null) == "del")) {
                // line 78
                echo "                        <span class=\"text-danger\">(";
                echo twig_escape_filter($this->env, (isset($context["mediaTypeName"]) ? $context["mediaTypeName"] : null), "html", null, true);
                echo "已删除)</span>
                    ";
            }
            // line 80
            echo "                ";
        }
        // line 81
        echo "
                ";
        // line 82
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "status", array()) == "unpublished")) {
            // line 83
            echo "                    <span class=\"unpublish-warning text-warning\">(未发布)</span>
                ";
        }
        // line 85
        echo "\t</div>

\t<div class=\"item-actions\">
           ";
        // line 88
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "practice")) {
            // line 89
            echo "                ";
            if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "testCount", array()) != $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "itemCount", array()))) {
                // line 90
                echo "                <a class=\"btn btn-link\" href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseTestpaperManage/createCourseLessonQuestions", array("id" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\"><span class=\"glyphicon glyphicon-edit prs\"></span>编辑</a>
                ";
            }
            // line 92
            echo "           ";
        } else {
            // line 93
            echo "                <a class=\"btn btn-link\" data-toggle=\"modal\" data-target=\"#modal\" data-backdrop=\"static\" data-keyboard=\"false\"
\t\t\t";
            // line 94
            if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "testpaper")) {
                // line 95
                echo "                            data-url=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_edit_testpaper", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\"
\t\t\t";
            } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "testtask")) {
                // line 97
                echo "                            data-url=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_edit_testpaper", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "type" => 1, "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\"
                        ";
                // line 100
                echo "\t\t\t";
            } elseif (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "live")) {
                // line 101
                echo "                            data-url=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("live_course_manage_lesson_edit", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\"
\t\t\t";
            } else {
                // line 103
                echo "                            data-url=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_edit", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
                echo "\"
\t\t\t";
            }
            // line 105
            echo "                ><span class=\"glyphicon glyphicon-pencil\"></span>编辑</a>
           ";
        }
        // line 107
        echo "            
            ";
        // line 108
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "practice")) {
            // line 109
            echo "                <a class=\"btn btn-link\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_preview_test", array("testId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "mediaId", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\" target=\"_blank\"><span class=\"glyphicon glyphicon-eye-open prs\"></span>预览</a>
            ";
        } else {
            // line 111
            echo "                <a class=\"btn btn-link\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_learn", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "preview" => 1, "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "#lesson/";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "html", null, true);
            echo "\" target=\"_blank\"><span class=\"glyphicon glyphicon-eye-open prs\"></span>预览</a>
            ";
        }
        // line 113
        echo "
            <a href=\"javascript:;\" class=\"publish-lesson-btn btn btn-link ";
        // line 114
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "status", array()) == "published")) {
            echo "hidden ";
        }
        echo " ";
        if ((($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "practice") && ($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "testCount", array()) != $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "itemCount", array())))) {
            echo "hidden ";
        }
        echo "\" data-url=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_publish", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        // line 115
        echo "\">
                <span class=\"glyphicon glyphicon-ok-circle prs\"></span>发布
            </a>

            <a href=\"javascript:;\" class=\"unpublish-lesson-btn btn btn-link  ";
        // line 119
        if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "status", array()) == "unpublished")) {
            echo "hidden ";
        }
        echo "\" data-url=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_unpublish", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\">
                <span class=\"glyphicon glyphicon-remove-circle prs\"></span>取消发布
            </a>

            <a href=\"javascript:;\" class=\"delete-lesson-btn btn btn-link\" data-url=\"";
        // line 123
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_delete", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\">
                <span class=\"glyphicon glyphicon-trash prs\"></span>删除
            </a>
\t</div>
</li>";
    }

    public function getTemplateName()
    {
        return "CourseLessonManage/list-item.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  354 => 123,  343 => 119,  337 => 115,  327 => 114,  324 => 113,  316 => 111,  310 => 109,  308 => 108,  305 => 107,  301 => 105,  295 => 103,  289 => 101,  286 => 100,  281 => 97,  275 => 95,  273 => 94,  270 => 93,  267 => 92,  261 => 90,  258 => 89,  256 => 88,  251 => 85,  247 => 83,  245 => 82,  242 => 81,  239 => 80,  233 => 78,  219 => 76,  213 => 74,  207 => 72,  204 => 71,  201 => 70,  199 => 69,  196 => 68,  190 => 66,  188 => 65,  185 => 64,  179 => 62,  177 => 61,  174 => 60,  168 => 58,  166 => 57,  163 => 56,  160 => 55,  158 => 54,  156 => 53,  150 => 51,  146 => 49,  144 => 48,  141 => 47,  136 => 46,  132 => 44,  128 => 42,  125 => 41,  121 => 39,  117 => 37,  113 => 35,  111 => 34,  108 => 33,  104 => 31,  100 => 29,  96 => 27,  93 => 26,  89 => 24,  85 => 22,  81 => 20,  77 => 18,  74 => 17,  71 => 16,  67 => 14,  63 => 12,  59 => 10,  55 => 8,  52 => 7,  50 => 6,  47 => 5,  45 => 4,  36 => 2,  19 => 1,);
    }
}
