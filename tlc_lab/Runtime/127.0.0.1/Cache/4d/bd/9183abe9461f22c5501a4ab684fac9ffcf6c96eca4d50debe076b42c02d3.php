<?php

/* @Home/layout.html.twig */
class __TwigTemplate_4dbd9183abe9461f22c5501a4ab684fac9ffcf6c96eca4d50debe076b42c02d3 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'keywords' => array($this, 'block_keywords'),
            'description' => array($this, 'block_description'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'head_scripts' => array($this, 'block_head_scripts'),
            'body' => array($this, 'block_body'),
            'content' => array($this, 'block_content'),
            'bottom' => array($this, 'block_bottom'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["web_macro"] = $this->env->loadTemplate("@Home/macro.html.twig");
        // line 2
        echo "<!DOCTYPE html>
<!--[if lt IE 7]>      <html class=\"lt-ie9 lt-ie8 lt-ie7\"> <![endif]-->
<!--[if IE 7]>         <html class=\"lt-ie9 lt-ie8\"> <![endif]-->
<!--[if IE 8]>         <html class=\"lt-ie9\"> <![endif]-->
<!--[if gt IE 8]><!--> <html class=\"\"> <!--<![endif]-->
<head>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
  ";
        // line 10
        echo "  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=7;IE=9;IE=10;IE=Edge;IE=8\">
  <meta name=\"renderer\" content=\"webkit\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
  <title>";
        // line 14
        $this->displayBlock('title', $context, $blocks);
        // line 17
        echo "</title>
  <meta name=\"keywords\" content=\"";
        // line 18
        $this->displayBlock('keywords', $context, $blocks);
        echo "\" />
  <meta name=\"description\" content=\"";
        // line 19
        $this->displayBlock('description', $context, $blocks);
        echo "\" />
  <meta property=\"wb:webmaster\" content=\"b4b8bd8943389d26\" /><!--新浪第三方接入-->
  <meta content=\"";
        // line 21
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderCsrfToken("site"), "html", null, true);
        echo "\" name=\"csrf-token\" />
  ";
        // line 22
        echo $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.verify_code", "");
        echo "
  ";
        // line 23
        if ($this->env->getExtension('topxia_web_twig')->getSetting("site.favicon")) {
            // line 24
            echo "  <link href=\"/";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.favicon"), "html", null, true);
            echo "\" rel=\"shortcut icon\" />
  ";
        }
        // line 26
        echo "  ";
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 45
        echo "    <!--[if lt IE 8]>
      <link href=\"";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/css/oldie.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
    <![endif]-->
    <!--[if lt IE 9]>
      <script src=\"";
        // line 49
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/html5shiv.js"), "html", null, true);
        echo "\"></script>
    <![endif]-->

    <!-- [if IE 8] -->
    <script  src=\"";
        // line 53
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/respond.min.js"), "html", null, true);
        echo "\"></script>
  ";
        // line 54
        $this->displayBlock('head_scripts', $context, $blocks);
        // line 55
        echo "

    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement(\"script\");
            hm.src = \"//hm.baidu.com/hm.js?";
        // line 61
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getConst("STATIC_KEY_BAIDU"), "html", null, true);
        echo "\";
            var s = document.getElementsByTagName(\"script\")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<script>
\twindow.webVersion = \"";
        // line 68
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getVersion(), "html", null, true);
        echo "\";
</script>
<body ";
        // line 70
        if (((array_key_exists("bodyClass", $context)) ? (_twig_default_filter((isset($context["bodyClass"]) ? $context["bodyClass"] : null), "")) : (""))) {
            echo "class=\"";
            echo twig_escape_filter($this->env, (isset($context["bodyClass"]) ? $context["bodyClass"] : null), "html", null, true);
            echo "\"";
        }
        echo ">

";
        // line 72
        $this->displayBlock('body', $context, $blocks);
        // line 116
        echo "

\t";
        // line 118
        $this->env->loadTemplate("@Home/script_boot.html.twig")->display(array_merge($context, array("script_main" => $this->env->getExtension('topxia_web_twig')->getAssetUrl("bundles/web/js/app.js"))));
        // line 119
        echo "


</body>
</html>
";
    }

    // line 14
    public function block_title($context, array $blocks = array())
    {
        // line 15
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.name", "瑞德口袋云"), "html", null, true);
        if ($this->env->getExtension('topxia_web_twig')->getSetting("site.slogan")) {
            echo " - ";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.slogan"), "html", null, true);
        }
        if ((!$this->env->getExtension('topxia_web_twig')->getSetting("copyright.owned"))) {
        }
    }

    // line 18
    public function block_keywords($context, array $blocks = array())
    {
    }

    // line 19
    public function block_description($context, array $blocks = array())
    {
    }

    // line 26
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 27
        echo "    <link href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/gallery2/bootstrap/3.1.1/css/bootstrap.css"), "html", null, true);
        echo "\" rel=\"stylesheet\" />
    <link rel=\"stylesheet\" media=\"screen\" href=\"";
        // line 28
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/css/common.css"), "html", null, true);
        echo "?";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getVersion(), "html", null, true);
        echo "\" />
    <link rel=\"stylesheet\" media=\"screen\" href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/css/font-awesome.min.css"), "html", null, true);
        echo "?";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getVersion(), "html", null, true);
        echo "\" />
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/global.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/login.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/bootstrap-amend.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/f_head_foot.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/index.css\"/>
      <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/course.css\"/>
      <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/course-show.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/my-cloud.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/news.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/user.css\"/>
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/ask.css\" />
    <!-- 模版样式，放在样式最底部，如有新的样子引入需放置在theme-color.css上方 -->
    <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/loadCss/static/css/theme-color.css\" />

  ";
    }

    // line 54
    public function block_head_scripts($context, array $blocks = array())
    {
    }

    // line 72
    public function block_body($context, array $blocks = array())
    {
        // line 73
        echo "
\t<div class=\"navbar navbar-inverse site-navbar\" id=\"site-navbar\"  data-counter-url=\"";
        // line 74
        echo $this->env->getExtension('routing')->getPath("user_remind_counter");
        echo "\">
            <div class=\"container-gap container\">
                <div class=\"navbar-header\">
                    <button type=\"button\" class=\"navbar-toggle\" data-toggle=\"collapse\" data-target=\".navbar-collapse\">
                        <span class=\"icon-bar\"></span>
                        <span class=\"icon-bar\"></span>
                        <span class=\"icon-bar\"></span>
                    </button>

                    ";
        // line 83
        if ($this->env->getExtension('topxia_web_twig')->getSetting("site.logo")) {
            // line 84
            echo "                        <a class=\"navbar-brand-logo all-school-logo\" href=\"";
            echo $this->env->getExtension('routing')->getPath("homepage");
            echo "\">
                            <img class=\"mrs\" src=\"/";
            // line 85
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.logo"), "html", null, true);
            if ($this->env->getExtension('topxia_web_twig')->getSetting("site.logoType")) {
                echo ".w200.jpg";
            }
            echo "\">
                            <span>瑞德口袋云</span>
                        </a>
                    ";
        } else {
            // line 89
            echo "                        <a class=\"navbar-brand-logo all-school-logo\" href=\"";
            echo $this->env->getExtension('routing')->getPath("homepage");
            echo "\">
                            <span>瑞德口袋云</span>
                        </a>
                    ";
        }
        // line 93
        echo "
                    <ul class=\"nav navbar-nav navbar-right\">
                            ";
        // line 95
        echo twig_include($this->env, $context, "@User/Partner/header-user.html.twig", array("user" => $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array())));
        echo "
                    </ul>
                </div>   
            </div>

\t\t<div class=\"navbar-hr\"></div>
\t</div>
\t
\t<div id=\"content-container\" class=\"m-height container ";
        // line 103
        echo twig_escape_filter($this->env, (isset($context["containerClassBlock"]) ? $context["containerClassBlock"] : null), "html", null, true);
        echo "\">
\t\t";
        // line 104
        $this->displayBlock('content', $context, $blocks);
        // line 105
        echo "\t</div><!-- /container -->

    ";
        // line 107
        if ((!twig_test_empty((isset($context["no_show_footpart"]) ? $context["no_show_footpart"] : null)))) {
            // line 108
            echo "    ";
        } else {
            // line 109
            echo "    ";
            echo $this->env->getExtension('http_kernel')->renderFragment($this->env->getExtension('http_kernel')->controller("Home:Default:footPartAction"));
            echo "
    ";
        }
        // line 111
        echo "      
\t";
        // line 112
        $this->displayBlock('bottom', $context, $blocks);
        // line 113
        echo "\t<div id=\"login-modal\" class=\"modal\" data-url=\"";
        echo $this->env->getExtension('routing')->getPath("login_ajax");
        echo "\"></div>
\t<div id=\"modal\" class=\"modal\" ";
        // line 114
        if ((isset($context["modalKeyboard"]) ? $context["modalKeyboard"] : null)) {
            echo "data-keyboard=\"false\"";
        }
        echo "></div>
";
    }

    // line 104
    public function block_content($context, array $blocks = array())
    {
    }

    // line 112
    public function block_bottom($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "@Home/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  313 => 112,  308 => 104,  300 => 114,  295 => 113,  293 => 112,  290 => 111,  284 => 109,  281 => 108,  279 => 107,  275 => 105,  273 => 104,  269 => 103,  258 => 95,  254 => 93,  246 => 89,  236 => 85,  231 => 84,  229 => 83,  217 => 74,  214 => 73,  211 => 72,  206 => 54,  184 => 29,  178 => 28,  173 => 27,  170 => 26,  165 => 19,  160 => 18,  150 => 15,  147 => 14,  138 => 119,  136 => 118,  132 => 116,  130 => 72,  121 => 70,  116 => 68,  106 => 61,  98 => 55,  96 => 54,  92 => 53,  85 => 49,  79 => 46,  76 => 45,  73 => 26,  67 => 24,  65 => 23,  61 => 22,  57 => 21,  52 => 19,  48 => 18,  45 => 17,  43 => 14,  38 => 10,  29 => 2,  27 => 1,);
    }
}
