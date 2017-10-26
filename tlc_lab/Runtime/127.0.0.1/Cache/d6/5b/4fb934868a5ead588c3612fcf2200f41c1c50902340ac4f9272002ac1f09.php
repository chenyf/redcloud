<?php

/* CourseLessonManage/lesson-modal.html.twig */
class __TwigTemplate_d65b4fb934868a5ead588c3612fcf2200f41c1c50902340ac4f9272002ac1f09 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/bootstrap-modal-layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/bootstrap-modal-layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["modal_class"] = "modal-lg";
        // line 4
        $context["lesson"] = ((array_key_exists("lesson", $context)) ? (_twig_default_filter((isset($context["lesson"]) ? $context["lesson"] : null), null)) : (null));
        // line 119
        $context["hideFooter"] = true;
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 7
    public function block_title($context, array $blocks = array())
    {
        // line 8
        echo "    ";
        if ((isset($context["lesson"]) ? $context["lesson"] : null)) {
            echo "编辑课程内容";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "number", array()), "html", null, true);
        } else {
            echo "添加课程内容";
        }
    }

    // line 11
    public function block_body($context, array $blocks = array())
    {
        // line 12
        echo "
<form id=\"course-lesson-form\" data-course-id=\"";
        // line 13
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "\" class=\"form-horizontal lesson-form\" method=\"post\" ";
        if (array_key_exists("parentId", $context)) {
            echo "data-parentId=\"";
            echo twig_escape_filter($this->env, (isset($context["parentId"]) ? $context["parentId"] : null), "html", null, true);
            echo "\" ";
        }
        // line 14
        echo "  data-create-draft-url=\"";
        echo $this->env->getExtension('routing')->getPath("course_draft_create");
        echo "\"
  ";
        // line 15
        if ((isset($context["lesson"]) ? $context["lesson"] : null)) {
            // line 16
            echo "    action=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_edit", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "lessonId" => $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()))), "html", null, true);
            echo "\" data-lesson-id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "id", array()), "html", null, true);
            echo "\"
  ";
        } else {
            // line 18
            echo "    action=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
            echo "\"
  ";
        }
        // line 20
        echo "  >

    <div class=\"form-group\">
        <div class=\"col-md-2 control-label\"><label>类型</label></div>
        <div class=\"col-md-9 controls\">
            <div class=\"radios\">
                ";
        // line 26
        if ((isset($context["lesson"]) ? $context["lesson"] : null)) {
            // line 27
            echo "                    ";
            if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "video")) {
                echo "<label><input type=\"radio\" name=\"type\" value=\"video\" checked=\"checked\"> 视频</label>";
            }
            // line 28
            echo "                    ";
            if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "document")) {
                echo "<label><input type=\"radio\" name=\"type\" value=\"document\" checked=\"checked\"> 文档</label>";
            }
            // line 29
            echo "                    ";
            if (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "type", array()) == "text")) {
                echo "<label><input type=\"radio\" name=\"type\" value=\"text\" checked=\"checked\"> 图文</label>";
            }
            // line 30
            echo "                ";
        } else {
            // line 31
            echo "                    <label><input type=\"radio\" name=\"type\" value=\"video\" checked=\"checked\"> 视频</label>
                    <label><input type=\"radio\" name=\"type\" value=\"document\"> 文档</label>
                    <label><input type=\"radio\" name=\"type\" value=\"text\"> 图文</label>
                ";
        }
        // line 35
        echo "                ";
        // line 36
        echo "            </div>
        </div>
    </div>

    <div class=\"form-group loading-tip\" style=\"text-align: center;font-size: 18px;\">加载中，请稍候...</div>

    <div class=\"form-group for-text-type for-video-type for-audio-type for-document-type \" >
        <div class=\"col-md-2 control-label\"><label for=\"lesson-title-field\">标题</label></div>
        <div class=\"col-md-9 controls\">
            <div class=\"row\">
                <div class=\"col-md-12\">
                    <input id=\"lesson-title-field\" class=\"form-control\" type=\"text\" name=\"title\" value=\"";
        // line 47
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["lesson"]) ? $context["lesson"] : null), "title");
        echo "\" >
                </div>
            </div>
        </div>
    </div>

    <div class=\"form-group for-text-type for-video-type for-audio-type  for-document-type \">
        <div class=\"col-md-2 control-label\"><label for=\"lesson-summary-field\">摘要</label></div>
        <div class=\"col-md-9 controls\">
            <textarea class=\"form-control\" id=\"lesson-summary-field\" name=\"summary\" >";
        // line 56
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["lesson"]) ? $context["lesson"] : null), "summary");
        echo "</textarea>
        </div>
    </div>

    <div class=\"form-group for-text-type\">
        <div class=\"col-md-2 control-label\"><label for=\"lesson-content-field\" class=\"style1\">内容</label></div>
        <div class=\"col-md-9 controls\">
            ";
        // line 63
        if (((array_key_exists("draft", $context)) ? (_twig_default_filter((isset($context["draft"]) ? $context["draft"] : null), false)) : (false))) {
            // line 64
            echo "                <a id =\"see-draft-btn\" class=\"btn btn-link\" data-url=\"";
            echo $this->env->getExtension('routing')->getPath("course_draft_view");
            echo "\" >
                  <small>您有一段自动保存内容，继续编辑请点击</small>
                </a>
            ";
        }
        // line 68
        echo "            <textarea class=\"form-control\" id=\"lesson-content-field\" name=\"content\"
            data-image-upload-url=\"";
        // line 69
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("editor_upload", array("token" => $this->env->getExtension('topxia_web_twig')->makeUpoadToken("course"))), "html", null, true);
        echo "\"
            data-flash-upload-url=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("editor_upload", array("token" => $this->env->getExtension('topxia_web_twig')->makeUpoadToken("course", "flash"))), "html", null, true);
        echo "\"
            >";
        // line 71
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "content", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "content", array()), "")) : ("")), "html", null, true);
        echo "</textarea>
        </div>
    </div>

    <div class=\"form-group for-video-type for-audio-type for-document-type  \">
        <div class=\"col-md-2 control-label for-video-type\"><label>视频</label></div>
        <div class=\"col-md-2 control-label for-audio-type\"><label>音频</label></div>
        <!-- <div class=\"col-md-2 control-label for-ppt-type\"><label>PPT</label></div>
        <div class=\"col-md-2 control-label for-flash-type\"><label>Flash</label></div>-->
        <div class=\"col-md-2 control-label for-document-type\"><label>文档</label></div>
        <div class=\"col-md-9 controls\">
            ";
        // line 82
        $this->env->loadTemplate("@Course/CourseLessonManage/media-choose.html.twig")->display($context);
        // line 83
        echo "            ";
        if ((!(isset($context["lesson"]) ? $context["lesson"] : null))) {
            // line 84
            echo "                <input type=\"hidden\" name=\"chapter-id\" value=\"";
            if (array_key_exists("parentId", $context)) {
                echo twig_escape_filter($this->env, (isset($context["parentId"]) ? $context["parentId"] : null), "html", null, true);
            }
            echo "\"/>
            ";
        }
        // line 86
        echo "            <input id=\"lesson-media-field\" type=\"hidden\" name=\"media\" value=\"";
        echo twig_escape_filter($this->env, twig_jsonencode_filter($this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["lesson"]) ? $context["lesson"] : null), "media")), "html", null, true);
        echo "\">
            <input type=\"hidden\" name=\"polyvVid\" value=\"";
        // line 87
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["lesson"]) ? $context["lesson"] : null), "polyvVid");
        echo "\"/>
            <input type=\"hidden\" name=\"polyvVideoSize\" value=\"";
        // line 88
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["lesson"]) ? $context["lesson"] : null), "polyvVideoSize", 0);
        echo "\"/>
        </div>
    </div>

    ";
        // line 92
        if (twig_in_filter("lesson_credit", (isset($context["features"]) ? $context["features"] : null))) {
            // line 93
            echo "        <div class=\"form-group for-text-type for-video-type for-audio-type for-ppt-type\">
            <div class=\"col-md-2 control-label\"><label for=\"lesson-give-credit-field\">学分</label></div>
            <div class=\"col-md-9 controls\">
                <input class=\"form-control width-input width-input-small\" id=\"lesson-give-credit-field\" type=\"text\" name=\"giveCredit\" value=\"";
            // line 96
            echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "giveCredit", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["lesson"]) ? $context["lesson"] : null), "giveCredit", array()), 0)) : (0)), "html", null, true);
            echo "\"> 分
                <div class=\"help-block\">学完此课程内容，可获得的学分</div>
            </div>
        </div>
    ";
        }
        // line 101
        echo "
    <input type=\"hidden\" name=\"_csrf_token\" value=\"";
        // line 102
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderCsrfToken("site"), "html", null, true);
        echo "\">
    <input type=\"hidden\" name=\"center\" value=\"";
        // line 103
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"), "html", null, true);
        echo "\">

</form>
 

<script>app.load('course-manage/lesson-modal')</script>



";
    }

    // line 114
    public function block_footer($context, array $blocks = array())
    {
        // line 115
        echo "    <button type=\"button\" class=\"btn btn-link\" data-dismiss=\"modal\" id=\"cancel-btn\">取消</button>
    <button id=\"course-lesson-btn\" disabled=\"disabled\" data-submiting-text=\"正在提交\" type=\"submit\" class=\"btn btn-primary\" data-toggle=\"form-submit\" data-target=\"#course-lesson-form\">";
        // line 116
        if ((isset($context["lesson"]) ? $context["lesson"] : null)) {
            echo "保存";
        } else {
            echo "添加";
        }
        echo "</button>
";
    }

    public function getTemplateName()
    {
        return "CourseLessonManage/lesson-modal.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  261 => 116,  258 => 115,  255 => 114,  241 => 103,  237 => 102,  234 => 101,  226 => 96,  221 => 93,  219 => 92,  212 => 88,  208 => 87,  203 => 86,  195 => 84,  192 => 83,  190 => 82,  176 => 71,  172 => 70,  168 => 69,  165 => 68,  157 => 64,  155 => 63,  145 => 56,  133 => 47,  120 => 36,  118 => 35,  112 => 31,  109 => 30,  104 => 29,  99 => 28,  94 => 27,  92 => 26,  84 => 20,  78 => 18,  70 => 16,  68 => 15,  63 => 14,  55 => 13,  52 => 12,  49 => 11,  39 => 8,  36 => 7,  31 => 119,  29 => 4,  27 => 3,);
    }
}
