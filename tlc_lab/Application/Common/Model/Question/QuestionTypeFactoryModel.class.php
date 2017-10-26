<?php
namespace  Common\Model\Question;


class QuestionTypeFactoryModel
{
    private static $cached = array();

    public static function create($type)
    {
        if (empty(self::$cached[$type])) {
            $type = ucfirst(str_replace('_', '', $type));
            $class = __NAMESPACE__  . "\\Type\\{$type}QuestionType";
            self::$cached[$type] = new $class();
        }

        return self::$cached[$type];
    }
}