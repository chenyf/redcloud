<?php

/* Course/show.html.twig */
class __TwigTemplate_2690b4c6843955adc9efd22918574b18f6c1c5fd1c79860e6fb54e6fe894bfd1 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'keywords' => array($this, 'block_keywords'),
            'description' => array($this, 'block_description'),
            'content' => array($this, 'block_content'),
            'bottom' => array($this, 'block_bottom'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["containerClassBlock"] = "index_container";
        // line 4
        $context["modalKeyboard"] = 1;
        // line 15
        $context["script_controller"] = "course/show";
        // line 17
        $context["siteNav"] = "/Course/Course/exploreAction";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo " - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 7
    public function block_keywords($context, array $blocks = array())
    {
        // line 8
        if ((isset($context["category"]) ? $context["category"] : null)) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["category"]) ? $context["category"] : null), "name", array()), "html", null, true);
        }
        // line 9
        echo "  ";
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["tags"]) ? $context["tags"] : null));
        foreach ($context['_seq'] as $context["_key"] => $context["tag"]) {
            echo twig_escape_filter($this->env, $this->getAttribute($context["tag"], "name", array()), "html", null, true);
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['tag'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 10
        echo "  ";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo " ";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.name"), "html", null, true);
    }

    // line 12
    public function block_description($context, array $blocks = array())
    {
        echo $this->env->getExtension('topxia_web_twig')->plainTextFilter($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "about", array()), 150);
    }

    // line 19
    public function block_content($context, array $blocks = array())
    {
        // line 20
        echo "
";
        // line 21
        $this->env->loadTemplate("@Course/Course/course-detail.html.twig")->display($context);
        // line 22
        echo "
";
    }

    // line 25
    public function block_bottom($context, array $blocks = array())
    {
        // line 26
        echo "<div id=\"course-modal\" class=\"modal\" ";
        if ((isset($context["modalKeyboard"]) ? $context["modalKeyboard"] : null)) {
            echo "data-keyboard=\"false\"";
        }
        echo "></div>
<div id=\"course-edit-modal\" class=\"modal\"></div>
";
    }

    public function getTemplateName()
    {
        return "Course/show.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  94 => 26,  91 => 25,  86 => 22,  84 => 21,  81 => 20,  78 => 19,  72 => 12,  65 => 10,  55 => 9,  51 => 8,  48 => 7,  40 => 5,  35 => 17,  33 => 15,  31 => 4,  29 => 3,);
    }
}
