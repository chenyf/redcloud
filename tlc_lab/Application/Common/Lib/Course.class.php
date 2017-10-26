<?php

namespace Common\Lib;

use Symfony\Component\Filesystem\Filesystem;

class Course
{

    private static function getCourseDao(){
        return createService('Course.CourseModel');
    }

    private static function getUploadFileDao(){
        return createService('File.UploadFileModel');
    }

    private static function getResourceDao(){
        return createService('Course.CourseResourceModel');
    }

    private static function getChapterDao(){
        return createService('Course.CourseChapterModel');
    }

    private static function getLessonDao(){
        return createService('Course.LessonModel');
    }

    private static function getRecordService(){
        return createService('Course.CourseUploadRecordServiceModel');
    }

    protected static function getUserService() {
        return createService('User.UserServiceModel');
    }

    private static function getCourseService() {
        return createService('Course.CourseServiceModel');
    }

    private static function getResourcePacketService(){
        return createService('Course.CourseResourcePacketService');
    }

    //生成课程资源包，未加密的zip压缩文件
    public static function generateResourceCourse($userId,$courseId){
        $course = self::getCourseService()->getCourseRaw($courseId);

        if(empty($course)){
            return array("success" => false,"msg" => "课程不存在！");
        }

        $path = C("course_packet_path"). "/" . $courseId;
        $dst_zip_file = C("course_packet_path"). "/" . $courseId . ".zip";

//        $path = DATA_PATH . "/course/resource/" . $courseId;
//        $dst_zip_file = DATA_PATH . "/course/resource/" . $courseId . ".zip";

        if(is_dir($path)){
            self::delDir($path);
        }

        if(is_file($dst_zip_file)){
            @unlink($dst_zip_file);
        }

        $resource_path = $path . "/resource/";
        $resource_courselesson_path = $path . "/resource/courselesson";
        $resource_resource_path = $path . "/resource/resource";
        $resource_course_path = $path . "/resource/course";
        $db_path = $path . "/db/";

        //创建资源包的课程内容目录
        if(!is_dir($resource_path)){
            if (true !== @mkdir($resource_path, 0777, true)){
                self::delDir($path);
                return array("success" => false,"msg" => "目录创建失败！");
            }
            if (true !== @mkdir($resource_courselesson_path, 0777, true)){
                self::delDir($path);
                return array("success" => false,"msg" => "目录创建失败！");
            }
            if (true !== @mkdir($resource_resource_path, 0777, true)){
                self::delDir($path);
                return array("success" => false,"msg" => "目录创建失败！");
            }
            if (true !== @mkdir($resource_course_path, 0777, true)){
                self::delDir($path);
                return array("success" => false,"msg" => "目录创建失败！");
            }
        }

        //创建资源包的数据库目录
        if(!is_dir($db_path)){
            if (true !== @mkdir($db_path, 0777, true)){
                self::delDir($path);
                return array("success" => false,"msg" => "目录创建失败！");
            }
        }

        //生成对应课程内容的SQL文件
        $sqlCourse = self::makeCourseSql($courseId);
        $sqlChapterLesson = self::makeCourseChapterUnionLessonSql($courseId);
        $sqlFileResource = self::makeUploadFilesUnionResourceSql($courseId);

        file_put_contents($db_path. "/db.sql",$sqlCourse . $sqlChapterLesson . $sqlFileResource);

        //将原课程内容拷贝到生成的课程资源包目录中
        $src_course_path = DATA_PATH . "/course/";
        $src_courselesson_path = DATA_PATH . "/courselesson/" . $courseId;
        $src_resource_path = DATA_PATH . "/resource/" . $courseId;

        foreach (array($course["selectPicture"],$course["smallPicture"],$course["middlePicture"],$course["largePicture"]) as $picture) {
            if (!empty(trim($picture))) {
                $picturePath = tripPath($picture, "/course");
                $picturePath = tripPath($picturePath, "public://course");
                $dstpPicturePath = pathjoin($resource_course_path, dirname($picturePath));
                if (!is_dir($dstpPicturePath) && true !== @mkdir($dstpPicturePath, 0777, true)) {
                    self::delDir($path);
                    return array("success" => false, "msg" => "目录创建失败！".$dstpPicturePath);
                }
                if (!@copy(pathjoin($src_course_path, $picturePath), pathjoin($dstpPicturePath, basename($picture)))) {
                    self::delDir($path);
                    return array("success" => false, "msg" => "复制课程图片失败！");
                }
            }
        }

        self::copy_dir($src_courselesson_path,$resource_courselesson_path);
        self::copy_dir($src_resource_path,$resource_resource_path);

        //数据库、文件生成完毕，现在进行目录压缩
        $zip_shell  = "cd " . $path . " && zip -r " . $dst_zip_file . " *";
        @exec($zip_shell,$err_info,$err_code);

        if(!is_file($dst_zip_file)){
            self::delDir($path);
            return array("success" => false, "msg" => "生成资源包失败！");
        }

//        if(!self::zipDir($path,$dst_zip_file)){
//            self::delDir($path);
//            return array("success" => false, "msg" => "生成资源包失败！");
//        }

        $resourcePacket = self::getResourcePacketService()->getByCourseId($courseId);
        if(!empty($resourcePacket)){
            self::getResourcePacketService()->update($resourcePacket["id"],["deleted"=>1]);
        }

        $packet["courseId"] = $courseId;
        $packet["userId"] = $userId;
        $packet["filepath"] = "/coursepacket/" . $courseId . ".zip";
        $packet["size"] = filesize(DATA_PATH . "/coursepacket/" . $courseId . ".zip");
        $packet["deleted"] = 0;
        $packet["createdTime"] = time();

        $newPacket = self::getResourcePacketService()->addPacket($packet);
        if(empty($newPacket)){
            self::delDir($path);
            @unlink($dst_zip_file);
            return array("success" => false, "msg" => "生成资源包失败！");
        }

        self::getCourseDao()->updateCourse($courseId, array("resourcePacketId" => $newPacket["id"]));

        self::delDir($path);
        return array("success" => true,"msg" => "生成资源包成功！");
    }

    //上传课程资源包
    public static function addResourceCourse($course_resource_path,$copy=false){

        $status = array("status" => false,"msg" => "");

        if(!is_file($course_resource_path)){
            $status["msg"] = "课程资源包不存在！";
            return $status;
        }

        $info = pathinfo($course_resource_path);
        $ext = $info['extension'];

        if($ext != "red"){
            $status["msg"] = "请选择正确格式的课程资源包！";
            return $status;
        }

        $filename = $info["filename"];

        $courseObj = self::getCourseDao();

        $crypt_cmd = C("CRYPT_COURSE_CMD");

        if(!is_file($crypt_cmd)){
            $status["msg"] = "上传课程错误！";
            return $status;
        }

        //如果需要把课程资源文件拷贝到tmp目录
        if($copy){
            $tmpDir = DATA_PATH . "/tmp/" .uniqid();

            if (is_dir($tmpDir)){
                self::delDir($tmpDir);
            }

            if (true !== @mkdir($tmpDir, 0777, true)){
                $status["msg"] = "创建临时目录失败！";
                return $status;
            }
        }else{
            $tmpDir = dirname($course_resource_path);
        }

        $cryptKey = "123456";

        $decrypt_file_path = pathjoin($tmpDir,$filename) . ".zip";

        $shell  = $crypt_cmd . " -d --key " . $cryptKey . " " . $course_resource_path . " " . $decrypt_file_path;
        //解密课程资源包
        @exec($shell,$err_info,$err_code);

        if(!is_file($decrypt_file_path)){
            self::delDir($tmpDir);
            $status["msg"] = "解密课程资源包失败！";
            return $status;
        }

        //解压出来的课程资源目录
        $course_resource_dictory = mb_substr($decrypt_file_path,0,mb_strlen($decrypt_file_path) - mb_strlen(".zip"));
        if(!is_dir($course_resource_dictory)){
            if (true !== @mkdir($course_resource_dictory, 0777, true)){
                $status["msg"] = "创建临时目录失败！";
                return $status;
            }
        }

        $zip = new \ZipArchive;
        $res = $zip->open($decrypt_file_path);
        if ($res === TRUE) {
            //解压缩到test文件夹
            $zip->extractTo($course_resource_dictory);
            $zip->close();
        } else {
            self::delDir($tmpDir);
            $status["msg"] = "解压课程资源包失败！";
            return $status;
        }

        //课程资源目录：课程封面图片、课程内容、课程资料
        $course_content_path = pathjoin($course_resource_dictory,"resource");
        //课程数据库目录：插入课程数据的SQL语句
        $course_db_path = pathjoin($course_resource_dictory,"db");
        if(!is_dir($course_content_path) || !is_dir($course_db_path)){
            self::delDir($tmpDir);
            $status["msg"] = "不是合法的课程资源包！";
            return $status;
        }

        //添加数据库
        $sqlList = array();
        $handle = @fopen(pathjoin($course_db_path,"db.sql"), "r");
        if ($handle) {
            while (!feof($handle)) {
                $buffer = fgets($handle, 4096);
                if(trim($buffer) != "") {
                    $sqlList[] = $buffer;
                }
            }
            fclose($handle);
        }
//        $sql = file_get_contents(pathjoin($course_db_path,"db.sql"));

        $err = $courseObj->execQuery($sqlList);

        if(!empty($err)){
            self::delDir($tmpDir);
            $status["msg"] = "数据库插入失败：" . $err;
            return $status;
        }

        $courseId = $courseObj->getMaxId();

        $course_dir = DATA_PATH . "/course/";
        $courselesson_dir = DATA_PATH . "/courselesson/" . $courseId;
        $resource_dir = DATA_PATH . "/resource/" . $courseId;

        if(!is_dir($courselesson_dir)){
            if (true !== @mkdir($courselesson_dir, 0777, true)){
                self::delDir($tmpDir);
                $status["msg"] = "创建课程资源目录失败！";
                return $status;
            }
        }

        if(!is_dir($resource_dir)){
            if (true !== @mkdir($resource_dir, 0777, true)){
                self::delDir($tmpDir);
                $status["msg"] = "创建课程资源目录失败！";
                return $status;
            }
        }

        //将课程资源包中的课程文件拷贝到本地
        self::copy_dir($course_content_path . "/course",$course_dir);
        self::copy_dir($course_content_path . "/courselesson",$courselesson_dir);
        self::copy_dir($course_content_path . "/resource",$resource_dir);

        $user = self::getUserService()->getCurrentUser();
        $record = array(
            "userId" => $user->id,
            "courseId" => $courseId,
            "filename" => basename($course_resource_path),
            "createdTime" => time()
        );
        self::getRecordService()->addRecord($record);

        self::delDir($tmpDir);
        $status["status"] = true;
        return $status;
    }

    //删除课程资源包
    public static function deleteResourceCourse($courseId){
        $course = self::getCourseService()->getCourseRaw($courseId);

        if(empty($course)){
            return array("success" => false,"msg" => "课程不存在！");
        }

        $resourcePacket = self::getResourcePacketService()->getByCourseId($courseId);
        if(empty($resourcePacket)){
            return array("success" => false,"msg" => "课程资源包不存在！");
        }

        self::getResourcePacketService()->update($resourcePacket["id"],["deleted"=>1]);
        self::getCourseDao()->updateCourse($courseId, array("resourcePacketId" => 0));

        $packetPath = DATA_PATH . $resourcePacket["filepath"];
        @unlink($packetPath);

        return array("success" => true,"msg" => "删除资源包成功！");
    }

    //删除课程
    public static function deleteCourse($courseId){

        $course = self::getCourseService()->getCourseRaw($courseId);

        if(empty($course)){
            return array("success" => false,"msg" => "课程不存在！");
        }

        /**  与课程有关的资源 **/
        $src_course_path = DATA_PATH . "/course/";
        $src_courselesson_path = DATA_PATH . "/courselesson/" . $courseId;
        $src_resource_path = DATA_PATH . "/resource/" . $courseId;

        //删除课程图片
        foreach (array($course["selectPicture"],$course["smallPicture"],$course["middlePicture"],$course["largePicture"]) as $picture) {
            if (!empty(trim($picture))) {
                $picturePath = tripPath($picture, "/course");
                $picturePath = tripPath($picturePath, "public://course");
                @unlink(pathjoin($src_course_path, $picturePath));
            }
        }

        //删除课程内容
        self::delDir($src_courselesson_path);
        //删除课程资料
        self::delDir($src_resource_path);

        /** 删除数据库记录 **/
        //删除上传的文件记录
        $delRes = self::getUploadFileDao()->deleteFiles(array("targetId" => $courseId));
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除资料
        $delRes = self::getResourceDao()->deleteResource(array("courseId" => $courseId));
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除笔记
        $delRes = createService('Course.CourseNoteModel')->deleteNoteByCourseId($courseId);
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除课程内容
        $delRes = self::getLessonDao()->deleteLessonsByCourseId($courseId);
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除章节
        $delRes = self::getChapterDao()->deleteChaptersByCourseId($courseId);
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除课程
        $delRes = self::getCourseDao()->deleteCourse($courseId);
        if($delRes == false){
            return array("success" => false,"msg" => "删除课程失败！");
        }

        //删除练习和试卷
        $testpapers = createService('Testpaper.TestpaperModel')->selectTestPapersByCourseId($courseId);
        foreach ($testpapers as $testpaper){
            createService('Testpaper.TestpaperItemResultModel')->deleteItemResultsByTestpaperId($testpaper["id"]);
            createService('Testpaper.TestpaperItemModel')->deleteItemsByTestpaperId($testpaper["id"]);
            createService('Testpaper.TestpaperResultModel')->deleteTestpaperResultByTestpaperId($testpaper["id"]);
            createService('Testpaper.TestpaperModel')->deleteTestpaper($testpaper["id"]);
        }

        return array("success" => true,"msg" => "删除课程成功！");
    }

    public static function copy_dir($src,$dst) {
        if(!is_dir($src)){
            return ;
        }
        $dir = opendir($src);

        if(!is_dir($dst)){
            @mkdir($dst);
        }

        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {

                $src_file = pathjoin($src,$file);
                $dst_file = pathjoin($dst,$file);

                if(is_file($dst_file)){
                    continue;
                }

                if (is_dir($src_file)) {
                    self::copy_dir($src_file,$dst_file);
                    continue;
                }else {
                    copy($src_file,$dst_file);
                }
            }
        }
        closedir($dir);
    }

    //自定义函数递归的函数整个目录
    public static function delDir($directory){
        if(file_exists($directory)){
            if($dir_handle=@opendir($directory)){
                while($filename=readdir($dir_handle)){
                    if($filename!='.' && $filename!='..'){
                        $subFile=$directory."/".$filename;
                        if(is_dir($subFile)){
                            self::delDir($subFile);
                        }
                        if(is_file($subFile)){
                            unlink($subFile);
                        }
                    }
                }
                closedir($dir_handle);
                rmdir($directory);
            }
        }
    }

    //压缩整个目录成为zip文件
    public static function zipDir($src_path, $dst_zip) {

        $zip = new \ZipArchive();

        if ($zip->open($dst_zip, \ZipArchive::CREATE) === TRUE) {
            $handler = opendir($src_path);
            while (($filename = readdir($handler)) !== false) {
                if ($filename != "." && $filename != "..") {
                    if (is_dir($src_path . "/" . $filename)) {
                        self::zipDir($src_path . "/" . $filename, $zip);
                    } else { //将文件加入zip对象
                        $zip->addFile($src_path . "/" . $filename);
                    }
                }
            }
            @closedir($src_path);
            $zip->close(); //关闭处理的zip文件
            return true;
        }

        return false;
    }

    public static function makeCourseSql($courseId){
        $course = self::getCourseDao()->getCourseFind($courseId);

        if(empty($course)){
            return null;
        }
        
        unset($course["id"]);
        $course["viewCount"] = 0;
        $course["resourcePacketId"] = 0;

        $sql = self::makeSql("course",[$course]);
        
        return $sql;
    }

    public static function makeCourseChapterUnionLessonSql($courseId){
        $chapters = self::getChapterDao()->findChaptersByCourseId($courseId);

        $childChapterList = [];
        $parentChapterList = [];
        foreach ($chapters as $k => $chapter){
            $chapters[$k]["courseId"] = "(select max(id) from course)";

            $parentId = $chapter["parentId"];

            if($parentId != 0){
                $chapters[$k]["parentId"] = "(select max(id) from course_chapter where parentId=0)";
                if(empty($childChapterList)){
                    $childChapterList[$parentId.""] = array($chapters[$k]);
                }else{
                    $childChapterList[$parentId.""][] = $chapters[$k];
                }
            }else{
                $parentChapterList[] = $chapters[$k];
            }
        }

        //将父章节排在前面，子章节随后
        $chapterList = array();
        foreach($parentChapterList as $parentChapter){
            $chapterList[] = $parentChapter;
            $parentId = $parentChapter["id"] . "";
            if(!empty($childChapterList[$parentId])){
                foreach ($childChapterList[$parentId] as $childChapter){
                    $chapterList[] = $childChapter;
                }
            }
        }

        $lessons = self::getLessonDao()->selectLessonsByCourseId($courseId);
        $lesson_group_by_chapterId = array();
        foreach ($lessons as $k => $lesson){
            unset($lessons[$k]["id"]);
            unset($lesson["id"]);
            $lesson["courseId"] = "(select max(id) from course)";

            $chapterId = $lesson["chapterId"];
            if(empty($lesson_group_by_chapterId[$chapterId])){
                $lesson_group_by_chapterId[$chapterId] = array($lesson);
            }else{
                $lesson_group_by_chapterId[$chapterId][] = $lesson;
            }
        }

        $dataList = array();
        foreach ($chapters as $k => $chapter){
            $chapterId = $chapter["id"];
            unset($chapters[$k]["id"]);
            unset($chapter["id"]);

            $data = array();
            $data["data"] = $chapter;
            $data["union"] = $lesson_group_by_chapterId[$chapterId];

            $dataList[] = $data;
        }

        $sql = self::makeSqlUnion("course_chapter",$dataList,"course_lesson","chapterId");
        return $sql;
    }

    public static function makeUploadFilesUnionResourceSql($courseId){
        $files = self::getUploadFileDao()->selectFilesByCourseId($courseId);
        foreach ($files as $k => $file){
            $files[$k]["targetId"] = "(select max(id) from course)";
        }

        $resources = self::getResourceDao()->selectResourceByCourseId($courseId);
        $lesson_group_by_fileId = array();
        foreach ($resources as $k => $res){
            unset($resources[$k]["id"]);
            unset($res["id"]);
            $res["courseId"] = "(select max(id) from course)";

            $fileId = $res["uploadFileId"];
            if(empty($lesson_group_by_fileId[$fileId])){
                $lesson_group_by_fileId[$fileId] = array($res);
            }else{
                $lesson_group_by_fileId[$fileId][] = $res;
            }
        }

        $dataList = array();
        foreach ($files as $k => $file){
            $fileId = $file["id"];
            unset($files[$k]["id"]);
            unset($file["id"]);

            $data = array();
            $data["data"] = $file;
            $data["union"] = $lesson_group_by_fileId[$fileId];
            $dataList[] = $data;
        }

        $sql = self::makeSqlUnion("upload_files",$dataList,"resource","uploadFileId");
        return $sql;
    }

    public static function makeCourseChapterSql($courseId){
        $chapters = self::getChapterDao()->findChaptersByCourseId($courseId);
        foreach ($chapters as $k => $chapter){
            unset($chapters[$k]["id"]);
            $chapters[$k]["courseId"] = "(select max(id) from course)";
        }

        $sql = self::makeSql("course_chapter",$chapters,"courseId");

        return $sql;
    }

    public static function makeCourseLessonSql($courseId){
        $lessons = self::getLessonDao()->selectLessonsByCourseId($courseId);
        foreach ($lessons as $k => $lesson){
            unset($lessons[$k]["id"]);
            $lessons[$k]["courseId"] = "%courseId%";
        }

        $sql = self::makeSql("course_lesson",$lessons,"chapterId");

        return $sql;
    }

    public static function makeUploadFilesSql($courseId){
        $files = self::getUploadFileDao()->selectFilesByCourseId($courseId);
        foreach ($files as $k => $file){
            unset($files[$k]["id"]);
            $files[$k]["targetId"] = "%courseId%";
        }

        $sql = self::makeSql("upload_files",$files);

        return $sql;
    }

    public static function makeResourceSql($courseId){
        $resources = self::getResourceDao()->selectResourceByCourseId($courseId);
        foreach ($resources as $k => $res){
            unset($resources[$k]["id"]);
            $resources[$k]["courseId"] = "%courseId%";
        }

        $sql = self::makeSql("resource",$resources);

        return $sql;
    }

    public static function makeSql($table_name,$data,$id_col=null){

        if(empty($data)){
            return "";
        }

        $sql = "insert into {$table_name} ";

        if(!is_array($data)){
            $dataList = [$data];
        }else{
            $dataList = $data;
        }

        $columns = array_keys($dataList[0]);

        $sql .= "(" . implode(",",$columns) . ") values ";

        $valueSql = "";
        foreach ( $dataList as $dataItem ){
            $values = array_values($dataItem);

            foreach ($dataItem as $col => $val){
                if($col == $id_col || (is_array($id_col) && in_array($col,$id_col)) || is_int($val) || is_float($val)){
                    $dataItem[$col] = $val;
                }else{
                    $dataItem[$col] = "'" . $val . "'";
                }
            }

            $valueSql .= "(" . implode(",",$dataItem) . ");\r\n";
        }

        $sql .= $valueSql;

        return $sql;
    }

    //创建联合数据Sql插入语句
    public static function makeSqlUnion($table_name,$dataList,$union_table_name,$union_col){
        if(!is_array($dataList)){
            $dataList = [$dataList];
        }

        $sql = "";
        foreach ($dataList as $data){
            $sql .= self::makeSql($table_name,[$data["data"]],["targetId","courseId","uploadFileId","chapterId","parentId"]);

            if(empty($data["union"])){
                break;
            }

            if(!is_array($data["union"])){
                $data["union"] = [$data["union"]];
            }

            foreach ($data["union"] as $k => $union_data){
                $data["union"][$k][$union_col] = "(select max(id) from {$table_name})";
            }

            $sql .= self::makeSql($union_table_name,$data["union"],["targetId","courseId","uploadFileId","chapterId","parentId"]);
        }

        return $sql;
    }

}