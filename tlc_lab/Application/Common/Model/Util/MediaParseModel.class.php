<?php
namespace Common\Model\Util;
use Common\Model\Common\BaseModel;
class MediaParseModel extends BaseModel
{
    protected $tableName = 'media_parse';

    public function getMediaParse($id)
    {   
        return $this->where("id=".$id)->find() ? :null;
    
    }

    public function findMediaParseByUuid($uuid)
    {
       return $this->where("uuid=".$uuid)->find() ? :null;
      
    }

    public function findMediaParseByHash($hash)
    {       
        $data = $this->where("hash='".$hash."'")->find();
        return $data;
    }

    public function addMediaParse(array $fields)
    {
        $affected= $this->add($fields);
        if ($affected <= 0) {
             E('Insert MediaParse error.');
        }
       return $affected;
    }

    public function updateMediaParse($id, array $fields)
    {  
        $res = $this->where("id=".$id)->save($fields);
        return $this->getMediaParse($id);
    }

}