<?php

/* @Home/script_boot.html.twig */
class __TwigTemplate_fd2a0ea86a49b3660faea28c4185702beafa0568c5a0c5631b92af8d0c91b6a0 extends Twig_Template
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
        if ($this->env->getExtension('topxia_web_twig')->getSetting("cdn.enabled")) {
            echo " ";
            $context["cdnUrl"] = $this->env->getExtension('topxia_web_twig')->getSetting("cdn.url");
            echo " ";
        } else {
            echo " ";
            $context["cdnUrl"] = "";
            echo " ";
        }
        // line 2
        echo "<script src=\"";
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs/seajs/2.2.1/sea.js"), "html", null, true);
        echo "\"></script>
<!--script src=\"";
        // line 3
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs/seajs-combo/1.0.1/seajs-combo.js"), "html", null, true);
        echo "\"></script-->
<script src=\"";
        // line 4
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs/seajs-log/1.0.1/seajs-log.js"), "html", null, true);
        echo "\"></script>
<!--script src=\"";
        // line 5
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs/seajs-flush/1.0.1/seajs-flush.js"), "html", null, true);
        echo "\"></script-->
<script src=\"";
        // line 6
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs/seajs-style/1.0.2/seajs-style.js"), "html", null, true);
        echo "\"></script>
<script src=\"";
        // line 7
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/seajs-global-config.js"), "html", null, true);
        echo "\"></script>
<script src=\"";
        // line 8
        echo twig_escape_filter($this->env, (isset($context["cdnUrl"]) ? $context["cdnUrl"] : null), "html", null, true);
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/function.js"), "html", null, true);
        echo "?";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getVersion(), "html", null, true);
        echo "\"></script>
<script src=\"/Public/bundles/web/js/controller/course/dashboard.js\"></script>
<script>
  var CKEDITOR_BASEPATH = '/Public/assets/libs/ckeditor/4.6.7/';
  seajs.config({
      uid: '";
        // line 13
        echo twig_escape_filter($this->env, (($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array(), "any", false, true), "id", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array(), "any", false, true), "id", array()), 0)) : (0)), "html", null, true);
        echo "',
      webCode: '";
        // line 14
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "webCode", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "webCode", array()), "")) : ("")), "html", null, true);
        echo "',
      mainScript: '";
        // line 15
        echo twig_escape_filter($this->env, (isset($context["script_main"]) ? $context["script_main"] : null), "html", null, true);
        echo "',
      scripts: ";
        // line 16
        echo twig_jsonencode_filter(_twig_default_filter($this->env->getExtension('topxia_web_twig')->exportScripts(), null));
        echo ",
      paths: ";
        // line 17
        echo twig_jsonencode_filter($this->env->getExtension('topxia_web_twig')->getJsPaths());
        echo ",
        ";
        // line 18
        if (array_key_exists("script_controller", $context)) {
            // line 19
            echo "      controller: '";
            echo twig_escape_filter($this->env, (isset($context["script_controller"]) ? $context["script_controller"] : null), "html", null, true);
            echo "',
        ";
        }
        // line 21
        echo "      dashboardJs: \"course/dashboard.js\",
         ";
        // line 22
        if (array_key_exists("script_arguments", $context)) {
            // line 23
            echo "      arguments: ";
            echo twig_jsonencode_filter((isset($context["script_arguments"]) ? $context["script_arguments"] : null));
            echo ",
        ";
        }
        // line 25
        echo "      config: ";
        echo twig_jsonencode_filter(array("api" => array("weibo" => array("key" => $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.weibo_key", "")), "qq" => array("key" => $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.qq_key", "")), "douban" => array("key" => $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.douban_key", "")), "renren" => array("key" => $this->env->getExtension('topxia_web_twig')->getSetting("login_bind.renren_key", ""))), "cloud" => array("video_player" => $this->env->getExtension('topxia_web_twig')->getParameter("cloud.video_player"), "video_player_watermark_plugin" => $this->env->getExtension('topxia_web_twig')->getParameter("cloud.video_player_watermark_plugin"), "video_player_fingerprint_plugin" => $this->env->getExtension('topxia_web_twig')->getParameter("cloud.video_player_fingerprint_plugin")), "loading_img_path" => $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/img/default/loading.gif")));
        // line 40
        echo "
  });
  var app = seajs.data;
  seajs.use(app.mainScript);
</script>";
    }

    public function getTemplateName()
    {
        return "@Home/script_boot.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  113 => 40,  110 => 25,  104 => 23,  102 => 22,  99 => 21,  93 => 19,  91 => 18,  87 => 17,  83 => 16,  79 => 15,  75 => 14,  71 => 13,  60 => 8,  55 => 7,  50 => 6,  45 => 5,  40 => 4,  35 => 3,  29 => 2,  19 => 1,);
    }
}
