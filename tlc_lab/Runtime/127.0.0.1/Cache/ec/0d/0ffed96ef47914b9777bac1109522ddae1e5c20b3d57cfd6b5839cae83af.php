<?php

/* @Home/Default/layout.html.twig */
class __TwigTemplate_ec0d0ffed96ef47914b9777bac1109522ddae1e5c20b3d57cfd6b5839cae83af extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/layout.html.twig");

        $this->blocks = array(
            'keywords' => array($this, 'block_keywords'),
            'description' => array($this, 'block_description'),
            'home_page_new_banner' => array($this, 'block_home_page_new_banner'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 7
        $context["bodyClass"] = "homepage";
        // line 8
        $context["script_controller"] = "index";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 4
    public function block_keywords($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.seo_keywords"), "html", null, true);
    }

    // line 5
    public function block_description($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("site.seo_description"), "html", null, true);
    }

    // line 10
    public function block_home_page_new_banner($context, array $blocks = array())
    {
        // line 11
        echo "  <div>
    ";
        // line 12
        if ($this->getAttribute((isset($context["blocks"]) ? $context["blocks"] : null), "home_top_banner", array())) {
            // line 13
            echo "          ";
            if (((isset($context["blocksImgsize"]) ? $context["blocksImgsize"] : null) == "big")) {
                // line 14
                echo "                <div class=\"homepage-feature homepage-feature-slides mbl\">
                  <div class=\"cycle-pager\"></div>
                  ";
                // line 16
                echo $this->getAttribute((isset($context["blocks"]) ? $context["blocks"] : null), "home_top_banner", array());
                echo "
                </div>
          ";
            } else {
                // line 19
                echo "        <div class=\"banner-template-box\">
            <div class=\"banner-template-con\">
                <div class=\"banner-scroll-box\">
                    <div class=\"banner-scroll-img\">
                        ";
                // line 25
                echo "                            ";
                echo $this->getAttribute((isset($context["blocks"]) ? $context["blocks"] : null), "home_top_banner", array());
                echo "
                    </div>
                    <div class=\"banner-scroll-btn\"><span class=\"active\">•</span></div>
                </div>
            </div>
        </div>
              <script type=\"text/javascript\" src=\"";
                // line 31
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/jquery/1.11.3/jquery-1.11.3.min.js"), "html", null, true);
                echo "\"></script>
              <script type=\"text/javascript\">
                  var index = 0;
                  timer = null;
                  len=\$(\".banner-scroll-img a\").length;
                  //by yjl start
                  if(len > 1){
                      var str = '';
                      for(var i = 0; i < len-1; i ++ ){
                          str +=\"<span>•</span>\";
                      }
                      \$(\".banner-scroll-btn\").append(str);
                  }
                  //by yjl end
                  function NextPage(){
                      if (index > len-1)
                          index = 0;
                      \$(\".banner-scroll-img a\").stop(true,false).filter(\":visible\").fadeOut(500).parent().children().eq(index).fadeIn(1000);
                      \$('.banner-scroll-btn span').removeClass('active');
                      \$('.banner-scroll-btn span').eq(index).addClass('active');
                      bgColor();
                  }
                  function PrevPage(){
                      if(index<0)
                          index = len-1;
                      \$(\".banner-scroll-img a\").stop(true,false).filter(\":visible\").fadeOut(500).parent().children().eq(index).fadeIn(1000);
                      \$('.banner-scroll-btn span').removeClass('active');
                      \$('.banner-scroll-btn span').eq(index).addClass('active');
                      bgColor();
                  }
                  function bgColor(){
                      var yanse= \$(\".banner-scroll-img a\").eq(index).attr('yanse') ;
                          yanse = yanse ?  yanse : '';
                      var dataimg=\$(\".banner-scroll-img a\").eq(index).attr('data-img');
                          dataimg = dataimg ?  dataimg : '';
                       if (dataimg.replace(/(^s*)|(s*\$)/g, \"\").length != 0 && yanse.replace(/(^s*)|(s*\$)/g, \"\").length == 0){
                        \$('.banner-template-box').removeAttr(\"style\");
                        \$('.banner-template-box').css(\"background\",'url(' + dataimg + ')');
                        \$('.banner-template-box').css(\"backgroundPosition\",\"center center\");
                        }
                      if (dataimg.replace(/(^s*)|(s*\$)/g, \"\").length == 0 && yanse.replace(/(^s*)|(s*\$)/g, \"\").length != 0){
                        \$('.banner-template-box').removeAttr(\"style\");
                        \$('.banner-template-box').css({\"background-color\":yanse}); 
                        }
                      if (dataimg.replace(/(^s*)|(s*\$)/g, \"\").length != 0 && yanse.replace(/(^s*)|(s*\$)/g, \"\").length != 0){
                        \$('.banner-template-box').removeAttr(\"style\");
                        \$('.banner-template-box').css(\"background\",'url(' + dataimg + ')');
                        \$('.banner-template-box').css(\"backgroundPosition\",\"center center\"); 
                        \$('.banner-template-box').css({\"background-color\":yanse}); 
                        }
                        
                  }
                  var timer = setInterval(function(){index++ ;NextPage();},3000)
                  \$(\".banner-template-box\").hover(function(){
                      clearInterval(timer);
                  },function(){
                      timer = setInterval(function(){index++ ;NextPage();},3000)
                  })

                  \$('.banner-scroll-btn span').click(function(){
                      index=\$(this).index();
                      NextPage();
                  });
                  bgColor();
              </script>
        ";
            }
            // line 97
            echo "  ";
        }
        // line 98
        echo "    </div>
";
    }

    public function getTemplateName()
    {
        return "@Home/Default/layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  155 => 98,  152 => 97,  83 => 31,  73 => 25,  67 => 19,  61 => 16,  57 => 14,  54 => 13,  52 => 12,  49 => 11,  46 => 10,  40 => 5,  34 => 4,  29 => 8,  27 => 7,);
    }
}
