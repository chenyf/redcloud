<?php

/* @My/MyCourse/nav-pill.html.twig */
class __TwigTemplate_5ec49f45b85a812fbbeee18cb8fef39908be771a4bf3f270bd12c8df536ab8d2 extends Twig_Template
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
        echo "<ul class=\"nav nav-tabline\">
\t<li";
        // line 2
        if (((isset($context["tab_nav"]) ? $context["tab_nav"] : null) == "learning")) {
            echo " class=\"active\"";
        }
        echo "><a href=\"";
        echo $this->env->getExtension('routing')->getPath("my_courses_learning");
        echo "\">学习中</a></li>
\t<li";
        // line 3
        if (((isset($context["tab_nav"]) ? $context["tab_nav"] : null) == "learned")) {
            echo " class=\"active\"";
        }
        echo "><a href=\"";
        echo $this->env->getExtension('routing')->getPath("my_courses_learned");
        echo "\">已学完</a></li>
\t<li";
        // line 4
        if (((isset($context["tab_nav"]) ? $context["tab_nav"] : null) == "favorited")) {
            echo " class=\"active\"";
        }
        echo "><a href=\"";
        echo $this->env->getExtension('routing')->getPath("my_courses_favorited");
        echo "\">收藏</a></li>
</ul>";
    }

    public function getTemplateName()
    {
        return "@My/MyCourse/nav-pill.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 4,  30 => 3,  22 => 2,  19 => 1,);
    }
}
