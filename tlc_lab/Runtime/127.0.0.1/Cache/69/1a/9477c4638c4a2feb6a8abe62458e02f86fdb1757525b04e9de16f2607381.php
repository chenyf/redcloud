<?php

/* MyTeaching/teaching.html.twig */
class __TwigTemplate_691a9477c4638c4a2feb6a8abe62458e02f86fdb1757525b04e9de16f2607381 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@My/MyTeaching/teaching-layout.html.twig");

        $this->blocks = array(
            'teachingBlock' => array($this, 'block_teachingBlock'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@My/MyTeaching/teaching-layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_teachingBlock($context, array $blocks = array())
    {
        // line 4
        echo "    <div class=\"panel-body\">
        <div class=\"row\" style=\"margin: 0;\">
                <ul class=\"my-teaching-list\">
                ";
        // line 7
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["courses"]) ? $context["courses"] : null));
        $context['_iterated'] = false;
        foreach ($context['_seq'] as $context["_key"] => $context["course"]) {
            // line 8
            echo "                     <li class=\"col-md-6 col-sm-6\">
                         <div class=\"cc-course-tit\">
                             <a class=\"cc-course-name pull-left\" href=\"";
            // line 10
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "title", array()), "html", null, true);
            echo "</a>
                             ";
            // line 14
            echo "                             <a class=\"cc-icon-edit\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_base", array("id" => $this->getAttribute($context["course"], "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\">[编辑]</a></div>
                         <div class=\"cc-course-con\">
                             <div class=\"col-md-7 col-sm-7\">
                                 <div class=\"cc-course-pic\">
                                    <a href=\"";
            // line 18
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
            echo "\"> <img  loaderrimg=\"1\" onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  class=\"course-picture\" src=\"";
            if (($this->getAttribute($context["course"], "selectPicture", array()) == "")) {
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute($context["course"], "middlePicture", array()), "large"), "html", null, true);
            } else {
                echo " ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "selectPicture", array()), "html", null, true);
            }
            echo "\" alt=\"";
            echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "title", array()), "html", null, true);
            echo "\" width=\"100%\"></a>
                                    <span class=\"cc-mask\">
                                        <em class=\"text-success\">
                                            ";
            // line 21
            echo $this->env->getExtension('topxia_web_twig')->getDictText("courseStatus:html", $this->getAttribute($context["course"], "status", array()));
            echo "
                                         </em>
                                    </span>
                                 </div>
                             </div>
                             <div class=\"col-md-5 col-sm-5\">
                                 <a class=\"btn btn-block btn-default btn-wireframe\" href=\"";
            // line 27
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson", array("id" => $this->getAttribute($context["course"], "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
            echo "\">课程内容管理</a>
                             </div>
                         </div>
                     </li>
                ";
            $context['_iterated'] = true;
        }
        if (!$context['_iterated']) {
            // line 32
            echo "                    <tr><td colspan=\"20\"><div class=\"empty\">暂无在教的课程</div></td></tr>
                ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['_key'], $context['course'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 34
        echo "                 </ul>
                ";
        // line 35
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["web_macro"]) ? $context["web_macro"] : null), "paginator", array(0 => (isset($context["paginator"]) ? $context["paginator"] : null)), "method"), "html", null, true);
        echo " 
            </div>
    </div>
 ";
    }

    public function getTemplateName()
    {
        return "MyTeaching/teaching.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  103 => 35,  100 => 34,  93 => 32,  83 => 27,  74 => 21,  59 => 18,  51 => 14,  45 => 10,  41 => 8,  36 => 7,  31 => 4,  28 => 3,);
    }
}
