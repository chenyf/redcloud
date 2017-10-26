<?php

/* @Course/Course/course-detail.html.twig */
class __TwigTemplate_1a0f374a92731a65b639aae4238b63fabed1b87bfd7feb366647f61cea364f50 extends Twig_Template
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
        echo "<div class=\"course-detail-box-top mbl\">
    <div class=\"container\">
        ";
        // line 3
        $this->env->loadTemplate("@Course/Course/course-detail-header.html.twig")->display($context);
        // line 4
        echo "    </div>
</div>


<div class=\"container course-detail-box-con\">
    <div class=\"row\">
        <div class=\"col-md-12\">
            <div class=\"panel panel-default\">
                ";
        // line 12
        $this->env->loadTemplate("@Course/Course/course-detail-main.html.twig")->display($context);
        // line 13
        echo "            </div>
        </div>
    </div>
</div>

";
    }

    public function getTemplateName()
    {
        return "@Course/Course/course-detail.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  37 => 13,  35 => 12,  25 => 4,  23 => 3,  19 => 1,);
    }
}
