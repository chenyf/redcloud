<?php

namespace Common\Model\Util;


class DictFilterUtil
{
    private static $dictory = null;
    private static $sensitiveWordList = null;
    private static $resTrie = null;

    public function __construct()
    {
        if(!extension_loaded("trie_filter")){
            return false;
        }

        if(empty(self::$dictory)){
            self::$dictory = getParameter('sensitive.dict.filter');
        }

        if(empty(self::$sensitiveWordList)){
            self::$sensitiveWordList = createService('System.SensitiveWordService')->getWordList();
        }

        if(empty(self::$resTrie)){
            self::$resTrie = trie_filter_new();
            foreach ( self::$sensitiveWordList as $k => $v) {
                trie_filter_store(self::$resTrie, $v);
            }
            trie_filter_save(self::$resTrie, self::$dictory . DIRECTORY_SEPARATOR . 'blackword.tree');
            self::$resTrie = trie_filter_load(self::$dictory . DIRECTORY_SEPARATOR . 'blackword.tree');
        }

        ini_set('memory_limit', '512M');
    }

    public function filterString($str){
        if(!extension_loaded("trie_filter")){
            return $str;
        }

        $arrRet = trie_filter_search_all(self::$resTrie, $str);
        $arrRet = array_map(function($item) use ($str){
            return substr($str, $item[0], $item[1]);
        },$arrRet);
        return str_ireplace($arrRet,"**",$str);
    }

}