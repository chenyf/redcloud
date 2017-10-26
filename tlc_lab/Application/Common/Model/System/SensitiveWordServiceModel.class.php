<?php

namespace Common\Model\System;

use \Common\Model\Common\BaseModel;
use Common\Lib\ArrayToolkit;
class SensitiveWordServiceModel  extends BaseModel
{

    public function getSensitiveWordDao(){
        return $this->createDao('System.SensitiveWordModel');
    }

    public function getWordRecordList(){
        return $this->getSensitiveWordDao()->getWordList();
    }

    //获取所有的词汇列表，只有词汇
    public function getWordList(){
        $res =  $this->getSensitiveWordDao()->getWordList();
        $wordList = ArrayToolkit::column($res,'word');

        return $wordList;
    }

    public function addWordList($wordList){
        if(empty($wordList)){
            return true;
        }

        $wordList = array_unique($wordList);
        $this->getSensitiveWordDao()->getConnection()->beginTransaction();
        try{
            foreach ($wordList as $word){
                $this->addWord($word);
            }
            $this->getSensitiveWordDao()->getConnection()->commit();
            return true;
        }catch(\Exception $e){
            $this->getSensitiveWordDao()->getConnection()->rollback();
            doLog($e->getMessage());
            return false;
            throw $e;
        }
    }

    public function addWord($word){
        return $this->getSensitiveWordDao()->addWord($word);
    }

    public function deleteWordList($wordIds){
        if(empty($wordIds)){
            return true;
        }
        $this->getSensitiveWordDao()->getConnection()->beginTransaction();
        try{
            foreach ($wordIds as $wordId){
                $wordId = intval($wordId);
                $this->deleteWord($wordId);
            }
            $this->getSensitiveWordDao()->getConnection()->commit();
            return true;
        }catch(\Exception $e){
            $this->getSensitiveWordDao()->getConnection()->rollback();
            doLog($e->getMessage());
            return false;
            throw $e;
        }
    }

    public function deleteWord($wordId){
        return $this->getSensitiveWordDao()->deleteWord($wordId);
    }

}