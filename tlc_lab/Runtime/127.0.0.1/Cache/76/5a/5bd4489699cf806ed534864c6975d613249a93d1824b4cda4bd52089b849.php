<?php

/* Course/learn.html.twig */
class __TwigTemplate_765a5bd4489699cf806ed534864c6975d613249a93d1824b4cda4bd52089b849 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Home/layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'head_scripts' => array($this, 'block_head_scripts'),
            'content' => array($this, 'block_content'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Home/layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 9
        $context["no_show_footpart"] = 1;
        // line 27
        $context["hideSetupHint"] = true;
        // line 29
        if ($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array()), "isTeacher", array())) {
            // line 30
            $context["script_arguments"] = array("plugins" => array(0 => "lesson", 1 => "material", 2 => "note"));
        } else {
            // line 32
            $context["script_arguments"] = array("plugins" => array(0 => "lesson", 1 => "material", 2 => "note"));
        }
        // line 35
        $context["script_controller"] = "course/learn";
        // line 36
        $context["bodyClass"] = "lesson-dashboard-page lesson-overflow";
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 2
    public function block_title($context, array $blocks = array())
    {
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
        echo " - ";
        $this->displayParentBlock("title", $context, $blocks);
    }

    // line 4
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 5
        echo "  ";
        $this->displayParentBlock("stylesheets", $context, $blocks);
        echo "
  <link rel=\"stylesheet\" media=\"screen\" href=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/jquery-plugin/perfect-scrollbar/0.4.8/perfect-scrollbar.css"), "html", null, true);
        echo "\" />
";
    }

    // line 11
    public function block_head_scripts($context, array $blocks = array())
    {
        // line 12
        echo "  <script>
    var center = \"";
        // line 13
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"), "html", null, true);
        echo "\";
    var windowUrl = {
        \"init\" : \"";
        // line 15
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["windowUrl"]) ? $context["windowUrl"] : null), "init", array()), "html", null, true);
        echo "\",
        \"add\" : \"";
        // line 16
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["windowUrl"]) ? $context["windowUrl"] : null), "add", array()), "html", null, true);
        echo "\",
        \"del\" : \"";
        // line 17
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["windowUrl"]) ? $context["windowUrl"] : null), "del", array()), "html", null, true);
        echo "\"
    };
    ";
        // line 19
        if ($this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array()), "isTeacher", array())) {
            // line 20
            echo "    var isTeacher = true;
    ";
        } else {
            // line 22
            echo "    var isTeacher = false;
    ";
        }
        // line 24
        echo "  </script>
";
    }

    // line 38
    public function block_content($context, array $blocks = array())
    {
        // line 39
        echo "  <div class=\"container lesson-dashboard\" id=\"lesson-dashboard\"
       data-course-id=\"";
        // line 40
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "\"
       data-course-uri=\"";
        // line 41
        echo "/course/";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "\"
       data-dashboard-uri=\"";
        // line 42
        echo "/course/";
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "/learn\"
       data-hide-media-lesson-learn-btn=\"";
        // line 43
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->isFeatureEnabled("hide_media_lesson_learn_btn"), "html", null, true);
        echo "\">
    <div class=\"dashboard-content\">
      <a class=\"btn btn-primary  nav-btn back-course-btn\" href=\"";
        // line 45
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\"><span class=\"glyphicon glyphicon-chevron-left\" ></span> 返回课程</a>
      <a class=\"btn btn-primary  nav-btn prev-lesson-btn\" href=\"javascript:\" data-role=\"prev-lesson\" data-placement=\"right\" title=\"上一课程内容\"><span class=\"glyphicon glyphicon-chevron-up\" title=\"上一课程内容\"></span></a>
      <a class=\"btn btn-primary nav-btn next-lesson-btn\" href=\"javascript:\" data-role=\"next-lesson\" data-placement=\"right\" title=\"下一课程内容\"><span class=\"glyphicon glyphicon-chevron-down\"></span></a>

      <div class=\"dashboard-header\">
        <div class=\"pull-left title-group\">
          <span class=\"chapter-label\">第<span data-role=\"chapter-number\"></span>";
        // line 51
        if ($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name")) {
            echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.chapter_name"), "章"), "html", null, true);
        } else {
            echo "章";
        }
        echo "</span>
          <span class=\"divider\">&raquo;</span>
          <span class=\"chapter-label\">第<span data-role=\"unit-number\"></span>";
        // line 53
        if ($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name")) {
            echo twig_escape_filter($this->env, _twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("default.part_name"), "节"), "html", null, true);
        } else {
            echo "节";
        }
        echo "</span>
          <span class=\"divider\">&raquo;</span>
          <span class=\"item-label\">课程内容<span data-role=\"lesson-number\">正在加载...</span></span>
          <span class=\"item-title\" data-role=\"lesson-title\"></span>
        </div>
      </div>

      <div class=\"dashboard-body\">
        <div class=\"lesson-content\" id=\"lesson-video-content\" data-role=\"lesson-content\" style=\"display:none;\"
                ";
        // line 62
        if ((($this->env->getExtension('topxia_web_twig')->getSetting("storage.video_watermark") > 0) && $this->env->getExtension('topxia_web_twig')->getSetting("storage.video_watermark_image"))) {
            // line 63
            echo "                  data-watermark=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getFileUrl($this->env->getExtension('topxia_web_twig')->getSetting("storage.video_watermark_image"), null, true), "html", null, true);
            echo "\"
                ";
        }
        // line 65
        echo "                ";
        if (($this->env->getExtension('topxia_web_twig')->getSetting("storage.video_fingerprint") && $this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array()))) {
            // line 66
            echo "                  data-fingerprint=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getUrl("cloud_video_fingerprint", array("userId" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array()), "id", array()))), "html", null, true);
            echo "\"
                ";
        }
        // line 68
        echo "             data-user-id=\"";
        echo twig_escape_filter($this->env, $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "user", array()), "id", array()), "html", null, true);
        echo "\"
        >
          <link rel=\"stylesheet\" media=\"screen\" type=\"text/css\" href=\"";
        // line 70
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/gallery2/mediaelement/2.22.0/mediaelementplayer.css"), "html", null, true);
        echo "\"/>
          ";
        // line 72
        echo "          ";
        // line 73
        echo "          ";
        // line 74
        echo "        </div>
        <div class=\"watermarkEmbedded\" ></div>

        <div class=\"lesson-content lesson-content-audio\" id=\"lesson-audio-content\" data-role=\"lesson-content\" style=\"display:none;\"></div>
        <div class=\"lesson-content\" id=\"lesson-swf-content\" data-role=\"lesson-content\" style=\"display:none;\"></div>
        <div class=\"lesson-content\" id=\"lesson-iframe-content\" data-role=\"lesson-content\" style=\"display:none;\"></div>
        <div class=\"lesson-content lesson-content-text\" id=\"lesson-text-content\" data-role=\"lesson-content\" style=\"display:none;\"
                ";
        // line 81
        if ((_twig_default_filter($this->env->getExtension('topxia_web_twig')->getSetting("course.copy_enabled"), 0) > 0)) {
            echo " oncopy=\"return false;\" oncut=\"return false;\" onselectstart=\"return false\" oncontextmenu=\"return false;\"";
        }
        echo ">
          <div class=\"lesson-content-text-body\"></div>
        </div>

        <div class=\"lesson-content lesson-content-document\" id=\"lesson-document-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-document-body\"></div>
        </div>

        <div class=\"lesson-content lesson-content-text\" id=\"lesson-live-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\"></div>
        </div>
        <div class=\"lesson-content lesson-content-text\" id=\"lesson-unpublished-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\">当前课程内容正在编辑中，暂时无法观看。</div>
        </div>

        <div class=\"lesson-content lesson-content-text\" id=\"lesson-testpaper-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\"></div>
        </div>
        <div class=\"lesson-content lesson-content-text\" id=\"lesson-testtask-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\"></div>
        </div>
        <div class=\"lesson-content lesson-content-text\" id=\"lesson-practice-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\"></div>
        </div>
        <div class=\"lesson-content lesson-content-text\" id=\"lesson-ppt-content\" data-role=\"lesson-content\" style=\"display:none;\">
          <div class=\"lesson-content-text-body\"></div>
        </div>

      </div>

      <div class=\"dashboard-footer clearfix\">

      </div>
    </div>

    <div class=\"toolbar toolbar-open\" id=\"lesson-dashboard-toolbar\">
      <div class=\"toolbar-nav\">

\t     <div id=\"tools\" style=\"height:25px;\"></div>
          <div id=\"clearTimer\" style=\"background:blue;\">停止</div>
          <div style=\"position:absolute;left:-20px;top:200px;z-index:10;\">
              <span class=\"hide-desktop\" id=\"hidetimer\">隐藏工具栏</span>
              <span class=\"hide-desktop\" id=\"showtimer\" style=\"display:none;\">显示工具栏</span>
          </div>

      </div>
      <div class=\"toolbar-pane-container\">
      </div>
    </div>

  </div>

  <div class=\"modal\" id=\"course-learned-modal\" style=\"display:none;\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
          <h4 class=\"modal-title\">学习进度提示</h4>
        </div>
        <div class=\"modal-body\">
          <p class=\"text-success\">赞一个，这个课程你已经都学完啦，你可以再回顾一下或者去看看别的课程～～～</p>
        </div>
        <div class=\"modal-footer\">
          <a href=\"";
        // line 144
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_show", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\" class=\"btn btn-primary\">回课程主页</a>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <div class=\"modal\" id=\"mediaPlayed-control-modal\" style=\"display:none;\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
          <h4 class=\"modal-title\">媒体课程内容学习提示</h4>
        </div>
        <div class=\"modal-body\">
          <p class=\"text-success\">此课程内容设置了必须完整播放完整个课程内容才能学完～～</p>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  <div class=\"modal\" id=\"homeworkDone-control-modal\" style=\"display:none;\">
    <div class=\"modal-dialog\">
      <div class=\"modal-content\">
        <div class=\"modal-header\">
          <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-hidden=\"true\">&times;</button>
          <h4 class=\"modal-title\">作业未完成提示</h4>
        </div>
        <div class=\"modal-body\">
          <p class=\"text-success\">此课程内容设置了必须做完本课程内容作业并提交后才能学完～～</p>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->

  ";
        // line 178
        if (((isset($context["scheme"]) ? $context["scheme"] : null) == "https")) {
            // line 179
            echo "    <script src=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/polyv/polyvplayer_v2.0_https.min.js"), "html", null, true);
            echo "\"></script>
  ";
        } else {
            // line 181
            echo "    <script src=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/polyv/polyvplayer_v2.0.min.js"), "html", null, true);
            echo "\"></script>
  ";
        }
        // line 183
        echo "

";
    }

    public function getTemplateName()
    {
        return "Course/learn.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  326 => 183,  320 => 181,  314 => 179,  312 => 178,  275 => 144,  207 => 81,  198 => 74,  196 => 73,  194 => 72,  190 => 70,  184 => 68,  178 => 66,  175 => 65,  169 => 63,  167 => 62,  151 => 53,  142 => 51,  133 => 45,  128 => 43,  123 => 42,  118 => 41,  114 => 40,  111 => 39,  108 => 38,  103 => 24,  99 => 22,  95 => 20,  93 => 19,  88 => 17,  84 => 16,  80 => 15,  75 => 13,  72 => 12,  69 => 11,  63 => 6,  58 => 5,  55 => 4,  47 => 2,  42 => 36,  40 => 35,  37 => 32,  34 => 30,  32 => 29,  30 => 27,  28 => 9,);
    }
}
