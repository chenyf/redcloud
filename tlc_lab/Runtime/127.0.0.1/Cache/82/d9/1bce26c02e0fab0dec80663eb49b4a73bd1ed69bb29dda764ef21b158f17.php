<?php

/* @Course/CourseLesson/item-list.html.twig */
class __TwigTemplate_82d91bce26c02e0fab0dec80663eb49b4a73bd1ed69bb29dda764ef21b158f17 extends Twig_Template
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
        $context["learnStatuses"] = ((array_key_exists("learnStatuses", $context)) ? (_twig_default_filter((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), array())) : (array()));
        // line 2
        $context["experience"] = ((array_key_exists("experience", $context)) ? (_twig_default_filter((isset($context["experience"]) ? $context["experience"] : null), false)) : (false));
        // line 3
        $context["deploy"] = $this->env->getExtension('topxia_web_twig')->intval($this->env->getExtension('topxia_web_twig')->getSetting("course.chapter_deploy_enabled", "0"));
        // line 4
        echo "<div class=\"panel-body\">
    
    <ul class=\"lesson-list sortable-list\" id=\"course-item-list\">
        ";
        // line 7
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
            // line 8
            echo "        ";
            if (twig_in_filter("chapter", $context["id"])) {
                echo " 
        ";
                // line 9
                if (($this->getAttribute($context["item"], "type", array()) == "unit")) {
                    // line 10
                    echo "        <li class=\"item item-chapter item-chapter-unit clearfix ";
                    if ((($this->getAttribute($context["item"], "show", array()) == 0) && ((isset($context["deploy"]) ? $context["deploy"] : null) == false))) {
                        echo "hide";
                    }
                    echo "\"  style=\"word-break: break-all;\" data-id=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "id", array()), "html", null, true);
                    echo "\" data-pid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "parentId", array()), "html", null, true);
                    echo "\">
            <div class=\"item-line item-line-chapter\"></div>
            <div class=\"item-content\">
                <i class=\"fa fa-file-text-o pull-left\"></i>第 <span class=\"number\">";
                    // line 13
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "number", array()), "html", null, true);
                    echo "</span> 节： ";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "title", array()), "html", null, true);
                    echo "
            </div>
        </li>
            ";
                } else {
                    // line 17
                    echo "        <li class=\"item item-chapter  clearfix ";
                    if ((($this->getAttribute($context["item"], "show", array()) == 0) && (isset($context["deploy"]) ? $context["deploy"] : null))) {
                        echo "hide";
                    }
                    echo "\"  style=\"word-break: break-all;\" data-id=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "id", array()), "html", null, true);
                    echo "\" data-pid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "parentId", array()), "html", null, true);
                    echo "\">
            <div class=\"item-content\">
                <i class=\"fa fa-list-ul pull-left deploy\"></i>第 <span class=\"number\">";
                    // line 19
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "number", array()), "html", null, true);
                    echo "</span> 章： ";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "title", array()), "html", null, true);
                    echo "<em class=\"deploy fa pull-right ";
                    if (((isset($context["deploy"]) ? $context["deploy"] : null) == false)) {
                        if (($context["id"] == (isset($context["firstItemKey"]) ? $context["firstItemKey"] : null))) {
                            echo "fa-minus ";
                        } else {
                            echo " fa-plus ";
                        }
                    }
                    echo "\"></em>
            </div>
        </li>
            ";
                }
                // line 23
                echo "        ";
            } else {
                // line 24
                echo "                ";
                $context["lessonUrl"] = (($this->env->getExtension('routing')->getPath("course_learn", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))) . "#lesson/") . $this->getAttribute($context["item"], "id", array()));
                // line 25
                echo "                ";
                $context["icon"] = array("text" => "fa-picture-o", "video" => "fa-play-circle-o", "practice" => "fa-pencil-square-o", "document" => "fa-file-word-o");
                // line 26
                echo "            <li class=\"item item-lesson clearfix ";
                if ((($this->getAttribute((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), $this->getAttribute($context["item"], "id", array()), array(), "array", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), $this->getAttribute($context["item"], "id", array()), array(), "array"), null)) : (null))) {
                    echo "lesson-item-";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), $this->getAttribute($context["item"], "id", array()), array(), "array"), "html", null, true);
                }
                echo " lesson-item-";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "id", array()), "html", null, true);
                echo " ";
                if ((($this->getAttribute($context["item"], "show", array()) == 0) && ((isset($context["deploy"]) ? $context["deploy"] : null) == false))) {
                    echo "hide";
                }
                echo "\"  style=\"word-break: break-all;";
                if (($this->getAttribute($context["item"], "lessonLevel", array()) == "unit")) {
                    echo "margin-left: 40px;";
                }
                echo "\" onclick=\"window.location.href='";
                echo twig_escape_filter($this->env, (isset($context["lessonUrl"]) ? $context["lessonUrl"] : null), "html", null, true);
                echo "'\" data-id=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "id", array()), "html", null, true);
                echo "\" data-pid=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "chapterId", array()), "html", null, true);
                echo "\" data-num=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["loop"], "index", array()), "html", null, true);
                echo "\">
                <div class=\"item-line ";
                // line 27
                if (($this->getAttribute($context["item"], "lessonLevel", array()) == "unit")) {
                    echo "item-line-unit";
                } else {
                    echo "item-line-chapter";
                }
                echo "\"></div>
                <div class=\"item-content pull-left\">
                    ";
                // line 29
                if ((($this->getAttribute($context["item"], "type", array()) == "document") && ($this->getAttribute($context["item"], "fileExt", array()) == "pdf"))) {
                    // line 30
                    echo "                        <i class=\"fa fa-file-pdf-o pull-left\"></i>
                    ";
                } elseif ((($this->getAttribute($context["item"], "type", array()) == "document") && (($this->getAttribute($context["item"], "fileExt", array()) == "ppt") || ($this->getAttribute($context["item"], "fileExt", array()) == "pptx")))) {
                    // line 32
                    echo "                        <i class=\"fa fa-file-powerpoint-o pull-left\"></i>
                    ";
                } else {
                    // line 34
                    echo "                        <i class=\"fa ";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["icon"]) ? $context["icon"] : null), $this->getAttribute($context["item"], "type", array()), array(), "array"), "html", null, true);
                    echo " pull-left\"></i>
                    ";
                }
                // line 36
                echo "                    ";
                echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "title", array()), "html", null, true);
                echo "
                </div>
                <em class=\"fa ";
                // line 38
                if (($this->getAttribute((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), $this->getAttribute($context["item"], "id", array()), array(), "array") == "learning")) {
                    echo " fa-adjust ";
                } elseif (($this->getAttribute((isset($context["learnStatuses"]) ? $context["learnStatuses"] : null), $this->getAttribute($context["item"], "id", array()), array(), "array") == "finished")) {
                    echo "fa-circle ";
                } else {
                    echo "fa-circle-o";
                }
                echo " pull-right\"></em>
                ";
                // line 39
                if (($this->getAttribute($context["item"], "type", array()) == "video")) {
                    // line 40
                    echo "                ";
                    // line 41
                    echo "                ";
                }
                // line 42
                echo "            </li>
        ";
            }
            // line 44
            echo "    ";
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
        // line 45
        echo "        </ul>
</div>
<script> app.load('course/deploy'); </script>
";
    }

    public function getTemplateName()
    {
        return "@Course/CourseLesson/item-list.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  206 => 45,  192 => 44,  188 => 42,  185 => 41,  183 => 40,  181 => 39,  171 => 38,  165 => 36,  159 => 34,  155 => 32,  151 => 30,  149 => 29,  140 => 27,  114 => 26,  111 => 25,  108 => 24,  105 => 23,  88 => 19,  76 => 17,  67 => 13,  54 => 10,  52 => 9,  47 => 8,  30 => 7,  25 => 4,  23 => 3,  21 => 2,  19 => 1,);
    }
}
