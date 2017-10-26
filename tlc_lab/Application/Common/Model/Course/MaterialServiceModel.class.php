<?php
namespace Common\Model\Course;
use Think\Model;
use Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;

class MaterialServiceModel extends BaseModel{
    private function getMaterialDao(){
        return $this->createDao("Course.CourseMaterial");
    }
    
    private function getCourseService(){
    	return $this->createService('Course.CourseServiceModel');
    }

    private function getFileService(){
        return $this->createService('Content.FileServiceModel');
    }

    private function getUploadFileService(){
        return $this->createService('File.UploadFileServiceModel');
    }
    
    public function deleteMaterialsByCourseId($courseId){
        $materials = $this->getMaterialDao()->findMaterialsByCourseId($courseId, 0, 1000);
        $fileIds = ArrayToolkit::column($materials, "fileId");
        if(!empty($fileIds)) $this->getUploadFileService()->decreaseFileUsedCount($fileIds);
        return $this->getMaterialDao()->deleteMaterialsByCourseId($courseId);
    }
    
    public function uploadMaterial($material){
        if (!ArrayToolkit::requireds($material, array('courseId', 'fileId'))) {
            E('参数缺失，上传失败！');
        }
        $course = $this->getCourseService()->getCourse($material['courseId']);
        if (empty($course)) {
            E('课程不存在，上传资料失败！');
        }
        $fields = array(
            'courseId' => $material['courseId'],
            'lessonId' => empty($material['lessonId']) ? 0 : $material['lessonId'],
            'description'  => empty($material['description']) ? '' : $material['description'],
            'userId' => $this->getCurrentUser()->id, //
            'createdTime' => time(),
        );

        if (empty($material['fileId'])) {
            if (empty($material['link'])) {
                E('资料链接地址不能为空，添加资料失败！');
            }
            $fields['fileId'] = 0;
            $fields['link'] = $material['link'];
            $fields['title'] = empty($material['description']) ? $material['link'] : $material['description'];
        } else {
            $fields['fileId'] = (int) $material['fileId'];
            $file = $this->getUploadFileService()->getFile($material['fileId']);
            if (empty($file)) {
                E('文件不存在，上传资料失败！');
            }
            $fields['link'] = '';
            $fields['title'] = $file['filename'];
            $fields['fileSize'] = $file['size'];
        }

        $material =  $this->getMaterialDao()->addMaterial($fields);
        if(!empty($material['fileId'])){
            $this->getUploadFileService()->increaseFileUsedCount(array($material['fileId']));
        }
        $this->getCourseService()->increaseLessonMaterialCount($fields['lessonId']);
        return $material;
    }

    public function deleteMaterial($courseId, $materialId){
        $material = $this->getMaterialDao()->getMaterial($materialId);
        if (empty($material) or $material['courseId'] != $courseId) {
            E('课程资料不存在，删除失败。');
        }
        $this->getMaterialDao()->deleteMaterial($materialId);
        if(!empty($material['fileId'])){
            $this->getUploadFileService()->decreaseFileUsedCount(array($material['fileId']));
        }
        if($material['lessonId']){
           $count = $this->getMaterialDao()->getLessonMaterialCount($courseId,$material['lessonId']);
           $this->getCourseService()->resetLessonMaterialCount($material['lessonId'], $count);
        }
    }

    public function deleteMaterialsByLessonId($lessonId){
        $materials = $this->getMaterialDao()->findMaterialsByLessonId($lessonId, 0, 1000);
        $fileIds = ArrayToolkit::column($materials, "fileId");
        if(!empty($fileIds)){
            $this->getUploadFileService()->decreaseFileUsedCount($fileIds);
        }
        return $this->getMaterialDao()->deleteMaterialsByLessonId($lessonId);
    }

    public function getMaterial($courseId, $materialId){
        $material = $this->getMaterialDao()->getMaterial($materialId);
        if (empty($material) or $material['courseId'] != $courseId) {
            return null;
        }
        return $material;
    }

    public function findCourseMaterials($courseId, $start, $limit){
        return $this->getMaterialDao()->findMaterialsByCourseId($courseId, $start, $limit);
    }

    public function findLessonMaterials($lessonId, $start, $limit){
        return $this->getMaterialDao()->findMaterialsByLessonId($lessonId, $start, $limit);
    }

    public function getMaterialCount($courseId){
        return $this->getMaterialDao()->getMaterialCountByFileId($fileId);
    }

    public function getMaterialCountByFileId($fileId){
        return $this->getMaterialDao()->getMaterialCountByFileId($fileId);
    }
    
}
?>