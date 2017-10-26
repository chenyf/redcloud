<?php

/* @Course/CourseLesson/item-list-multi.html.twig */
class __TwigTemplate_0fe12060629465f359b9d14e43db4164ab4fbd5c65e03c60e5dc7429588cd464 extends Twig_Template
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
        $context["deploy"] = $this->env->getExtension('topxia_web_twig')->intval($this->env->getExtension('topxia_web_twig')->getSetting("course.chapter_deploy_enabled", "0"));
        // line 2
        echo "  
<ul class=\"lesson-list sortable-list\" id=\"course-item-list\">
    ";
        // line 4
        $context['_parent'] = (array) $context;
        $context['_seq'] = twig_ensure_traversable((isset($context["groupedItems"]) ? $context["groupedItems"] : null));
        foreach ($context['_seq'] as $context["id"] => $context["group"]) {
            // line 5
            echo "        ";
            if (($this->getAttribute($context["group"], "type", array()) == "chapter")) {
                // line 6
                echo "            ";
                $context["chapter"] = $this->getAttribute($context["group"], "data", array());
                // line 7
                echo "            ";
                if (($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "type", array()) == "unit")) {
                    // line 8
                    echo "        <li class=\"item-chapter item-chapter-unit clearfix hide\"  style=\"word-break: break-all;\" data-id=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "html", null, true);
                    echo "\" data-pid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "parentId", array()), "html", null, true);
                    echo "\">
            <div class=\"item-line item-line-chapter\"></div>
            <div class=\"item-content\">
                <i class=\"fa fa-file-text-o pull-left\"></i>第 <span class=\"number\">";
                    // line 11
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "number", array()), "html", null, true);
                    echo "</span> 节： ";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "title", array()), "html", null, true);
                    echo "
            </div>
        </li>
            ";
                } else {
                    // line 15
                    echo "        <li class=\"item-chapter item-chapter-chapter clearfix ";
                    if ((($this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "show", array()) == 0) && (isset($context["deploy"]) ? $context["deploy"] : null))) {
                        echo "hide";
                    }
                    echo "\"  style=\"word-break: break-all;\" data-id=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "id", array()), "html", null, true);
                    echo "\" data-pid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "parentId", array()), "html", null, true);
                    echo "\">
            <div class=\"item-content\">
                <i class=\"fa fa-list-ul pull-left deploy\"></i>第 <span class=\"number\">";
                    // line 17
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "number", array()), "html", null, true);
                    echo "</span> 章： ";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["chapter"]) ? $context["chapter"] : null), "title", array()), "html", null, true);
                    echo "<em class=\"deploy fa pull-right fa-plus\"></em>
            </div>
        </li>
            ";
                }
                // line 21
                echo "        ";
            } else {
                // line 22
                echo "            ";
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute($context["group"], "data", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["item"]) {
                    // line 23
                    echo "                ";
                    $context["icon"] = array("text" => "fa-picture-o", "video" => "fa-play-circle-o", "practice" => "fa-pencil-square-o", "document" => "fa-file-word-o");
                    // line 24
                    echo "                ";
                    $context["lessonUrl"] = $this->env->getExtension('routing')->getPath("course_learn", array("id" => $this->getAttribute($context["item"], "courseId", array()), "#lesson" => $this->getAttribute($context["item"], "id", array())));
                    // line 25
                    echo "            <li class=\"item-lesson clearfix hide\" href=\"javascript:;\" style=\"word-break: break-all;";
                    if (($this->getAttribute($context["item"], "lessonLevel", array()) == "unit")) {
                        echo "margin-left: 40px;";
                    }
                    echo "\"  data-url=\"";
                    echo twig_escape_filter($this->env, (isset($context["lessonUrl"]) ? $context["lessonUrl"] : null), "html", null, true);
                    echo "\" data-id=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "id", array()), "html", null, true);
                    echo "\" data-pid=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "chapterId", array()), "html", null, true);
                    echo "\">
                <div class=\"item-line ";
                    // line 26
                    if (($this->getAttribute($context["item"], "lessonLevel", array()) == "unit")) {
                        echo "item-line-unit";
                    } else {
                        echo "item-line-chapter";
                    }
                    echo "\"></div>
                <div class=\"item-content pull-left ";
                    // line 27
                    if (((isset($context["member"]) ? $context["member"] : null) == false)) {
                        echo " tietleWidth ";
                    }
                    echo "\">
                    ";
                    // line 28
                    if ((($this->getAttribute($context["item"], "type", array()) == "document") && ($this->getAttribute($context["item"], "fileExt", array()) == "pdf"))) {
                        // line 29
                        echo "                        <i class=\"fa fa-file-pdf-o pull-left\"></i>
                    ";
                    } elseif ((($this->getAttribute($context["item"], "type", array()) == "document") && (($this->getAttribute($context["item"], "fileExt", array()) == "ppt") || ($this->getAttribute($context["item"], "fileExt", array()) == "pptx")))) {
                        // line 31
                        echo "                        <i class=\"fa fa-file-powerpoint-o pull-left\"></i>
                    ";
                    } else {
                        // line 33
                        echo "                        <i class=\"fa ";
                        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["icon"]) ? $context["icon"] : null), $this->getAttribute($context["item"], "type", array()), array(), "array"), "html", null, true);
                        echo " pull-left\"></i>
                    ";
                    }
                    // line 35
                    echo "
                    ";
                    // line 36
                    echo twig_escape_filter($this->env, $this->getAttribute($context["item"], "title", array()), "html", null, true);
                    echo "
                </div>
                ";
                    // line 38
                    if (($this->getAttribute($context["item"], "type", array()) == "video")) {
                        // line 39
                        echo "                ";
                        // line 40
                        echo "                ";
                    }
                    // line 41
                    echo "            </li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['item'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 43
                echo "        ";
            }
            // line 44
            echo "    ";
        }
        $_parent = $context['_parent'];
        unset($context['_seq'], $context['_iterated'], $context['id'], $context['group'], $context['_parent'], $context['loop']);
        $context = array_intersect_key($context, $_parent) + $_parent;
        // line 45
        echo "        </ul>

";
    }

    public function getTemplateName()
    {
        return "@Course/CourseLesson/item-list-multi.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  165 => 45,  159 => 44,  156 => 43,  149 => 41,  146 => 40,  144 => 39,  142 => 38,  137 => 36,  134 => 35,  128 => 33,  124 => 31,  120 => 29,  118 => 28,  112 => 27,  104 => 26,  91 => 25,  88 => 24,  85 => 23,  80 => 22,  77 => 21,  68 => 17,  56 => 15,  47 => 11,  38 => 8,  35 => 7,  32 => 6,  29 => 5,  25 => 4,  21 => 2,  19 => 1,);
    }
}
