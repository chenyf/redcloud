<?php

/* courseResource/resourcePane.html.twig */
class __TwigTemplate_7ea419bdd7fb8eed6a3e6932dfbd3bcd62e6fa88e8bdc397e7c992464cfcdede extends Twig_Template
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
        $context["web_macro"] = $this->env->loadTemplate("@Home/macro.html.twig");
        // line 2
        if (twig_test_empty((isset($context["courseResources"]) ? $context["courseResources"] : null))) {
            // line 3
            echo "   <div class=\"empty\">暂无课程资料！</div>
";
        } else {
            // line 5
            echo "    <div class=\"panel-body\">
        <table class=\"table table-striped table-hover mtm c-table\">
            <thead>
                <tr class=\"active\">
                    <th>资料名称</th>
                    <th width=\"12%\">资料大小</th>
                    <th width=\"12%\">上传者</th>
                    <th width=\"16%\">上传时间</th>
                    ";
            // line 13
            if (((isset($context["showDownloadNum"]) ? $context["showDownloadNum"] : null) == 1)) {
                // line 14
                echo "                    <th width=\"12%\" class=\"download-num-th\">下载次数</th>
                    ";
            }
            // line 16
            echo "                    <th width=\"12%\">操作</th>
                </tr>
            </thead>
            <tbody>
                ";
            // line 20
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["courseResources"]) ? $context["courseResources"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["resource"]) {
                // line 21
                echo "                    ";
                $context["type"] = $this->env->getExtension('topxia_web_twig')->getResourceType($this->getAttribute($context["resource"], "ext", array()));
                // line 22
                echo "                    ";
                if (twig_test_empty((isset($context["type"]) ? $context["type"] : null))) {
                    // line 23
                    echo "                        ";
                    $context["ext"] = "fa-file-o";
                    // line 24
                    echo "                    ";
                } else {
                    // line 25
                    echo "                        ";
                    $context["ext"] = (("fa-file-" . (isset($context["type"]) ? $context["type"] : null)) . "-o");
                    // line 26
                    echo "                    ";
                }
                // line 27
                echo "                    <tr>
                        <td>
                            <span class=\"table-text\">
                                <i class=\"fa mrs ";
                // line 30
                echo twig_escape_filter($this->env, (isset($context["ext"]) ? $context["ext"] : null), "html", null, true);
                echo "\"></i>";
                echo twig_escape_filter($this->env, $this->getAttribute($context["resource"], "title", array()), "html", null, true);
                echo "
                            </span>
                        </td>
                        <td>";
                // line 33
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->fileSizeFilter($this->getAttribute($context["resource"], "size", array())), "html", null, true);
                echo "</td>
                        <td>";
                // line 34
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getUserNameById($this->getAttribute($context["resource"], "createUid", array())), "html", null, true);
                echo "</td>
                        <td>";
                // line 35
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->dataformatFilter($this->getAttribute($context["resource"], "updateTm", array())), "html", null, true);
                echo "</td>
                        ";
                // line 36
                if (((isset($context["showDownloadNum"]) ? $context["showDownloadNum"] : null) == 1)) {
                    // line 37
                    echo "                        <td>";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["resource"], "downloadNum", array()), "html", null, true);
                    echo "次</td>
                        ";
                }
                // line 39
                echo "                        <td>
                            <a class=\"btn btn-sm btn-primary btn-width download-course-resource\" href=\"javascript:\" data-courseid=\"";
                // line 40
                echo twig_escape_filter($this->env, $this->getAttribute($context["resource"], "courseId", array()), "html", null, true);
                echo "\" data-id=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["resource"], "id", array()), "html", null, true);
                echo "\" data-url=\"";
                echo $this->env->getExtension('routing')->getPath("course_resource_download");
                echo "\">下载</a>
                        </td>
                    </tr>
                ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['resource'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 44
            echo "            </tbody>
        </table>
        ";
            // line 46
            echo $context["web_macro"]->getpaginator((isset($context["paginator"]) ? $context["paginator"] : null));
            echo "
    </div>
";
        }
    }

    public function getTemplateName()
    {
        return "courseResource/resourcePane.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  125 => 46,  121 => 44,  107 => 40,  104 => 39,  98 => 37,  96 => 36,  92 => 35,  88 => 34,  84 => 33,  76 => 30,  71 => 27,  68 => 26,  65 => 25,  62 => 24,  59 => 23,  56 => 22,  53 => 21,  49 => 20,  43 => 16,  39 => 14,  37 => 13,  27 => 5,  23 => 3,  21 => 2,  19 => 1,);
    }
}
