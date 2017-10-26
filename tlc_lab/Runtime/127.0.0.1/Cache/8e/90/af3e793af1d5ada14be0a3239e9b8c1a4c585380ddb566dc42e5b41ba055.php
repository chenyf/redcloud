<?php

/* Default/index.html.twig */
class __TwigTemplate_8e90af3e793af1d5ada14be0a3239e9b8c1a4c585380ddb566dc42e5b41ba055 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/Default/layout.html.twig");

        $this->blocks = array(
            'stylesheets' => array($this, 'block_stylesheets'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/Default/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 3
        $context["siteNav"] = "/";
        // line 4
        $context["bodyClass"] = "homepage";
        // line 5
        $context["containerClassBlock"] = "index_container";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 7
        echo "    ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
    <link rel=\"stylesheet\" media=\"screen\" href=\"";
        // line 8
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("themes/default/css/class-default.css"), "html", null, true);
        echo "\"/>
";
    }

    // line 11
    public function block_content($context, array $blocks = array())
    {
        // line 12
        echo "
    ";
        // line 13
        if ((!(isset($context["courses"]) ? $context["courses"] : null))) {
            // line 14
            echo "        <div class=\"empty-center\">
            暂无课程
        </div>
    ";
        }
        // line 18
        echo "
    <div class=\"course-row-wrap container-gap m-height\">
        ";
        // line 20
        if ((isset($context["courses"]) ? $context["courses"] : null)) {
            // line 21
            echo "            <div class=\"second-course container\">
                <div class=\"second-course-tit\">
                    <h3><b><i><img src=\"/Public/static/img/icon/book-tit-icon.png\"/></i>最新课程</b></h3>
                </div>
                <div class=\"second-course-list\">
                    ";
            // line 26
            echo $this->getAttribute($this, "course_lists", array(0 => $this->env->getExtension('topxia_data_twig')->getData("LatestCourses", array("count" => 8))), "method");
            echo "
                    <div class=\"second-course-btn\">
                        <a href=\"";
            // line 28
            echo $this->env->getExtension('routing')->getPath("course_explore");
            echo "\"><b>更多课程</b><i>&gt;</i></a>
                    </div>
                </div>
            </div>
        ";
        }
        // line 33
        echo "    </div>
";
    }

    // line 35
    public function getcourse_lists($__courses__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "courses" => $__courses__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 36
            echo "    ";
            $context["mode"] = ((array_key_exists("mode", $context)) ? (_twig_default_filter((isset($context["mode"]) ? $context["mode"] : null), "default")) : ("default"));
            // line 37
            echo "    <ul style=\"min-height: 500px;\">
        ";
            // line 38
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["courses"]) ? $context["courses"] : null));
            foreach ($context['_seq'] as $context["_key"] => $context["course"]) {
                // line 39
                echo "            <li class=\"course-item col-md-3 col-sm-4 clearfix \">
                <div class=\"second-course-box\">
                    <a class=\"second-course-link\" href=\"";
                // line 41
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
                echo "\">
                        <img class=\"course-picture\" src=\"";
                // line 42
                if (($this->getAttribute($context["course"], "selectPicture", array()) == "")) {
                    echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute($context["course"], "largePicture", array()), "large"), "html", null, true);
                } else {
                    echo " ";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "selectPicture", array()), "html", null, true);
                }
                echo "\" alt=\"";
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "title", array()), "html", null, true);
                echo "\">
                    </a>
                    <div class=\"second-course-text\">
                        <div class=\"course-body\">
                            <h4 class=\"course-title\">
                                <a href=\"";
                // line 47
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute($context["course"], "id", array()))), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "title", array()), "html", null, true);
                echo "</a>
                            </h4>

                            <div class=\"course-footer clearfix\">
                                <div class=\"course-metas\">
                                    <div class=\"text-muted mrm mls\">
                                        <span class=\"course-lessonHour\"><i class=\"glyphicon glyphicon-time\"></i><em>";
                // line 53
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "lessonNum", array()), "html", null, true);
                echo "课时</em></span>
                                        <span class=\"course-teacher pull-right\"><i class=\"glyphicon glyphicon-user\"></i><em>";
                // line 54
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "teacherName", array()), "html", null, true);
                echo " 老师</em></span>
                                    </div>
                                    <div class=\"text-muted mrm mls mts clearfix\">
                                        <span class=\"course-lessonHour\">浏览量：<em>";
                // line 57
                echo twig_escape_filter($this->env, $this->getAttribute($context["course"], "viewCount", array()), "html", null, true);
                echo "</em></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            ";
                // line 66
                echo "        ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['course'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 67
            echo "    </ul>
    ";
            // line 69
            echo "    ";
            if ($this->env->getExtension('topxia_web_twig')->getSetting("mobile.enabled")) {
                // line 70
                echo "        <div class=\"app-popupbox\">
            <a class=\"p-close-btn\" href=\"javascript:void(0)\">关闭</a>

            <div class=\"app-popupcon\"><img class=\"mrl\" src=\"";
                // line 73
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("System/Mobile/downloadQrcode"), "html", null, true);
                echo "\" width=\"100\" height=\"100\"><span>微信扫描二维码<br/>下载本校移动App</span></div>
            <a class=\"btn btn-info down-app-btn\" href=\"";
                // line 74
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("System/Mobile/getDownloadUrl"), "html", null, true);
                echo "\"> <span class=\"glyphicon glyphicon-phone\"></span>点击下载本校移动App</a>
        </div>
    ";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "Default/index.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  191 => 74,  187 => 73,  182 => 70,  179 => 69,  176 => 67,  170 => 66,  159 => 57,  153 => 54,  149 => 53,  138 => 47,  123 => 42,  119 => 41,  115 => 39,  111 => 38,  108 => 37,  105 => 36,  94 => 35,  89 => 33,  81 => 28,  76 => 26,  69 => 21,  67 => 20,  63 => 18,  57 => 14,  55 => 13,  52 => 12,  49 => 11,  43 => 8,  38 => 7,  35 => 6,  30 => 5,  28 => 4,  26 => 3,);
    }
}
