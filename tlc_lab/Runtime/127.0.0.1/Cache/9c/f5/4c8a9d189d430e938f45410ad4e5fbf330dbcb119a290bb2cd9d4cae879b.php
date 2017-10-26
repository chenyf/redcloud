<?php

/* Default/foot-part.html.twig */
class __TwigTemplate_9cf54c8a9d189d430e938f45410ad4e5fbf330dbcb119a290bb2cd9d4cae879b extends Twig_Template
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
        echo "<div class=\"footer-box\">
            <div class=\"footer-top-con\">
                <div class=\"container\">
                    ";
        // line 4
        if (($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "friend_link_enable", array()) == 1)) {
            // line 5
            echo "                    <div class=\"blogroll-box\">
                        <h2><img src=\"/Public/static/img/blogroll-img.png\"/>友情链接</h2>
                        <ul class=\"ft-link-con clearfix\">
                            ";
            // line 8
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "friend_link", array()));
            foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                if ($context["value"]) {
                    // line 9
                    echo "                            <li><a href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "friend_link", array()), $context["key"], array(), "array"), "url", array(), "array"), "html", null, true);
                    echo "\" target=\"_blank\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "friend_link", array()), $context["key"], array(), "array"), "title", array(), "array"), "html", null, true);
                    echo "</a></li>
                        
                            ";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 11
            echo " 
                        </ul>
                    </div>
                    ";
        }
        // line 15
        echo "                    ";
        if (($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "about_us_enable", array()) == 1)) {
            // line 16
            echo "                    <div class=\"about-box\">
                        <h2><img src=\"/Public/static/img/about-img.png\"/>";
            // line 17
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["site"]) ? $context["site"] : null), "about_us_title", array()), "html", null, true);
            echo "</h2>
                        <ul class=\"ft-link-con clearfix\">
                            ";
            // line 19
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable((isset($context["about_us"]) ? $context["about_us"] : null));
            foreach ($context['_seq'] as $context["key"] => $context["value"]) {
                if ($context["value"]) {
                    echo " 
                            <li><a href=\"";
                    // line 20
                    echo twig_escape_filter($this->env, ((isset($context["test"]) ? $context["test"] : null) . $this->env->getExtension('topxia_web_twig')->U("/Home/AboutUs/aboutAction", array("id" => $this->getAttribute($context["value"], "id", array(), "array")))), "html", null, true);
                    echo "\" target=\"_blank\">";
                    echo twig_escape_filter($this->env, $this->getAttribute($context["value"], "title", array(), "array"), "html", null, true);
                    echo "</a></li>
                            ";
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['key'], $context['value'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
            // line 22
            echo "                        </ul>
                    </div>
                    ";
        }
        // line 25
        echo "                </div>
            </div>
            <div class=\"copyright-bar\">
                <div class=\"container\">
                    <p>";
        // line 29
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["site"]) ? $context["site"] : null), "icp", array()), "html", null, true);
        echo " &nbsp;&nbsp;";
        if ($this->getAttribute((isset($context["site"]) ? $context["site"] : null), "contact", array())) {
            echo "联系方式：";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["site"]) ? $context["site"] : null), "contact", array()), "html", null, true);
        }
        echo "</p>
                </div>
            </div>
        </div>";
    }

    public function getTemplateName()
    {
        return "Default/foot-part.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  95 => 29,  89 => 25,  84 => 22,  73 => 20,  66 => 19,  61 => 17,  58 => 16,  55 => 15,  49 => 11,  36 => 9,  31 => 8,  26 => 5,  24 => 4,  19 => 1,);
    }
}
