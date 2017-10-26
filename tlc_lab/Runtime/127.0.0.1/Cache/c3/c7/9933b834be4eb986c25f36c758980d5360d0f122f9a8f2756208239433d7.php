<?php

/* LessonLessonPlugin/list.html.twig */
class __TwigTemplate_c3c79933b834be4eb986c25f36c758980d5360d0f122f9a8f2756208239433d7 extends Twig_Template
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
        echo "<div class=\"course-item-list-in-toolbar-pane\">
\t";
        // line 2
        $this->env->loadTemplate("@Course/CourseLesson/item-list.html.twig")->display($context);
        // line 3
        echo "</div>
";
    }

    public function getTemplateName()
    {
        return "LessonLessonPlugin/list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  24 => 3,  22 => 2,  19 => 1,);
    }
}
