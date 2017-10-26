<?php

namespace Common\Lib;

class EncryptTool
{

    public static function getSessionRoleKey(){
        return md5(strrev(C('WEBSITE_CODE')));
    }

    public static function getSessionRoleValue($code,$name){
        switch ($code){
            case 1:
                return md5(strrev(C('WEBSITE_CODE').str_pad($name,10,"&")));
                break;
            case 2:
                return sha1(strrev(C('WEBSITE_CODE').str_pad($name,10,"&")));
                break;
            case 3:
                return base64_encode(strrev(C('WEBSITE_CODE').str_pad($name,10,"&")));
                break;
        }
    }

}