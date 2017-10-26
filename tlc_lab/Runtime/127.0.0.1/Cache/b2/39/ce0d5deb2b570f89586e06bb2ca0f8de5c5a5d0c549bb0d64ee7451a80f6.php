<?php

/* MyCourse/learning.html.twig */
class __TwigTemplate_b239ce0d5deb2b570f89586e06bb2ca0f8de5c5a5d0c549bb0d64ee7451a80f6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@My/MyCourse/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@My/MyCourse/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 5
        $context["tab_nav"] = "learning";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "学习中 - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 7
    public function block_main($context, array $blocks = array())
    {
        // line 8
        echo "    <div class=\"panel panel-default panel-col\">
        ";
        // line 12
        echo "         ";
        if ($this->env->getExtension('topxia_web_twig')->isOpenPublicCourse()) {
            echo "    
            <div class=\"course-list-tit\">
            </div>
         ";
        }
        // line 15
        echo "    
        <div class=\"panel-body cc-panel-body\">
            ";
        // line 27
        echo "            <div style=\"\">";
        $this->env->loadTemplate("@My/MyCourse/nav-pill.html.twig")->display($context);
        echo "</div>
            ";
        // line 28
        if ((isset($context["courses"]) ? $context["courses"] : null)) {
            // line 29
            echo "                ";
            echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('http_kernel')->controller("Course:Course:coursesBlock", array("courses" => (isset($context["courses"]) ? $context["courses"] : null), "view" => "grid", "mode" => "learn")));
            echo "
                ";
            // line 30
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["web_macro"]) ? $context["web_macro"] : null), "paginator", array(0 => (isset($context["paginator"]) ? $context["paginator"] : null)), "method"), "html", null, true);
            echo " 
            ";
        } else {
            // line 32
            echo "                <div class=\"empty\">暂无学习中的课程</div>
            ";
        }
        // line 34
        echo "        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "MyCourse/learning.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  77 => 34,  73 => 32,  68 => 30,  63 => 29,  61 => 28,  56 => 27,  52 => 15,  44 => 12,  41 => 8,  38 => 7,  31 => 3,  26 => 5,);
    }
}
