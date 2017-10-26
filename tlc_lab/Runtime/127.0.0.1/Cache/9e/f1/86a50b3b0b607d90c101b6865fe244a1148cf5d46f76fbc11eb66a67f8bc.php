<?php

/* CourseManage/base.html.twig */
class __TwigTemplate_9ef186a50b3b0b607d90c101b6865fe244a1148cf5d46f76fbc11eb66a67f8bc extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("@Course/CourseManage/courseLayout.html.twig");

        $this->blocks = array(
            'courseContent' => array($this, 'block_courseContent'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "@Course/CourseManage/courseLayout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["side_nav"] = "my-teaching-courses";
        // line 2
        $context["menu"] = "base";
        // line 4
        $context["script_controller"] = "course-manage/base";
        // line 5
        if (((isset($context["create"]) ? $context["create"] : null) != "1")) {
            // line 6
            $context["script_controller"] = "course-manage/detail";
        }
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 9
    public function block_courseContent($context, array $blocks = array())
    {
        // line 10
        echo "    ";
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()) && $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))) {
            // line 11
            echo "        ";
            $context["banShow"] = 1;
            // line 12
            echo "    ";
        }
        // line 13
        echo "    <div class=\"t-course-set-box\">
        <div class=\"cc-found-content ptl\">
            <form class=\"form-horizontal\" id=\"course-form\" method=\"post\" action=\"/Course/CourseManage/baseAction/id/";
        // line 15
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "\">
                <div class=\"form-group ";
        // line 16
        if (((isset($context["banShow"]) ? $context["banShow"] : null) == 1)) {
            echo " row-disabled ";
        }
        echo "\">
                    <label class=\"col-md-2 control-label\">课程名称<span style=\"font-size:12px;color:#ff0000;margin-left:5px;\">(必填)</span></label>

                    <div class=\"col-md-8 controls\">
                        <input type=\"text\" id=\"course_title\" name=\"title\" required=\"required\" placeholder=\"为课程起一个名称\" class=\"form-control\" value=\"";
        // line 20
        echo twig_escape_filter($this->env, (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array(), "any", true, true)) ? (_twig_default_filter($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "")) : ("")), "html", null, true);
        echo "\" ";
        if (((isset($context["banShow"]) ? $context["banShow"] : null) == 1)) {
            echo " disabled ";
        }
        echo ">

                        <div class=\"help-block\" style=\"display:none;\"></div>
                    </div>
                </div>
                <input type=\"hidden\" name=\"serializeMode\" value=\"";
        // line 25
        if ($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "serializeMode", array())) {
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "serializeMode", array()), "html", null, true);
        } else {
            echo "none";
        }
        echo "\">

                <div class=\"form-group ";
        // line 27
        if (((isset($context["banShow"]) ? $context["banShow"] : null) == 1)) {
            echo " row-disabled ";
        }
        echo "\">
                    <label class=\"col-md-2 control-label\">课程分类</label>
                    <div class=\"col-md-8 controls\">
                        <div class=\"course-relevance-specialty\">
                            <input type=\"hidden\" name=\"categoryId\" value=\"";
        // line 31
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "categoryId", array()), "html", null, true);
        echo "\"/>
                            <a class=\"btn btn-sm btn-default pull-right\" style=\"background:#edf1f2;border:1px solid #cbe8ef;\" ";
        // line 32
        if (((isset($context["banShow"]) ? $context["banShow"] : null) == 1)) {
            echo " disabled ";
        } else {
            echo " data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\" data-url=\"";
            echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseManage/category", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
            echo "\"";
        }
        echo ">选择课程分类</a>
                            <ul>
                                ";
        // line 34
        if ($this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "name", array())) {
            // line 35
            echo "                                    <li id=\"course-category-choose\" data-cateCode=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "courseCode", array()), "html", null, true);
            echo "\" data-id=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "id", array()), "html", null, true);
            echo "\">";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course_category"]) ? $context["course_category"] : null), "name", array()), "html", null, true);
            echo "</li>
                                ";
        } else {
            // line 37
            echo "                                    <li id=\"course-category-choose\"><span class=\"text-muted\" style=\"cursor:pointer\" ";
            if (((isset($context["banShow"]) ? $context["banShow"] : null) != 1)) {
                echo " data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\" data-url=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->U("Course/CourseManage/category", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
                echo " \" ";
            }
            echo " >请选择课程分类</span></li>
                                ";
        }
        // line 39
        echo "                            </ul>
                        </div>
                        <div class=\"tip-text-0\"></div>
                    </div>
                </div>
                <div class=\"form-group\">
                    <label class=\"col-md-2 control-label\">课程编号<span style=\"font-size:12px;color:#ff0000;margin-left:5px;\">(必填)</span></label>
                    <div class=\"col-md-8 controls\">
                        <input type=\"text\" name=\"number\" id=\"course-number\" rows=\"2\" class=\"form-control\" value=\"";
        // line 47
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["course"]) ? $context["course"] : null), "number");
        echo "\" style=\"width: 30%;\" />
                    </div>
                </div>
                <div class=\"form-group\">
                    <label class=\"col-md-2 control-label\">课程简介</label>
                    <div class=\"col-md-8 controls\">
                        <textarea name=\"about\" rows=\"10\" id=\"course-about-field\" class=\"form-control\" data-image-upload-url=\"";
        // line 53
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("editor_upload", array("token" => $this->env->getExtension('topxia_web_twig')->makeUpoadToken("course"))), "html", null, true);
        echo "\">";
        echo $this->env->getExtension('topxia_html_twig')->fieldValue((isset($context["course"]) ? $context["course"] : null), "about");
        echo "</textarea>
                    </div>
                </div>
                <div class=\"form-group\">
                    <label class=\"col-md-2 control-label\">选择课程图片</label>
                    <div class=\"col-md-8 controls\">
                        ";
        // line 59
        $context["posterList"] = $this->env->getExtension('topxia_web_twig')->loadDefaultCoursePoster();
        // line 60
        echo "                        <ul class=\"cc-pic-list\" id=\"course-pic\">
                            ";
        // line 61
        if (!twig_in_filter($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), array(0 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array"), 1 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array"), 2 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array")))) {
            // line 62
            echo "                                <li class=\"col-md-2 pic-list active\">
                                    <span class=\"c-icon-checkd ";
            // line 63
            if (twig_in_filter($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), array(0 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array"), 1 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array"), 2 => $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array")))) {
                echo "hide ";
            }
            echo "\" aria-hidden=\"true\"></span>
                                    <img onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  loaderrimg=\"1\" class=\"course-picture\" ";
            // line 64
            if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == "")) {
                echo "   src=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getDefaultPath("coursePicture", $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "largePicture", array()), "large"), "html", null, true);
                echo " \"  data-value=\"\" ";
            } else {
                echo " src=\"";
                echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), "html", null, true);
                echo " \"  data-value=\"";
                echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->tripPath($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array())), "html", null, true);
                echo "\" ";
            }
            echo " alt=\"";
            echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "title", array()), "html", null, true);
            echo "\" width=\"100%\">
                                </li>
                            ";
        }
        // line 67
        echo "                            <li class=\"col-md-2 pic-list ";
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array"))) {
            echo "active ";
        }
        echo "\">
                                <span class=\"c-icon-checkd ";
        // line 68
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) != $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array"))) {
            echo "hide ";
        }
        echo "\" aria-hidden=\"true\"></span>
                                <img onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  loaderrimg=\"1\"  class=\"course-picture\" src=\"";
        // line 69
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array"), "html", null, true);
        echo "\" data-value=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->tripPath($this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 0, array(), "array")), "html", null, true);
        echo "\" width=\"100%\">
                            </li>
                            <li class=\"col-md-2 pic-list ";
        // line 71
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array"))) {
            echo "active ";
        }
        echo "\">
                                <span class=\"c-icon-checkd ";
        // line 72
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) != $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array"))) {
            echo "hide ";
        }
        echo "\" aria-hidden=\"true\"></span>
                                <img onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\" loaderrimg=\"1\"  class=\"course-picture\" src=\"";
        // line 73
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array"), "html", null, true);
        echo "\" data-value=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->tripPath($this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 1, array(), "array")), "html", null, true);
        echo "\" width=\"100%\">
                            </li>
                            <li class=\"col-md-2 pic-list ";
        // line 75
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) == $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array"))) {
            echo "active ";
        }
        echo "\">
                                <span class=\"c-icon-checkd ";
        // line 76
        if (($this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()) != $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array"))) {
            echo "hide ";
        }
        echo "\" aria-hidden=\"true\"></span>
                                <img onerror=\"javascript:this.src='/Public/assets/img/default/loading-error.jpg?5.1.4';\"  loaderrimg=\"1\" class=\"course-picture\" src=\"";
        // line 77
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array"), "html", null, true);
        echo "\" data-value=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->tripPath($this->getAttribute((isset($context["posterList"]) ? $context["posterList"] : null), 2, array(), "array")), "html", null, true);
        echo "\"  width=\"100%\">
                            </li>
                            <li class=\"col-md-2 c-more-img\"><a href=\"javascript:;\" id=\"addpicture\" data-url=\"/Course/CourseManage/pictureAutoAction/id/";
        // line 79
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "/tab/0/\" data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\">更多图片 &gt;</a></li>
                        </ul>
                        <div class=\"c-add-img\">
                            <a href=\"javascript:;\"   data-url=\"/Course/CourseManage/pictureAutoAction/id/";
        // line 82
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()), "html", null, true);
        echo "/tab/1/\" data-backdrop=\"static\" data-toggle=\"modal\" data-target=\"#modal\">＋ 添加本地图片</a>
                        </div>
                    </div>
                    <input type=\"hidden\" id=\"selectPicture\" name=\"selectPicture\" value=\"";
        // line 85
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "selectPicture", array()), "html", null, true);
        echo "\"/>
                </div>
                <div class=\"form-group\">
                    <div class=\"col-md-offset-2 col-md-8 controls text-right\">
                        <button style=\"width:150px;\" class=\"btn btn-lg btn-primary\" id=\"course-create-btn\" type=\"submit\">保存修改</button>
                    </div>
                </div>
                <input type=\"hidden\" name=\"_csrf_token\" value=\"";
        // line 92
        echo twig_escape_filter($this->env, $this->env->getExtension('form')->renderCsrfToken("site"), "html", null, true);
        echo "\">
            </form>
        </div>
    </div>
";
    }

    public function getTemplateName()
    {
        return "CourseManage/base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  279 => 92,  269 => 85,  263 => 82,  257 => 79,  250 => 77,  244 => 76,  238 => 75,  231 => 73,  225 => 72,  219 => 71,  212 => 69,  206 => 68,  199 => 67,  181 => 64,  175 => 63,  172 => 62,  170 => 61,  167 => 60,  165 => 59,  154 => 53,  145 => 47,  135 => 39,  125 => 37,  115 => 35,  113 => 34,  102 => 32,  98 => 31,  89 => 27,  80 => 25,  68 => 20,  59 => 16,  55 => 15,  51 => 13,  48 => 12,  45 => 11,  42 => 10,  39 => 9,  33 => 6,  31 => 5,  29 => 4,  27 => 2,  25 => 1,);
    }
}
