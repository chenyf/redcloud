<?php

/* CourseLessonManage/index.html.twig */
class __TwigTemplate_182ab5e620034412c015c2bcf4bd3d55fbef3fe23a95c0e6897c1851cb533143 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Course/CourseManage/courseLayout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'side' => array($this, 'block_side'),
            'courseContent' => array($this, 'block_courseContent'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Course/CourseManage/courseLayout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["side_nav"] = "lesson";
        // line 2
        $context["menu"] = "lesson";
        // line 3
        $context["script_controller"] = "course-manage/lesson";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_title($context, array $blocks = array())
    {
        echo "课程内容管理 - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 8
    public function block_side($context, array $blocks = array())
    {
        // line 9
        echo "     <div class=\"t-course-handle\">
         <div class=\"t-add-content pull-right\">
             <button class=\"btn btn-info btn-sm\" id=\"chapter-create-btn\" data-toggle=\"modal\" data-target=\"#modal\" data-backdrop=\"static\" data-keyboard=\"false\" data-url=\"";
        // line 11
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_chapter_create", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">
                 <i class=\"glyphicon glyphicon-plus mrs\"></i> 添加章目录
             </button>
         </div>
     </div>
 ";
    }

    // line 18
    public function block_courseContent($context, array $blocks = array())
    {
        // line 19
        echo "    <div class=\"t-course-set-box\">
        ";
        // line 20
        if (twig_test_empty((isset($context["items"]) ? $context["items"] : null))) {
            // line 21
            echo "            <div class=\"empty\">暂无课程内容！</div>
        ";
        }
        // line 23
        echo "        <ul class=\"lesson-list sortable-list\" id=\"course-item-list\" data-sort-url=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_lesson_sort", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\">
            ";
        // line 24
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["items"]) ? $context["items"] : null));
        $context['loop'] = array(
          'parent' => $context['_parent'],
          'index0' => 0,
          'index'  => 1,
          'first'  => true,
        );
        if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
            $length = count($context['_seq']);
            $context['loop']['revindex0'] = $length - 1;
            $context['loop']['revindex'] = $length;
            $context['loop']['length'] = $length;
            $context['loop']['last'] = 1 === $length;
        }
        foreach ($context['_seq'] as $context["id"] => $context["item"]) {
            // line 25
            echo "                ";
            if (twig_in_filter("chapter", $context["id"])) {
                // line 26
                echo "                    ";
                $this->env->loadTemplate("@Course/CourseChapterManage/list-item.html.twig")->display(array_merge($context, array("chapter" => $context["item"])));
                // line 27
                echo "                ";
            } elseif (twig_in_filter("lesson", $context["id"])) {
                // line 28
                echo "                    ";
                $this->env->loadTemplate("@Course/CourseLessonManage/list-item.html.twig")->display(array_merge($context, array("lesson" => $context["item"], "file" => (($this->getAttribute((isset($context["files"]) ? $context["files"] : null), $this->getAttribute($context["item"], "mediaId", array()), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["files"]) ? $context["files"] : null), $this->getAttribute($context["item"], "mediaId", array()), array(), "array"), null)) : (null)))));
                // line 29
                echo "                ";
            }
            // line 30
            echo "            ";
            ++$context['loop']['index0'];
            ++$context['loop']['index'];
            $context['loop']['first'] = false;
            if (isset($context['loop']['length'])) {
                --$context['loop']['revindex0'];
                --$context['loop']['revindex'];
                $context['loop']['last'] = 0 === $context['loop']['revindex0'];
            }
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['item'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 31
        echo "        </ul>
    </div>
";
    }

    public function getTemplateName()
    {
        return "CourseLessonManage/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  123 => 31,  109 => 30,  106 => 29,  103 => 28,  100 => 27,  97 => 26,  94 => 25,  77 => 24,  72 => 23,  68 => 21,  66 => 20,  63 => 19,  60 => 18,  50 => 11,  46 => 9,  43 => 8,  36 => 6,  31 => 3,  29 => 2,  27 => 1,);
    }
}
