<?php

/* @My/My/layout.html.twig */
class __TwigTemplate_a636fa054b9e4136beb25ade6f6de3761bd04cc4754e872441529b0f3d181bd6 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'content' => array($this, 'block_content'),
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "我的学习 - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 5
    public function block_content($context, array $blocks = array())
    {
        // line 6
        echo "    ";
        echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('http_kernel')->controller("My:My:avatarAlert"));
        echo "

    <div class=\"row row-3-9 mtl\">
        <div class=\"col-md-3 new-col-md-3\">
            <div class=\"list-group-box\">
                ";
        // line 11
        if ((($this->env->getExtension('security')->isGranted("ROLE_ADMIN") || $this->env->getExtension('security')->isGranted("ROLE_TEACHER")) || $this->getAttribute((isset($context["my"]) ? $context["my"] : null), "isTeacher", array()))) {
            // line 12
            echo "                    <div class=\"list-group-con\">
                        <div class=\"list-group-heading\"><i class=\"fa fa-bars mrm\"></i>我的教学</div>
                        <ul class=\"list-group\">
                            <li>
                                <a class=\"list-group-item ";
            // line 16
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "my-teaching-courses")) {
                echo " active ";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("my_teaching_courses");
            echo "\">
                                    <span class=\"c-side-nav\"><em class=\"c-item-icon1\" title=\"在教课程\"></em>在教课程</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class=\"list-group-con\">
                        <div class=\"list-group-heading\"><i class=\"fa fa-bars mrm\"></i>个人中心</div>
                        <ul class=\"list-group\">
                            ";
            // line 26
            $context["side_nav"] = ((array_key_exists("side_nav", $context)) ? (_twig_default_filter((isset($context["side_nav"]) ? $context["side_nav"] : null), null)) : (null));
            // line 27
            echo "                            <li>
                                <a class=\"list-group-item ";
            // line 28
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "profile")) {
                echo "active";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("settings");
            echo "\"><span class=\"my-center-icon\"><i class=\"glyphicon glyphicon-user\"></i> 基础信息</span></a>
                            </li>
                            <li>
                                <a class=\"list-group-item ";
            // line 31
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "change_pwd")) {
                echo "active";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("change_pwd");
            echo "\"><span class=\"my-center-icon\"><i class=\"glyphicon glyphicon-lock\"></i> 修改密码</span></a>
                            </li>
                        </ul>
                    </div>

                ";
        } else {
            // line 37
            echo "                    <div class=\"list-group-con\">
                        <div class=\"list-group-heading\"><i class=\"fa fa-bars mrm\"></i>我的学习</div>
                        <ul class=\"list-group\">
                            <li>
                                <a class=\"list-group-item ";
            // line 41
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "my-learning")) {
                echo " active ";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("my_courses_learning");
            echo "\">
                                    <span class=\"c-side-nav\"><em class=\"c-item-icon4\" title=\"在学课程\"></em>在学课程</span>
                                </a>
                            </li>
                            <li>
                                <a class=\"list-group-item ";
            // line 46
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "my-notes")) {
                echo " active ";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("my_notebooks");
            echo "\">
                                    <span class=\"c-side-nav\"><em class=\"c-item-icon8\" title=\"我的笔记\"></em>我的笔记</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class=\"list-group-con\">
                        <div class=\"list-group-heading\"><i class=\"fa fa-bars mrm\"></i>个人中心</div>
                        <ul class=\"list-group\">
                            ";
            // line 56
            $context["side_nav"] = ((array_key_exists("side_nav", $context)) ? (_twig_default_filter((isset($context["side_nav"]) ? $context["side_nav"] : null), null)) : (null));
            // line 57
            echo "                            <li>
                                <a class=\"list-group-item ";
            // line 58
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "profile")) {
                echo "active";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("settings");
            echo "\"><span class=\"my-center-icon\"><i class=\"glyphicon glyphicon-user\"></i> 基础信息</span></a>
                            </li>
                            <li>
                                <a class=\"list-group-item ";
            // line 61
            if (((isset($context["side_nav"]) ? $context["side_nav"] : null) == "change_pwd")) {
                echo "active";
            }
            echo "\" href=\"";
            echo $this->env->getExtension('routing')->getPath("change_pwd");
            echo "\"><span class=\"my-center-icon\"><i class=\"glyphicon glyphicon-lock\"></i> 修改密码</span></a>
                            </li>
                        </ul>
                    </div>

                ";
        }
        // line 67
        echo "
            </div>

        </div>
        <div class=\"col-md-9 new-col-md-9\">";
        // line 71
        $this->displayBlock('main', $context, $blocks);
        echo "</div>
    </div>

    <input type=\"hidden\" name=\"vps_list_url\" value=\"";
        // line 74
        echo $this->env->getExtension('routing')->getPath("my_vps_list");
        echo "\" />
    <div id=\"vps-list-modal\" class=\"modal\">

    </div>

    <script>
        function loadVpsStatus(){
            var url = \$(\"input[name='vps_list_url']\").val();
            \$(\"#vps-list-modal\").load(url,function(){
                \$(this).modal({keyboard: true});
            });
        }
    </script>

";
    }

    // line 71
    public function block_main($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "@My/My/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  196 => 71,  177 => 74,  171 => 71,  165 => 67,  152 => 61,  142 => 58,  139 => 57,  137 => 56,  120 => 46,  108 => 41,  102 => 37,  89 => 31,  79 => 28,  76 => 27,  74 => 26,  57 => 16,  51 => 12,  49 => 11,  40 => 6,  37 => 5,  30 => 3,);
    }
}
