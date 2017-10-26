<?php

/* @Home/macro.html.twig */
class __TwigTemplate_50b93db3378c89833c1bff395b5bb037490e43561004bea107449e17fc9a3ff0 extends Twig_Template
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
        // line 14
        echo "    
";
        // line 23
        echo "
";
        // line 31
        echo "
";
        // line 54
        echo "

";
    }

    // line 1
    public function getuser_avatar($__user__ = null, $__class__ = null, $__center__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "user" => $__user__,
            "class" => $__class__,
            "center" => $__center__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "<a class=\"user-link user-avatar-link ";
            echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : null), "html", null, true);
            echo "\" href=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_show", array("id" => $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()), "center" => (isset($context["center"]) ? $context["center"] : null))), "html", null, true);
            echo "\">
    <img src=\"";
            // line 3
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getUserDefaultPath($this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()), "small"), "html", null, true);
            echo "\"   loaderrimg=\"1\" onerror=\"javascript:this.src='/Public/assets/img/default/pic-error.png?5.1.4';\" >
  </a>";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 7
    public function getuser_link($__user__ = null, $__class__ = null, $__center__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "user" => $__user__,
            "class" => $__class__,
            "center" => $__center__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 8
            echo "  ";
            if ((isset($context["user"]) ? $context["user"] : null)) {
                // line 9
                echo "    <a ";
                if ((isset($context["class"]) ? $context["class"] : null)) {
                    echo "class=\"";
                    echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : null), "html", null, true);
                    echo "\"";
                }
                echo " href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_show", array("id" => $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()), "center" => (isset($context["center"]) ? $context["center"] : null))), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "nickname", array()), "html", null, true);
                echo "</a>
  ";
            } else {
                // line 11
                echo "    <span class=\"text-muted\">用户已删除</span>
  ";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 16
    public function getuser_link_blank($__user__ = null, $__class__ = null, $__center__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "user" => $__user__,
            "class" => $__class__,
            "center" => $__center__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 17
            echo "  ";
            if ((isset($context["user"]) ? $context["user"] : null)) {
                // line 18
                echo "    <a ";
                if ((isset($context["class"]) ? $context["class"] : null)) {
                    echo "class=\"";
                    echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : null), "html", null, true);
                    echo "\"";
                }
                echo " target=\"_blank\" href=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("user_show", array("id" => $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "id", array()), "center" => (isset($context["center"]) ? $context["center"] : null))), "html", null, true);
                echo "\">";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["user"]) ? $context["user"] : null), "nickname", array()), "html", null, true);
                echo "</a>
  ";
            } else {
                // line 20
                echo "    <span class=\"text-muted\">用户已删除</span>
  ";
            }
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 24
    public function getflash_messages()
    {
        $context = $this->env->getGlobals();

        $blocks = array();

        ob_start();
        try {
            echo "  
  ";
            // line 25
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "session", array()), "flashbag", array()), "all", array(), "method"));
            foreach ($context['_seq'] as $context["type"] => $context["flashMessages"]) {
                // line 26
                echo "    ";
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($context["flashMessages"]);
                foreach ($context['_seq'] as $context["_key"] => $context["flashMessage"]) {
                    // line 27
                    echo "      <div class=\"alert alert-";
                    echo twig_escape_filter($this->env, $context["type"], "html", null, true);
                    echo "\">";
                    echo $context["flashMessage"];
                    echo "</div>
    ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['flashMessage'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 29
                echo "  ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['type'], $context['flashMessages'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 32
    public function getbytesToSize($__bytes__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "bytes" => $__bytes__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 33
            echo "  ";
            ob_start();
            // line 34
            echo "
      ";
            // line 35
            $context["kilobyte"] = 1024;
            // line 36
            echo "      ";
            $context["megabyte"] = ((isset($context["kilobyte"]) ? $context["kilobyte"] : null) * 1024);
            // line 37
            echo "      ";
            $context["gigabyte"] = ((isset($context["megabyte"]) ? $context["megabyte"] : null) * 1024);
            // line 38
            echo "      ";
            $context["terabyte"] = ((isset($context["gigabyte"]) ? $context["gigabyte"] : null) * 1024);
            // line 39
            echo "
      ";
            // line 40
            if (((isset($context["bytes"]) ? $context["bytes"] : null) < (isset($context["kilobyte"]) ? $context["kilobyte"] : null))) {
                // line 41
                echo "          ";
                echo twig_escape_filter($this->env, ((isset($context["bytes"]) ? $context["bytes"] : null) . " B"), "html", null, true);
                echo "
      ";
            } elseif (((isset($context["bytes"]) ? $context["bytes"] : null) < (isset($context["megabyte"]) ? $context["megabyte"] : null))) {
                // line 43
                echo "          ";
                echo twig_escape_filter($this->env, (twig_number_format_filter($this->env, ((isset($context["bytes"]) ? $context["bytes"] : null) / (isset($context["kilobyte"]) ? $context["kilobyte"] : null)), 2, ".") . " KB"), "html", null, true);
                echo "
      ";
            } elseif (((isset($context["bytes"]) ? $context["bytes"] : null) < (isset($context["gigabyte"]) ? $context["gigabyte"] : null))) {
                // line 45
                echo "          ";
                echo twig_escape_filter($this->env, (twig_number_format_filter($this->env, ((isset($context["bytes"]) ? $context["bytes"] : null) / (isset($context["megabyte"]) ? $context["megabyte"] : null)), 2, ".") . " MB"), "html", null, true);
                echo "
      ";
            } elseif (((isset($context["bytes"]) ? $context["bytes"] : null) < (isset($context["terabyte"]) ? $context["terabyte"] : null))) {
                // line 47
                echo "          ";
                echo twig_escape_filter($this->env, (twig_number_format_filter($this->env, ((isset($context["bytes"]) ? $context["bytes"] : null) / (isset($context["gigabyte"]) ? $context["gigabyte"] : null)), 2, ".") . " GB"), "html", null, true);
                echo "
      ";
            } else {
                // line 49
                echo "          ";
                echo twig_escape_filter($this->env, (twig_number_format_filter($this->env, ((isset($context["bytes"]) ? $context["bytes"] : null) / (isset($context["terabyte"]) ? $context["terabyte"] : null)), 2, ".") . " TB"), "html", null, true);
                echo "
      ";
            }
            // line 51
            echo "
  ";
            echo trim(preg_replace('/>\s+</', '><', ob_get_clean()));
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 56
    public function getpaginator($__paginator__ = null, $__class__ = null, $__aClass__ = "ajax-load", $__pageRecord__ = 1)
    {
        $context = $this->env->mergeGlobals(array(
            "paginator" => $__paginator__,
            "class" => $__class__,
            "aClass" => $__aClass__,
            "pageRecord" => $__pageRecord__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 57
            echo "    ";
            $context["maxPage"] = $this->env->getExtension('topxia_web_twig')->ceil(($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "itemCount", array()) / $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "perPageCount", array())));
            // line 58
            echo "    ";
            if (($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "lastPage", array()) > 1)) {
                // line 59
                echo "        <ul class=\"pagination ";
                echo twig_escape_filter($this->env, (isset($context["class"]) ? $context["class"] : null), "html", null, true);
                echo "\" data-maxPage = \"";
                echo twig_escape_filter($this->env, (isset($context["maxPage"]) ? $context["maxPage"] : null), "html", null, true);
                echo "\"  >
            ";
                // line 60
                if (($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()) > 6)) {
                    // line 61
                    echo "                <li class=\"";
                    echo twig_escape_filter($this->env, (isset($context["aClass"]) ? $context["aClass"] : null), "html", null, true);
                    echo "\"><a  href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "getPageUrl", array(0 => $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "firstPage", array())), "method"), "html", null, true);
                    echo "\">首页</a></li>
            ";
                }
                // line 63
                echo "            ";
                if (($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()) == $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "firstPage", array()))) {
                    // line 64
                    echo "                <li class=\"disabled\"><span>上一页</span></li>
            ";
                } else {
                    // line 66
                    echo "                <li><a class=\"";
                    echo twig_escape_filter($this->env, (isset($context["aClass"]) ? $context["aClass"] : null), "html", null, true);
                    echo "\" href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "getPageUrl", array(0 => $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "previousPage", array())), "method"), "html", null, true);
                    echo "\">上一页</a></li>
            ";
                }
                // line 68
                echo "            ";
                $context['_parent'] = (array) $context;
                $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "pages", array()));
                foreach ($context['_seq'] as $context["_key"] => $context["page"]) {
                    // line 69
                    echo "                <li ";
                    if (($context["page"] == $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()))) {
                        echo "class=\"active\"";
                    }
                    echo "><a class=\"";
                    echo twig_escape_filter($this->env, (isset($context["aClass"]) ? $context["aClass"] : null), "html", null, true);
                    echo "\" href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "getPageUrl", array(0 => $context["page"]), "method"), "html", null, true);
                    echo "\">";
                    echo twig_escape_filter($this->env, $context["page"], "html", null, true);
                    echo "</a></li>
            ";
                }
                $_parent = $context['_parent'];
                unset($context['_seq'], $context['_iterated'], $context['_key'], $context['page'], $context['_parent'], $context['loop']);
                $context = array_intersect_key($context, $_parent) + $_parent;
                // line 71
                echo "
            ";
                // line 72
                if (($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()) == $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "lastPage", array()))) {
                    // line 73
                    echo "                <li class=\"disabled\"><span>下一页</span></li>
            ";
                } else {
                    // line 75
                    echo "                <li><a class=\"";
                    echo twig_escape_filter($this->env, (isset($context["aClass"]) ? $context["aClass"] : null), "html", null, true);
                    echo "\" href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "getPageUrl", array(0 => $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "nextPage", array())), "method"), "html", null, true);
                    echo "\" id=\"next-page\">下一页</a></li>
            ";
                }
                // line 77
                echo "            ";
                if (($this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()) != $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "lastPage", array()))) {
                    // line 78
                    echo "                <li class=\"";
                    echo twig_escape_filter($this->env, (isset($context["aClass"]) ? $context["aClass"] : null), "html", null, true);
                    echo "\" ><a href=\"";
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "getPageUrl", array(0 => $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "lastPage", array())), "method"), "html", null, true);
                    echo "\">末页</a></li>
            ";
                }
                // line 80
                echo "        </ul>
        ";
                // line 81
                if (((isset($context["pageRecord"]) ? $context["pageRecord"] : null) == 1)) {
                    // line 82
                    echo "            <ul class=\"pagination\">
                <li><span class=\"text-muted\">第";
                    // line 83
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "currentPage", array()), "html", null, true);
                    echo "/";
                    echo twig_escape_filter($this->env, (isset($context["maxPage"]) ? $context["maxPage"] : null), "html", null, true);
                    echo "页</span></li>
                <li><span class=\"text-muted\">共";
                    // line 84
                    echo twig_escape_filter($this->env, $this->getAttribute((isset($context["paginator"]) ? $context["paginator"] : null), "itemCount", array()), "html", null, true);
                    echo "条记录</span></li>
            </ul>
        ";
                }
                // line 87
                echo "    ";
            }
            // line 88
            echo "

";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "@Home/macro.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  400 => 88,  397 => 87,  391 => 84,  385 => 83,  382 => 82,  380 => 81,  377 => 80,  369 => 78,  366 => 77,  358 => 75,  354 => 73,  352 => 72,  349 => 71,  332 => 69,  327 => 68,  319 => 66,  315 => 64,  312 => 63,  304 => 61,  302 => 60,  295 => 59,  292 => 58,  289 => 57,  275 => 56,  262 => 51,  256 => 49,  250 => 47,  244 => 45,  238 => 43,  232 => 41,  230 => 40,  227 => 39,  224 => 38,  221 => 37,  218 => 36,  216 => 35,  213 => 34,  210 => 33,  199 => 32,  184 => 29,  173 => 27,  168 => 26,  164 => 25,  153 => 24,  140 => 20,  126 => 18,  123 => 17,  110 => 16,  97 => 11,  83 => 9,  80 => 8,  67 => 7,  54 => 3,  47 => 2,  34 => 1,  28 => 54,  25 => 31,  22 => 23,  19 => 14,);
    }
}
