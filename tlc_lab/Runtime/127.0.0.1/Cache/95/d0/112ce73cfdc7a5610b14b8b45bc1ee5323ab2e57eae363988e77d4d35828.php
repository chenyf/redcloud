<?php

/* CourseChapterManage/chapter-modal.html.twig */
class __TwigTemplate_95d0112ce73cfdc7a5610b14b8b45bc1ee5323ab2e57eae363988e77d4d35828 extends Twig_Template
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
        $context["chapter"] = ((array_key_exists("chapter", $context)) ? (_twig_default_filter((isset($context["chapter"]) ? $context["chapter"] : null), null)) : (null));
        // line 54
        $context["hideFooter"] = true;
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        // line 6
        echo "  ";
        if ((isset($context["chapter"]) ? $context["chapter"] : null)) {
            echo "编辑";
        } else {
            echo "添加";
        }
        if (((isset($context["type"]) ? $context["type"] : null) == "unit")) {
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
    }

    // line 8
    public function block_body($context, array $blocks = array())
    {
        // line 9
        echo "
<form id=\"course-chapter-form\" class=\"form-horizontal\" method=\"post\" ";
        // line 10
        if (array_key_exists("parentId", $context)) {
            echo "data-parentId=\"";
            echo twig_escape_filter($this->env, (isset($context["parentId"]) ? $context["parentId"] : null), "html", null, true);
            echo "\" ";
        }
        // line 11
        echo "  ";
        if ((isset($context["chapter"]) ? $context["chapter"] : null)) {
            // line 12
            echo "\t  action=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_edit", array("courseId" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "chapterId" => $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()))), "html", null, true);
            echo "\"
\t";
        } else {
            // line 14
            echo "\t  action=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
            echo "\"
  ";
        }
        // line 16
        echo "  >
  ";
        // line 18
        echo "  <div class=\"row form-group\">
    <div class=\"col-md-3 control-label\">
      ";
        // line 20
        if (((isset($context["type"]) ? $context["type"] : null) == "unit")) {
            // line 21
            echo "        <label for=\"chapter-title-field\">";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name"), "节"), "html", null, true);
            } else {
                echo "节";
            }
            echo "标题</label>
      ";
        } else {
            // line 23
            echo "        <label for=\"chapter-title-field\">";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name"), "章"), "html", null, true);
            } else {
                echo "章";
            }
            echo "标题</label>
        ";
        }
        // line 25
        echo "    </div>
    <div class=\"col-md-8 controls\"><input id=\"chapter-title-field\" type=\"text\" name=\"title\" value=\"";
        // line 26
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["chapter"]) ? $context["chapter"] : null), "title");
        echo "\" class=\"form-control\"></div>
  </div>
  ";
        // line 29
        echo "  <div class=\"row form-group\">
    <div class=\"col-md-3 control-label\">
      ";
        // line 31
        if (((isset($context["type"]) ? $context["type"] : null) == "unit")) {
            // line 32
            echo "        <label for=\"chapter-title-field\">";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name"), "节"), "html", null, true);
            } else {
                echo "节";
            }
            echo "描述</label>
      ";
        } else {
            // line 34
            echo "        <label for=\"chapter-title-field\">";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name")) {
                echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name"), "章"), "html", null, true);
            } else {
                echo "章";
            }
            echo "描述</label>
      ";
        }
        // line 36
        echo "    </div>
    <div class=\"col-md-8 controls\">
      <textarea class=\"form-control\" id=\"chapter-desc-field\" name=\"description\" >";
        // line 38
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["chapter"]) ? $context["chapter"] : null), "description");
        echo "</textarea>
    </div>
  </div>
  <input type=\"hidden\" name=\"type\" value=\"";
        // line 41
        echo twig_escape_filter($this->env, (isset($context["type"]) ? $context["type"] : null), "html", null, true);
        echo "\">
  <input type=\"hidden\" name=\"pid\" value=\"";
        // line 42
        echo twig_escape_filter($this->env, (isset($context["parentId"]) ? $context["parentId"] : null), "html", null, true);
        echo "\">
</form>

<script>app.load('course-manage/chapter-modal')</script>

";
    }

    // line 49
    public function block_footer($context, array $blocks = array())
    {
        // line 50
        echo "    <button type=\"button\" class=\"btn btn-link\" data-dismiss=\"modal\">取消</button>
    <button id=\"course-chapter-btn\" data-submiting-text=\"正在提交\" type=\"submit\" class=\"btn btn-primary\" data-toggle=\"form-submit\" data-target=\"#course-chapter-form\" data-chapter=\"";
        // line 51
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "chapter_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "chapter_name", array()), "章")) : ("章")), "html", null, true);
        echo "\" data-part=\"";
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "part_name", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["default"]) ? $context["default"] : null), "part_name", array()), "节")) : ("节")), "html", null, true);
        echo "\">";
        if ((isset($context["chapter"]) ? $context["chapter"] : null)) {
            echo "保存";
        } else {
            echo "添加";
        }
        echo "</button>
";
    }

    public function getTemplateName()
    {
        return "CourseChapterManage/chapter-modal.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  179 => 51,  176 => 50,  173 => 49,  163 => 42,  159 => 41,  153 => 38,  149 => 36,  139 => 34,  129 => 32,  127 => 31,  123 => 29,  118 => 26,  115 => 25,  105 => 23,  95 => 21,  93 => 20,  89 => 18,  86 => 16,  80 => 14,  74 => 12,  71 => 11,  65 => 10,  62 => 9,  59 => 8,  37 => 6,  34 => 5,  29 => 54,  27 => 3,);
    }
}
