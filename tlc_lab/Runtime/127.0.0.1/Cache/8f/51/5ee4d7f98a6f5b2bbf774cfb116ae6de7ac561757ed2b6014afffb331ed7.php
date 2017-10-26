<?php

/* @Home/bootstrap-modal-layout.html.twig */
class __TwigTemplate_8f515ee4d7f98a6f5b2bbf774cfb116ae6de7ac561757ed2b6014afffb331ed7 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'body' => array($this, 'block_body'),
            'footer' => array($this, 'block_footer'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["web_macro"] = $this->env->loadTemplate("@Home/macro.html.twig");
        // line 2
        echo "<div class=\"modal-dialog ";
        if (((array_key_exists("modal_class", $context)) ? (_twig_default_filter((isset($context["modal_class"]) ? $context["modal_class"] : null), "")) : (""))) {
            echo " ";
            echo twig_escape_filter($this->env, (isset($context["modal_class"]) ? $context["modal_class"] : null), "html", null, true);
        }
        echo "\">
  <div class=\"modal-content\">
    <div class=\"modal-header\">
      ";
        // line 5
        if ((!((array_key_exists("hide_close", $context)) ? (_twig_default_filter((isset($context["hide_close"]) ? $context["hide_close"] : null), false)) : (false)))) {
            // line 6
            echo "      <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
      ";
        }
        // line 8
        echo "      <h4 class=\"modal-title\">";
        $this->displayBlock('title', $context, $blocks);
        echo "</h4>
    </div>
    <div class=\"modal-body\">";
        // line 10
        $this->displayBlock('body', $context, $blocks);
        echo "</div>
    ";
        // line 11
        if ((!((array_key_exists("hide_footer", $context)) ? (_twig_default_filter((isset($context["hide_footer"]) ? $context["hide_footer"] : null), false)) : (false)))) {
            // line 12
            echo "      <div class=\"modal-footer\">";
            $this->displayBlock('footer', $context, $blocks);
            echo "</div>
    ";
        }
        // line 14
        echo "  </div>
</div>";
    }

    // line 8
    public function block_title($context, array $blocks = array())
    {
    }

    // line 10
    public function block_body($context, array $blocks = array())
    {
    }

    // line 12
    public function block_footer($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "@Home/bootstrap-modal-layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  73 => 12,  68 => 10,  63 => 8,  58 => 14,  52 => 12,  50 => 11,  46 => 10,  40 => 8,  36 => 6,  34 => 5,  24 => 2,  22 => 1,);
    }
}
