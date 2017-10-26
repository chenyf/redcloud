<?php

/* @User/Partner/header-user.html.twig */
class __TwigTemplate_12a0e49ce77e2e816a5061cc479790e715144ab46b349aedad8be6d82ea05efd extends Twig_Template
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
        if ((isset($context["user"]) ? $context["user"] : null)) {
            // line 2
            echo "
    <li class=\"hidden-xs\"><a href=\"";
            // line 3
            echo $this->env->getExtension('routing')->getPath("search");
            echo "\"><span class=\"glyphicon glyphicon-search nav-top-icon\" title=\"搜索\" data-placement=\"bottom\"  data-toggle=\"tooltip\"></span></a></li>


    <li class=\"visible-lt-ie8 hidden-xs\"><a href=\"";
            // line 6
            echo $this->env->getExtension('routing')->getPath("settings");
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "nickname", array()), "html", null, true);
            echo "</a></li>
    <li class=\"dropdown hidden-lt-ie8\">
        <a href=\"javascript:;\" class=\"dropdown-toggle\" data-toggle=\"dropdown\" style=\"margin-top: -2px;\">";
            // line 8
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "nickname", array()), "html", null, true);
            echo " <b class=\"caret\"></b></a>
        <ul class=\"dropdown-menu\">
            <li><a href=\"";
            // line 10
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_show", array("id" => $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()))), "html", null, true);
            echo "\"><i class=\"glyphicon glyphicon-home\"></i> 我的主页</a></li>
            <li class=\"divider\"></li>
            <li><a href=\"";
            // line 12
            echo $this->env->getExtension('routing')->getPath("settings");
            echo "\"><i class=\"glyphicon glyphicon-user\"></i> 个人中心</a></li>
            <li class=\"divider\"></li>
            <li><a href=\"";
            // line 14
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_show", array("id" => $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()))), "html", null, true);
            echo "\"><i class=\"glyphicon glyphicon-book\"></i> 我的课程</a></li>
            <li class=\"divider\"></li>
            ";
            // line 16
            if ($this->env->getExtension('security')->isUserRole()) {
                // line 17
                echo "                <li><a href=\"";
                echo $this->env->getExtension('routing')->getPath("admin");
                echo "\"><i class=\"glyphicon glyphicon-dashboard\"></i> 管理后台</a></li>
                <li class=\"divider\"></li>
            ";
            }
            // line 20
            echo "            <li><a href=\"";
            echo $this->env->getExtension('routing')->getPath("logout");
            echo "\"><i class=\"glyphicon glyphicon-off\"></i> 退出</a></li>
        </ul>
    </li>
";
        } else {
            // line 24
            echo "
    <li class=\"hidden-xs\"><a href=\"";
            // line 25
            echo $this->env->getExtension('routing')->getPath("search");
            echo "\"><span class=\"glyphicon glyphicon-search\"></span> 搜索</a></li>
    <li><a href=\"";
            // line 26
            echo $this->env->getExtension('routing')->getPath("login");
            echo "\">登录</a></li>
";
        }
    }

    public function getTemplateName()
    {
        return "@User/Partner/header-user.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  81 => 26,  77 => 25,  74 => 24,  66 => 20,  59 => 17,  57 => 16,  52 => 14,  47 => 12,  42 => 10,  37 => 8,  30 => 6,  24 => 3,  21 => 2,  19 => 1,);
    }
}
