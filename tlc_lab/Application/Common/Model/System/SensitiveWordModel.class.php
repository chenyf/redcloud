<?php

namespace Common\Model\System;

use \Common\Model\Common\BaseModel;
class SensitiveWordModel extends BaseModel
{
    protected $tableName = 'sensitive_words';

    public function getWordList(){
        $res = $this->select() ?: array();
        return $res;
    }

    public function addWord($word){
        $r = $this->add(array(
            'word'  =>  $word,
            'createTime'    =>  time()
        ));
        if(!$r)
            E("Insert Sensitive Word error.");
        return $r;
    }

    public function deleteWord($wordId){
        return $this->where("id = {$wordId}")->delete();
    }
}