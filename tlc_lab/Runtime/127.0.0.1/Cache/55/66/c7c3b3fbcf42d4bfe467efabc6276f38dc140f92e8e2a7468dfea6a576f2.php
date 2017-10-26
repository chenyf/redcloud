<?php

/* @Course/CourseLessonManage/media-choose.html.twig */
class __TwigTemplate_5566c7c3b3fbcf42d4bfe467efabc6276f38dc140f92e8e2a7468dfea6a576f2 extends Twig_Template
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
        echo "<div id=\"media-choosers\">
    <script src=\"";
        // line 2
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/libs/vuejs/vue.min.js"), "html", null, true);
        echo "\"></script>
    <script type=\"text/javascript\">

        Vue.config.delimiters = ['[[', ']]'];

        (function(\$){
            createVueComponent(\"#video_tb_file\");
            createVueComponent(\"#document_tb_file\");
        })(jQuery);

        function createVueComponent(id){
            new Vue({
                el: id,
                data: {
                    currFolder: \"\",
                    files: [],
                    url:\$(id).data('url'),
                    errMsg:\"\",
                    isRoot:true,
                },
                created:function(){
                    this.errMsg = \"\";
                    var self = this;
                    loadFileList(this.url,\"\",function(err,result){
                        if(err != null){
                            self.errMsg = err;
                            return;
                        }
                        self.files = result;
                        if(result.length > 0 && result[0] != undefined){
                            self.currFolder = result[0].currFolder;
                        }
                        self.isRoot = true;
                    });
                },
                methods: {
                    isFolder: function (file){
                        if (file.type == \"directory\") {
                            return true;
                        } else if (file.type == \"file\") {
                            return false;
                        }
                    },
                    suffix: function (file) {
                        if (this.isFolder(file)) {
                            return \"\";
                        } else {
                            return file.name.split('.').pop();
                        }
                    },
                    formatSize:function(a){
                        return a===d||/\\D/.test(a)?h.translate(\"N/A\"):a>1099511627776?Math.round(a/1099511627776,1)+\" \"+h.translate(\"tb\"):a>1073741824?Math.round(a/1073741824,1)+\" \"+h.translate(\"gb\"):a>1048576?Math.round(a/1048576,1)+\" \"+h.translate(\"mb\"):a>1024?Math.round(a/1024,1)+\" \"+h.translate(\"kb\"):a+\" \"+h.translate(\"b\")
                    },
                    getDirFiles:function(index){
                        var file = this.files[index];
                        if(file == undefined){
                            return ;
                        }
                        var dir = file.currFolder+file.name;
                        this.errMsg = \"\";
                        var self = this;
                        if (isFile(dir)) {

                        } else {
                            loadFileList(this.url,dir,function(err,result){
                                if(err != null){
                                    self.errMsg = err;
                                    return;
                                }

                                self.files = result;
                                if(result.length > 0 && result[0] != undefined){
                                    self.currFolder = result[0].currFolder;
                                }else{
                                    self.currFolder = theTrim(dir,\"/\") + \"/\";
                                }

                                self.isRoot = false;
                            });
                        }
                    },
                    backDir:function(){
                        this.errMsg = \"\";
                        var self = this;
                        var curr = this.currFolder.substr(0, this.currFolder.lastIndexOf(\"/\"));
                        var dir = curr.substr(0,curr.lastIndexOf(\"/\"));
                        loadFileList(this.url,dir,function(err,result){
                            if(err != null){
                                self.errMsg = err;
                                return;
                            }

                            self.files = result;
                            if(result.length > 0 && result[0] != undefined){
                                self.currFolder = result[0].currFolder;
                                self.isRoot = self.currFolder == \"/\";
                            }
                        });
                    }
                }
            });
        }

        function theTrim(s,c){
            s = theTrimLeft(s,c);
            s = theTrimRight(s,c);
            return s;
        }

        function theTrimLeft(s,c){
            if(s.indexOf(c) == 0){
                s = s.substr(1,s.length);
            }
            return s;
        }

        function theTrimRight(s,c){
            if(s.lastIndexOf(c) == s.length - 1){
                s = s.substr(0,s.length - 1);
            }
            return s;
        }

        function isFile(path) {
            curr = path.substring(path.lastIndexOf(\"/\")+1);
            //不等于-1 说明有后缀 即为文件（不考虑无后缀的文件
            if (curr.indexOf(\".\") !== -1) {
                return true;
            } else {
                return false;
            }
        }

        function loadFileList(url,dir,callback){
            \$.ajax({
                url: url,
                type:'POST',
                data: {dir:dir},
                dataType: 'json',
                success: function(result) {
                    if(result.code != 1){
                        return callback(result.message,null);
                    }
                    var resultList = [];
                    for(var i in result.data){
                        var item = {};
                        for(key in result.data[i]){
                            item[key] = result.data[i][key];
                        }
                        resultList.push(item);
                    }
                    return callback(null,resultList);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    if(textStatus == 'parsererror'){
                        return callback(null,[]);
                    }
                    return callback(\"加载文件列表出错\",null);
                }
            });
        }
    </script>
<span id=\"data-setting\"
       data-endpoint=\"";
        // line 165
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "cloud_url", array()), "html", null, true);
        echo "\"
       data-writetoken=\"";
        // line 166
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "write_token", array()), "html", null, true);
        echo "\"
       data-readtoken=\"";
        // line 167
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "read_token", array()), "html", null, true);
        echo "\"
       data-polycatid=\"";
        // line 168
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "poly_catid", array()), "html", null, true);
        echo "\"
         ></span>

";
        // line 172
        echo "<div class=\"file-chooser\" id=\"video-chooser\" style=\"display:none;\"
    data-params-url=\"";
        // line 173
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("uploadfile_params", array("storage" => "local", "targetType" => (isset($context["targetType"]) ? $context["targetType"] : null), "targetId" => (isset($context["targetId"]) ? $context["targetId"] : null))), "html", null, true);
        echo "\"
    data-hls-encrypted=\"";
        // line 174
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getSetting("developer.hls_encrypted", 1), "html", null, true);
        echo "\"
    data-targetType=\"";
        // line 175
        echo twig_escape_filter($this->env, (isset($context["targetType"]) ? $context["targetType"] : null), "html", null, true);
        echo "\"
    data-targetId=\"";
        // line 176
        echo twig_escape_filter($this->env, (isset($context["targetId"]) ? $context["targetId"] : null), "html", null, true);
        echo "\"
  >
    <div class=\"file-chooser-bar\" style=\"display:none;\">
        <span data-role=\"placeholder\"></span>
        <button class=\"btn btn-link btn-sm\" type=\"button\" data-role=\"trigger\"><i class=\"glyphicon glyphicon-edit\"></i> 重新上传</button>
        <div class=\"alert alert-warning\" data-role=\"waiting-tip\" style=\"display:none;\">正在转换视频格式，转换需要一定的时间，期间将不能播放该视频。<br />转换完成后将以站内消息通知您。</div>
    </div>
    <div class=\"file-chooser-main\">
        <ul class=\"nav nav-tabline\" style=\"border-bottom:none;\">
            <li class=\"active\"><a class=\"file-chooser-uploader-tab polyv-cloud\" href=\"#video-chooser-upload-pane\" data-toggle=\"tab\" id=\"upload-cloud-video\">上传视频</a></li>
            <li><a class=\"file-chooser-uploader-tab\" href=\"#video-chooser-import-cloud-pane\" data-toggle=\"tab\">从云盘导入视频</a></li>
        </ul>
        <div class=\"tab-content\">
            <div class=\"tab-pane active\" id=\"video-chooser-upload-pane\">
                <div class=\"file-chooser-uploader\">
                    <div class=\"file-chooser-uploader-label\">选择你要上传的视频文件：</div>
                    <div class=\"file-chooser-uploader-control\">
                        <span id=\"video-choose-uploader-btn\"
                        data-role=\"uploader-btn\"
                        data-filetypes=\"*.avi;*.mov;*.mp4;*.wmv;*.flv;*.rmvb;*.webm;*.ogg;*.3gp\"
                        data-button-image=\"";
        // line 196
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/img/common/swfupload-btn.png"), "html", null, true);
        echo "\"
                        data-callback=\"";
        // line 197
        if (($this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "upload_mode", array()) == "cloud")) {
            echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("uploadfile_cloud_callback", array("targetType" => (isset($context["targetType"]) ? $context["targetType"] : null), "targetId" => (isset($context["targetId"]) ? $context["targetId"] : null), "lazyConvert" => 1)), "html", null, true);
        }
        echo "\"
                        data-progressbar=\"#video-chooser-progress\"
                        data-storage-type=\"local\"
                        data-get-media-info=\"";
        // line 200
        echo $this->env->getExtension('routing')->getPath("uploadfile_cloud_get_media_info", array("type" => "video"));
        echo "\"
                        >
                        <a class=\"uploadBtn btn btn-default btn-lg\">上传</a>
                        <div style=\"display:none\">
                            <input data-role=\"fileSelected\" class=\"filePrew\" type=\"file\" />
                        </div>
                      </span>
                    </div>
                    <div class=\"progress\" id=\"video-chooser-progress\" style=\"display:none;\">
                        <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\">
                        </div>
                    </div>
                    <div class=\"alert alert-info\">
                      <ul>
                          <li>支持<strong>mp4,,webm,ogg,avi,mov,wmv,flv,rmvb,3gp</strong>格式的视频文件上传。文件大小不能超过<strong>2 GB</strong>。</li>
                          <li>支持<strong>断点续传</strong></li>
                          <li>视频将上传到<strong>云视频服务器</strong>，视频格式是MP4,WEBM,OGG以外的视频，上传之后会对视频进行格式转换，转换过程需要一定的时间，在这个过程中视频将无法播放。转换完成后将以站内消息通知您。</li>
                      </ul>
                    </div>
                </div>
            </div>
            <!-- 保利威视 start-->
            <div class=\"tab-pane\" id=\"video-polyv-upload-pane22\">
                <div class=\"file-chooser-uploader\">
                    <div class=\"file-chooser-uploader-label\">选择你要上传的视频文件：</div>
                    <div class=\"file-chooser-uploader-control\">
                        <input   type=\"file\" value=\"上传\"/>
                    </div>
                    <div class=\"progress\" id=\"polyv-video-chooser-progress\" style=\"display:none;\">
                        <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\">
                        </div>
                    </div>
                    <div class=\"alert alert-info\">
                        <ul>
                            <li>支持<strong>mp4, avi, flv, wmv, mov等</strong>格式的视频文件上传，文件大小不能超过<strong>2 GB</strong>。</li>
                            <li>支持<strong>断点续传</strong></li>
                            <li>视频将上传到<strong>云视频服务器</strong>，上传之后会对视频进行格式转换，转换过程需要一定的时间，在这个过程中视频将无法播放。转换完成后将以站内消息通知您。</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- 保利威视 end-->

            <div class=\"tab-pane\" id=\"video-chooser-course-file\">
                <div id=\"file-browser-video\" data-role=\"course-file-browser\"
                     data-url=\"";
        // line 245
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("uploadfile_browsers", array("targetType" => (isset($context["targetType"]) ? $context["targetType"] : null), "targetId" => (isset($context["targetId"]) ? $context["targetId"] : null), "type" => "video", "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\"
                     data-empty=\"暂无视频文件，请先上传。\">
                </div>
            </div>
            <div class=\"tab-pane\" id=\"video-chooser-import-cloud-pane\">
                <div class=\"input-group tb_file\" id=\"video_tb_file\" data-url=\"";
        // line 250
        echo $this->env->getExtension('routing')->getPath("course_lesson_listvideo");
        echo "\">
                    <input class=\"form-control\" name=\"import_file_path\" type=\"hidden\" value=\"\" >
                    <div class=\"alert alert-danger\" role=\"alert\" v-if=\"errMsg.length > 0\" style=\"margin-bottom: 0px;margin-top: 10px;\">[[ errMsg ]]</div>
                    <div class=\"gridbox gridbox_material isModern\" style=\" position: relative; overflow: hidden; cursor: default; border: 0px solid white;\">
                        <div class=\"objbox\" style=\"width: 100%; overflow: auto; min-height: 66px;height: auto;\">
                            <table cellpadding=\"0\" cellspacing=\"0\" class=\"obj row20px\" style=\"width: 581px; table-layout: fixed;margin: 10px;\">
                                <tbody>
                                <tr style=\"height: auto;\">
                                    <th style=\"height: 0px; width: 30px;\"></th>
                                    <th style=\"height: 0px; width: 45px;\"></th>
                                    <th style=\"height: 0px; width: 280px;\"></th>
                                    <th style=\"height: 0px; width: 100px;\"></th>
                                    <th style=\"height: 0px; width: 155px;\"></th>
                                </tr>
                                <tr data-count=\"[[files.length]]\">
                                    <td colspan=\"2\">#</td>
                                    <td>Name</td>
                                    <td>Size</td>
                                    <td>Date</td>
                                </tr>
                                <tr @click=\"backDir\" style=\"cursor: pointer;\" v-if=\"!isRoot\">
                                    <td></td>
                                    <td><i class=\"fa fa-arrow-left fa-pull-right\"></i></td>
                                    <td colspan=\"3\">返回上一级</td>
                                </tr>
                                <tr v-if=\"files.length == 0\" style=\"height: 50px;\">
                                    <td style=\"border-bottom: none;\"></td>
                                    <td style=\"border-bottom: none;text-align: center;\" colspan=\"3\">暂无文件</td>
                                    <td style=\"border-bottom: none;\"></td>
                                </tr>
                                <tr @click=\"getDirFiles(index)\" data-fullname=\"[[file.currFolder]][[file.name]]\" style='cursor:pointer' v-for=\"(index, file) in files\">
                                    <td><input v-if=\"!isFolder(file)\" type=\"radio\" name=\"video_file\" id=\"video_file_[[index]]\" data-name=\"[[file.name]]\" data-size=\"[[file.size]]\" data-ext=\"[[file.ext]]\" value=\"[[file.currFolder]][[file.name]]\" /></td>
                                    <td align=\"center\">
                                        <span v-if=\"suffix(file) == ''\"><i class=\"fa fa-folder-o fa-pull-right\"></i></span>

                                        <span v-if=\"suffix(file)=='doc'||suffix(file)=='docx'\"><i class=\"fa fa-file-word-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file)=='ppt'||suffix(file)=='pptx'\"><i class=\"fa fa-file-powerpoint-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file)=='xls'||suffix(file)=='xlsx'\"><i class=\"fa fa-file-excel-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file) == 'pdf'\"><i class=\"fa fa-file-pdf-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file) == 'txt'\"><i class=\"fa fa-file-text-o fa-pull-right\"></i></span>
                                        <!--由于vue没有v-else-if 下面这行写的特别恶心-->
                                        <span v-if=\"suffix(file)!=''&&suffix(file)!='doc'&&suffix(file)!='docx'&&suffix(file)!='xls'&&suffix(file)!='xlsx'&&
                                        suffix(file)!='ppt'&&suffix(file)!='pptx'&&suffix(file)!='txt'&&suffix(file)!='pdf'\"><i class=\"fa fa-file-o fa-pull-right\"></i></span>
                                    </td>
                                    <td align=\"left\">[[ file.name ]]</td>
                                    <td align=\"center\">[[ file.size ]]</td>
                                    <td align=\"center\">[[ file.time ]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div v-if=\"files.length > 0\">
                        <button type=\"button\" class=\"btn btn-default import-btn\"  data-url=\"";
        // line 303
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_media_import", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\" data-loading-text=\"正在导入，请稍等\">导入</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
";
        // line 311
        echo "
";
        // line 313
        echo "<div class=\"file-chooser\" id=\"document-chooser\" style=\"display:none;\"
    data-params-url=\"";
        // line 314
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("uploadfile_params", array("storage" => $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "upload_mode", array()), "targetType" => (isset($context["targetType"]) ? $context["targetType"] : null), "targetId" => (isset($context["targetId"]) ? $context["targetId"] : null), "convertor" => "document")), "html", null, true);
        echo "\"
    data-targetType=\"";
        // line 315
        echo twig_escape_filter($this->env, (isset($context["targetType"]) ? $context["targetType"] : null), "html", null, true);
        echo "\"
    data-targetId=\"";
        // line 316
        echo twig_escape_filter($this->env, (isset($context["targetId"]) ? $context["targetId"] : null), "html", null, true);
        echo "\"
  >
    <div class=\"file-chooser-bar\"  style=\"display:none;\">
        <span data-role=\"placeholder\"></span>
        <button class=\"btn btn-link btn-sm\" type=\"button\" data-role=\"trigger\"><i class=\"glyphicon glyphicon-edit\"></i> 重新上传</button>
    </div>
    <div class=\"file-chooser-main\">
        <ul class=\"nav nav-tabline\">
            <li class=\"active\"><a class=\"file-chooser-uploader-tab\" href=\"#document-chooser-upload-pane\" data-toggle=\"tab\">上传文档</a></li>
            <li><a class=\"file-chooser-uploader-tab\" href=\"#document-chooser-import-cloud-pane\" data-toggle=\"tab\">从云盘导入文档</a></li>
        </ul>
        <div class=\"tab-content\">
            <div class=\"tab-pane active\" id=\"document-chooser-upload-pane\">
                <div class=\"file-chooser-uploader\">
                    <div class=\"file-chooser-uploader-label\">选择你要上传的文档：</div>
                    <div class=\"file-chooser-uploader-control\">
                        <span id=\"document-choose-uploader-btn\"
                        data-role=\"uploader-btn\"
                        data-button-image=\"";
        // line 334
        echo twig_escape_filter($this->env, $this->env->getExtension('topxia_web_twig')->getAssetUrl("assets/img/common/swfupload-btn.png"), "html", null, true);
        echo "\"
                        data-filetypes=\"*.doc;*.docx;*.pdf;*.ppt;*.pptx\"
                        data-callback=\"\"
                        data-storage-type=\"";
        // line 337
        echo twig_escape_filter($this->env, $this->getAttribute((isset($context["storageSetting"]) ? $context["storageSetting"] : null), "upload_mode", array()), "html", null, true);
        echo "\"
                        data-progressbar=\"#document-chooser-progress\">
                        <a class=\"uploadBtn btn btn-default btn-lg\">
                          上传
                        </a>
                        <div style=\"display:none\">
                            <input data-role=\"fileSelected\" class=\"filePrew\" type=\"file\" />
                        </div>
                      </span>
                    </div>
                    <div class=\"progress\" id=\"document-chooser-progress\" style=\"display:none;\">
                        <div class=\"progress-bar\" role=\"progressbar\" aria-valuenow=\"0\" aria-valuemin=\"0\" aria-valuemax=\"100\" style=\"width: 0%;\">
                        </div>
                    </div>
                    <div class=\"alert alert-info\">
                      <ul>
                        <li>支持<strong> pdf,doc,docx,ppt,pptx </strong>格式的文档上传，且文件大小不能超过<strong>200 MB</strong>。</li>
                        <li>所有格式的文档都将被转换成 <strong>pdf</strong> 格式</li>
                        <li>文档转码需要一些时间，请您稍等片刻</li>
                        ";
        // line 357
        echo "                      </ul>
                    </div>
                </div>
            </div>
            <div class=\"tab-pane\" id=\"document-chooser-course-file\">
                <div id=\"file-browser-document\" data-role=\"course-file-browser\"
                     data-url=\"";
        // line 363
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("uploadfile_browser", array("targetType" => (isset($context["targetType"]) ? $context["targetType"] : null), "targetId" => (isset($context["targetId"]) ? $context["targetId"] : null), "type" => "document", "center" => $this->getAttribute($this->getAttribute((isset($context["app"]) ? $context["app"] : null), "request", array()), "get", array(0 => "center"), "method"))), "html", null, true);
        echo "\" data-empty=\"暂无文档，请先上传。\"></div>
            </div>
            <!-- 网盘导入文档 -->
            <div class=\"tab-pane\" id=\"document-chooser-import-cloud-pane\">
                <div class=\"input-group tb_file\" id=\"document_tb_file\" data-url=\"";
        // line 367
        echo $this->env->getExtension('routing')->getPath("course_lesson_listdoc");
        echo "\">
                    <input class=\"form-control\" name=\"import_file_path\" type=\"hidden\" value=\"\" >
                    <div class=\"alert alert-danger\" role=\"alert\" v-if=\"errMsg.length > 0\" style=\"margin-bottom: 0px;margin-top: 10px;\">[[ errMsg ]]</div>
                    <div class=\"gridbox gridbox_material isModern\" style=\" position: relative; overflow: hidden; cursor: default; border: 0px solid white;\">
                        <div class=\"objbox\" style=\"width: 100%; overflow: auto; min-height: 66px;height: auto;\">
                            <table cellpadding=\"0\" cellspacing=\"0\" class=\"obj row20px\" style=\"width: 581px; table-layout: fixed;margin: 10px;\">
                                <tbody>
                                <tr style=\"height: auto;\">
                                    <th style=\"height: 0px; width: 30px;\"></th>
                                    <th style=\"height: 0px; width: 45px;\"></th>
                                    <th style=\"height: 0px; width: 280px;\"></th>
                                    <th style=\"height: 0px; width: 100px;\"></th>
                                    <th style=\"height: 0px; width: 155px;\"></th>
                                </tr>
                                <tr>
                                    <td colspan=\"2\">#</td>
                                    <td>Name</td>
                                    <td>Size</td>
                                    <td>Date</td>
                                </tr>
                                <tr @click=\"backDir\" style='cursor:pointer' v-if=\"!isRoot\">
                                    <td></td>
                                    <td><i class=\"fa fa-arrow-left fa-pull-right\"></i></td>
                                    <td colspan=\"3\">返回上一级</td>
                                </tr>
                                <tr v-if=\"files.length == 0\" style=\"height: 50px;\">
                                    <td style=\"border-bottom: none;\"></td>
                                    <td style=\"border-bottom: none;text-align: center;\" colspan=\"3\">暂无文件</td>
                                    <td style=\"border-bottom: none;\"></td>
                                </tr>
                                <tr @click=\"getDirFiles(index)\" style='cursor:pointer' v-for=\"(index, file) in files\">
                                    <td><input v-if=\"!isFolder(file)\" type=\"radio\" name=\"doc_file\" id=\"doc_file_[[index]]\" data-name=\"[[file.name]]\" data-size=\"[[file.size]]\" data-ext=\"[[file.ext]]\" value=\"[[file.currFolder]][[file.name]]\"/></td>
                                    <td align=\"center\">
                                        <span v-if=\"suffix(file) == ''\"><i class=\"fa fa-folder-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file)=='doc'||suffix(file)=='docx'\"><i class=\"fa fa-file-word-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file)=='ppt'||suffix(file)=='pptx'\"><i class=\"fa fa-file-powerpoint-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file)=='xls'||suffix(file)=='xlsx'\"><i class=\"fa fa-file-excel-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file) == 'pdf'\"><i class=\"fa fa-file-pdf-o fa-pull-right\"></i></span>
                                        <span v-if=\"suffix(file) == 'txt'\"><i class=\"fa fa-file-text-o fa-pull-right\"></i></span>
                                        <!--由于vue没有v-else-if 下面这行写的特别恶心-->
                                        <span v-if=\"suffix(file)!=''&&suffix(file)!='doc'&&suffix(file)!='docx'&&suffix(file)!='xls'&&suffix(file)!='xlsx'&&
                                        suffix(file)!='ppt'&&suffix(file)!='pptx'&&suffix(file)!='txt'&&suffix(file)!='pdf'\"><i class=\"fa fa-file-o fa-pull-right\"></i></span>
                                    </td>
                                    <td align=\"left\">[[ file.name ]]</td>
                                    <td align=\"center\">[[ file.size ]]</td>
                                    <td align=\"center\">[[ file.time ]]</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <button type=\"button\" class=\"btn btn-default import-btn\"  data-url=\"";
        // line 419
        echo twig_escape_filter($this->env, $this->env->getExtension('routing')->getPath("course_manage_media_import", array("id" => $this->getAttribute((isset($context["course"]) ? $context["course"] : null), "id", array()))), "html", null, true);
        echo "\" data-loading-text=\"正在导入，请稍等\">导入</button>
                    </div>
                </div>
            </div>
            <!-- 网盘导入文档end -->
        </div>
    </div>
</div>
";
        // line 428
        echo "

</div>";
    }

    public function getTemplateName()
    {
        return "@Course/CourseLessonManage/media-choose.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  522 => 428,  511 => 419,  456 => 367,  449 => 363,  441 => 357,  419 => 337,  413 => 334,  392 => 316,  388 => 315,  384 => 314,  381 => 313,  378 => 311,  368 => 303,  312 => 250,  304 => 245,  256 => 200,  248 => 197,  244 => 196,  221 => 176,  217 => 175,  213 => 174,  209 => 173,  206 => 172,  200 => 168,  196 => 167,  192 => 166,  188 => 165,  22 => 2,  19 => 1,);
    }
}
