<?php

/* Signin/login.html.twig */
class __TwigTemplate_4e7fa127771f77688cb83e1cf31be1520b16f08a44997c2ccc26e67b3642512e extends Twig_Template
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
    <meta http-equiv=\"X-UA-Compatible\" content=\"IE=7;IE=9;IE=10;IE=Edge;IE=8\">
    <meta name=\"renderer\" content=\"webkit\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>";
        // line 13
        $this->displayBlock('title', $context, $blocks);
        // line 16
        echo "</title>
    <meta name=\"keywords\" content=\"";
        // line 17
        $this->displayBlock('keywords', $context, $blocks);
        echo "\" />
    <meta name=\"description\" content=\"";
        // line 18
        $this->displayBlock('description', $context, $blocks);
        echo "\" />
    <meta content=\"";
        // line 19
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderCsrfToken("site"), "html", null, true);
        echo "\" name=\"csrf-token\" />
    ";
        // line 20
        echo $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.verify_code", "");
        echo "
    ";
        // line 21
        if ($this->env->getExtension('topxia_web_twig')->getSetting("site.favicon")) {
            // line 22
            echo "        <link href=\"/";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.favicon"), "html", null, true);
            echo "\" rel=\"shortcut icon\" />
    ";
        }
        // line 24
        echo "    ";
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 33
        echo "    <!--[if lt IE 9]>
    <script src=\"";
        // line 34
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/html5shiv.js"), "html", null, true);
        echo "\"></script>
    <![endif]-->

    <!--[if IE 8]>
    <script src=\"";
        // line 38
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/respond.min.js"), "html", null, true);
        echo "\"></script>
    <![endif]-->

    ";
        // line 41
        $this->displayBlock('head_scripts', $context, $blocks);
        // line 42
        echo "
    ";
        // line 43
        $context["script_controller"] = "auth/login";
        // line 44
        echo "</head>
<script>
    window.webVersion = \"";
        // line 46
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getVersion(), "html", null, true);
        echo "\";
</script>
<body>
<div class=\"wrapper\">
    <div class=\"left-img\">
        <img src=\"";
        // line 51
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/left.png"), "html", null, true);
        echo "\"/>
    </div>
    <div class=\"right-content\">
        <div>
            <h1 class=\"title\"><img src=\"";
        // line 55
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/logo.png"), "html", null, true);
        echo "\"/><a href=\"/\">瑞德口袋云</a></h1>
            <div class=\"login-form panel panel-default panel-page\">
                <div class=\"form\">
                    <form id=\"login-form\" data-url=\"";
        // line 58
        echo $this->env->getExtension('routing')->getPath("check_login");
        echo "\" data-trigger=\"";
        echo twig_escape_filter($this->env, ((array_key_exists("trigger", $context)) ? (_twig_default_filter((isset($context["trigger"]) ? $context["trigger"] : null), "")) : ("")), "html", null, true);
        echo "\" data-goto=\"";
        if ((isset($context["goto_url"]) ? $context["goto_url"] : null)) {
            echo twig_escape_filter($this->env, (isset($context["goto_url"]) ? $context["goto_url"] : null), "html", null, true);
        } else {
            echo $this->env->getExtension('routing')->getPath("my");
        }
        echo "\">
                        <div class=\"form-group\">
                            <input type=\"text\" placeholder=\"请输入账号\" class=\"form-input\" name=\"username\" id=\"login_username\"/>
                            <p class=\"help-block glyphicon hide\" style=\"color:#a94442;margin-top:10px;\">
                                <i class=\"glyphicon pull-left\"></i>
                                <span class=\"text-danger mls\"></span>
                            </p>
                        </div>
                        <div class=\"form-group\">
                            <input type=\"password\" placeholder=\"请输入密码\" class=\"form-input\" name=\"password\" id=\"login_password\"/>
                            <p class=\"help-block glyphicon hide\" style=\"color:#a94442;margin-top:10px;\">
                                <i class=\" glyphicon pull-left\"></i>
                                <span class=\"text-danger mls\"></span>
                            </p>
                        </div>
                        <div class=\"form-group dl-box ";
        // line 73
        if (((isset($context["errorNum"]) ? $context["errorNum"] : null) < 3)) {
            echo "hide";
        }
        echo "\" data-errornum=\"";
        echo twig_escape_filter($this->env, (isset($context["errorNum"]) ? $context["errorNum"] : null), "html", null, true);
        echo "\">
                            <div class=\"controls row\">
                                <div class=\"col-md-6 col-sm-6 col-xs-6\" >
                                    <input type=\"text\" class=\"form-input\" placeholder=\"请输入验证码\" id=\"captcha_num\" name=\"captcha_num\" maxlength=\"5\"  data-url=\"";
        // line 76
        echo $this->env->getExtension('routing')->getPath("register_captcha_check");
        echo "\">
                                    <p class=\"help-block glyphicon hide\" style=\"color:#a94442;margin-top:10px;\">
                                        <i class=\" glyphicon pull-left\"></i>
                                        <span class=\"text-danger mls\"></span>
                                    </p>
                                </div>
                                <div class=\"col-md-4 col-sm-4 col-xs-4\">
                                    <img src=\"";
        // line 83
        echo $this->env->getExtension('routing')->getPath("register_captcha_num");
        echo "\" data-url=\"";
        echo $this->env->getExtension('routing')->getPath("register_captcha_num");
        echo "\" id=\"getcode_num\" title=\"看不清，点击换一张\" style=\"max-width:100%;height:50px;cursor:pointer;margin-top: 23px;\" >
                                </div>
                                <div class=\"col-md-2 col-sm-2 col-xs-2\">
                                    <i class=\"fa fa-refresh\" style=\"cursor:pointer;font-size:20px;color:#666;margin-top:5px;\"></i>
                                </div>
                            </div>
                        </div>
                        <input type=\"button\" value=\"登录\" id=\"login-btn\" class=\"form-btn\"/>
                        <div class=\"side-btn\">
                            <div class=\"left-text\">
                                <input id=\"remember-pwd\" class=\"remember-pwd\" type=\"checkbox\"><label for=\"remember-pwd\">记住密码</label>
                                <a class=\"forget-pwd\" href=\"#\">忘记密码？</a>
                            </div>
                            <a href=\"#\" class=\"register-btn\">
                                <img src=\"";
        // line 97
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/arrow-icon2.png"), "html", null, true);
        echo "\"/>注册
                            </a>
                        </div>
                    </form>
                </div>
                <div class=\"share_block\">
                    <p>快速登录</p>
                    <img src=\"";
        // line 104
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/weibo.png"), "html", null, true);
        echo "\"/>
                    <img src=\"";
        // line 105
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/qq.png"), "html", null, true);
        echo "\"/>
                    <img src=\"";
        // line 106
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("static/img/login-v2/wechat.png"), "html", null, true);
        echo "\"/>
                </div>
            </div>
        </div>
    </div>
    <div class=\"copyright\">
        <p class=\"line\"></p>
        <p class=\"copyright-text\">";
        // line 113
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.icp"), "html", null, true);
        echo " &nbsp;&nbsp;";
        if ($this->env->getExtension('topxia_web_twig')->getSetting("site.contact")) {
            echo "联系方式：";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.contact"), "html", null, true);
        }
        echo "</p>
        <p class=\"line\"></p>
    </div>
</div>

";
        // line 118
        $this->env->loadTemplate("@Home/script_boot.html.twig")->display(array_merge($context, array("script_main" => $this->env->getExtension('topxia_web_twig')->getAssetUrl("bundles/web/js/app.js"))));
        // line 119
        echo "
</body>
</html>
";
    }

    // line 13
    public function block_title($context, array $blocks = array())
    {
        // line 14
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.name", "redcloud"), "html", null, true);
    }

    // line 17
    public function block_keywords($context, array $blocks = array())
    {
    }

    // line 18
    public function block_description($context, array $blocks = array())
    {
    }

    // line 24
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 25
        echo "        ";
        // line 26
        echo "        ";
        // line 27
        echo "        ";
        // line 28
        echo "        <!--[if lt IE 8]>
        <link href=\"";
        // line 29
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/css/oldie.css"), "html", null, true);
        echo "\" rel=\"stylesheet\">
        <![endif]-->
        <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"/Public/static/css/login_v2.css\"/>
    ";
    }

    // line 41
    public function block_head_scripts($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "Signin/login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  270 => 41,  262 => 29,  259 => 28,  257 => 27,  255 => 26,  253 => 25,  250 => 24,  245 => 18,  240 => 17,  236 => 14,  233 => 13,  226 => 119,  224 => 118,  211 => 113,  201 => 106,  197 => 105,  193 => 104,  183 => 97,  164 => 83,  154 => 76,  144 => 73,  118 => 58,  112 => 55,  105 => 51,  97 => 46,  93 => 44,  91 => 43,  88 => 42,  86 => 41,  80 => 38,  73 => 34,  70 => 33,  67 => 24,  61 => 22,  59 => 21,  55 => 20,  51 => 19,  47 => 18,  43 => 17,  40 => 16,  38 => 13,  26 => 2,  24 => 1,);
    }
}
