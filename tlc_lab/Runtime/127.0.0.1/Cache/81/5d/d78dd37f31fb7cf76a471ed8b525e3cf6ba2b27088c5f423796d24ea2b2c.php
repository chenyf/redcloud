<?php

/* @Course/Course/course-detail-main.html.twig */
class __TwigTemplate_815dd78dd37f31fb7cf76a471ed8b525e3cf6ba2b27088c5f423796d24ea2b2c extends Twig_Template
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
        echo "
<div class=\"course-list-tit\">
    ";
        // line 3
        if ((isset($context["isShowVirtualLab"]) ? $context["isShowVirtualLab"] : null)) {
            // line 4
            echo "        <a class=\"pull-right btn btn-primary btn-fat only-btn mts mrs\" target=\"_blank\" href=\"http://www.hyids.net:3340\">虚拟实验室</a>
    ";
        }
        // line 6
        echo "        <ul class=\"nav nav-tabs\"  id=\"course-nav-tabs\">
            <li class=\"";
        // line 7
        if (((isset($context["type"]) ? $context["type"] : null) == "index")) {
            echo "active";
        }
        echo "\">
                <a class=\"btn-index\" href=\"javascript:;\" data-anchor1=\"course-list-pane\">课程目录</a>
            </li>
            <li class=\"";
        // line 10
        if (((isset($context["type"]) ? $context["type"] : null) == "about")) {
            echo "active";
        }
        echo "\">
                <a class=\"btn-index\" href=\"javascript:;\" data-anchor1=\"course-about-pane\">课程介绍</a>
            </li>
            <li class=\"";
        // line 13
        if (((isset($context["type"]) ? $context["type"] : null) == "resource")) {
            echo "active";
        }
        echo "\">
                <a class=\"btn-index\" href=\"javascript:;\" data-anchor1=\"course-resource-pane\">课程资料</a>
            </li>
        </ul>
    </div>
<div class=\"panel-body\">
    ";
        // line 19
        $context["class"] = "hide";
        // line 20
        $this->env->loadTemplate("@Course/Course/course-detail-main.html.twig", "6841400")->display(array_merge($context, array("id" => "course-list-pane", "class" => $this->env->getExtension('topxia_web_twig')->panelClass((isset($context["type"]) ? $context["type"] : null), "index"))));
        // line 28
        echo "
";
        // line 29
        $this->env->loadTemplate("@Course/Course/course-detail-main.html.twig", "1699780455")->display(array_merge($context, array("id" => "course-resource-pane", "class" => $this->env->getExtension('topxia_web_twig')->panelClass((isset($context["type"]) ? $context["type"] : null), "resource"))));
        // line 37
        echo "
";
        // line 38
        $this->env->loadTemplate("@Course/Course/course-detail-main.html.twig", "428547091")->display(array_merge($context, array("id" => "course-about-pane", "class" => $this->env->getExtension('topxia_web_twig')->panelClass((isset($context["type"]) ? $context["type"] : null), "about"))));
        // line 60
        echo "
</div>







";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail-main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 60,  71 => 38,  68 => 37,  66 => 29,  63 => 28,  61 => 20,  59 => 19,  48 => 13,  40 => 10,  32 => 7,  29 => 6,  25 => 4,  23 => 3,  19 => 1,);
    }
}


/* @Course/Course/course-detail-main.html.twig */
class __TwigTemplate_815dd78dd37f31fb7cf76a471ed8b525e3cf6ba2b27088c5f423796d24ea2b2c_6841400 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/Bootstrap/panel.html.twig");

        $this->blocks = array(
            'heading' => array($this, 'block_heading'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/Bootstrap/panel.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 21
    public function block_heading($context, array $blocks = array())
    {
        // line 22
        echo "        <h3 class=\"panel-title\">课程目录</h3>
    ";
    }

    // line 24
    public function block_body($context, array $blocks = array())
    {
        // line 25
        echo "        ";
        $this->env->loadTemplate("@Course/CourseLesson/item-list-multi.html.twig")->display(array_merge($context, array("experience" => true)));
        // line 26
        echo "    ";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail-main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  142 => 26,  139 => 25,  136 => 24,  131 => 22,  128 => 21,  73 => 60,  71 => 38,  68 => 37,  66 => 29,  63 => 28,  61 => 20,  59 => 19,  48 => 13,  40 => 10,  32 => 7,  29 => 6,  25 => 4,  23 => 3,  19 => 1,);
    }
}


/* @Course/Course/course-detail-main.html.twig */
class __TwigTemplate_815dd78dd37f31fb7cf76a471ed8b525e3cf6ba2b27088c5f423796d24ea2b2c_1699780455 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/Bootstrap/panel.html.twig");

        $this->blocks = array(
            'heading' => array($this, 'block_heading'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/Bootstrap/panel.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 30
    public function block_heading($context, array $blocks = array())
    {
        // line 31
        echo "        <h3 class=\"panel-title\">课程资料</h3>
    ";
    }

    // line 33
    public function block_body($context, array $blocks = array())
    {
        // line 34
        echo "        ";
        echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('http_kernel')->controller("Course:CourseResource:resourcePane", array("id" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "id"), "method"))));
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail-main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  199 => 34,  196 => 33,  191 => 31,  188 => 30,  142 => 26,  139 => 25,  136 => 24,  131 => 22,  128 => 21,  73 => 60,  71 => 38,  68 => 37,  66 => 29,  63 => 28,  61 => 20,  59 => 19,  48 => 13,  40 => 10,  32 => 7,  29 => 6,  25 => 4,  23 => 3,  19 => 1,);
    }
}


/* @Course/Course/course-detail-main.html.twig */
class __TwigTemplate_815dd78dd37f31fb7cf76a471ed8b525e3cf6ba2b27088c5f423796d24ea2b2c_428547091 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/Bootstrap/panel.html.twig");

        $this->blocks = array(
            'heading' => array($this, 'block_heading'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/Bootstrap/panel.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 39
    public function block_heading($context, array $blocks = array())
    {
        // line 40
        echo "        <h3 class=\"panel-title\">课程介绍</h3>
    ";
    }

    // line 42
    public function block_body($context, array $blocks = array())
    {
        // line 43
        echo "
        ";
        // line 44
        if ($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "about", array())) {
            // line 45
            echo "            <span style=\"word-break: break-all;\">";
            echo $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "about", array());
            echo "</span>
        ";
        } else {
            // line 47
            echo "            <span class=\"text-muted\">还没有课程介绍...</span>
        ";
        }
        // line 49
        echo "        ";
        if ((isset($context["tags"]) ? $context["tags"] : null)) {
            // line 50
            echo "            <div class=\"mtm\">
                <span class=\"text-muted\">标签：</span>
                ";
            // line 52
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["tags"]) ? $context["tags"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
                // line 53
                echo "                    <a href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("tag_show", array("name" => $this->getAttribute($context["tag"], "name", array()))), "html", null, true);
                echo "\" class=\"mrs\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["tag"], "name", array()), "html", null, true);
                echo "</a>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 55
            echo "            </div>
        ";
        }
        // line 57
        echo "
    ";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail-main.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  300 => 57,  296 => 55,  285 => 53,  281 => 52,  277 => 50,  274 => 49,  270 => 47,  264 => 45,  262 => 44,  259 => 43,  256 => 42,  251 => 40,  248 => 39,  199 => 34,  196 => 33,  191 => 31,  188 => 30,  142 => 26,  139 => 25,  136 => 24,  131 => 22,  128 => 21,  73 => 60,  71 => 38,  68 => 37,  66 => 29,  63 => 28,  61 => 20,  59 => 19,  48 => 13,  40 => 10,  32 => 7,  29 => 6,  25 => 4,  23 => 3,  19 => 1,);
    }
}
