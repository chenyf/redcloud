<?php

/* @My/MyTeaching/teaching-layout.html.twig */
class __TwigTemplate_935aafd0fbb6f0628021f10f405068f89fb4a82126a3f14c79d43c99fe05d1b8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@My/My/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'main' => array($this, 'block_main'),
            'teachingBlock' => array($this, 'block_teachingBlock'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@My/My/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["side_nav"] = "my-teaching-courses";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "在教课程 - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 7
    public function block_main($context, array $blocks = array())
    {
        // line 8
        echo "<div class=\"panel panel-default panel-col lesson-manage-panel\">
    <div class=\"course-list-tit\">
        <div class=\"pull-right create-course-btn\">
            ";
        // line 11
        if (($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method") == 0)) {
            // line 12
            echo "                <a id=\"create-course\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_base", array("id" => 0, "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"), "create" => 1)), "html", null, true);
            echo "\" class=\"btn btn-primary btn-default\">
                    <span class=\"glyphicon glyphicon-plus\"></span> 创建课程
                </a>
            ";
        }
        // line 16
        echo "        </div>
    </div>

   ";
        // line 20
        echo "   ";
        $this->displayBlock('teachingBlock', $context, $blocks);
        // line 21
        echo "    
</div>

";
    }

    // line 20
    public function block_teachingBlock($context, array $blocks = array())
    {
        echo " ";
    }

    public function getTemplateName()
    {
        return "@My/MyTeaching/teaching-layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  72 => 20,  65 => 21,  62 => 20,  57 => 16,  49 => 12,  47 => 11,  42 => 8,  39 => 7,  32 => 3,  27 => 5,);
    }
}
