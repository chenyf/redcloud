<?php
namespace Common\Model\Testpaper\Builder;

class TestpaperBuilderFactory
{
    public static function create($name)
    {
        $class = __NAMESPACE__ . "\\{$name}TestpaperBuilder";
        return new $class();
    }
}